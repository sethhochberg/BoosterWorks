<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
		$this->load->library('tank_auth');
		$this->load->model('Site_model');
	}

	function index()
	{
		$text = $this->Site_model->get_text('homepage');
		$data['view']['body'] = stripslashes($text['0']['body']);
		$data['view']['title'] = '';
		$this->template->load('template', 'text_view', $data);
	}

	function old_index()
	{
		$contact = '<div class="prepend-4 span-7 append-1"><strong>Tropicana Field Concessions:</strong><br /> 
			James Brennan<br /> 
			<a href="mailto:james.brennan@tarponspringsband.com">Email</a></div> 
			<div class="span-7"><strong>Raymond James Stadium Concessions</strong><br />
			Terri Isenhour<br /> 
			<a href="mailto:terri.isenhour@tarponspringsband.com">Email</a></div> 
			<hr class="space">
			<div class="prepend-4 span-7 append-1"><strong>Innisbrook Golf Parking</strong><br /> 
			Contact not available</div> 
			<div class="span-7"><strong>Renaissance Festival</strong><br /> 
			Contact not available</div>';

		if(!$this->tank_auth->is_logged_in())
		{
			$data['view']['body'] ='		
			<p>You have reached the event volunteer portal for the Tarpon Springs Leadership Conservatory for The Arts. Please login above to access the events portal, or use the registration link to request an account should you not have one. Prior to creating an account, please review the information at the "Help and Info" link at the top of the page. Important information is posted there regarding the policies and restrictions (such as age) that apply to each of our volunteer-based fundraising events. For further questions or concerns, please contact the individuals below, who are your event coordinators:</p>' . $contact;
		}
		else
		{
			$data['view']['body'] ='
		
			<p>
			<ul class="prepend-1">
				<li>Browse and sign up for events at any time</li>
				<li>View all posted volunteer opportunities on a calendar or in list views</li>
				<li>Track your scheduled volunteer dates</li>
				<li>Document events you\'ve worked</li></p>
			</ul>
				' . $contact;
		}
		
		$data['view']['title'] = '<h3>Volunteering Made Easy</h3>';
		
		
		$this->template->load('template', 'text_view', $data);
	}


}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */