<?php

namespace ultimo\translate\mvc\phptpl\helpers;

class Translate extends \ultimo\phptpl\mvc\Helper {
  
  /**
   * Helper initial function. Translates a key.
   * @param string $key The key to translate.
   * @param array $vars The variables to replace in the translation.
   * @param string $locale The locale to translate into, or null to use the
   * current locale.
   * @param string $moduleNamespace Namespace of the module to fetch the
   * translation from. Null to use the current namespace.
   * @return string The translation.
   */
  public function __invoke($key, array $vars = array(), $locale=null, $moduleNamespace=null) {
    if ($moduleNamespace === null) {
      $module = $this->engine->getModule();
    } else {
      $module = $this->engine->getApplication()->getModule($moduleNamespace);
    }
    return $module->getPlugin('translator')->translate($key, $vars, $locale);
  }
}