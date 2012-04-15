<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Shifts extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->database();
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->library('security');
		$this->load->model('Events_model');
		$this->load->model('Shifts_model');
	}
	
	function signup()
	{
		//first check to make sure we're able to sign up for a shift, then get the shift ID and make sure it is valid
		if(!$this->tank_auth->is_logged_in())
		{
			redirect('auth/login');
		}
		else
		{
			$profile_data = $this->Profiles_model->get_user();
		}		
		
		$shift_id = $this->uri->segment(3, 0);
		
		if($shift_id == '0')
		{
			echo("Invalid event ID specified.");
			return;
		}
		
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

		$data['transportation_list'] = array('0' => 'This person will provide their own transportation', '1' => 'This person needs a carpool ride', '3' => 'This person is able/willing to drive in a carpool');
		
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
		
		if (empty($data['user_list'])) 
		{
			$this->session->set_flashdata('notice', '<div class="notice">All members of your family group are currently signed up for this event. You cannot sign up anyone else.</div>');
			redirect("events/view/{$event->event_id}");
		}
		
		//if the event is a tag day, set a variable that will hide money designation 
		$data['event_id'] = $event->event_type;
		if($event->event_type == '2')
		{
			$data['is_tagday'] = '1';
		}
		else
		{
			$data['is_tagday'] = '0';
		}
		$data['event'] = $event;
		$data['shift_id'] = $shift['0']['shift_id'];
		
		//load the form for a user to fill in shift details
		$this->form_validation->set_rules('selected_user','selected_user','max_length[11]');			
		$this->form_validation->set_rules('shift_for','shift_for','max_length[255]|required');
		$this->form_validation->set_rules('transportation','shift transporation','required');			
		$this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');
	
		if ($this->form_validation->run() == FALSE)
		{
			$this->template->load('template', 'events/shift_signup', $data);
		}
		else // passed validation proceed to post success logic
		{			
			$selected_user = set_value('selected_user');
			$shift_for = set_value('shift_for');
			$transportation = set_value('transportation');

			$updated_shift['shift_for'] = $shift_for;
			$updated_shift['shift_id'] = $shift['0']['shift_id'];
			$updated_shift['transportation'] = $transportation;
			$updated_relations['shift_id'] = $shift['0']['shift_id'];
			$updated_relations['user_id'] = $selected_user;	
			
			$success = $this->Shifts_model->save_shift($updated_relations, $updated_shift);
			if($success == FALSE)
			{	
				$this->session->set_flashdata('notice', "<div class='error'>Something went horribly wrong, and your shift was not saved. Please try again later.</div>");
				redirect("events/view/{$event->event_id}");
			}
			else			
			{
				$this->session->set_flashdata('notice', "<div class='success'>User signed up successfully! Please look for future emails that will come to you concerning this event's details.</div>");
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