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
		$this->load->library('table');
		$this->load->model('Events_model');
		$this->load->model('Shifts_model');

		if($this->Profiles_model->is_admin() != TRUE)
		{
			redirect('account');
		}
	}

	function confirm()
	{
		$action = $this->uri->segment(4, NULL);
		$object = $this->uri->segment(5, NULL);

		if($action == NULL || $object == NULL)
		{
			//redirect with error - invalid request
			echo "invalid action or object id";
		}

		switch($action)
		{
			case 'delete':
				$data['action'] = 'delete';
				$data['url'] = "admin/shifts/delete/{$object}";
				$this->template->load('template', 'admin/confirm', $data);
				break;
			case 'unsign':
				$data['action'] = 'remove the signup for';
				$data['url'] = "admin/shifts/remove_signup/{$object}";
				$this->template->load('template', 'admin/confirm', $data);
				break;
			case 'remove':
				$data['action'] = 'remove a slot from';
				$data['url'] = "admin/shifts/remove_slot/{$object}";
				$this->template->load('template', 'admin/confirm', $data);
				break;
			case 'remove_from_waitlist':
				$data['action'] = 'remove the waitlist signup for';
				$data['url'] = "admin/shifts/remove_from_waitlist/{$object}";
				$this->template->load('template', 'admin/confirm', $data);
				break;
		}
	}

	function remove_signup()
	{
		$shift_id = $this->uri->segment(4);
		$event_id = $this->Shifts_model->remove_signup($shift_id);
		$this->session->set_flashdata('notice', "<div class='success'>Shift signup removed successfully. The user is no longer signed up for this shift.</div>");
		redirect("admin/events/details/{$event_id->event_id}");
	}

	function remove_slot()
	{
		$event_id = $this->uri->segment(4);
		$status = $this->Shifts_model->remove_slot($event_id);
		if($status != TRUE)
		{
			$this->session->set_flashdata('notice', "<div class='error'>The slot could not be removed because there either are none left to remove, or there are no open slots. If you need to remove a slot that a volunteer currently is signed up for, delete that slot from within the volunteer list.</div>");
		}
		else
		{
			$this->session->set_flashdata('notice', "<div class='success'>One open slot has been removed from this event.</div>");
		}
		redirect("admin/events/details/{$event_id}");
	}
	
	function delete()
	{
		$shift_id = $this->uri->segment(4);
		$status = $this->Shifts_model->delete($shift_id);
		if($status != TRUE)
		{
			$this->session->set_flashdata('notice', "<div class='error'>The slot could not be removed.</div>");
		}
		else
		{
			$this->session->set_flashdata('notice', "<div class='success'>The slot has been deleted successfully. If there was a user signed up for it, their confirmation has been removed.</div>");
		}
		redirect("admin/events/listing");
	}
	
	function waitlist_to_shift()
	{
		$waitlist_slot_id = $this->uri->segment(4);
		$result = $this->Shifts_model->waitlist_to_shift($waitlist_slot_id);
		if($result['success'] != TRUE)
		{
			$this->session->set_flashdata('notice', "<div class='error'>An error occured - the waitlist was not modified. Do open confirmed slots exist?</div>");
		}
		else
		{
			$this->session->set_flashdata('notice', "<div class='success'>This user has successfully been moved to a confirmed shift.</div>");
		}
		redirect("admin/events/details/{$result['item']}");
	}

	function shift_to_waitlist()
	{
		$shift_id = $this->uri->segment(4);

		$this->load->model('Waitlists_model');
		$status = $this->Waitlists_model->shift_to_waitlist($shift_id);
		if($status['success'] != TRUE)
		{
			$this->session->set_flashdata('notice', "<div class='error'>An error occured - the shift was not modified.</div>");
		}
		else
		{
			$this->session->set_flashdata('notice', "<div class='success'>The user was moved to the waitlist successfully.</div>");
		}
		redirect("admin/events/details/{$status['item']}");
	}
	
	function remove_from_waitlist()
	{
		$waitlist_slot_id = $this->uri->segment(4);
		
		$this->load->model('Waitlists_model');
		$status = $this->Waitlists_model->delete($waitlist_slot_id);
		if($status != TRUE)
		{
			$this->session->set_flashdata('notice', "<div class='error'>An error occured - the waitlist was not modified.</div>");
		}
		else
		{
			$this->session->set_flashdata('notice', "<div class='success'>The user was removed from the waitlist successfully.</div>");
		}
		redirect("admin/events/listing");
	}
	
	

	
}
