<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profiles_model extends CI_Model {

    function Profiles_model()
    {
        parent::__construct();
    }
	
	function is_admin()
	{
			if($this->tank_auth->is_logged_in())
			{
				$user_id = $this->tank_auth->get_user_id();
				$profile_data = $this->Profiles_model->get_by_user($user_id);
				$profile_data = $profile_data->row();
				
				if($profile_data->is_admin == '1')
				{
					return TRUE;
				}
			}	
			else
			{	
				return FALSE;
			}
	}

	function get($filters = "|is_student=1") //sample param: $filters = "type=rays|date_min=04012011"
	{
		if(isset($filters)) //if filters are supplied, explode them into an array, then for each filter add its corresponding where clause
		{
			$filters = explode("|", $filters);
			$this->db->start_cache();
			foreach($filters as $filter)
			{
				$filter = explode("=", $filter); //filter['0'] becomes filter type, filter['1'] becomes filter modifier
				switch($filter['0'])
				{
					case "is_student":
						if($filter['1'] == 'void')
							break;
						else
							$this->db->where('is_student', $filter['1']);
						break;
					case "name_search":
						if($filter['1'] == '' || $filter['1'] == 'Name')
						{
							break; //no search
						}
						else
						{
							$this->db->like('first_name', $filter['1']);
							$this->db->or_like('last_name', $filter['1']);
							break;
						}
				}
			}
		}
		$this->db->stop_cache();
		$results['total'] = $this->db->count_all_results('profiles');
		$results['page'] = $this->db->get('profiles')->result_array();
		$this->db->flush_cache();
		return $results;
	}

	function count_all()
	{
		return $this->db->count_all('profiles');
	}
	
	function all_for_mail()
	{
		$this->db->select('profiles.first_name, profiles.last_name, profiles.secondary_email, users.email');
		$this->db->from('profiles');
		$this->db->join('users', 'profiles.user_id= users.id');
		$this->db->where('profiles.active', '1');
		$q = $this->db->get();
		return $q->result_array();
	}
	
	function all_for_report()
	{
		$this->db->select('profiles.first_name, profiles.last_name, profiles.is_student, profiles.family_group, shifts.shift_id');
		$this->db->from('profiles');
		$this->db->join('shifts', 'profiles.user_id= shifts.user_id');
		$q = $this->db->get();
		return $q->result_array();
	}

	function get_paged_list($limit = 10, $offset = 0)
	{
		$this->db->order_by('user_id','asc');
		return $this->db->get('profiles', $limit, $offset);
	}

	function get_by_user($user_id)
	{
		
		$this->db->select('profiles.*, users.email');
		$this->db->from('profiles');
		$this->db->join('users', 'profiles.user_id= users.id');
		$this->db->where('profiles.user_id', $user_id);
		$q = $this->db->get();
		return $q;
	}
	
	function get_family_group($family_group)
	{
		$this->db->where('family_group', $family_group);
		$this->db->select('user_id, first_name, last_name');
		$this->db->from('profiles');
		$q = $this->db->get()->result_array();
		return $q;
	}
	
	function update($user_id, $profile)
	{
		$this->db->where('user_id', $user_id);
		$this->db->update('profiles', $profile);
		
		if ($this->db->affected_rows() == '1')
		{
			return TRUE;
		}		
		else
		{
			return FALSE;
		}
	}
	
	function update_status($user_id, $value)
	{
		$this->db->where('user_id', $user_id);
		$this->db->update('profiles', $value);
	}

	function save_new_user($form_data)
	{
		$this->db->insert('profiles', $form_data);
		
		if ($this->db->affected_rows() == '1')
		{
			return TRUE;
		}		
		else
		{
			return FALSE;
		}
	}

	function get_user()
	{
		$user_id = $this->tank_auth->get_user_id();
		$profile_data = $this->get_by_user($user_id);
		$profile_data = $profile_data->row();
		return $profile_data;
	}
}

/* End of file profiles_model.php */
/* Location: ./system/application/models/profiles_model.php */


