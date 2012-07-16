<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Profiles extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		
		if($this->Profiles_model->is_admin() != TRUE)
		{
			redirect('account');
		}
		
		
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
	}
	
	function create()
	{
		$this->_set_rules('create');

		$data['errors'] = array();

		if ($this->form_validation->run() == FALSE) 
		{
			$data['form_open'] = form_open('admin/profiles/create');
			$this->template->load('template', 'profiles/register_form', $data);
		}
		else
		{								// validation ok
			if (!is_null($data = $this->tank_auth->create_user('',
					$this->form_validation->set_value('email'),
					$this->form_validation->set_value('password'),
					FALSE))) 
					{									// success creating user, so now add profile
						$profile_data = array(
							'user_id' => $this->db->insert_id(),
							'first_name' => set_value('first_name'),
							'last_name' => set_value('last_name'),	
							'date_of_birth' => set_value('date_of_birth'),
							'primary_email' => set_value('email'),
							'secondary_email' => set_value('secondary_email'),
							'is_student' => set_value('is_student'),
							'carpool' => set_value('carpool'),
							'primary_phone' => set_value('primary_phone'),
							'secondary_phone' => set_value('secondary_phone'),
							'family_group' => set_value('family_group')
						);
						
						$write_status = $this->Profiles_model->save_new_user($profile_data);			
						if ($write_status == TRUE) // the information has therefore been successfully saved in the db
						{
							$this->session->set_flashdata('notice', '<div class="success">User creation successful</div>');
							redirect('admin/dashboard');   //write okay, let them log in
						}
						else
						{
							$this->session->set_flashdata('notice', '<div class="success">User creation failed</div>');
							redirect('admin/dashboard');
						}
					}
		}
	}
	
	function edit()
	{			
		$uid = $this->uri->segment('4', '0');
		if($uid == '0')
		{
			$this->session->set_flashdata('notice', '<div class="error">Invalid profile ID specified.</div>');
			redirect('admin/profiles/listing');
		}


		$stored_profile = $this->Profiles_model->get_by_user($uid);
		$stored_profile = $stored_profile->result_array();
		$data = $stored_profile['0'];
		
		//establish form validation rules
		$this->_set_rules('edit');
	
		if ($this->form_validation->run() == FALSE) // validation hasn'\t been passed
		{
			$data['view']['do'] = '';
			$data['form_open'] = form_open("admin/profiles/edit/{$uid}");
			$this->template->load('template', 'profiles/admin_update', $data);
		}
		else // passed validation proceed to post success logic
		{
			
			
			$profile_data = array(
					       	'first_name' => set_value('first_name'),
					       	'last_name' => set_value('last_name'),
							'date_of_birth' => set_value('date_of_birth'),
					       	'is_student' => set_value('is_student'),
					       	'carpool' => set_value('carpool'),
					       	'primary_phone' => set_value('primary_phone'),
					       	'family_group' => set_value('family_group'),
					       	'active' => set_value('active')
						);
			$sec_mail = set_value('secondary_email');
			if(!empty($sec_mail))
				$profile_data['secondary_email'] = set_value('secondary_email');
			$sec_phone = set_value('secondary_phone');	
			if(!empty($sec_phone))
				$profile_data['secondary_phone'] = set_value('secondary_phone');
			
						
			// run insert model to write data to db, but first send emails for account info/activation
		
			$write_status = $this->Profiles_model->update($uid, $profile_data);
			
			if ($write_status == TRUE) // the information has therefore been successfully saved in the db
			{
				$this->session->set_flashdata('notice', '<div class="success">The profile was updated successfully</div>');
				redirect('admin/profiles/listing'); 
			}
			else
			{
				$this->session->set_flashdata('notice', '<div class="error">Something went horribly wrong, and the profile could not be saved.</div>');
				redirect("admin/profiles/edit/{$uid}");
			}
		}
	}	

	function listing()
	{
		$filter_string = ""; //initialize the filters to none

		$post_submit = $this->input->post('submit');
		if(!empty($post_submit)) //form has been submitted, set up filters
		{
			$filter_string = $this->input->post('filters_hidden') . '|' . $this->input->post('is_student') . '|' . 'name_search=' . $this->input->post('name_search');
		}

		$results = $this->Profiles_model->get($filter_string);
		
		// load the HTML Table Class
		$this->load->library('table');
		$this->table->set_heading('First Name', 'Last Name', 'Date of Birth', 'Family Group', 'Actions');
		$tmpl = array ( 'table_open'  => '<table border="0" cellpadding="4" cellspacing="0" id="sort" class="tablesorter">' ); //define the table as sortable
		$this->table->set_template($tmpl);
		
		$row_count = 0;

		foreach($results['page'] as $result)
		{
			if($result['is_admin'] == '0')
			{
				$admin_status = anchor("admin/profiles/make_admin/{$result['user_id']}", 'Make an Admin');
			}
			else
			{
				$admin_status = anchor("admin/profiles/remove_admin/{$result['user_id']}", 'Remove as Admin');
			}

			$this->table->add_row($result['first_name'], $result['last_name'], $result['date_of_birth'], $result['family_group'], anchor("admin/profiles/edit/{$result['user_id']}", 'Edit').' - '.$admin_status);
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

				
		$data['is_student_selection'] = array('is_student=void' => 'Any Student Status', 'is_student=1' => 'Only Students', 'is_student=0' => 'Only Non-Students');	

		$data['tablesortjs'] = '<script type="text/javascript" src="' . base_url() . 'assets/js/jquery.tablesorter.min.js"></script>	
		<script type="text/javascript">
			$(document).ready(function() 
			{ 
				$("#sort").tablesorter( {sortList: [[1,0]]} ); 
			} 
			); 
		</script>';
			
		// load the view
		$this->template->load('template', 'profiles/list_view', $data);

	}

	function make_admin()
	{
		$uid = $this->uri->segment('4');
		$profile_data = array( 'is_admin' => '1' );
		$write_status = $this->Profiles_model->update($uid, $profile_data);
			
			if ($write_status == TRUE) // the information has therefore been successfully saved in the db
			{
				$this->session->set_flashdata('notice', '<div class="success">The user has been made an administrator.</div>');
				redirect('admin/profiles/listing'); 
			}
			else
			{
				echo "could not make admin";
			}
	}

	function remove_admin()
	{
		$uid = $this->uri->segment('4');
		$profile_data = array( 'is_admin' => '0' );
		$write_status = $this->Profiles_model->update($uid, $profile_data);
			
			if ($write_status == TRUE) // the information has therefore been successfully saved in the db
			{
				$this->session->set_flashdata('notice', '<div class="success">This user no longer has admin access.</div>');
				redirect('admin/profiles/listing'); 
			}
			else
			{
				echo "could not make regular user";
			}
	}

	function _set_rules($form)
	{
		if($form == 'create')
		{
			$this->form_validation->set_rules('email','Primary Email','required|trim|xss_clean|valid_email|max_length[255]');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|min_length['.$this->config->item('password_min_length', 'tank_auth').']|max_length['.$this->config->item('password_max_length', 'tank_auth').']|alpha_dash');
			$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|xss_clean|matches[password]');
		}

		$this->form_validation->set_rules('first_name','First Name','required|trim|xss_clean|max_length[50]');		
		$this->form_validation->set_rules('last_name','Last Name','required|trim|xss_clean|max_length[50]');
		$this->form_validation->set_rules('date_of_birth','Date of Birth','required|trim|xss_clean|max_length[255]');
		$this->form_validation->set_rules('secondary_email','Secondary Email','trim|xss_clean|valid_email|max_length[255]');		
		$this->form_validation->set_rules('is_student','Is Student?','required|xss_clean|max_length[11]');						
		$this->form_validation->set_rules('primary_phone','Primary Phone','required|trim|xss_clean|is_numeric|max_length[20]');			
		$this->form_validation->set_rules('secondary_phone','Secondary Phone','xss_clean|is_numeric|max_length[20]');
		$this->form_validation->set_rules('family_group','Family/Group Name','required|trim|xss_clean|max_length[50]');
		$this->form_validation->set_rules('active','Is Active?','required|xss_clean|max_length[11]');						
		$this->form_validation->set_rules('submit_button', 'Submit', '');
		$this->form_validation->set_error_delimiters('<span class="error" style="float:right;">', '</span>');
	}
}