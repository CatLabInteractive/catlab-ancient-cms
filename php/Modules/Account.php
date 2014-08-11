<?php
/*
	Neuron CMS
	Author: Thijs Van der Schaeghe
	Copyright: Neuron Interactive
*/
class Modules_Account extends Modules_Page
{
	private $gambic = null;

	protected function initialize ()
	{
		$this->gambic = new Models_Gambic ();
	}

	public function getEditorActions ($id)
	{
		$page = new Core_Template ();
		$page->set ('remove_url', $this->objCMS->getAdminUrl ('pages', 'delete', $id, 'output=json'));
		return $page->parse ('modules/page/actions.phpt');
	}

	public function getNavigationRow ($page)
	{
		$row = parent::getNavigationRow ($page);

		$row['bShow'] = true;

		switch ($page['n_options'])
		{
			case 'play':
				$row['sName'] = 'Play';
				$row['sUrl'] = $this->objCMS->getUrl ($page['n_module'], 'select-server');
			break;

			case 'register':
				$row['sName'] = 'Register';
				$row['sUrl'] = $this->objCMS->getUrl ($page['n_module'], 'register');

				if ($this->gambic->isLogin ())
				{
					$row['bShow'] = false;
				}
			break;

			case 'login':
			default:
				if ($this->gambic->isLogin ())
				{
					$row['sName'] = 'Logout';
					$row['sUrl'] = $this->objCMS->getUrl ($page['n_module'], 'logout');
				}
				else
				{
					$row['sName'] = 'Login';
					$row['sUrl'] = $this->objCMS->getUrl ($page['n_module'], 'login');
				}
			break;
		}

		return $row;
	}

	public function getHTML ()
	{
		$action = $this->objCMS->getAction ();

		switch ($action)
		{
			case 'api':

				header ('Content-type: application/json');

				$subaction = $this->objCMS->getSubAction ();

				switch ($subaction)
				{
					case 'login':
						return json_encode ($this->getPersonaLogin ());
					break;

					case 'logout':
						return json_encode ($this->getPersonaLogin ());
					break;
				}

			break;

			default:
				return parent::getHTML ();
			break;
		}
	}

	private function getPersonaLogin ()
	{
		$assertion = Core_Tools::getInput ('_POST', 'assertion', 'varchar');
		$response = $this->gambic->personaLogin ($assertion);

		if ($response)
		{
			$_SESSION['personauser'] = $this->gambic->getEmail ();

			$out = array ();
			$out['status'] = 'success';
			return $out;
		}

		else
		{
			$out['status'] = 'error';
			$out['error'] = 'Something went wrong while connecting with the server.';
			$out['debug'] = $this->gambic->getDebug ();
		}

		return $out;
	}

	private function getPersonaLogout ()
	{
		$out = array ();
		$out['status'] = 'success';
		return $out;
	}
	
	public function getContent ()
	{
		$action = $this->objCMS->getAction ();

		if ($action === 'logout')
		{
			return $this->logout ();
		}

		else if ($action === 'select-server')
		{
			if ($this->gambic->isLogin ())
			{
				return $this->selectServer ();
			}
			else
			{
				return $this->login ($this->gambic->getError ());
			}
		}

		else if ($action === 'play')
		{
			if ($this->gambic->isLogin ())
			{
				return $this->play ();
			}
			else
			{
				return $this->login ($this->gambic->getError ());
			}
		}

		else if ($this->gambic->isLogin ())
		{
			//return $this->play ();
			header ('Location: ' . $this->objCMS->getUrl ('account', 'select-server'));
			return null;
		}

		else if ($action === 'register')
		{
			return $this->register ();
		}

		else
		{
			return $this->login ();
		}

	}

	private function selectServer ()
	{
		$nickname = $this->gambic->getNickname ();

		if ($this->gambic->hasRequirements ())
		{
			return $this->askRequirements ();
		}
		else
		{
			$servers = $this->gambic->getServers ();
			
			//header ("Location: " . $this->gambic->getPlayURL ());
			//return '<p><a href="' . $this->gambic->getPlayURL () . '">Redirecting to game</a></p>';
			$page = new Core_Template ();

			foreach ($servers as $k => $v)
			{
				$servers[$k]['play_url'] = $this->objCMS->getUrl ('account', 'play', $v['id']);
			}

			$page->set ('servers', $servers);

			return $page->parse ('modules/gambic/servers.phpt');
		}
	}

