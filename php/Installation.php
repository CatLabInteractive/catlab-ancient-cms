<?php
class Installation
{
	public static function doMysqlInitialSetup ()
	{
		$db = Core_Database::__getInstance ();
		
		// Table site settings
		$db->customQuery 
		("
			CREATE TABLE IF NOT EXISTS `sitesettings` 
			(
				`s_id` INT NOT NULL AUTO_INCREMENT ,
				`s_key` VARCHAR( 25 ) collate utf8_unicode_ci NOT NULL ,
				`s_lang` VARCHAR( 2 ) collate utf8_unicode_ci NULL ,
				`s_value` TEXT collate utf8_unicode_ci NOT NULL ,
				PRIMARY KEY ( `s_id` )
			)
			ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");
		
		$db->customQuery
		("
			CREATE TABLE IF NOT EXISTS `meta2content` (
				`m_id` int(11) NOT NULL auto_increment,
				`c_id` int(11) NOT NULL,
				`m_type` varchar(20) collate utf8_unicode_ci NOT NULL,
				`m_lang` varchar(2) collate utf8_unicode_ci NOT NULL,
				`m_title` varchar(255) collate utf8_unicode_ci NOT NULL,
				`m_text` text collate utf8_unicode_ci NOT NULL,
				PRIMARY KEY  (`m_id`)
			)
			ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		
		$db->customQuery
		("
			CREATE TABLE IF NOT EXISTS `navigation` (
				`n_id` int(11) NOT NULL auto_increment,
				`n_container` varchar(20) collate utf8_unicode_ci default NULL,
				`n_p_id` int(11) NOT NULL,
				`n_level` tinyint(4) NOT NULL,
				`n_module` varchar(20) collate utf8_unicode_ci NOT NULL,
				`n_sorting` int(11) NOT NULL default '0',
				`n_options` varchar(50) collate utf8_unicode_ci NOT NULL,
				PRIMARY KEY  (`n_id`)
			)
			ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
	}
	
	public static function doGenericTableSetup ($sTable, $fields)
	{
		$db = Core_Database::__getInstance ();
		
		$f = substr ($sTable, 0, 1);
		
		$sQuery = "
			CREATE TABLE IF NOT EXISTS `".$sTable."` 
			(
				`{$f}_id` INT NOT NULL AUTO_INCREMENT ,
		";

		foreach ($fields as $k => $v)
		{
			$sQuery .= "`{$f}_{$k}` {$v} collate utf8_unicode_ci NOT NULL, ";
		}
		
		$sQuery .= "
				PRIMARY KEY ( `{$f}_id` )
			)
			ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		";
		
		$db->customQuery ($sQuery);
	}
}
?>
