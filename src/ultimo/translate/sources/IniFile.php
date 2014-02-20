<?php

namespace ultimo\translate\sources;

class IniFile extends FileSource {
  protected function readFile($filePath) {
    $translations = array();

    $iniValues = @parse_ini_file($filePath);
    if (!$iniValues) {
      return array();
    }
    
    foreach($iniValues as $key => $text) {
      if ($key[0] == '\\') {
        $key = substr($key, 1);
      }
      $translations[$key] = $text;
    }
    return $translations;
  }
}