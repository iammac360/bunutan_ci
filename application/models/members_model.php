<?php
class Members_model extends CI_Model {
	
	public function __construct()
	{
		$this->load->database();
	}

	public function set_all_members($data)
	{
		$this->db->insert_batch('members', $data);
	}

	public function update_member($fb_id, $data = array())
	{
		// $data = array(
		// 		'pick_id'	=> $this->input->post('pick_id'),
		// 		'pick_name'	=> $this->input->post('pick_name')
		// 	);
		$this->db->where('fb_id', $fb_id);
		$this->db->update('members', $data);
	}

	public function get_all_members()
	{
		$query = $this->db->get('members');
		return $query->result();
	}

	public function get_member($fb_id)
	{
		$query = $this->db->get_where('members', array('fb_id' => $fb_id));
		return $query->result();
	}
}