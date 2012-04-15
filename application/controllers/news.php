<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class News extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
		$this->load->library('tank_auth');
	}

	function index()
	{
		$data['view']['title'] = '';
		$data['view']['body'] = '<h4><strong>PleaseLocate.what?</strong></h4> 
		<p>A community management consultant started writing a member map script for one of his forums to replace a service that had been bought out and closed down. An idea was born; that script was expanded, tweaked, and made available to all. That script is PleaseLocate.Me.</p> 
		<h4><strong>What goes on the maps?</strong></h4> 
		<p>You\'ve got a group of people - members of a website, a club, maybe your graduating class - and you want to provide a way for them to show where they are (or just show <i>something</i>) on a global, interactive map viewable by all the other people in the group - we provide that map, and the code you need to make it work for you.</p> 
		<h4><strong>...and it is free?</strong></h4> 
		<p>There may not be any free lunches out there, but there sure are free and useful websites. If you like what we do and want to donate, we\'d really appreciate it (servers aren\'t cheap!) We think our rates for branded, custom solutions are quite reasonable, too, if you\'d
		like a totally custom design that integrates into your existing site layout.</p> ';
		$this->template->load('template', 'text_view', $data);
	}
}
/* End of file about.php */
/* Location: ./application/controllers/about.php */