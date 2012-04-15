<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		
		if($this->Profiles_model->is_admin() != TRUE)
		{
			redirect('account');
		}
		
		$this->load->model(array('Events_model', 'Profiles_model', 'Shifts_model'));
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('table');
	}
	
	function index()
	{
		
	}	

	function selection()
	{		
		$target = $this->uri->segment(4, NULL);
		
		$this->form_validation->set_rules('selection' , 'required');
				
		if($target == NULL)
		{
			$this->session->set_flashdata('notice', '<div class="error">The report selection dropdown cannot be used without selecting a report from the admin dashboard.</div>');
			redirect('admin/dashboard');
		}			
		if($target == 'event')
		{					
				if($this->form_validation->run() == FALSE)
				{	
					$events = $this->Events_model->get_names('past');	
					
						$i = 0;
						foreach($events as $event_temp) //this little swap just collapses a multidimensional array
						{
							$temp[$event_temp['id']] = $event_temp['event_date'] . ' - ' . $event_temp['event_name'];
							$i++;			
						}		
						$events = $temp;
						unset($temp);
					
					$data['title'] = 'Event Selection';
					$data['form_open'] = form_open("admin/reports/selection/{$target}");
					$data['selection'] = form_dropdown('event', $events);
					$this->template->load('template', 'admin/selection_dropdown', $data);
				}
				else //form submitted
				{
					$id = $this->input->post('event');
					redirect("admin/reports/event/{$id}");
				}
		}			
		if($target == 'type')
		{
				if($this->form_validation->run() == FALSE)
				{
				$types = array('0' => 'Tropicana Field Concessions', '1' => 'Raymond James Stadium Concessions', '2' => 'Student Tag Day', '3' => 'Renaissance Festival', '4' => 'Golf Tournament Parking', '5' => 'All Other');
								
				$data['title'] = 'Event Type Selection';
				$data['form_open'] = form_open("admin/broadcast/selection/{$target}");
				$data['selection'] = form_dropdown('type', $types);
				$this->template->load('template', 'admin/selection_dropdown', $data);
				}
				else //form submitted
				{

				}
		}
		if($target == 'family')
		{
			
		}
		if($target == 'all')
		{

		}
	}
	
	function event()
	{
		$id = $this->uri->segment(4);
		$event = $this->Events_model->get_by_id($id)->row();
		$shifts = $this->Shifts_model->shifts_by_event($id);
		
		$this->form_validation->set_rules('*', 'attendance', 'is_numeric');
		$table = $this->_generate_table($shifts, $event);
		
		if($this->form_validation->run() == FALSE)
		{
			$data['event'] = $event;			
			$data['table'] = $table;		
			$data['form_open'] = form_open("admin/events/details/{$id}");
			$data['attendance_submit'] = form_submit('attendance', 'Update Records', 'style="float: right;"');
			$data['report_submit'] = form_submit('report', 'Generate PDF Report', 'style="float: right;"');
			$data['tablesortjs'] = '<script type="text/javascript" src="' . base_url() . 'assets/js/jquery.tablesorter.min.js"></script>	
		<script type="text/javascript">
			$(document).ready(function() 
			{ 
				$("#sort").tablesorter( {sortList: [[1,0]]} ); 
			} 
			); 
		</script>';
			
			$this->template->load('template', 'admin/event_details', $data);
		}
		else //form submitted
		{
			if($this->input->post('attendance'))
			{
				$attendance = $_POST;
				unset($attendance['attendance']); //remove the submit button entry from the postdata

				
				foreach($shifts as $shift)
				{
					if($shift['user_id'] != '0')
					{	
						if($attendance["{$shift['shift_id']}"] != $shift['attended'])
						{
							$update = array('attended' => $attendance["{$shift['shift_id']}"]);
							$this->Shifts_model->update_shift($shift['shift_id'], $update);	
						}
					}
				}
				$this->session->set_flashdata('notice', "<div class='success'>Attendance updated successfully!</div>");
				redirect("admin/events/details/{$id}");
			}
			elseif($this->input->post('report'))
			{
				$this->_generate_pdf($table);
			}
		}
	}
	
	function user()
	{
		//landing page for user search/list
		$this->form_validation->set_rules('selection', 'required|xss_clean');
		
		$this->load->library('table');
		
		if($this->form_validation->run() == FALSE)
		{
			$data['search_form_open'] = form_open('admin/reports/user');
			$data['list_form_open'] = form_open('admin/reports/user');
			
			//establish a table of all users, add checkboxes next to names where checkbox id is user id
			$this->table->set_heading('First Name', 'Last Name', 'Student?', 'Shift Count', 'Family Group', 'Select');
			$this->Profiles_model->
			$data['user_table'] = 
			$this->template->load('template', 'admin/reports/user_selection', $data);
		}
		else //form submitted okay, determine if selection made or we're searching
		{
			//check postdata array for search or select key
			if(array_key_exists('search', $_POST))
				$action = 'search';
			if(array_key_exists('list', $_POST))
				$action = 'list';

			echo "<pre>";
			print_r($action);
			echo "</pre>";
		}
	}		
	function family()
	{
		//landing page for family search/list
	}	
	function events()
	{
		//grab the total number of events in the system
		
		//grab the number of total shifts
	}	
	function users()
	{
		//grab total number of users
		
		//
	}
	
	function _get_user()
	{

	}
	
	function _generate_table($shifts, $event)
	{		
		$this->table->set_heading('First Name', 'Last Name', 'Student/Adult', 'Primary Phone', 'Email', 'Times', 'Attended?');
		$tmpl = array ( 'table_open'  => '<table border="0" cellpadding="4" cellspacing="0" id="sort" class="tablesorter">' ); //define the table as sortable
		$this->table->set_template($tmpl);
	
		//prepare the table for displaying who is working a shift
		foreach($shifts as $shift)
		{
			if($shift['user_id'] != '0')
			{	
				$shift_details = $this->Shifts_model->shift($shift['shift_id']);
				$profile = $this->Profiles_model->get_by_user($shift['user_id'])->row();
				if($profile->is_student == '1') //human readable student status
					$is_student = 'Student';
				else
					$is_student = 'Adult';
					
				//create checkbox for attendance	
				if($shift_details['0']['attended'] == '1')
				{
					$values = array('1' => 'Yes', '0' => 'No');
					$attendance = form_dropdown("{$shift['shift_id']}", $values, '1');							
				}
				else
				{
					$values = array('1' => 'Yes', '0' => 'No');
					$attendance = form_dropdown("{$shift['shift_id']}", $values, '0');
				}
				
				//generate the actual HTML table row
				$this->table->add_row(array($profile->first_name, $profile->last_name, $is_student, $profile->primary_phone, mailto($profile->email), $shift_details['0']['shift_start'] . ' - ' . $shift_details['0']['shift_end'], $attendance));
			}
		}
		$table = $this->table->generate();
		return $table;
	}
}