<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->model(array('Profiles_model', 'Events_model'));
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		if ($this->Profiles_model->is_admin() != TRUE) 
		{									
			$this->session->set_flashdata('notice', '<div class="error">You must be logged in and an administrator to access the control panel.</div>');
			redirect('');
		}
	}
	
	function index()
	{	
		//check if we have form input, proceed to load form/validation if not
		if($this->input->post('mail'))
		{
			$this->form_validation->set_rules('target_selection', 'mail target selection', 'required|xss_clean');
		}
		elseif($this->input->post('report'))
		{
			$this->form_validation->set_rules('report_selection', 'desired report selection', 'required|xss_clean');
		}		
		if($this->form_validation->run() != TRUE) //form not submitted yet or validation failed
		{
			 $lists = array(
				  'event'  => 'Users signed up for a single event',
				  'type'    => 'All users signed up for an event type',
				  'all' => 'All registered users'
				);
			 $data['mail_target'] = form_dropdown('target_selection', $lists);
			 $mail_attributes = array('id' => 'mail', 'name' => 'mail');
			 $data['mail_form_open'] = form_open('admin/dashboard/index', $mail_attributes);
			 
			 $reports = array(
				  'event'    => 'Attendance report for a single event'
				);
			 $data['report_selection'] = form_dropdown('report_selection', $reports);
			 $report_attributes = array('id' => 'report', 'name' => 'report');
			 $data['report_form_open'] = form_open('admin/dashboard/index', $report_attributes);
			 
			 $data['view']['title'] = "Welcome!";
			 $this->template->load('template', 'admin/dashboard_view', $data);	
		}
		else //form processing
		{
			if($this->input->post('mail')) //mail selection was submitted
			{
				$target = $this->input->post('target_selection');
				if($target == 'event' || $target == 'type' || $target == 'all')
				{
					redirect("admin/broadcast/selection/{$target}");
				}			
				else
				{
					$this->session->set_flashdata('notice', '<div class="error">You managed to request a mailing list that does not exist yet.</div>');
					redirect('admin/dashboard');
				}
			}
			elseif($this->input->post('report')) //report selection was submitted
			{
				$report = $this->input->post('report_selection');
				//extra reports for this if stored here:  || $report == 'type' || $report == 'user' || $report == 'family' || $report == 'events' || $report == 'users' 
				if($report == 'event')
				{
					redirect("admin/reports/selection/{$report}");
				}
				else
				{
					$this->session->set_flashdata('notice', '<div class="error">You managed to request a report that does not exist yet.</div>');
					redirect('admin/dashboard');
				}						
			}
			else //invalid post?
			{
				$this->session->set_flashdata('notice', '<div class="error">Please submit a bug report. "Invalid postdata from dashboard selection form".</div>');
				redirect('admin/dashboard');
			}	
		}
	}

}

/* End of file admin/dashboard.php */
/* Location: ./system/application/controllers/admin/dashboard.php */