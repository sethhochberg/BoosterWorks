<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Broadcasts_model extends CI_Model {

	function Broadcasts_model()
	{
		parent::__construct();
	}

	function count_all()
	{
		return $this->db->count_all('broadcasts');
	}

	function get_paged_list($limit = 10, $offset = 0)
	{
		$this->db->order_by('id','dec');
		return $this->db->get('broadcasts', $limit, $offset);
	}

	function get_by_id($id)
	{
		$this->db->where('id', $id);
		return $this->db->get('broadcasts');
	}

	function save_new_item($form_data)
	{
		$this->db->insert('broadcasts', $form_data);
		
		if ($this->db->affected_rows() == '1')
		{
			$id = $this->db->insert_id();
		}		
		return $id;
	}
}
