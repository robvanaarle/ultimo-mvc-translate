<?php

namespace ultimo\translate\mvc\plugins;

class FileTranslator extends Translator {
  protected $fileSourceClass = 'ultimo\translate\sources\IniFile';
  
  public function setFileSourceClass($fileSourceClass) {
    $this->fileSourceClass = $fileSourceClass;
  }
  
  public function getFileSourceClass() {
    return $this->fileSourceClass;
  }
  
  public function onModuleCreated(\ultimo\mvc\Module $module) {
    if ($this->locale === null) {
      $locale = $this->fallbackLocale;
    } else {
      $locale = $this->locale;
    }
    $translator = new \ultimo\translate\mvc\plugins\ModuleFileTranslator($this, $module);
    $module->addPlugin($translator, $this->pluginName);
    
    // add the view helpers directory, if the view is phptpl
    $view = $module->getView();
    if ($view instanceof \ultimo\phptpl\Engine) {
      $helperPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'phptpl' . DIRECTORY_SEPARATOR . 'helpers';
      
      $nsElems = explode('\\', __NAMESPACE__);
      array_pop($nsElems);
      array_push($nsElems, 'phptpl', 'helpers');
      $helperNamespace = '\\' . implode('\\', $nsElems);
      $view->addHelperPath($helperPath, $helperNamespace);
    }
  }
}