<?php

namespace ultimo\translate;

abstract class ModularTranslator {
  private $locale;
  private $module;
  private $fallbackLocale;
  private $sourceChain;
  private $fallbackModule = 'general';
  private $sources = array();
  
  public function __construct($module, $locale, $fallbackLocale=null) {
    $this->locale = $locale;
    $this->module = $module;
    $this->fallbackLocale = $fallbackLocale;
    $this->rebuildSourceChain();
  }
  
  public function setModule($module) {
    $this->module = $module;
    $this->rebuildSourceChain();
  }
  
  public function setLocale($locale) {
    $this->locale = $locale;
    $this->rebuildSourceChain();
  }
  
  public function setFallbackLocale($locale) {
    $this->fallbackLocale = $locale;
    $this->rebuildSourceChain();
  }
  
  private function rebuildSourceChain() {
    $this->sourceChain = $this->createSourceChain($this->module, $this->fallbackModule, $this->locale, $this->fallbackLocale);
  }
  
  private function createSourceChain($module, $fallbackModule, $locale, $fallbackLocale) {
    $locales = array($locale);
    
    $baseLocale = static::extractBaseLocale($locale);
    if ($baseLocale != $locale) {
      $locales[] = $baseLocale;
    }
    
    if ($fallbackLocale !== null && $fallbackLocale != $locale && $fallbackLocale != $baseLocale) {
      $locales[] = $fallbackLocale;
    }
    
    $modules = array($module, $fallbackModule);
    
    $sourceChain = new SourceChain();
    foreach ($locales as $locale) {
      foreach ($modules as $module) {
        $path = $module . '/' . $locale;
        if (!isset($this->sources[$path])) {
          $this->sources[$path] = $this->createSources($module, $locale);
        }
        
        foreach ($this->sources[$path] as $source) {
          $sourceChain->prependSource($source);
        }
      }
    }
    
    return $sourceChain;
  }
  
  abstract protected function createSources($module, $locale);
  
  private static function extractBaseLocale($locale) {
    list($language, $country) = explode('_', $locale);
    return $language . '_' . strtoupper($language);
  }
  
  public function translate($key, array $vars = array(), $locale = null) {
    if ($locale !== null && $locale !== $this->locale) {
      $sourceChain = $this->createSourceChain($this->module, $this->fallbackModule, $locale, $this->fallbackLocale);
    } else {
      $sourceChain = $this->sourceChain;
    }
    
    $text = $sourceChain->getTranslation($key);
    if ($text !== null) {
      $text = $this->replaceVars($text, $vars);
    }
    return $text;
  }
  
  protected function replaceVars($text, array $vars) {
    preg_match_all('/\{{([a-zA-Z0-9_\.]+)\}}/', $text, $varsToReplace);
    
    foreach($varsToReplace[1] as $i => $name) {
      if (isset($vars[$name])) {
        $text = str_replace($varsToReplace[0][$i], $vars[$name], $text);
      }
    }
    
    return $text;
  }
}