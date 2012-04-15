<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Waitlists_model extends CI_Model {

	function Waitlists_model()
	{
		parent::__construct();
	}

	function new_waitlist_spot($wait_list)
	{
		$this->db->insert('wait_list', $wait_list);
		if ($this->db->affected_rows() == '1')
			return TRUE;
		else
			return FALSE;		
	}
	
	function fetch_id($user_id, $event_id)
	{
		$this->db->select('id');
		$this->db->where('user_id', $user_id);
		$this->db->where('event_id', $event_id);
		$q = $this->db->get('wait_list');
		return $q->result_array();
	}
	
	function get_waitlist($user)
	{
		$this->db->where('user_id', $user);
		$q = $this->db->get('wait_list');
		return $q->result_array();
	}

	function list_by_event($event_id)
	{
		$this->db->where('event_id', $event_id);
		$this->db->join('profiles', 'profiles.user_id = wait_list.user_id');
		$q = $this->db->get('wait_list');
		return $q->result_array();
	}
	
	function shift_to_waitlist($shift_id)
	{
		$this->db->trans_start();

		//fetch user id and event id
		$this->db->where('shift_id', $shift_id);
		$target = $this->db->get('shift_relationships')->result_array();
		$this->db->flush_cache();

		//fetch shift details for insert into waitlist table
		$this->db->where('shift_id', $shift_id);
		$shift = $this->db->get('shifts')->result_array();
		$this->db->flush_cache();

		//build waitlist item array and insert details into waitlists table
		$slot['event_id'] = $target['0']['event_id'];
		$slot['user_id'] = $target['0']['user_id'];
		$slot['shift_start'] = $shift['0']['shift_start'];
		$slot['shift_end'] = $shift['0']['shift_end'];
		$slot['shift_for'] = $shift['0']['shift_for'];
		$slot['transportation'] = $shift['0']['transportation'];
		$slot['corresponding_shift'] = $shift_id;

		//insert into wait list
		$this->db->insert('wait_list', $slot);
		$this->db->flush_cache();

		//remove user from confirmed slot now that we've moved to wait list
		$this->db->where('shift_id', $shift_id);
		$this->db->set('user_id', 0);
		$this->db->update('shift_relationships');	

		$this->db->trans_complete();

		$status['item'] = $target['0']['event_id'];
		if ($this->db->trans_status() === FALSE) //some step failed, roll back
		{
		   $status['success'] = FALSE;
		} 
		else //success
		{
			$status['success'] = TRUE;
		}
		return $status;
	}
	
	function delete($waitlist_slot_id)
	{
		$this->db->where('id', $waitlist_slot_id);
		
		$this->db->delete('wait_list');
		if ($this->db->affected_rows() == '1')
			return TRUE;
		else
			return FALSE;	
	}
}
