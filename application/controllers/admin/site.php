<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Site extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->helper('ckeditor');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->model('Site_model');

		if ($this->Profiles_model->is_admin() != TRUE) 
		{									
			$this->session->set_flashdata('notice', '<div class="error">You must be logged in and an administrator to access the control panel.</div>');
			redirect('');
		}
	}
	
	function index()
	{
		echo "no index - bad link; site.php";
	}
	
	function help()
	{		
		$this->form_validation->set_rules('ckedit', 'text', 'required|xss_clean');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');

		if($this->form_validation->run() == FALSE)
		{
			$data['ckeditor'] = array(	'id' =>'text', 'path'=>'assets/js/ckeditor');
			$existing = $this->Site_model->get_text('help');
			$data['current'] = $existing['0']['body'];
			$data['item'] = 'Help and Info';
			$data['form_open'] = form_open('admin/site/help');
			$this->template->load('template', 'admin/text_edit', $data);
		}
		else
		{
			$body = $this->input->post('ckedit');

			$row = array( 'body' => $body);
			$this->Site_model->update_text('help', $row);
			$this->session->set_flashdata('notice', '<div class="success">Page updated successfully!</div>');
			redirect('admin/dashboard');
		}		
	}

	function homepage()
	{		
		$this->form_validation->set_rules('ckedit', 'text', 'required|xss_clean');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');

		if($this->form_validation->run() == FALSE)
		{
			$data['ckeditor'] = array(	'id' =>'text', 'path'=>'assets/js/ckeditor');
			$existing = $this->Site_model->get_text('homepage');
			$data['current'] = $existing['0']['body'];
			$data['item'] = 'Home Page';
			$data['form_open'] = form_open('admin/site/homepage');
			$this->template->load('template', 'admin/text_edit', $data);
		}
		else
		{
			$body = $this->input->post('ckedit');

			$row = array( 'body' => $body);
			$this->Site_model->update_text('homepage', $row);
			$this->session->set_flashdata('notice', '<div class="success">Page updated successfully!</div>');
			redirect('admin/dashboard');
		}		
	}
}