	private function play ()
	{
		$nickname = $this->gambic->getNickname ();

		if ($this->gambic->hasRequirements ())
		{
			return $this->askRequirements ();
		}
		else
		{
			$servers = $this->gambic->getServers ();

			$server = $this->objCMS->getRecordId ();
			if ($server)
			{
				foreach ($servers as $v)
				{
					if ($v['id'] == $server)
					{
						header ('Location: ' . $this->gambic->getPlayURL ($v));
						return '<p>Redirecting to game...</p>';
					}
				}
			}

			header ('Location: ' . $this->objCMS->getUrl ('account', 'select-server'));
			return;
		}
	}

	private function askRequirements ()
	{
		$out = array ();

		if (isset ($_POST['register']))
		{
			foreach ($this->gambic->getRequirements () as $v)
			{
				$value = Core_Tools::getInput ('_REQUEST', 'requirements_' . $v['name'], 'varchar');
				if (isset ($value) && $value)
				{
					$out[$v['name']] = $value;
				}

				else if (strpos ($v['validation'], 'optional') !== false)
				{
					$out[$v['name']] = 0;	
				}
			}
		}

		if (count ($out) > 0)
		{
			if ($this->gambic->setRequirements ($out))
			{
				header ('Location: ' . $this->objCMS->getUrl ('account', 'play'));
			}
		}

		$page = new Core_Template ();

		$page->set ('play_url', $this->objCMS->getUrl ('account', 'play'));
		$page->set ('requirements', $this->gambic->getRequirements ());

		return $page->parse ('modules/gambic/requirements.phpt');
	}

	private function chooseNickname ()
	{
		$okay = false;

		// Requirements:
		// $_SESSION['gambic_requirements']

		$nickname = Core_Tools::getInput ('_POST', 'register_username', 'varchar');
		if ($nickname)
		{
			if ($this->gambic->setNickname ($nickname))
			{
				$okay = true;
			}
			else
			{
				$error = $this->gambic->getError ();
			}
		}

		if (!$okay)
		{
			$page = new Core_Template ();
			$page->set ('play_url', $this->objCMS->getUrl ('account', 'play'));

			if (isset ($error))
			{
				$page->set ('error', $error);
			}

			return $page->parse ('modules/gambic/choosenickname.phpt');
		}

		else
		{
			header ('Location: ' . $this->objCMS->getUrl ('account', 'play'));
		}
	}

	private function logout ()
	{
		$this->gambic->logout ();
		header ('Location: ' . $this->objCMS->getUrl ('account', 'play'));
	}

	private function login ($error = null)
	{
		$text = Core_Text::__getInstance ();

		$email = Core_Tools::getInput ('_POST', 'email', 'varchar');
		$pass = Core_Tools::getInput ('_POST', 'password', 'varchar');

		if ($email && $pass)
		{
			if ($this->gambic->login ($email, $pass))
			{
				//header ('Location: ' . $this->gambic->getPlayURL () );
				//return '<p><a href="' . $this->gambic->getPlayURL () . '">Redirecting to game</a></p>';
				header ('Location: ' . $this->objCMS->getUrl ('account', 'play'));
				return $this->play ();
			}
			else
			{
				$error = $this->gambic->getError ();
			}
		}
		
		$page = new Core_Template ();

		$page->set ('login_action', $this->objCMS->getUrl ('account', 'login'));
		$page->set ('register_url', $this->objCMS->getUrl ('account', 'register'));

		if (isset ($error))
		{
			$page->set ('error', $text->get ($error, 'account', 'gambic'));
		}

		return $page->parse ('modules/gambic/login.phpt');
	}

