<?php
/*
	Neuron CMS
	Author: Thijs Van der Schaeghe
	Copyright: Neuron Interactive
*/

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
	Stupid magic quotes
*/
if (get_magic_quotes_gpc())
{
        $in = array(&$_GET, &$_POST, &$_COOKIE);
        while (list($k,$v) = each($in))
        {
                foreach ($v as $key => $val)
                {
                        if (!is_array($val))
                        {
                                $in[$k][$key] = stripslashes($val);
                                continue;
                        }
                        $in[] =& $in[$k][$key];
                }
        }
        unset ($in);
}

/* 
	Auto include (real OOP, hehe) 
*/
function __autoload ($classname)
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
}

error_reporting(E_ALL ^ E_STRICT);