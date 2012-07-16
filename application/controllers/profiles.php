<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profiles extends CI_Controller {

	function Profiles()
	{
		parent::__construct();	
		$this->load->library('form_validation');
		$this->load->database();
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->library('security');
		$this->load->model('Profiles_model');
	}

	
	
	function change_group()
	{
		if (!$this->tank_auth->is_logged_in()) 
		{									// logged in
			redirect('/auth/login/');
		} 		
		//Load stuff we need

		$this->load->library(array('email', 'form_validation'));
		$this->load->helper(array('email', 'form'));
		
		$this->form_validation->set_rules('request', 'Request', 'required|xss_clean');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');

		if($this->form_validation->run() == FALSE)
		{
			$this->template->load('template', 'account/group_landing');
		}
		else
		{
			$user= $this->_get_user();
			$this->email->from('boosterworks@tarponspringsband.com', 'Boosterworks');
			$this->email->to('hochberg.jeff@gmail.com');
			$this->email->subject('Family/Group Change Requested');
			$this->email->message("User " . $user->first_name . ' ' .  $user->last_name . " has requested a change to thier family group. Please follow up with them via email at " . $user->primary_email);
			$this->email->send();
			
			$this->session->set_flashdata('notice', '<div class="success">You have requested a change to your family group. For privacy reasons, an administrator will contact you via email to continue.</div>');
			redirect('');
		}
	}
	
	function update()
	{			
		//begin with some basic checks to see if we really can proceed with registration
		if (!$this->tank_auth->is_logged_in()) 
		{									// logged in
			redirect('/auth/login/');
		} 
		elseif ($this->tank_auth->is_logged_in(FALSE)) 
		{						// logged in, not activated
			redirect('/auth/send_again/');
		}
		
		$stored_profile = $this->Profiles_model->get_by_user($this->tank_auth->get_user_id());
		$stored_profile = $stored_profile->result_array();
		$data = $stored_profile['0'];
		
		//establish form validation rules
		$this->form_validation->set_rules('first_name','First Name','required|trim|xss_clean|max_length[50]');			
		$this->form_validation->set_rules('last_name','Last Name','required|trim|xss_clean|max_length[50]');
		$this->form_validation->set_rules('date_of_birth','Date of Birth','required|trim|xss_clean|max_length[255]');					
		$this->form_validation->set_rules('secondary_email','Secondary Email','trim|xss_clean|valid_email|max_length[255]');			
		$this->form_validation->set_rules('is_student','Is Student?','required|xss_clean|max_length[11]');			
		//$this->form_validation->set_rules('carpool','Carpool?','required|max_length[11]');			
		$this->form_validation->set_rules('primary_phone','Primary Phone','required|trim|xss_clean|is_numeric|max_length[20]');			
		$this->form_validation->set_rules('secondary_phone','Secondary Phone','xss_clean|is_numeric|max_length[20]');	
		
		$this->form_validation->set_rules('submit_button', 'Submit', '');
			
		$this->form_validation->set_error_delimiters('<br /><span style="color: red;">', '</span>');
	
		if ($this->form_validation->run() == FALSE) // validation hasn'\t been passed
		{
			$data['view']['do'] = '';
			$this->template->load('template', 'profiles/update_form', $data);
		}
		else // passed validation proceed to post success logic
		{
			
			
			$profile_data = array(
					       	'first_name' => set_value('first_name'),
					       	'last_name' => set_value('last_name'),
							'date_of_birth' => set_value('date_of_birth'),
					       	'is_student' => set_value('is_student'),
					       	'carpool' => set_value('carpool'),
					       	'primary_phone' => set_value('primary_phone')
						);
			$sec_mail = set_value('secondary_email');
			if(!empty($sec_mail))
				$profile_data['secondary_email'] = set_value('secondary_email');
			$sec_phone = set_value('secondary_phone');	
			if(!empty($sec_phone))
				$profile_data['secondary_phone'] = set_value('secondary_phone');
			
						
			// run insert model to write data to db, but first send emails for account info/activation
		
			$write_status = $this->Profiles_model->update($this->tank_auth->get_user_id(), $profile_data);
			
			if ($write_status == TRUE) // the information has therefore been successfully saved in the db
			{
				$this->session->set_flashdata('notice', '<div class="success">The profile was updated successfully</div>');
				redirect('account'); 
			}
			else
			{
				$this->session->set_flashdata('notice', '<div class="error">Something went horribly wrong, and the profile could not be saved.</div>');
				redirect('account');
			}
		}
	}
	
	function _send_email($type, $email, &$data)
	{
		$this->load->library('email');
		$this->email->set_newline("\r\n"); 
		$this->email->from($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
		$this->email->reply_to($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
		$this->email->to($email);
		$this->email->subject(sprintf($this->lang->line('auth_subject_'.$type), $this->config->item('website_name', 'tank_auth')));
		$this->email->message($this->load->view('email/'.$type.'-html', $data, TRUE));
		$this->email->set_alt_message($this->load->view('email/'.$type.'-txt', $data, TRUE));
		$this->email->send();
	}
	
	function _get_user()
	{
		$user_id = $this->tank_auth->get_user_id();
		$profile_data = $this->Profiles_model->get_by_user($user_id);
		$profile_data = $profile_data->row();
		return $profile_data;
	}
}	
/* End of file profiles.php */
/* Location: ./system/application/controllers/profiles.php */