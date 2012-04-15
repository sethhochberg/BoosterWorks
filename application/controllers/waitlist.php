<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Waitlist extends CI_Controller 
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

	function signup()
	{
		if(!$this->tank_auth->is_logged_in())
		{
			redirect('auth/login');
		}
		else
		{
			$profile_data = $this->_get_user();
		}		
		
		$shift_id = $this->uri->segment(3, 0);
		
		if($shift_id == '0')
		{
			echo("Invalid timeslot/shift ID specified.");
			return;
		}
		
		$this->load->library('form_validation');
		$this->load->helper('form');
		
		//go to the db and fetch the shift and the event
		$shift = $this->Shifts_model->shift($shift_id);
		$event = $this->Events_model->get_event_by_shift($shift_id);
		
		//build the dropdown list of other users this user can sign up for a shift, doing nothing if they are already signed up for it
		$users = $this->Profiles_model->get_family_group($profile_data->family_group);
		$shift_list = $this->Shifts_model->shifts_by_event($event->event_id);
		
		$i = 0;
		foreach($shift_list as $shift_temp) //this little swap just collapses a multidimensional array
		{
			$temp[$shift_temp['shift_id']] = $shift_temp['user_id'];
			$i++;			
		}		
		$shift_list = $temp;
		unset($temp);
		
		$data['current_uid'] = $profile_data->user_id;
		$data['user_list'] = array();		

		foreach($users as $user) //for each user, look and see if they match any ids on an existing shift
		{					
			if(!in_array($user['user_id'], $shift_list))
			{
				$data['user_list'][$user['user_id']] = $user['first_name'] . ' ' . $user['last_name'];
			}
			else
			{
				$test_shifts = array_keys($shift_list, $user['user_id']);
				foreach($test_shifts as $test)
				{
					$current = $this->Shifts_model->shift($test);
					if($current['0']['shift_start'] != $shift['0']['shift_start'] && $current['0']['shift_end'] != $shift['0']['shift_end'])
					{
						$data['user_list'][$user['user_id']] = $user['first_name'] . ' ' . $user['last_name'];
					}
				}
			}
		}
		
		$data['event_id'] = $event->event_type;
		if($event->event_type == '2')
		{
			$data['is_tagday'] = '1';
		}
		else
		{
			$data['is_tagday'] = '0';
		}
		
		if (empty($data['user_list'])) 
		{
			$this->session->set_flashdata('notice', '<div class="notice">All members of your family group are currently signed up for the time that you selected. You cannot place people on the wait list that are currently signed up to be working a shift. If you believe this to be an error, please contact the event coordinator.</div>');
			redirect("events/view/{$event->event_id}");
		}

		$data['event'] = $event;
		$data['shift_id'] = $shift['0']['shift_id'];
		$data['transportation_list'] = array('0' => 'This person will provide their own transportation', '1' => 'This person needs a carpool ride', '3' => 'This person is able/willing to drive in a carpool');

		
		//load the form for a user to fill in shift details
		$this->form_validation->set_rules('selected_user','selected_user','max_length[11]');			
		$this->form_validation->set_rules('shift_for','account working for','max_length[255]|required');
		$this->form_validation->set_rules('transportation','shift transporation','required');			
		$this->form_validation->set_error_delimiters('<br /><br /><span class="error">', '</span><br />');
	
		if ($this->form_validation->run() == FALSE)
		{
			$this->template->load('template', 'events/waitlist_form', $data);
		}
		else // passed validation proceed to post success logic
		{			
			$selected_user = set_value('selected_user');
			$waitlist_spot['shift_start'] = $shift['0']['shift_start'];
			$waitlist_spot['shift_end'] = $shift['0']['shift_end'];	
			$waitlist_spot['corresponding_shift'] = $shift['0']['shift_id'];
			$waitlist_spot['event_id'] = $event->event_id;
			$waitlist_spot['user_id'] = $selected_user;	
			$waitlist_spot['shift_for'] = set_value('shift_for');
			$waitlist_spot['transportation'] = set_value('transportation');
			
			$success = $this->Shifts_model->new_waitlist_spot($waitlist_spot);
			if($success == FALSE)
			{	
				$this->session->set_flashdata('notice', "<div class='error'>Something went horribly wrong, and you were not added to the waitlist. Please try again later.</div>");
				redirect("events/view/{$event->event_id}");
			}
			else			
			{
				$this->session->set_flashdata('notice', "<div class='success'>This person has successfully been added to the waiting list for this time slot. They will be contacted if space opens for this event.</div>");
				redirect("events/view/{$event->event_id}");
			}
		}		
	}
	
	function _get_user()
	{
		$user_id = $this->tank_auth->get_user_id();
		$profile_data = $this->Profiles_model->get_by_user($user_id);
		$profile_data = $profile_data->row();
		return $profile_data;
	}
}