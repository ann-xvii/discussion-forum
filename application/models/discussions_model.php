<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Discussions_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}

	function fetch_discussions($filter = null, $direction = null) {

		// $this->db->get('discussions');
		// $this->db->get('users');
		// $this->db->join('users', 'users.usr_id=discussions.usr_id');
		// $this->db->where('discussions.ds_is_active !=', 0);
		// sql queries kept failing until i used backticks, then failed again without quotes around 0
		$query = "SELECT * FROM `discussions`, `users` WHERE `discussions`.`usr_id` = `users`.`usr_id` AND `discussions`.`ds_is_active` != '0' ";
		#$query = "SELECT * FROM 'discussions', 'users' WHERE 'discussions'.'usr_id' = 'users'.'usr_id' AND 'discussions'.'ds_is_active' != '0' ";

		if ($filter != null) {
			if ($filter == 'age') {
				$filter = 'ds_created_at';
				switch ($direction) {
					case 'ASC':
						$dir = 'ASC';
						break;
					case 'DESC':
						$dir = 'DESC';
						break;
					default:
						$dir = 'ASC';
				}
			}
		} else {
			$dir = 'ASC';
		}

		
		// $query = $this->db->order_by('discussions.ds_created_at', $dir);
		// $result = $this->db->get($query);
		$query .= "ORDER BY 'ds_created_at' " . $dir;
		$result = $this->db->query($query, array($dir));

		if ($result) {
			return $result;
		} else {
			return false;
		}
	} // end fetch_discussions


	function fetch_discussion($ds_id) {
		$query = $this->db->select('*')
						  ->from('discussions')
						  ->join('users', 'users.usr_id = discussions.usr_id');
		$result = $this->db->get($query, array($ds_id));
	} // end fetch discussion


	function create($data) {
		// Look and see if the email address already exists in the suers table
		// if it does return the primary key, if not, create them a user account
		// and return the primary key

		$usr_email = $data['usr_email'];
		$this->db->select('*')
				 ->from('users')
				 ->where('usr_email', $usr_email);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $rows) {
				$data['usr_id'] = $rows->usr_id;
			}
		} else {
			// if email address doesn't exist in the users table
			$password = random_string('alnum', 16);
			$hash = $this->encrypt->sha1($password);

			$user_data = array('usr_email' => $data['usr_email'],
							   'usr_name' => $data['usr_name'],
							   'usr_is_active' => '1',
							   'usr_level' => '1',
							   'usr_hash' => $hash);

			// user data array is inserted into the database

			if ($this->db->insert('users', $user_data)) {
				$data['usr_id'] = $this->db->insert_id();
				// Send email with password ???
			}	
		}
		$discussion_data = array('ds_title' => $data['ds_title'],
								 'ds_body' => $data['ds_body'],
								 'usr_id' => $data['usr_id'],
								 'ds_is_active' => '1');

		if ($this->db->insert('discussions', $discussion_data)) {
			return $this->db->insert_id();
		} else {
			return false;
		}
	} // end create function


	function flag($ds_id) {
		$this->db->where('ds_id', $ds_id);
		if ($this->db->update('discussions', array('ds_is_active' => '0'))) {
			return true;
		} else {
			return false;
		}
	} // end flag function


}