<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Help extends CI_Controller
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
		$data['view']['title'] = '';
		$body = $this->Site_model->get_text('help');
		$data['view']['body'] = stripslashes($body['0']['body']);
		$this->template->load('template', 'text_view', $data);
	}
}
/* End of file help.php */
/* Location: ./application/controllers/help.php */