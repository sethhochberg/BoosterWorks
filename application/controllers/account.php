<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Account extends CI_Controller 
{

	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Profiles_model');
		$this->load->model('Events_model');
		$this->load->model('Shifts_model');
		
		if(!$this->tank_auth->is_logged_in())
		{	
			redirect('auth/login');
		}
	}
	
	function index()
	{
		$user_id = $this->tank_auth->get_user_id();
		$profile_data = $this->Profiles_model->get_by_user($user_id);
		$data['profile_data'] = $profile_data->row();
			 
		$this->template->load('template', 'account/account_home', $data);		
	}
	
	function future_shifts()
	{
		$this->load->library('table');
		
		$user = $this->_get_user($this->tank_auth->get_user_id());		
		$family = $this->Profiles_model->get_family_group($user->family_group);
		
		$this->table->set_heading('Person Working', 'Event Name', 'Shift Date', 'Shift Start Time', 'Shift End Time', 'Event Details');
		$tmpl = array ( 'table_open'  => '<table border="0" cellpadding="4" cellspacing="0" id="sort" class="tablesorter">' );
		$this->table->set_template($tmpl);

		foreach ($family as $member)
		{
			$shifts = $this->Shifts_model->shifts_by_user($member['user_id']);
			foreach ($shifts as $shift) 
			{
				$event = $this->Events_model->get_event_by_shift($shift['shift_id']);
				$profile_data = $this->_get_user($shift['user_id']);
				if(strtotime($event->event_date) >= strtotime('today'))
				{
					$this->table->add_row($profile_data->first_name . ' ' . $profile_data->last_name, $event->event_name, $event->event_date, $shift['shift_start'], $shift['shift_end'], anchor("events/view/{$event->id}", 'View'));
				}
			}
		}
		
		
		$data['page_title'] = "Upcoming shifts";
		$data['shift_list'] = $this->table->generate();
		$data['tablesortjs'] = '<script type="text/javascript" src="' . base_url() .'assets/js/jquery.tablesorter.min.js"></script>	
		<script type="text/javascript">
			$(document).ready(function() 
			{ 
				$("#sort").tablesorter( {sortList: [[2,0]]} ); 
			} 
			); 
		</script>';
		
		$this->template->load('template', 'account/shift_list', $data);
	}

	function wait_list()
	{
		$this->load->library('table');
		
		$user = $this->_get_user($this->tank_auth->get_user_id());		
		$family = $this->Profiles_model->get_family_group($user->family_group);
		
		$this->table->set_heading('Person Waitlisted', 'Event Name', 'Shift Date', 'Shift Start Time', 'Shift End Time', 'Event Details');
		$tmpl = array ( 'table_open'  => '<table border="0" cellpadding="4" cellspacing="0" id="sort" class="tablesorter">' );
		$this->table->set_template($tmpl);

		foreach ($family as $member)
		{
			$shifts = $this->Shifts_model->get_waitlist($member['user_id']);
			foreach ($shifts as $shift) 
			{
				$event = $this->Events_model->get_event_by_shift($shift['corresponding_shift']);
				$shift_detail = $this->Shifts_model->shift($shift['corresponding_shift']);
				$profile_data = $this->_get_user($shift['user_id']);
				if(strtotime($event->event_date) >= strtotime('today'))
				{
					$this->table->add_row($profile_data->first_name . ' ' . $profile_data->last_name, $event->event_name, $event->event_date, $shift_detail['0']['shift_start'], $shift_detail['0']['shift_end'], anchor("events/view/{$event->id}", 'View'));
				}
			}
		}
		
		
		$data['page_title'] = "Waitlist Details";
		$data['shift_list'] = $this->table->generate();
		$data['tablesortjs'] = '<script type="text/javascript" src="' . base_url() .'assets/js/jquery.tablesorter.min.js"></script>	
		<script type="text/javascript">
			$(document).ready(function() 
			{ 
				$("#sort").tablesorter( {sortList: [[2,0]]} ); 
			} 
			); 
		</script>';
		
		$this->template->load('template', 'account/shift_list', $data);
	}		
	
	function past_shifts()
	{
		$this->load->library('table');
		
		$user = $this->_get_user($this->tank_auth->get_user_id());		
		$family = $this->Profiles_model->get_family_group($user->family_group);
		$this->table->set_heading('Person that worked', 'Event Name', 'Shift Date', 'Shift Start Time', 'Shift End Time', 'Event Details', 'Marked as Attended');
		
		$tmpl = array ( 'table_open'  => '<table border="0" cellpadding="4" cellspacing="0" id="sort" class="tablesorter">' );
		$this->table->set_template($tmpl);

		foreach ($family as $member)
		{
			$shifts = $this->Shifts_model->shifts_by_user($member['user_id']);
			foreach ($shifts as $shift) 
			{
				$event = $this->Events_model->get_event_by_shift($shift['shift_id']);
				$profile_data = $this->_get_user($shift['user_id']);
				if(strtotime($event->event_date) < strtotime('today'))
				{
					if($shift['attended'] == '1')
						$attended = 'Yes';
					else
						$attended = '<b>NO</b>';
					$this->table->add_row($profile_data->first_name . ' ' . $profile_data->last_name, $event->event_name, $event->event_date, $shift['shift_start'], $shift['shift_end'], anchor("events/view/{$event->id}", 'View'), $attended);
				}
			}
		}
		
		$data['page_title'] = "Previous shifts";
		$data['shift_list'] = $this->table->generate();
		$data['tablesortjs'] = '<script type="text/javascript" src="' . base_url() .'assets/js/jquery.tablesorter.min.js"></script>	
		<script type="text/javascript">
			$(document).ready(function() 
			{ 
				$("#sort").tablesorter( {sortList: [[2,1]]} ); 
			} 
			); 
		</script>';
		
		$this->template->load('template', 'account/shift_list', $data);
	}	
	
	function _get_user($user_id)
	{
		$profile_data = $this->Profiles_model->get_by_user($user_id);
		$profile_data = $profile_data->row();
		return $profile_data;
	}
}

/* End of file account.php */
/* Location: ./system/application/controllers/account.php */