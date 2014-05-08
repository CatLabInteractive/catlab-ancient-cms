<?php
class Core_Login
{
	private $isLogin = false;

	public function __getInstance ()
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
	
	public function doLogin ($email, $password)
	{ 
		$_SESSION['isLogin'] = ($email == 'admin' && md5($password) == 'da25915f039ea9a9e7176c0fd9646115');
		return $_SESSION['isLogin'] == true;
	}
	
	public function doLogout ()
	{
		unset ($_SESSION['isLogin']);
	}
}
?>
