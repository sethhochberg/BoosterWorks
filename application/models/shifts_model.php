<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shifts_model extends CI_Model {

	function Shifts_model()
	{
		parent::__construct();
	}
	
	function shift($id)
	{
		$this->db->where('shift_id', $id);
		$query = $this->db->get('shifts');
		return $query->result_array();
	}

	function delete($shift_id)
	{
		$this->db->trans_start();

		$this->db->where('shift_id', $shift_id);
		$this->db->delete('shifts');

		$this->db->where('shift_id', $shift_id);
		$this->db->delete('shift_relationships');

		$this->db->trans_complete();
		if ($this->db->trans_status() === TRUE)
			return TRUE;
		else
			return FALSE;
	}

	function remove_slot($event_id)
	{
		$this->db->trans_start();

		$this->db->where('event_id', $event_id);
		$this->db->where('user_id', '0');
		$this->db->select('shift_id');
		$shift_id = $this->db->get('shift_relationships')->result_array();
		$shift_id = $shift_id['0']['shift_id'];

		$this->db->flush_cache();

		$this->db->where('shift_id', $shift_id);
		$this->db->delete('shifts');

		$this->db->where('shift_id', $shift_id);
		$this->db->delete('shift_relationships');

		$this->db->trans_complete();
		if ($this->db->trans_status() === TRUE)
			return TRUE;
		else
			return FALSE;
	}

	function shifts_by_event($id)
	{
		$this->db->where('event_id', $id);
		$this->db->join('shifts', 'shifts.shift_id = shift_relationships.shift_id');
		$this->db->order_by('shift_start');
		$shifts = $this->db->get('shift_relationships');
		return $shifts->result_array();
	}
	
	function shifts_open_by_event($id)
	{	
		$this->db->where('event_id', $id);
		$this->db->where('user_id', '0');
		$query = $this->db->get('shift_relationships');
		return $query->result_array();
	}
	
	function events_by_user($user_id)
	{
		//set params and get list of events where user is related
		$this->db->where('user_id', $user_id);
		$this->db->select('event_id');
		$events = $this->db->get('shift_relationships');
		//transform the list of events into raw numbers, then grab the full event info for each event id
		$events = $events->row_array();
		$this->db->where_in('id', $events);
		$shifts = $this->db->get('events');		
		return $shifts->result_array();
	}
	
	function shifts_by_user($user_id)
	{		
		$this->db->where('user_id', $user_id);
		$this->db->join('shifts', 'shifts.shift_id = shift_relationships.shift_id');
		$shifts = $this->db->get('shift_relationships');
		return $shifts->result_array();
	}

	function count_all()
	{
		return $this->db->count_all('shifts');
	}

	function get_paged_list($limit = 10, $offset = 0)
	{
		$this->db->order_by('id');
		return $this->db->get('events', $limit, $offset)->result();
	}

	function save_new_shifts($form_data)
	{
		$this->db->insert('shifts', $form_data);
		
		if ($this->db->affected_rows() == '1')
		{
			$id = $this->db->insert_id();
		}		
		return $id;
	}
	
	function save_shift($shift_relations, $shift_data)
	{
		$this->db->trans_start();
		//set up relations
		$this->db->where('shift_id', $shift_relations['shift_id']);
		$this->db->update('shift_relationships', $shift_relations);
		//now set up the shift data
		$this->db->where('shift_id', $shift_data['shift_id']);
		$this->db->update('shifts', $shift_data);
		//if those were successful, commit changes
		$this->db->trans_complete();
		if ($this->db->trans_status() === TRUE)
			return TRUE;
		else
			return FALSE;
	}
	
	function create_shift_relations($relations)
	{
		$this->db->insert('shift_relationships', $relations);
	}

	function remove_signup($shift_id)
	{
		$user = array('user_id' => '0');

		$this->db->where('shift_id', $shift_id);
		$this->db->update('shift_relationships', $user);

		$this->db->where('shift_id', $shift_id);
		$this->db->select('event_id');
		$event_id = $this->db->get('shift_relationships');

		return $event_id->row();
	}
	
	function new_waitlist_spot($wait_list)
	{
		$this->db->insert('wait_list', $wait_list);
		if ($this->db->affected_rows() == '1')
			return TRUE;
		else
			return FALSE;		
	}
	
	function get_waitlist($user)
	{
		$this->db->where('user_id', $user);
		$q = $this->db->get('wait_list');
		return $q->result_array();
	}
	
	function update_shift($id, $shift)
	{
		$this->db->where('shift_id', $id);
		$this->db->update('shifts', $shift);
	}
	
	//Move user from waitlist slot to corresponding confirmed slot for a given event
	//Accepts waitlist slot ID, returns confirmation of successful move
	function waitlist_to_shift($id)
	{
		//fetch details for the waitlist slot we're trying to match an open confirmed slot to
		$this->db->where('id', $id);
		//$this->db->select('user_id,event_id');
		$target = $this->db->get('wait_list')->result_array();
		$this->db->flush_cache();
		
		//fetch available confirmed slots that have our criteria
		$this->db->where('event_id', $target['0']['event_id']);
		$this->db->where('user_id', '0');
		$available = $this->db->get('shift_relationships')->result_array();
		$this->db->flush_cache();
		
		//if there are available shifts, take the first shift and assign it to our waitlisted user
		if(!empty($available))
		{
			$this->db->trans_start();
			//move to shift
			$shift_id = $available['0']['shift_id'];
			$shift_relationships['user_id'] = $target['0']['user_id'];
			$this->db->where('shift_id', $shift_id);
			$this->db->update('shift_relationships', $shift_relationships);
			$this->db->flush_cache();

			$new_shift['shift_for'] = $target['0']['shift_for'];
			$new_shift['transportation'] = $target['0']['transportation'];
			$this->db->where('shift_id', $shift_id);
			$this->db->update('shifts', $new_shift);
			$this->db->flush_cache();

			//remove waitlist slot
			$this->db->where('id', $id);
			$this->db->delete('wait_list');

			$this->db->trans_complete();	
			if ($this->db->trans_status() === TRUE)
				$result['success'] = TRUE;
			else
				$result['success'] = FALSE;
		}
		else
		{
			$result['success'] = FALSE;
		}
		$result['item'] = $target['0']['event_id'];
		return $result;
	}
}
