<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comments_model extends CI_Model {
	function __construct() {
		parent::__construct();
	}

	function fetch_comments($ds_id) {
		$query = $this->db->select('*')
				 		  ->from('comments')
				 		  ->join('discussions', 'comments.ds_id=discussions.ds_id')
				 		  ->join('users', 'comments.usr_id=users.usr_id')
				 		  ->where('discussions.ds_id', $ds_id)
				 		  ->where('comments.cm_is_active', '1')
				 		  ->order_by('comments.cm_created_at', 'desc');

		$result = $this->db->get($query);

		if ($result) {
			return $result;
		} else {
			return false;
		}

	} // end fetch comments

	function new_comment($data) {
		// Look and see if the email address already exists in the users
		// table, if it does, return the primary key, if not create them
		// a user account and return the primary key

		$usr_email = $data['usr_email'];
		$query = $this->db->select('*')
						  ->from('users')
						  ->where('usr_email', $usr_email);

		$result = $this->db->get($query);

		if ($result->num_rows() > 0) {
			// if we arrive here in the code, then the email address is obv already
			// in the database, so we grap the users' primary key and store it in $data['usr_id']
			foreach ($result->result() as $rows) {
				$data['usr_id'] = $rows->usr_id;
			}
		} else {
			// create the user and return the priamry key
			$password = random_string('alnum', 16);
			$hash = $this->encrypt->sha1($password);

			$user_data = array('usr_email' => $data['usr_email'],
							   'usr_name' => $data['usr_name'],
							   'usr_is_acctive' => '1',
							   'usr_level' => '1',
							   'usr_hash' => $hash);

			if ($this->db->insert('users'. $user_data)) {
				$data['usr_id'] = $this->db->insert_id();
			}

		}
		$comment_data = array('cm_body' => $data['cm_body'],
							  'ds_id' => $data['ds_id'],
							  'cm_is_active' => '1',
							  'usr_id' => $data['usr_id']);

		if ($this->db->insert('comments', $comment_data)) {
			return $this->db->insert_id();
		} else {
			return false;
		}
	} // end new_comment method

	function flag($cm_id) {
		$this->db->where('cm_id', $cm_id);

		if ($this->db->update('comments', array('cm_is_active' => '0'))) {
			return true;
		} else {
			return false;
		}
	} // end flag method
}