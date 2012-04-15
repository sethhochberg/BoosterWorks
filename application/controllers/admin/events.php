<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Events extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->database();
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->library('security');
		$this->load->library('table');
		$this->load->model('Events_model');
		$this->load->model('Shifts_model');
		
		if($this->Profiles_model->is_admin() != TRUE)
		{
			redirect('account');
		}
		
	}
	
	function details()
	{		
		$this->load->helper('form');
		$this->load->model('Waitlists_model');
		
		$id = $this->uri->segment(4);
		$event = $this->Events_model->get_by_id($id)->row();

		$shifts = $this->Shifts_model->shifts_by_event($id);
		$waitlist = $this->Waitlists_model->list_by_event($id);

		$slots_available = $this->Events_model->get_shifts_available($event->id);
		$slots_available = count($slots_available);
						
		$this->form_validation->set_rules('*', 'attendance', 'is_numeric');
		
		
		if($this->form_validation->run() == FALSE)
		{
			$data['event'] = $event;
			$data['slots_available'] = $slots_available;			
			$data['confirmed_table'] = $this->_generate_table($shifts, $event);	
			$data['waitlist_table'] = $this->_waitlist_table($waitlist);
			$data['form_open'] = form_open("admin/events/details/{$id}");
			$data['attendance_submit'] = form_submit('attendance', 'Update Attendance Records', 'style="float: right;"');
			$data['report_submit'] = form_submit('report', 'Generate PDF Report', 'style="float: right;"');
			$data['add_slot'] = form_button('add_slot', 'Add additional volunteer slots', "onclick=\"location.href='" . base_url() . "index.php/admin/events/shift_form/{$id}/1'\"");
			$data['remove_slot'] = form_button('remove_slot', 'Remove an open slot', "onclick=\"location.href='" . base_url() . "index.php/admin/shifts/confirm/remove/{$id}/'\"");
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
				$table = $this->_generate_table($shifts, $event);

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
				$table = $this->_generate_table($shifts, $event, 'print');
				$this->_generate_pdf($table, $event->event_name);
			}
		}
	}
	
	function edit()
	{
		$this->_set_edit_rules();
		$id = $this->uri->segment(4, 0);
		$stored_event = $this->Events_model->get_by_id($id);
		$stored_event = $stored_event->result_array();
	
		if ($this->form_validation->run() == FALSE) // validation hasn'\t been passed
		{
			$data['view']['do'] = '';
			$data['datepickerjs'] = '<script>
									$(function() {
										$( "#event_date" ).datepicker({
												changeMonth: true,
												changeYear: true,
												dateFormat: \'yy-mm-dd\'
												
										});
									});
									</script>';
			$data['id'] = $id;
			$data['event'] = $stored_event['0'];
			$this->template->load('template', 'events/edit_form', $data);
		}
		else // passed validation proceed to post success logic
		{
			// build arrays for the model			
			$event = array(
							'event_type' => set_value('event_type'),
							'event_name' => set_value('event_name'),
							'event_date' => set_value('event_date'),
							'event_location' => set_value('event_location'),
							'event_notes' => set_value('event_notes'),
						);
			$this->Events_model->update($event, $id);
			redirect("admin/events/details/{$id}");
		}
	}
	
	function confirm()
	{
		$action = $this->uri->segment(4, NULL);
		$object = $this->uri->segment(5, NULL);

		if($action == NULL || $object == NULL)
		{
			//redirect with error - invalid request
			echo "invalid action or object id";
		}

		switch($action)
		{
			case 'delete':
				$data['action'] = 'delete';
				$data['url'] = "admin/events/delete/{$object}";
				$this->template->load('template', 'admin/confirm', $data);
				break;
		}
	}

	function delete()
	{
		$id = $this->uri->segment(4);
		$status = $this->Events_model->delete_event($id);
		if($status != TRUE)
		{
			$this->session->set_flashdata('notice', '<div class="error">Event was not deleted - database error.</div>');
			redirect('admin/events/listing');
		}
		else
		{
			$this->session->set_flashdata('notice', '<div class="success">Event deleted successfully.</div>');
			redirect('admin/events/listing');
		}
	}

	function listing()
	{
		$filter_string = ""; //initialize the filters to none
		$data['view']['title'] = 'Events Listing';

		$post_submit = $this->input->post('submit');
		if(!empty($post_submit)) //form has been submitted, set up filters
		{
			$filter_string = $this->input->post('filters_hidden') . '|' . $this->input->post('event_type') . '|' . $this->input->post('event_date');
		}
		

		$results = $this->Events_model->get($filter_string);
			
		// load the HTML Table Class
		$this->load->library('table');
		$this->table->set_heading('Type', 'Name', 'Date','Reports', 'Mail', 'Actions');
		$tmpl = array ( 'table_open'  => '<table border="0" cellpadding="4" cellspacing="0" id="sort" class="tablesorter">' ); //define the table as sortable
		$this->table->set_template($tmpl);
		
		$row_count = 0;
		foreach($results['page'] as $result)
		{
			$shifts_available = $this->Events_model->get_shifts_available($result['id']);
						
			//set the human readable event type based on the numeric id
			if($result['event_type'] == '0')
				$human_type = 'Tropicana Field Concessions';
			elseif($result['event_type'] == '1') 
				$human_type = 'Raymond James Stadium Concessions';
			elseif($result['event_type'] == '2') 
				$human_type = 'Student Tag Day';
			elseif($result['event_type'] == '3') 
				$human_type = 'Renaissance Festival';
			elseif($result['event_type'] == '4') 
				$human_type = 'Golf Tournament Parking';
			elseif($result['event_type'] == '5') 
				$human_type = 'Other';
				
			$this->table->add_row(array($human_type, $result['event_name'], $result['event_date'], anchor("admin/events/details/{$result['id']}", 'Details/Attendance'), anchor("admin/broadcast/event/{$result['id']}", 'Email Volunteers'), anchor("admin/events/edit/{$result['id']}", 'Edit').' - '.anchor("admin/events/confirm/delete/{$result['id']}", 'Delete')));
			$row_count++;

		}

		if($row_count != 0) //there is data in the table
		{
			$data['table'] = $this->table->generate();
		}
		else //else no results were found
		{
			$data['table'] = "<br /><p>No results were found for your query.</p>";
		}

				
		$data['event_types'] = array('type=void' => 'All Event Types', 'type=0' => 'Tropicana Field Concessions', 'type=1' => 'Raymond James Stadium Concessions', 'type=2' => 'Student Tag Day', 'type=3' => 'Renaissance Festival', 'type=4' => 'Golf Tournament Parking', 'type=5' => 'Other');		
		$data['date_ranges'] = array('dates=all' => 'All Dates', 'dates=past' => 'Past Events Only', 'dates=future' => 'Future Events Only');

		$data['tablesortjs'] = '<script type="text/javascript" src="' . base_url() . 'assets/js/jquery.tablesorter.min.js"></script>	
		<script type="text/javascript">
			$(document).ready(function() 
			{ 
				$("#sort").tablesorter( {sortList: [[2,0]]} ); 
			} 
			); 
		</script>';
			
		// load the view
		$this->template->load('template', 'admin/event_list', $data);

	}
	 
	

	function create()
	{			
		//begin with some basic checks to see if we really can proceed with creation
		if($this->Profiles_model->is_admin() != TRUE)
		{
			redirect('account');
		}
			
		//establish all the event creation form rules
		$this->_set_rules();
	
		if ($this->form_validation->run() == FALSE) // validation hasn'\t been passed
		{
			$data['view']['do'] = '';
			$data['datepickerjs'] = '<script>
									$(function() {
										$( "#event_date" ).datepicker({
												changeMonth: true,
												changeYear: true,
												dateFormat: \'yy-mm-dd\'
												
										});
									});
									</script>';
			$this->template->load('template', 'events/create_form', $data);
		}
		else // passed validation proceed to post success logic
		{
			// build arrays for the models
			
			$shifts_count = set_value('shifts_count');
			
			$event = array(
							'event_type' => set_value('event_type'),
							'event_name' => set_value('event_name'),
							'event_date' => set_value('event_date'),
							'event_location' => set_value('event_location'),
							'event_notes' => set_value('event_notes'),
						);
					
			// run insert model to write data to db			
			$event_id = $this->Events_model->save_new_event($event);		
			
			
			if ($event_id != NULL) 
			{ // call up the shift form stuff since we've got an event now
				redirect("admin/events/shift_form/{$event_id}/{$shifts_count}");			
			}
			else
			{
				$data['view']['error'] = "Something went wrong savng this event. Reload the form, try again, and contact Seth if you still recieve this error.";
				$this->template->load('template', 'error', $data);
				return;
			}	
		}
	}
	
	function shift_form()
	{
		//need to add aditional checks to make sure we're coming here from the event creation form 

		$event_id = $this->uri->segment(4);
		$shifts_count = $this->uri->segment(5);
		
		if ($shifts_count == NULL || $event_id == NULL)
		{
			echo("bad event id or bad shift count <br />");
			echo("id:" . $event_id . " --- " . "count:" . $shifts_count);
			return FALSE;
		} 		
		
		for($counter=1; $shifts_count >= $counter; $counter++)
		{
			$this->form_validation->set_rules("shift_start_${counter}","shift start ${counter}",'trim|required|xss_clean');
			$this->form_validation->set_rules("shift_end_${counter}","shift end ${counter}",'trim|required|xss_clean');
			$this->form_validation->set_rules("shift_persons_${counter}","shift persons ${counter}",'trim|required|xss_clean');
		}
		unset($counter);
		
		$this->form_validation->set_error_delimiters('<div class="error">Error: ', '</div>');
		
		if ($this->form_validation->run() == FALSE)
		{
			$data['shifts_count'] = $shifts_count;		
			$data['event_id'] = $event_id;
			$this->template->load('template', 'events/shift_create_form', $data);
		}
		else
		{ //passed validation, now lets get to business and make db changes
						
			for($counter=1; $shifts_count >= $counter; $counter++)
			{
				$shift_persons = set_value("shift_persons_{$counter}");
				
				$j = 1;
				while($shift_persons >= $j)
				{
					$shift = array(
									'shift_start' => set_value("shift_start_{$counter}"),
									'shift_end' => set_value("shift_end_{$counter}"));	
									
					$shift_id = $this->Events_model->save_new_shifts($shift);
					
					$relations = array(
									'shift_id' => $shift_id,
									'event_id' => $event_id
									);	
					$this->Events_model->create_shift_relations($relations);	
					$j++;
				}
			}
				//all shifts written to db, so load event view	
			$this->session->set_flashdata('notice', "<div class='success'>New shifts/slots created successfully.</div>");
			redirect("admin/events/details/{$event_id}");
		}		
	}
	
	function _get_user()
	{
		$user_id = $this->tank_auth->get_user_id();
		$profile_data = $this->Profiles_model->get_by_user($user_id);
		$profile_data = $profile_data->row();
		return $profile_data;
	}
	
	function _generate_pdf($table, $title)
	{
		$this->load->library('pdf');


		// add a page
        $this->pdf->AddPage();

        // set document information
        $this->pdf->SetFont('helvetica', '', 12);
        $this->pdf->SetSubject('BoosterWorks Event Report - ' . $title);      
             
        // print a line using Cell()
        $this->pdf->SetFont('helvetica', '', 8);
        $this->pdf->WriteHTML($table, true, false, true, false, '');

        $this->pdf->lastPage();
        
        //Close and output PDF document
        $this->pdf->Output(time() . '.pdf', 'I');        
	}
	
	function _generate_table($shifts, $event, $type = 'web')
	{		
		$this->table->set_heading('First Name', 'Last Name', 'Details', 'Times', 'Transportation', 'Working for:', 'Attended?', 'Actions');
		$tmpl_web = array ( 'table_open'  => '<table border="0" cellpadding="4" cellspacing="0" id="sort" class="tablesorter">' );
		$tmpl_print = array ( 'table_open'  => '<table style="font-size:10px;" border="0" cellpadding="4" cellspacing="0" id="sort" class="tablesorter">' ); 
		
		if($type == 'web')
			$this->table->set_template($tmpl_web);
		else if ($type == 'print')
			$this->table->set_template($tmpl_print);
	
		//prepare the table for displaying who is working a shift
		$row_count = 0;
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

				if($shift_details['0']['transportation'] == '0') //human readable transportation status
				{
					$transportation = 'Has own transport';
				}
				if($shift_details['0']['transportation'] == '1')
				{
					$transportation = 'Needs carpool ride';
				}
				if($shift_details['0']['transportation'] == '2')
				{
					$transportation = 'Can drive carpool';
				}

					
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
				$this->table->add_row(
					array(
						$profile->first_name, 
						$profile->last_name, 
						$is_student.'<br />'.$profile->primary_phone.'<br />'.mailto($profile->email), 
						$shift_details['0']['shift_start'] . ' - ' . $shift_details['0']['shift_end'], 
						@$transportation, 
						$shift_details['0']['shift_for'], 
						$attendance, 
						anchor("admin/shifts/confirm/unsign/{$shift_details['0']['shift_id']}", 'Remove Volunteer') .'<br />' . 
						anchor("admin/shifts/shift_to_waitlist/{$shift_details['0']['shift_id']}", 'Move to waitlist') .'<br />' . anchor("admin/shifts/confirm/delete/{$shift_details['0']['shift_id']}", 'Delete Slot'))
					);
				$row_count++;
			}
		}
		if($row_count != 0) //there is data in the table
		{
			$table = $this->table->generate();
		}
		else //else no results were found
		{
			$table = "<br /><p>No data to display.</p>";
		}
		$this->table->clear();
		return $table;
	}

	function _waitlist_table($waitlists)
	{		
		$this->table->set_heading('First Name', 'Last Name', 'Details', 'Times', 'Signup Time', 'Actions');
		$tmpl = array( 'table_open'  => '<table border="0" cellpadding="4" cellspacing="0" id="sort_waitlist" class="tablesorter">' );		
		$this->table->set_template($tmpl);

		//prepare the table for displaying who is signed up in a waitlist slot
		$row_count = 0;
		
		//loop through results and generate table
		foreach($waitlists as $waitlist_item)
		{
			if(isset($waitlist_item))
			{
				if($waitlist_item['is_student'] == '1') //human readable student status
					$is_student = 'Student';
				else
					$is_student = 'Adult';

				//depricated - $shift = $this->Shifts_model->shift($waitlist_item['corresponding_shift']);
				$times = $waitlist_item['shift_start'] . ' - ' . $waitlist_item['shift_end'];
				
				$this->table->add_row(array($waitlist_item['first_name'], $waitlist_item['last_name'], $is_student.'<br />'.$waitlist_item['primary_phone'].'<br />'.mailto($waitlist_item['primary_email']), $times, $waitlist_item['timestamp'], anchor("admin/shifts/waitlist_to_shift/{$waitlist_item['id']}", 'Move to open slot').'<br />'. anchor("admin/shifts/confirm/remove_from_waitlist/{$waitlist_item['id']}", 'Remove from waitlist') ));
				
				$row_count++;
			}
		}

		if($row_count != 0) //there is data in the table
		{
			$table = $this->table->generate();
		}
		else //else no results were found
		{
			$table = "<br /><p>No data to display.</p>";
		}
		$this->table->clear();
		return $table;
	}
	
	function _set_rules()
	{
		$this->form_validation->set_rules('event_type','Event Type','required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('event_name','Event Name','required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('event_date','Event Date','required|trim|xss_clean|max_length[55]');			
		$this->form_validation->set_rules('event_location','Event Location','required|trim|xss_clean|max_length[255]');					
		$this->form_validation->set_rules('event_notes','Event Notes','trim|xss_clean|max_length[1000]');			
		$this->form_validation->set_rules('shifts_count','ShiftsCount','required|max_length[11]');
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	}
	
	function _set_edit_rules()
	{
		$this->form_validation->set_rules('event_type','Event Type','required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('event_name','Event Name','required|trim|xss_clean|max_length[255]');			
		$this->form_validation->set_rules('event_date','Event Date','required|trim|xss_clean|max_length[55]');			
		$this->form_validation->set_rules('event_location','Event Location','required|trim|xss_clean|max_length[255]');					
		$this->form_validation->set_rules('event_notes','Event Notes','trim|xss_clean|max_length[1000]');			
			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	}
}	

/* End of file events.php */
/* Location: ./application/controllers/admin/events.php */
