<?php

namespace ultimo\translate\sources;

abstract class FileSource implements \ultimo\translate\Source {
  private $path;
  private $translationsCache = null;
  
  public function __construct($path) {
    $this->path = $path;
  }
  
  public function resetCache() {
    $this->translationsCache = null;
  }
  
  public function getTranslation($key) {
    $this->initCache();
    
    foreach ($this->translationsCache as $filePath => $fileTranslations) {
      if ($fileTranslations === null) {
        $fileTranslations = $this->getTranslationsInFile($filePath);
      }
      
      if (isset($fileTranslations[$key])) {
        return $fileTranslations[$key];
      }
    }
    
    return null;
  }
  
  public function getTranslations() {
    $this->initCache();
    $translations = array();
    foreach ($this->translationsCache as $filePath => $fileTranslations) {
      if ($fileTranslations === null) {
        $fileTranslations = $this->getTranslationsInFile($filePath);
      }
      
      $translations = array_merge($translations, $fileTranslations);
    }
    return $translations;
  }
  
  private function initCache() {
    if ($this->translationsCache !== null) {
      return;
    }
    $this->translationsCache = array();
    if (is_dir($this->path)) {
      if ($handle = opendir($this->path)) {
        while (false !== ($file = readdir($handle))) {
          $filePath = $this->path . DIRECTORY_SEPARATOR . $file;
          if (!is_file($filePath)) {
            continue;
          }
          $this->translationsCache[$filePath] = null;
        }
        closedir($handle);
      }
    } else {
      $this->translationsCache[$this->path] = array();
    }
  }
  
  private function getTranslationsInFile($filePath) {
    if (!isset($this->translationsCache[$filePath])) {
      if (is_readable($filePath)) {
        $this->translationsCache[$filePath] = $this->readFile($filePath);
      } else {
        $this->translationsCache[$filePath] = array();
      }
    }
    
    return $this->translationsCache[$filePath];
  }
  
  abstract protected function readFile($filePath);
}