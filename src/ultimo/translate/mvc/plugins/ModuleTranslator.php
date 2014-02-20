<?php

namespace ultimo\translate\mvc\plugins;

abstract class ModuleTranslator implements \ultimo\mvc\plugins\ModulePlugin, \ultimo\translate\Translator {

  /**
   * The creator of the plugin.
   * @var Translator
   */
  protected $applicationPlugin;
  

  
  /**
   * @var \ultimo\mvc\Module
   */
  protected $module;
  
  /**
   * @var \ultimo\mvc\Application
   */
  protected $application;
  
  protected $sourceChainlocale;
  protected $sourceChainFallbackLocale;
  protected $sourceChain = null;
  protected $sources = array();
  
  public function __construct(Translator $applicationPlugin, \ultimo\mvc\Module $module) {
    $this->applicationPlugin = $applicationPlugin;
    $this->module = $module;
    $this->application = $module->getApplication();
  }
  
  public function getModule() {
    return $this->module;
  }
  
  public function setLocale($locale, $persistent=false) {
    $this->applicationPlugin->setLocale($locale, $persistent);
  }
  
  public function getLocale() {
    return $this->applicationPlugin->getLocale();
  }
  
  public function getAvailableLocales() {
    return $this->applicationPlugin->getAvailableLocales();
  }

  protected function getSourceChain() {
    if ($this->checkRebuildSourceChain()) {
      $this->rebuildSourceChain();
    }
    return $this->sourceChain;
  }
  
  protected function checkRebuildSourceChain() {
    return ($this->sourceChain === null ||
            $this->sourceChainLocale !== $this->applicationPlugin->getLocale() ||
            $this->sourceChainFallbackLocale !== $this->applicationPlugin->getFallbackLocale());
  }
  
  protected function rebuildSourceChain() {
    $this->sourceChainLocale = $this->applicationPlugin->getLocale();
    $this->sourceChainFallbackLocale = $this->applicationPlugin->getFallbackLocale();
    $this->sourceChain = $this->createSourceChain($this->sourceChainLocale, $this->sourceChainFallbackLocale);
  }
  
  protected function createSourceChain($locale, $fallbackLocale) {
    $locales = array($locale);
    
    $localeObj = new \ultimo\util\locale\Locale($locale);
    $language = $localeObj->getLanguage();
    
    if ($language != $locale) {
      $locales[] = $language;
    }
    
    if ($fallbackLocale !== null && $fallbackLocale != $locale && $fallbackLocale != $language) {
      $locales[] = $fallbackLocale;
    }
    
    $modules = array();
    $module = $this->module;
    while($module !== null) {
      $modules[] = $module;
      foreach ($module->getPartials() as $partial) {
        $modules[] = $partial;
      }
      $module = $module->getParent();
    }
    $modules[] = $this->application->getGeneralModule();
    
    $sourceChain = new \ultimo\translate\SourceChain();
    foreach ($locales as $locale) {
      foreach ($modules as $module) {
        $path = $module->getNamespace() . DIRECTORY_SEPARATOR . $locale;
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
  
  public function translate($key, array $vars = array(), $locale = null) {
    if ($locale !== null && $locale !== $this->sourceChainLocale) {
      $sourceChain = $this->createSourceChain($locale, $this->sourceChainFallbackLocale);
    } else {
      $sourceChain = $this->getSourceChain();
    }
    
    $text = $sourceChain->getTranslation($key);
    if ($text !== null) {
      $text = $this->replaceVars($text, $vars);
    }
    return $text;
  }
  
  protected function replaceVars($text, array $vars) {
    if (is_array($text)) {
      foreach ($text as $index => $textItem) {
        $text[$index] = $this->replaceVars($textItem, $vars);
      }
    } else {
      preg_match_all('/\{{([a-zA-Z0-9_\.]+)\}}/', $text, $varsToReplace);
      
      foreach($varsToReplace[1] as $i => $name) {
        if (isset($vars[$name])) {
          $text = str_replace($varsToReplace[0][$i], $vars[$name], $text);
        }
      }
    }
    
    return $text;
  }
  
  public function onControllerCreated(\ultimo\mvc\Controller $controller) { }
}