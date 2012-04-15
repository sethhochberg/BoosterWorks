<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Broadcast extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		
		if($this->Profiles_model->is_admin() != TRUE)
		{
			redirect('account');
		}
		
		$this->load->helper('ckeditor');
		$this->load->library('form_validation');
		$this->load->library('table');
		$this->load->library('email');
		$this->load->model(array('Events_model', 'Profiles_model', 'Shifts_model'));
	}
	
	function selection()
	{		
		$target = $this->uri->segment(4, NULL);
		
		$this->form_validation->set_rules('selection' , 'required');
				
			if($target == NULL)
			{
				$this->session->set_flashdata('notice', '<div class="error">The mail selection dropdown cannot be used without selecting a mail target from the admin dashboard.</div>');
				redirect('admin/dashboard');
			}			
			if($target == 'event')
			{					
					if($this->form_validation->run() == FALSE)
					{	
						$events = $this->Events_model->get_names();	
						
							$i = 0;
							foreach($events as $event_temp) //this little swap just collapses a multidimensional array
							{
								$temp[$event_temp['id']] = $event_temp['event_date'] . ' - ' . $event_temp['event_name'];
								$i++;			
							}		
							$events = $temp;
							unset($temp);
						
						$data['title'] = 'Event Selection';
						$data['form_open'] = form_open("admin/broadcast/selection/{$target}");
						$data['selection'] = form_dropdown('event', $events);
						$this->template->load('template', 'admin/selection_dropdown', $data);
					}
					else //form submitted
					{
						$id = $this->input->post('event');
						redirect("admin/broadcast/event/{$id}");
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
						$id = $this->input->post('type');
						redirect("admin/broadcast/type/{$id}");
					}
			}
			if($target == 'family')
			{
				
			}
			if($target == 'all')
			{
				redirect("admin/broadcast/all");
			}
	}
	
	function event()
	{		
		$id = $this->uri->segment(4);

		$event = $this->Events_model->get_by_id($id)->row();
		$shifts = $this->Shifts_model->shifts_by_event($id);
		
		$this->_validation_rules();
		
		//prepare the table of emails to show to the composer, and then build the arrays of emails for the mailer
			$i = 0;
			foreach($shifts as $shift)
			{
				if($shift['user_id'] != '0')
				{	
					$shift_details = $this->Shifts_model->shift($shift['shift_id']);
					$profile = $this->Profiles_model->get_by_user($shift['user_id'])->row();
					$this->table->add_row(array($profile->first_name, $profile->last_name, mailto($profile->email), mailto($profile->secondary_email)));
					$primary[$i] = $profile->primary_email;
					if(isset($profile->secondary_email))
					{
						$secondary[$i] = $profile->secondary_email;
						$i++;
					}
				}
			}			
			$list = array_merge((array)$primary, (array)$secondary);
		
		if ($this->form_validation->run() == FALSE)
		{			
			$this->table->set_heading('First Name', 'Last Name', 'Primary Email', 'Secondary Email');
			
			$data['target'] = "volunteers working {$event->event_name}";
			$data['ckeditor'] = array(	'id' =>'message', 'path'=>'assets/js/ckeditor');
			$data['url'] = "admin/broadcast/event/{$id}";
			$this->template->load('template', 'admin/compose_broadcast', $data);			
		}
		else
		{
			$message = $this->input->post('message');
			$subject = $this->input->post('subject');
			$this->_send_mail($list, $subject, $message);
		}
	}
	
	function type()
	{		
		$type = $this->uri->segment(4);

		$shifts = $this->Events_model->get_relations_by_type($type);
		
		$this->_validation_rules();

		$primary = array();
		
		//prepare the table of emails to show to the composer, and then build the arrays of emails for the mailer
			$i = 0;
			foreach($shifts as $shift)
			{
				if($shift['user_id'] != '0')
				{	
					$shift_details = $this->Shifts_model->shift($shift['shift_id']);
					$profile = $this->Profiles_model->get_by_user($shift['user_id'])->row();
					
					if(!in_array($profile->primary_email, $primary)) //dont add a new listing if we already have this one
					{
						$this->table->add_row(array($profile->first_name, $profile->last_name, mailto($profile->email), mailto($profile->secondary_email)));
						$primary[$i] = $profile->primary_email;
						if(isset($profile->secondary_email))
						{
							$secondary[$i] = $profile->secondary_email;
							$i++;
						}
					}
				}
			}			
			//if there is a secondary email array, merge the primary and secondary email lists together
			//if no secondary exists, simply use the primary list
			//if no lists exist, throw an error
			$list = array();
			if(!empty($primary))
			{
				if(!empty($secondary))
				{
					$list = array_merge((array)$primary, (array)$secondary);
				}
				else
				{
					$list = $primary;
				}
				$list = array_unique($list);
			}
			else
			{
				$this->session->set_flashdata('notice', '<div class="error">No users will recieve this message - there are no users working in the selected category at this time.</div>');
			}			
			
		
		if ($this->form_validation->run() == FALSE)
		{			
			$this->table->set_heading('First Name', 'Last Name', 'Primary Email', 'Secondary Email');
			

			if($type == '0')
				$human_type = 'Tropicana Field Concessions';
			elseif($type == '1') 
				$human_type = 'Raymond James Stadium Concessions';
			elseif($type == '2') 
				$human_type = 'Student Tag Day';
			elseif($type == '3') 
				$human_type = 'Renaissance Festival';
			elseif($type == '4') 
				$human_type = 'Golf Tournament Parking';
			elseif($type == '5') 
				$human_type = 'Other';
			
			$data['target'] = "volunteers working events of type " . $human_type;
			$data['ckeditor'] = array(	'id' =>'message', 'path'=>'assets/js/ckeditor');
			$data['url'] = "admin/broadcast/type/{$type}";
			$this->template->load('template', 'admin/compose_broadcast', $data);			
		}
		else
		{
			$message = $this->input->post('message');
			$subject = $this->input->post('subject');
			$this->_send_mail($list, $subject, $message);
		}
	}
	
	function all()
	{
		if($this->Profiles_model->is_admin() != TRUE)
		{
			redirect('account');
		}
		
		$this->_validation_rules();
			
		$this->table->set_heading('First Name', 'Last Name', 'Primary Email', 'Secondary Email');
		
		$users = $this->Profiles_model->all_for_mail();
		
		$i = 0;
		foreach($users as $user)
		{
				$this->table->add_row(array($user['first_name'], $user['last_name'], mailto($user['email']), mailto($user['secondary_email'])));
				$primary[$i] = $user['email'];
				if(isset($user['secondary_email']))
				{
					$secondary[$i] = $user['secondary_email'];
					$i++;
				}
		}		
		$list = array_merge((array)$primary, (array)$secondary);
			
		if ($this->form_validation->run() == FALSE)
		{			
			
			$data['target'] = "all volunteers";
			$data['ckeditor'] = array(	'id' =>'message', 'path'=>'assets/js/ckeditor');
			$data['url'] = "admin/broadcast/all";
			$this->template->load('template', 'admin/compose_broadcast', $data);			
		}
		else
		{
			$message = $this->input->post('message');
			$subject = $this->input->post('subject');
			$this->_send_mail($list, $subject, $message);
		}
	}
	
	function _validation_rules()
	{
		$this->form_validation->set_rules('subject', 'Subject', 'required|min_length[10]');
		$this->form_validation->set_rules('message', 'Message', 'required|min_length[20]|');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
	}
	
	function _send_mail($list, $subject, $message)
	{
		//prepare the email itself if we've validated the form

		foreach ($list as $email)
		{
			$this->email->clear();

			$this->email->to($email);
			$this->email->from('boosterworks@tarponspringsband.com');
			$this->email->subject($subject);
			$this->email->message($message);
			$this->email->send();
		}
		$this->session->set_flashdata('notice', '<div class="success">Mail sent successfully!</div>');
		redirect('admin/dashboard');
	}
}