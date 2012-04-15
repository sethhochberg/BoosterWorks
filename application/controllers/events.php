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
		
		if(!$this->tank_auth->is_logged_in())
		{
			redirect('auth/login');
		}
		else
		{
			$profile_data = $this->Profiles_model->get_user();
		}		
	}
	
	function index()
	{
		$this->load->library('calendar');		
		
		$events = $this->Events_model->get_all();
		
		$view_year = $this->uri->segment(3, date("Y"));
		$view_month = $this->uri->segment(4, date("n"));
		
		foreach ($events as $event)
		{
			$date = explode('-', $event['event_date']);
			$shifts_available = $this->Events_model->get_shifts_available($event['id']);
			
			if($date['1'] == $view_month && $date['0'] == $view_year)
			{
				$day = ltrim($date['2'], '0');
				
				//event is in future, don't strikethrough
				if( strtotime($event['event_date']) > time() )
				{
					$show_events["{$day}"] =  anchor("events/view/{$event['id']}", $event['event_name']) . ' - <i>' . anchor("events/shifts/{$event['id']}", count($shifts_available)) . '</i>';
				}
				else
				{	
					//event is in past, strikethrough and kill link
					$show_events["{$day}"] = '<del>' . $event['event_name'] . '</del>';
				}
			}
		}

		$data['view']['title'] = "Event Calendar";
		$data['cal'] = $this->calendar->generate($this->uri->segment(3), $this->uri->segment(4), $show_events);
		$this->template->load('template', 'events/calendar_view', $data);
	}
	
	function calendar()
	{
		$this->load->library('calendar');		
		
		$year = $this->uri->segment(3, date("Y"));
		$month = $this->uri->segment(4, date("n"));

		$events = $this->Events_model->get_by_month($year, $month);
		
		//echo "<pre>";
		//print_r($events);
		//echo "</pre>";
		//return;
		
		$show_events=array();
		foreach ($events as $event)
		{
			$date = explode('-', $event['event_date']);
			$shifts_available = $this->Events_model->get_shifts_available($event['id']);			
		
			$day = ltrim($date['2'], '0');
			
			//event is in future, don't strikethrough
			if( strtotime($event['event_date']) > time() )
			{
				if ( array_key_exists( $day, $show_events ) )
                {
                    $show_events["{$day}"] = $show_events["{$day}"] . '<br/>' . '&nbsp;&bull;&nbsp;' . anchor("events/view/{$event['id']}", $event['event_name']) . ' - <i>' . anchor("events/shifts/{$event['id']}", count($shifts_available)) . '</i>';
                }
                else
                {
                    $show_events["{$day}"] =  anchor("events/view/{$event['id']}", $event['event_name']) . ' - <i>' . anchor("events/shifts/{$event['id']}", count($shifts_available)) . '</i>';
                }
			}
			else
			{	
				//event is in past, strikethrough and kill link
				$show_events["{$day}"] = '<del>' . $event['event_name'] . '</del>';
			}
		}

		$data['view']['title'] = "Event Calendar";
		$data['cal'] = $this->calendar->generate($this->uri->segment(3), $this->uri->segment(4), $show_events);
		$this->template->load('template', 'events/calendar_view', $data);
	}
	
	function listing()
	{
		$filter_string = ""; //initialize the filters to none
		$config['per_page'] = '15'; //set the default results per page
		$data['view']['title'] = 'Events Listing';

		$post_submit = $this->input->post('submit');
		if(!empty($post_submit)) //form has been submitted, set up filters
		{
			$filter_string = $this->input->post('filters_hidden') . '|' . $this->input->post('event_type') . '|' . $this->input->post('event_date');	
		}

		$results = $this->Events_model->get($filter_string);
		

		// load pagination class
		$this->load->library('pagination');
		$config['base_url'] = base_url().'index.php/events/listing/';
		$config['total_rows'] = $results['total'];
		$config['full_tag_open'] = '<p>';
		$config['full_tag_close'] = '</p>';

		//$this->pagination->initialize($config);
			
		// load the HTML Table Class
		$this->load->library('table');
		$this->table->set_heading('Type', 'Name', 'Date','Slots Available', 'Details');
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
				
			$this->table->add_row($human_type, $result['event_name'], date('m/d/Y', strtotime($result['event_date'])), anchor("events/shifts/{$result['id']}", count($shifts_available)), anchor("events/view/{$result['id']}", 'View'));
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

		$data['links'] = $this->pagination->create_links();
		$data['tablesortjs'] = '<script type="text/javascript" src="' . base_url() . 'assets/js/jquery.tablesorter.min.js"></script>	
		<script type="text/javascript">
			$(document).ready(function() 
			{ 
				$("#sort").tablesorter( {sortList: [[2,0]]} ); 
			} 
			); 
		</script>';
			
		// load the view
		$this->template->load('template', 'events/list_view', $data);

	}
	
	function view($id)
	{
		if($id == NULL)
		{
			redirect('events');
		}	
		$event = $this->Events_model->get_by_id($id);
		$shifts_available = $this->Events_model->get_shifts_available($id);
		$event = $event->row();
		$data['event'] = $event;
		
			
		//Check if the shifts_available array is empty, and either display an error or count how many shifts remain
		if (empty($shifts_available)) 
		{
				$data['shifts'] = anchor("events/shifts/{$id}", "No slots remain, but you may still sign up for the waiting list. Click here to view times to waitlist for.");
		}
		else
		{
				$remaining_count = count($shifts_available);
				$data['shifts'] = anchor("events/shifts/{$id}", "There are currently {$remaining_count} slots left. Click here to view the list of available slots.");
		}
		
		$this->template->load('template', 'events/event_info', $data);
	}
	
	function shifts()
	{		
		$event_id = $this->uri->segment(3, 0);
		
		if($event_id == '0')
		{
			echo("Invalid event ID specified.");
			return;
		}
		
		//get the event details, convert them to a row object for usefullness 
		$event = $this->Events_model->get_by_id($event_id)->row();
		//$event = $event->row();
		
		//check and make sure the event is not in the past
		if( strtotime($event->event_date) < time() )
		{
			$this->session->set_flashdata('notice', '<div class="notice">You cannot sign up for events in the past.</div>');
			redirect("events/view/{$event_id}");
		}
		
		
		//select what shift for this event we want to sign up for
		$this->load->model('Shifts_model');
		$shifts_total = $this->Shifts_model->shifts_by_event($event_id);
		
		$this->table->set_heading('Shift Start Time', 'Shift End Time', 'Availability', 'Wait List');
		$timeslot = array();
		$previous = array();
		for($i=0; $i<count($shifts_total); $i++)
		{
			if($i == 0)
			{
				$previous['shift_start'] = 'xxx'; 
				$previous['shift_end'] = 'xxx';
			}
			
			//build the table row for the shift form
			$shift = $shifts_total[$i];
			if($shift['user_id'] == '0')
				$signup = anchor("shifts/signup/{$shift['shift_id']}", 'Signup');
			else
				$signup = "Claimed";
			if($shift['shift_start'] != $previous['shift_start'])
			{
				$wait_list = anchor("waitlist/signup/{$shift['shift_id']}", 'Waitlist for this time slot');
				$previous['shift_start'] = $shift['shift_start'];
				$previous['shift_end'] = $shift['shift_end'];
			}
			else
				$wait_list = '';
			$this->table->add_row($shift['shift_start'], $shift['shift_end'], $signup, $wait_list);
		}
		

		$data['shift_table'] = $this->table->generate();
		$data['event_name'] = $event->event_name;
		$data['event_id'] = $event_id;
		$this->template->load('template', 'events/shift_list', $data);
	}
}

/* End of file events.php */
/* Location: ./application/controllers/events.php */