<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Events_model extends CI_Model {

	function Events_model()
	{
		parent::__construct();
	}

	function get($filters = "") //sample param: $filters = "type=rays|date_min=04012011"
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
					case "type":
						if($filter['1'] == 'void')
							break;
						else
							$this->db->where('event_type', $filter['1']);
						break;
					case "dates":
						if($filter['1'] == 'all')
						{
							break; //all ranges okay, do nothing
						}
						else if($filter['1'] == 'past')
						{
								$this->db->where('event_date <=', date('Y-m-d'));
								break;
						}
						else if($filter['1'] == 'future')
						{
							$this->db->where('event_date >', date('Y-m-d'));
							break;
						}
					case "name":
						$this->db->where('event_name', $filter['1']);
						break;
					/*case "slots_available_true":
						$this->db->where('type', $filter['1'])
						break;*/
				}
			}
		}
		$this->db->stop_cache();
		$results['total'] = $this->db->count_all_results('events');
		$results['page'] = $this->db->get('events')->result_array();
		$this->db->flush_cache();
		return $results;
	}

	function get_all()
	{	
		$query = $this->db->get('events');
		return $query->result_array();
	}
	
	function get_by_month($year, $month)
	{
		$this->db->like('event_date', $year.'-'.$month);
		$query = $this->db->get('events');
		return $query->result_array();
	}
	
	function get_names($dates = 'all')
	{
		if($dates == 'past')
		{
				$this->db->where('event_date <=', date('Y-m-d'));
		}
		else if($dates == 'future')
		{
			$this->db->where('event_date >', date('Y-m-d'));
		}

		$this->db->select('id , event_date, event_name');
		$q = $this->db->get('events');
		return $q->result_array();	
	}
	
	function get_next_available_shift($id)
	{
		$this->db->where('event_id', $id);
		$this->db->where('user_id', '0');
		$query = $this->db->get('shift_relationships', 1);
		return $query->row();
	}
	
	function get_shifts_available($id)
	{	
		$this->db->where('event_id', $id);
		$this->db->where('user_id', '0');
		$query = $this->db->get('shift_relationships');
		return $query->result_array();
	}
	
	function get_shifts_by_user($user_id)
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
	
	function get_event_by_shift($shift_id)
	{
		$this->db->where('shift_id', $shift_id);
		$this->db->join('events', 'events.id = shift_relationships.event_id');
		$event = $this->db->get('shift_relationships');
		return $event->row();
	}
	
	function get_relations_by_type($type)
	{
		$this->db->where('event_type', $type);
		$this->db->join('events', 'events.id = shift_relationships.event_id');
		$event = $this->db->get('shift_relationships');
		return $event->result_array();
	}
	
	
	function get_event_ids_by_user($user_id)
	{
		$this->db->where('user_id', $user_id);
		$this->db->select('event_id');
		$events = $this->db->get('shift_relationships');
		return $events->result_array();
	}

	function count_all()
	{
		return $this->db->count_all('events');
	}

	function get_paged_list($limit = 10, $offset = 0)
	{
		return $this->db->get('events', $limit, $offset)->result_array();
	}

	function get_by_id($id)
	{
		$this->db->where('id', $id);
		return $this->db->get('events');
	}
	
	function update($event, $id)
	{
		$this->db->where('id', $id);
		$this->db->update('events', $event);
	}

	function save_new_event($form_data)
	{
		$this->db->insert('events', $form_data);
		
		if ($this->db->affected_rows() == '1')
		{
			$id = $this->db->insert_id();
		}		
		return $id;
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
		$this->db->where('shift_id', $shift_relations->shift_id);
		$this->db->update('shift_relationships', $shift_relations);
		//now set up the shift data
		$this->db->where('shift_id', $shift_data->shift_id);
		$this->db->update('shifts', $shift_data);
		//if those were successful, commit changes
		$this->db->trans_complete();
		if ($this->db->trans_status() === TRUE)
			return TRUE;
		else
			return FALSE;
	}

	function delete_event($event_id)
	{
		$this->db->trans_start();
		
		//get shift IDs for the event to be deleted
		$this->db->select('shift_id')->from('shift_relationships')->where('event_id', $event_id);
		$shifts = $this->db->get()->result_array();

		$i = 0;
		foreach($shifts as $shift) //this little swap just collapses a multidimensional array
		{
			$temp[$i] = $shift['shift_id'];
			$i++;			
		}		
		$shifts = $temp;
		unset($temp);

		//remove the event's shifts
		$this->db->where_in('shift_id', $shifts);
		$this->db->delete('shifts');
		//remove the event
		$this->db->where('id', $event_id);
		$this->db->delete('events');
		//remove the relations
		$this->db->where('event_id', $event_id);
		$this->db->delete('shift_relationships');

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
}
