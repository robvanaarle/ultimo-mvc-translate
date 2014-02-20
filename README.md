# Ultimo Translate MVC
Translations for Ultimo MVC

An Phptpl helper for translations is supplied. Translations are inherited from:
* Parent modules
* Parent themes
* Parent locales (nl_BE inherits from nl_NL)

## Requirements
* PHP 5.3
* Ultimo Translate
* Ultimo PhpTpl MVC
* Ultimo MVC
* Ultimo Session

## Usage
### Register plugin
	$translator = new \ultimo\translate\mvc\plugins\FileTranslator($this->application, 'nl');
    $translator->setAvailableLocales(array('nl_BE', 'nl', 'en'));
    $this->application->addPlugin($translator);

### &lt;module&gt;/views/&lt;theme&gt;/languages/en_US/admin.ini
All translation files in &lt;module&gt;/views/&lt;theme&gt;/languages/&lt;locale&gt; are merged.

	user.create = "Add user"
	user.delete = "Remove user"

### View
	<?php echo $this->translate('user.create') ?>