<?php

namespace ultimo\translate\mvc\plugins;

class ModuleFileTranslator extends ModuleTranslator {
  protected $fileSourceClass;
  
  public function __construct(Translator $translator, \ultimo\mvc\Module $module) {
    if (!$translator instanceof FileTranslator) {
      throw new \InvalidArgumentException("'translator' must be of type FileTranslator");
    }
    parent::__construct($translator, $module);
    $this->fileSourceClass = $this->applicationPlugin->getFileSourceClass();
  }
  
  
  public function createSources($module, $locale) {
    return array(
      new $this->fileSourceClass($module->getBasePath() . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $this->applicationPlugin->getTheme() . DIRECTORY_SEPARATOR . 'languages'. DIRECTORY_SEPARATOR . $locale),
      new $this->fileSourceClass($module->getBasePath() . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'general' . DIRECTORY_SEPARATOR . 'languages'. DIRECTORY_SEPARATOR . $locale),
    );
  }
}