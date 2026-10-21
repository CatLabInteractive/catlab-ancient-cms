<?php
/*
	Neuron CMS
	Author: Thijs Van der Schaeghe
	Copyright: Neuron Interactive
*/

error_reporting(E_ALL);

session_start ();

set_include_path (get_include_path () . PATH_SEPARATOR . CMS_SYSTEM_PATH.'php' . PATH_SEPARATOR . CMS_CLIENT_PATH.'php');
require 'fix_php7_mysql.php';

/*
	Check for configuration 
*/
if
(
	!(
		defined ('CMS_SYSTEM_PATH')
		&& defined ('CMS_CLIENT_PATH')
		&& defined ('CMS_FULL_URL')
		&& defined ('DB_SERVER')
		&& defined ('DB_USERNAME')
		&& defined ('DB_PASSWORD')
		&& defined ('DB_DATABASE')
		&& defined ('CMS_NICE_URLS')
	)
)
{
	die ('<h1>System error</h1><p>Incomplete configuration!</p>');
}

/*
	Magic quotes were removed in PHP 5.4; the old stripslashes() loop
	(get_magic_quotes_gpc / each) no longer exists on PHP 8.
*/

/* 
	Auto include (real OOP, hehe) 
	__autoload() was removed in PHP 8.0; use spl_autoload_register().
*/
spl_autoload_register (function ($classname)
{
	$classpath = str_replace ('_', '/', $classname) . '.php';
	
	// Check for file in the CMS dir
	if (file_exists (CMS_CLIENT_PATH.'php/'.$classpath))
	{
		include_once (CMS_CLIENT_PATH.'php/'.$classpath);
	}
	elseif (file_exists (CMS_SYSTEM_PATH.'php/'.$classpath))
	{
		include_once (CMS_SYSTEM_PATH.'php/'.$classpath);
	}
	else
	{
		return false;
	}
});

function br2nl($string)
{
    return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
}

$_SESSION = $_SESSION;
$_REQUEST = $_REQUEST;