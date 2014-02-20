<?php

namespace ultimo\translate;

interface Translator {
  public function translate($key, array $vars = array(), $locale = null);
}