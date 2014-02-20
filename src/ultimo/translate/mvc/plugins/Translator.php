<?php

namespace ultimo\translate\mvc\plugins;

abstract class Translator implements \ultimo\mvc\plugins\ApplicationPlugin {
  /**
   * The application the plugin is for.
   * @var \ultimo\mvc\Application
   */
  protected $application;
  protected $pluginName = 'translator';
  protected $theme;
  
  /**
   * The current set locale.
   * @var string
   */
  protected $locale;
  
  /**
   * The fallback locale used when the desired locale is not available.
   * @var string
   */
  protected $fallbackLocale;
  
  protected $availableLocales = array();
  
  /**
   * The session to store the persistent locale in.
   * @var \ultimo\util\net\Session
   */
  protected $session;
  
  public function __construct(\ultimo\mvc\Application $application, $fallbackLocale = 'en') {
    $this->application = $application;
    $this->setTheme($this->detectTheme($application));
    $this->setAvailableLocales(array($fallbackLocale));
    
    $this->setFallbackLocale($fallbackLocale);
    
    $this->session = new \ultimo\util\net\Session('ultimo.translate.mvc.plugins.Translator');
    if (isset($this->session->locale)) {
      $this->setLocale($this->session->locale);
    } else {
      $this->setLocale($fallbackLocale);
    }
  }
  
  public function setPluginName($pluginName) {
    $this->pluginName = $pluginName;
  }
  
  protected function detectTheme() {
    $viewRenderer = $this->application->getPlugin('viewRenderer');
    $theme = 'general';
    
    if ($viewRenderer !== null && $viewRenderer instanceof \ultimo\mvc\plugins\ViewRenderer) {
      $viewRendererTheme = $viewRenderer->getTheme();
      if ($viewRendererTheme !== null) {
        $theme = $viewRendererTheme;
      }
    }
    return $theme;
  }
  
  public function setTheme($theme) {
    $this->theme = $theme;
  }
  
  public function getTheme() {
    return $this->theme;
  }
  
  /**
   * Sets the fallback locale.
   * @param string $locale The locale to set as fallback.
   */
  public function setFallbackLocale($locale) {
    $this->fallbackLocale = $locale; 
  }
  
  /**
   * Returns the fallback locale.
   * @return string The fallback locale.
   */
  public function getFallbackLocale() {
    return $this->fallbackLocale;
  }
  
  /**
   * Sets the current locale.
   * @param string $locale The current locale to set, or null to unset the
   * locale. The fallback locale will then be used.
   * @param boolean $persistent Whether to use the locale between requests.
   */
  public function setLocale($locale, $persistent=false) {
    $this->locale = $locale;
    if ($persistent) {
      $this->session->locale = $locale;
      $this->session->flush();
    }
    
    if ($locale === null) {
      $locale = $this->fallbackLocale;
    }
    
    $this->formatter = \ultimo\util\locale\FormatterFactory::getFormatter(new \ultimo\util\locale\Locale($locale));
    if ($this->formatter === null) {
      $this->formatter = \ultimo\util\locale\FormatterFactory::getFormatter(new \ultimo\util\locale\Locale($this->fallbackLocale));
    }
  }
  
  /**
   * Returns the current locale, or null if no locale is set.
   * @return string The current locale.
   */
  public function getLocale() {
    return $this->locale;
  }
  
  public function setAvailableLocales(array $availableLocales) {
    $this->availableLocales = $availableLocales;
  }
  
  public function getAvailableLocales() {
    return $this->availableLocales;
  }
  
  public function onPluginAdded(\ultimo\mvc\Application $application) { }
  
  public function onModuleCreated(\ultimo\mvc\Module $module) { }
  
  public function onRoute(\ultimo\mvc\Application $application, \ultimo\mvc\Request $request) { }
  
  public function onRouted(\ultimo\mvc\Application $application, \ultimo\mvc\Request $request=null) { }
  
  public function onDispatch(\ultimo\mvc\Application $application) { }
  
  public function onDispatched(\ultimo\mvc\Application $application) { }
}