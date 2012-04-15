<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Contact extends CI_Controller
{
	function __construct()
		{
		parent::__construct();
		}	 

	function index()
	{
		//Load stuff we need

		$this->load->library(array('email', 'form_validation'));
		$this->load->helper(array('email', 'form'));

		//Establish field names and requirements

		$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
		$this->form_validation->set_rules('email', 'Email Address','required|valid_email|xss_clean');
		$this->form_validation->set_rules('subject', 'Subject', 'required|xss_clean');
		$this->form_validation->set_rules('message', 'Message', 'required|xss_clean');
		$this->form_validation->set_rules('type', 'Message Type', 'required|xss_clean');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');

		//Check form input, see what we need to do

		if($this->form_validation->run() === FALSE)
		{
			$this->template->load('template', 'contact_form');
		}
		else
		{
			$name = $this->input->post('name');
			$email = $this->input->post('email');
			$subject = $this->input->post('subject');
			$message = $this->input->post('message');
			$this->email->from($email, $name);
			
			if(set_value('type' == 'Software Bug Report'))
				$this->email->to('seth@sethhochberg.com');
			else
				$this->email->to('boosters@tarponspringsband.com');

			$this->email->subject($subject);
			$this->email->message($message);
			$this->email->send();
			
			$this->session->set_flashdata('notice', '<div class="success">Your message has been sent! Thank you, we will reply as soon as we can.</div>');
			redirect('');
		}
	}
}