<?php

namespace ultimo\translate;

interface Source {
  public function getTranslation($key);
  
  public function getTranslations();
  
  public function resetCache();
  //public function putTranslations(array $translations);
}