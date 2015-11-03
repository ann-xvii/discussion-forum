<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_model extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	// fetch all comments for moderation from the database. Comments are for moderation if comments.cm_is_active is set to 0
	function dashboard_fetch_comments() {
		$query = $this->db->select('*')
						  ->from('comments')
						  ->join('users', 'comments.usr_id=users.usr_id')
						  ->where('ds_is_active', '0');

		$result = $this->db->query($query);

		if ($result) {
			return $result;
		} else {
			return false;
		}
	} // end dashboard_fetch_comments method

	function does_user_exist($email) {
		$query = $this->db->where('usr_email', $email);

		$result = $this->db->get($query);
		return $result;
	} // end does user exist method

	function update_comments($is_active, $id) {

		if ($is_active == 1) {
			$query = "UPDATE 'comments' SET 'cm_is_active' = ? WHERE 'cm_id' = ? ";

			if ($this->db->get($query, array($is_active, $id))) {
				return true;
			} else {
				return false;
			}
			
			// $this->db->set('cm_is_active', $is_active);
			// $this->db->insert('comments');
			// $this->db->where('cm_id', $id);
			// $this->db->update('comments',  )
		} else {
			$query = "DELETE FROM 'comments' WHERE 'cm_id' = ? ";
			if ($this->db->get($query, array($id))) {
				return true;
			} else {
				return false;
			}
		}
	} // end update comments method 

	function update_discussions($is_active, $id) {
		if ($is_active == 1) {
			$query = "UPDATE 'discussions' SET 'ds_is_active' = ? WHERE 'ds_id' = ? ";
			if ($this->db->get($query, array($is_active, $id))) {
				return true;
			} else {
				return false;
			}
		} else {
			$query = "DELETE FROM 'discussions' WHERE 'ds_id' = ?";
			if ($this->db->get($query, array($id))) {
				$query = "DELETE FROM 'comments' WHERE 'ds_id' = ?";
				if ($this->db->get($query, array($id))) {
					return true;
				} else {
					return false;
				}
			}
		}
	} // end update_discussions method

	
}