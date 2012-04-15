<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site_model extends CI_Model {

	function Site_model()
	{
		parent::__construct();
	}

	function get_text($location)
	{
		$this->db->where('location', $location);
		$query = $this->db->get('text');
		return $query->result_array();
	}

	function update_text($location, $row)
	{
		$this->db->where('location', $location);
		$this->db->update('text', $row);
	}
}
