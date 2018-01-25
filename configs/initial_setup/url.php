<?php
/**Возврашает пути скриптов, используемых в проекте
 * @return "PATH_ROOT" 		- проекта
 * @return "PATH_CONFIGS"	- конфигурационных файлов
 * @return "PATH_SETUP"		- первоначальные настройки
 * @return "PATH_CONTROLLER"-
 * @return "PATH_MODEL"		- модели
 * @return "PATH_IMG"		- изображений
 * @return "PATH_BD_IMG"	- изображений для работы с БД
 * @return "PATH_JS"		- JavaScript
 * @return "PATH_LIB"		- библиотека простых скриптов
 * @return "PATH_VIEW"		- шаблонов
 * @return "PATH_TEMPLATE"	- шаблонов heder & footer
 * */
define("PATH_ROOT",		 	$_SERVER['DOCUMENT_ROOT']);
define("PATH_CONFIGS",	 	'configs/');
define("PATH_SETUP",	 	'configs/initial_setup/');

define("PATH_CONTROLLER",	"c/");

define("PATH_MODEL",	 	"m/");
define("PATH_IMG",	 	 	"m/img/");
define("PATH_DB_IMG",	 	"m/img/db/");
define("PATH_JS",	 		"m/js/");
define("PATH_LIB",	 		"m/lib/");

define("PATH_VIEW", 	 	"v/");
define("PATH_TEMPLATE", 	"v/template/");





