<?php
/*
 * This Codeigniter helper is designed to blend authentication between 
 * tank_auth and Drupal.
 *
 * Copyright (c) 2011  http://sethhochberg.com
 * AUTHOR:
 *   Seth Hochberg
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED. IT MAY BREAK THINGS, BE INSECURE, OR CAUSE OTHER PROBLEMS. THE AUTHOR
 * CANNOT BE HELD LIABLE FOR ANY ISSUES THAT ARISE.
 */

/*
 * Cookie details
 */
define("DRUPAL_COOKIE_ID", "SESSa6b11fbbef7649200118fe1bd73c7afa");

function get_session()
{
	//see if there is a drupal session
	if( isset($_COOKIE[DRUPAL_COOKIE_ID]) )
	{
		//get the codeigniter instance
		$ci = &get_instance();

		//get the session's id
		$session_hash = $_COOKIE[DRUPAL_COOKIE_ID];
		
		//read user info from drupal database for found session
		$session = $ci->Drupal_model->lookup_session($session_hash);
		return $session;
	}
	else
	{
		return FALSE;
	}
}

function get_user($session)
{
	//get the codeigniter instance
	$ci = &get_instance();
	$drupal_user = $ci->Drupal_model->lookup_user($session->uid);

	//search for drupal uid in boosterworks profile table
	$profile = $ci->Drupal_model->check_profile($drupal_user->uid);
	return $profile->row();
}

function login_with_cookie($profile)
{	
	//get the codeigniter instance
	$ci = &get_instance();
	$ci->load->library('tank_auth');

	if($profile == NULL) //no login through drupal yet, create boosterworks account prior to login
	{
		$ci->tank_auth->create_user('', $drupal_user->mail, rand() . md5(microtime()), '');
		$data['token'] = rand() . md5(microtime());
		$ci->template->load('template', 'profile/create');
	}
	
	//drupal uid can be linked to boosterworks account, log them in
		
}	

	

/* End of file drupal.php */
/* Location: ./application/controllers/drupal.php */