	private function register ()
	{
		return '<p>Please use persona login to register.</p>';

		$text = Core_Text::__getInstance ();

		$error = null;

		$register = Core_Tools::getInput ('_POST', 'register', 'varchar');

		if ($register)
		{
			$username = Core_Tools::getInput ('_POST', 'register_username', 'varchar');
			$email = Core_Tools::getInput ('_POST', 'register_email', 'varchar');
			$password = Core_Tools::getInput ('_POST', 'register_password', 'varchar');
			$repeatpassword = Core_Tools::getInput ('_POST', 'register_password-repeat', 'varchar');

			if (strlen ($username) < 3)
			{
				$error = 'NO_USERNAME';
			}

			else if (strlen ($email) < 3)
			{
				$error = 'INVALID_EMAIL';
			}

			else if (strlen ($password) < 3)
			{
				$error = 'INSUFFICIENT_PASSWORD';
			}

			else if ($password !== $repeatpassword)
			{
				$error = 'PASSWORD_MISMATCH';
			}

			else {
				// Check for registration
				$result = $this->gambic->register ($username, $email, $password);

				if ($result)
				{
					header ('Location: ' . $this->objCMS->getUrl ('account', 'play'));
					return $this->play ();
				}
				else
				{
					$error = $this->gambic->getError ();
				}
			}
		}

		$page = new Core_Template ();

		$page->set ('register_action', $this->objCMS->getUrl ('account', 'register'));
		$page->set ('login_url', $this->objCMS->getUrl ('account', 'login'));

		if (isset ($error))
		{
			$page->set ('registrationError', $text->get (strtoupper ($error), 'account', 'gambic'));
		}

		return $page->parse ('modules/gambic/register.phpt');
	}

	private function saveContentFromPost ($id)
	{
		$db = Core_Database::__getInstance ();

		// First: take all translatable fields
		$trans = array ();
		foreach ($this->aFields as $v)
		{
			if ($v['bTranslated'])
			{
				$trans[] = $v;
			}
		}
		
		foreach ($this->objCMS->getAllLanguages () as $langk => $lang)
		{
			$sText = "";
			$sTitle = "";
			foreach ($trans as $v)
			{
				if ($v['sName'] == 'title')
				{
					$sTitle = Core_Tools::getInput ('_POST', $v['sName'].'_'.$langk, 'varchar');
				}
				else
				{
					$sInput = Core_Tools::getInput ('_POST', $v['sName'].'_'.$langk, 'varchar');
				
					switch ($v['sType'])
					{
						case 'html':
							$sInput = Core_Tools::fixTinyMCEInput ($sInput);
						break;
					}
				
					// | is the special character here.
					$sText .= str_replace 
					(
						'|',
						'&#124;',
						$sInput
					) . '|';
				}
				$sText = substr ($sText, 0, -1);
			}
			
			$this->setMetaContent ($langk, $id, $sTitle, $sText);
		}
	}

	public function getEditorJSON ()
	{
		$id = intval ($this->objCMS->getRecordId ());
		
		$db = Core_Database::__getInstance ();
		
		// Option
		$option = Core_Tools::getInput ('_POST', 'overview_type', 'varchar');
		if ($option)
		{
			$db->update
			(
				'navigation',
				array
				(
					'n_options' => $option
				),
				"n_id = ".$id
			);
		}
		
		foreach ($this->objCMS->getAllLanguages () as $k => $v)
		{
			$sTitle = Core_Tools::getInput ('_POST', 'title_'.$k, 'varchar');
			$this->setMetaContent ('page', $k, $id, $sTitle, null);
		}
		
		return json_encode
		(
			array
			(
				'status' => 'success',
				'message' => 'Your page has been saved.'
			)
		);
	}

	public function getPluginEditor ()
	{
		$id = $this->objCMS->getRecordId ();
		$aContent = $this->getMetaContent ($id, true);

		$page = new Core_Template ();

		$page->set ('content_action', $this->objCMS->getAdminUrl ('pages', 'edit', $id, 'output=json'));
		$page->set ('overview', $aContent['n_options']);

		return $page->parse ('modules/gambic/form.phpt');
	}
}
?>
