<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Waitlist extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('Events_model');
		$this->load->model('Shifts_model');
		
		if($this->Profiles_model->is_admin() != TRUE)
		{
			redirect('account');
		}		
	}
	


/* End of file events.php */
/* Location: ./application/controllers/admin/events.php */
