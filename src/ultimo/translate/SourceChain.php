<?php

namespace ultimo\translate;

class SourceChain {
  private $sources = array();
  
  public function appendSource(Source $source) {
    array_unshift($this->sources, $source);
  }
  
  public function prependSource(Source $source) {
    $this->sources[] = $source;
  }
  
  public function getTranslation($key) {
    foreach ($this->sources as $source) {
      $translation = $source->getTranslation($key);
      if ($translation !== null) {
        return $translation;
      }
    }
    
    return null;
  }
}