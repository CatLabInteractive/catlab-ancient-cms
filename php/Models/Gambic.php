<?php

//define ('GAMBIC_API_URL', 'http://localhost/gambic/games/rest/dolumar/');

if (!defined ('GAMBIC_API_URL'))
{
	define ('GAMBIC_API_URL', 'http://api.catlab.eu/dolumar/');
}

/*
	Neuron CMS
	Author: Thijs Van der Schaeghe
	Copyright: Neuron Interactive
*/
class Models_Gambic
{
	const API_URL = GAMBIC_API_URL;

	private $error;
	private $debug;

	public function __construct ()
	{

	}

	private function getReferer ()
	{
		if (isset ($_COOKIE['cms_ref_ref']))
		{
			return $_COOKIE['cms_ref_ref'];
		}
		return null;
	}

	public function isLogin ()
	{
		if (isset ($_SESSION['gambic_token']))
		{
			return true;
		}
		return false;
	}

	public function logout ()
	{
		unset ($_SESSION['gambic_token']);
		unset ($_SESSION['gambic_nickname']);
	}

	public function getNickname ()
	{
		if ($this->isLogin ())
		{
			return $_SESSION['gambic_nickname'];
		}
		return null;
	}

	public function hasRequirements ()
	{
		return count ($this->getRequirements ()) > 0;
	}

	public function getRequirements ()
	{
		if ($this->isLogin ())
		{
			if (isset ($_SESSION['gambic_requirements']) && count ($_SESSION['gambic_requirements']) > 0)
			{
				//var_dump ($_SESSION['gambic_requirements']);
				return $_SESSION['gambic_requirements'];
			}
		}
		return array ();
	}

	public function setRequirements ($requirements)
	{
		if (!isset ($_SESSION['gambic_token']))
		{
			throw new Exception ("Game token not set.");
		}

		if (!is_array ($requirements))
		{
			throw new Exception ("Requirements must be an array in the form of (key => value)");
		}

		$reqs = array ();
		foreach ($this->getRequirements () as $v)
		{
			$value = isset ($requirements[$v['name']]) ? $requirements[$v['name']] : null;

			if (isset ($value))
			{
				$reqs[] = array 
				(
					'name' => $v['name'],
					'value' => $value
				);
			}
		}

		// Call the api
		$chk = $this->file_get_contents
		(
			self::API_URL . 'setrequirements',
			array
			(
				'token' => $_SESSION['gambic_token'],
				'requirements' => json_encode ($reqs)
			)
		);

		//var_dump ($chk);

		$chk = json_decode ($chk, true);

		if ($chk && $chk['status'] == 'success')
		{
			$this->doLogin ($chk);
			return true;
		}
		else if ($chk)
		{
			$this->error = $chk['error'];
			$_SESSION['gambic_requirements'] = $chk['requirements'];

			return false;
		}
		else
		{
			$this->error = 'Could not connect to authentication server.';
			return false;
		}
	}

	/*
	public function setNickname ($nickname)
	{
		if (!isset ($_SESSION['gambic_token']))
		{
			throw new Exception ("Game token not set.");
		}

		// Call the api
		$chk = $this->file_get_contents
		(
			self::API_URL . 'setnickname',
			array
			(
				'nickname' => $nickname,
				'token' => $_SESSION['gambic_token']
			)
		);		

		$chk = json_decode ($chk, true);

		if ($chk && $chk['status'] === 'success')
		{
			$_SESSION['gambic_nickname'] = $nickname;
			return true;
		}
		else if ($chk)
		{
			$this->error = $chk['error'];
			return false;
		}
		else
		{
			$this->error = 'Could not connect to authentication server.';
			return false;
		}
	}
	*/

	public function login ($email, $password)
	{
		$chk = $this->file_get_contents
		(
			self::API_URL . 'login',
			array
			(
				'email' => $email,
				'password' => $password 
			)
		);

		$chk = json_decode ($chk, true);

		if ($chk && $chk['status'] === 'success')
		{
			$this->doLogin ($chk);
			return true;
		}

		$this->error = $chk['error'];

		return false;
	}

	public function personaLogin ($assertion)
	{
	    $this->error = 'No longer supported';
	    return false;

		$chk = $this->file_get_contents
		(
			self::API_URL . 'login',
			array
			(
				'persona_assertion' => $assertion,
				'persona_domain' => 'http://' . $_SERVER['HTTP_HOST'] . ':80',
				'referer' => $this->getReferer ()
			)
		);

		$this->debug = $chk;

		$chk = json_decode ($chk, true);
		if (!$chk)
		{
			$this->error = $chk;
			return false;
		}

		if ($chk && $chk['status'] === 'success')
		{
			$this->doLogin ($chk);
			return true;
		}

		if (isset ($chk['error'])) {
			$this->error = $chk['error'];
		}
		else {
			$this->error = $chk;
		}

		return false;
	}

	public function getEmail ()
	{
		if ($this->isLogin ())
		{
			return $_SESSION['gambic_email'];
		}
		return null;
	}

	private function doLogin ($chk)
	{
		$_SESSION['gambic_token'] = $chk['token'];
		$_SESSION['gambic_nickname'] = $chk['nickname'];
		$_SESSION['gambic_email'] = $chk['email'];
		$_SESSION['gambic_requirements'] = $chk['requirements'];
	}

	public function register ($username, $email, $password)
	{
		// First register
		$chk = $this->file_get_contents
		(
			self::API_URL . 'register',
			array
			(
				'username' => $username,
				'email' => $email,
				'password' => $password,
				'referer' => $this->getReferer ()
			)
		);

		$chk = json_decode ($chk, true);

		if ($chk && $chk['status'] === 'success')
		{
			$this->doLogin ($chk);
			return true;
		}

		$this->error = $chk['error'];

		return false;
	}

	public function getServers ()
	{
		$data = $this->file_get_Contents
		(
			self::API_URL . 'servers',
			array ()
		);

		return json_decode ($data, true);
	}

	public function getPlayURL ($server)
	{
		if ($this->isLogin ())
		{
			return self::API_URL . 'play?token=' . $_SESSION['gambic_token'].'&server=' . $server['id'];
		}
		else
		{
			return false;
		}
	}

	public function getError ()
	{
		if (!empty ($this->error))
		{
			return strtoupper ($this->error);
		}
		return null;
	}

	public function getDebug ()
	{
		return $this->debug;
	}

	private function file_get_contents ($url, $data = null)
	{
		//Initialize the Curl session 
		$ch = curl_init(); 

		//Set curl to return the data instead of printing it to the browser. 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

		//Set the URL 
		curl_setopt($ch, CURLOPT_URL, $url); 

		// Set a reasonable timeout
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);

		if (isset ($data))
		{
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
		}

		//Execute the fetch 
		$data = curl_exec($ch); 

		if ($error = curl_error ($ch))
		{
			echo $error;
		}

		//Close the connection 
		curl_close($ch);

		return $data;
	}
}