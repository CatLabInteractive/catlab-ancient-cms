<?php
class Core_Login
{
	private $isLogin = false;

	public static function __getInstance ()
	{
		static $in;
		if (!isset ($in))
		{
			$in = new self ();
		}
		return $in;
	}

	public function __construct ()
	{
		if (isset ($_GET['logout']) && $_GET['logout'] == 'yes')
		{
			$this->doLogout ();
		}
	
		// Check if guy is logged in.
		if (isset ($_SESSION['isLogin']) && $_SESSION['isLogin'] == true)
		{
			$this->isLogin = true;
		}
	}
	
	public function isLogin ()
	{
		return $this->isLogin == true;
	}
	
	public function isAdmin ()
	{
		return true;
	}

	/**
	 * @param $email
	 * @param $password
	 * @return bool
	 */
	public function doLogin ($email, $password)
	{
		if (!defined('ADMIN_USERNAME') || !defined('ADMIN_PASSWORD')) {
			$_SESSION['isLogin'] = false;
			return false;
		}

		$_SESSION['isLogin'] = ($email == ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD));
		return $_SESSION['isLogin'] == true;
	}
	
	public function doLogout ()
	{
		unset ($_SESSION['isLogin']);
	}
}