<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Drupal 
{
	function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->library('session');
		$this->ci->load->helper(array('form', 'url'));
		$this->ci->load->model('Drupal_model');
	}

	function get_session()
	{
		//see if there is a drupal session
		if( isset($_COOKIE[DRUPAL_COOKIE_ID]) )
		{
			//get the session's id
			$session_hash = $_COOKIE[DRUPAL_COOKIE_ID];
			
			//read user info from drupal database for found session
			$session = $ci->Drupal_model->lookup_session($session_hash);
			return $session;
		}
		else
		{
			$session = NULL;
			return $session;
		}
	}

	function get_drupal_user($session)
	{
		//lookup drupal user based on session user id
		$drupal_user = $ci->Drupal_model->lookup_user($session->uid);
		return $drupal_user;
	}

	function get_bw_profile($drupal_user)
	{
		//search for drupal uid in boosterworks profile table
		$profile = $ci->Drupal_model->check_profile($drupal_user->uid);
		return $profile->row();
	}

	public function login_with_cookie($profile)
	{	
		$session = $this->get_session();

		if(isset($session->uid))
		{
			$drupal_user = $this->get_drupal_user($session);
			$profile = $this->get_bw_profile($drupal_user);

			if($profile == NULL) //no login through drupal yet, create boosterworks account prior to login
			{
				return FALSE;
			}			
			//drupal uid can be linked to boosterworks account, log them in
			else
			{
				//login via clone of tank_auth login procedure, without username or password checks
				$this->ci->session->set_userdata(array(
						'user_id'	=> $profile->user_id,
						'username'	=> $drupal_user->mail,
						'status'	=> '1',
				));

				$this->ci->users->update_login_info(
						$profile->user_id,
						$this->ci->config->item('login_record_ip', 'tank_auth'),
						$this->ci->config->item('login_record_time', 'tank_auth'));
				return TRUE;
			}
		}
	}

	public function create_profile($drupal_user)
	{
		$this->ci->load->
		$ci->tank_auth->create_user('', $drupal_user->mail, rand() . md5(microtime()), '');
		$data['token'] = rand() . md5(microtime());
		$ci->template->load('template', 'profile/create');

	}
}



	

/* End of file Drupal.php */
/* Location: ./application/libraries/Drupal.php */