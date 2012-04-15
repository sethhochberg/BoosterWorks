<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Drupal extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->helper(array('form', 'url'));
		$this->load->model('Drupal_model');
	}

	function index()
	{
		//check for native php cookie from drupal site - if cookie exists, grab session
		if( isset($_COOKIE["SESSa6b11fbbef7649200118fe1bd73c7afa"]) )
		{
			$session_hash = $_COOKIE["SESSa6b11fbbef7649200118fe1bd73c7afa"];
			$session = $this->Drupal_model->lookup_session($session_hash);
			//fetch drupal user profile
			$drupal_user = $this->Drupal_model->lookup_user($session->uid);

			//search for drupal uid in boosterworks profile table
			$profile = $this->Drupal_model->check_profile($drupal_user->uid);
			
			if($profile == FALSE) //no login through drupal yet, create boosterworks account prior to login
			{
				$this->tank_auth->create_user('', $drupal_user->mail, rand() . md5(microtime()), '');
				$data['token'] = rand() . md5(microtime());
				$this->template->load('template', 'profile/create');
			}
			
			//drupal uid can be linked to boosterworks account, log them in
			

		}
		else
		{
			redirect('auth');
		}
	}
}

	

/* End of file drupal.php */
/* Location: ./application/controllers/drupal.php */