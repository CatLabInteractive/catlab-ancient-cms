<?php
/*
	Neuron CMS
	Author: Thijs Van der Schaeghe
	Copyright: Neuron Interactive
*/
class Pages_Login extends Modules_Module
{
	public function getContent ()
	{
		$login = Core_Login::__getInstance ();
		
		if ($login->isLogin ())
		{
			header ('Location: '.$this->objCMS->getAdminUrl ());
		}
		
		$text = Core_Text::__getInstance ();
		$text->setFile ('admin');
		$text->setSection ('login');
		
		$email = Core_Tools::getInput ('_POST', 'login_email', 'varchar');
		$pass = Core_Tools::getInput ('_POST', 'login_password', 'varchar');
		
		$page = new Core_Template ();
		
		// Fetch the text values
		$page->set ('login', $text->get ('login'));
		$page->set ('about', $text->get ('about'));
		$page->set ('email', $text->get ('email'));
		$page->set ('password', $text->get ('password'));
		$page->set ('submit', $text->get ('submit'));
		$page->set ('account', $text->get ('account'));
		
		if ($email && $pass)
		{
			if ($login->doLogin ($email, $pass))
			{
				// Login succeeded.
				header ('Location: '.$this->objCMS->getAdminUrl ());
			}
			else
			{
				// Login failed:
				$page->set ('sError', $text->get ('notFound'));
			}
		}
		
		$page->set ('submit_url', $this->objCMS->getAdminUrl ('login'));
		
		return $page->parse ('pages/login/loginform.phpt');
	}
	
	/*
		This login form will be displayed without any other data.
		(Makes you think about Wordpress, right? Well, that's the basic idea.)
	*/
	public function getHTML ($template = 'index.phpt')
	{
		return parent::getHTML ('login.phpt');
	}
}
?>
