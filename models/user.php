<?php
/**
 * Short description
 *
 * Long description
 *
 * Copyright 2008, Garrett J. Woodworth <gwoo@cakephp.org>
 * Redistributions not permitted
 *
 * @copyright		Copyright 2008, Garrett J. Woodworth
 * @package			chaw
 * @subpackage		chaw.models
 * @since			Chaw 0.1
 * @license			commercial
 *
 */
class User extends AppModel {
/**
 * undocumented class variable
 *
 * @var string
 **/
	var $name = 'User';
/**
 * undocumented class variable
 *
 * @var string
 **/
	var $displayField = 'username';
/**
 * undocumented class variable
 *
 * @var string
 **/
	var $validate = array(
		'username' => array(
			'allowedChars' => array(
				'rule' => '/^[\-_\.a-zA-Z0-9]{3,}$/',
				'required' => true,
				'message' => 'Required: Minimum three (3) characters, letters (no accents), numbers and .-_ permitted.'
			),
			'unique' => array(
				'rule' => 'isUnique',
				'required' => true,
				'message' => 'Required: Username must be unique'
			)
		),
		'email' => array(
			'valid' => array(
				'rule' => 'email',
				'required' => true,
				'message' => 'Required: Valid email address'
			),
			'unique' => array(
				'rule' => 'isUnique',
				'required' => true,
				'message' => 'Required: Email must be unique'
			)
		),
		'password' => array(
			'rule' => 'alphaNumeric',
			'message' => 'Required: Alpha-numeric passwords only'
		)
	);
/**
 * undocumented class variable
 *
 * @var string
 **/
	var $hasOne = array('Permission');
/**
 * undocumented class variable
 *
 * @var string
 **/
	var $SshKey = null;
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->SshKey = ClassRegistry::init('SshKey');
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function beforeSave() {

		if (!empty($this->data['SshKey']['content'])) {
			$this->SshKey->set(array(
				'username' => $this->data['User']['username'],
			));

			return $this->SshKey->save($this->data['SshKey']);
		}

		if (!empty($this->data['Key']) && !empty($this->data['User']['username'])) {
			$delete = array();
			foreach ($this->data['Key'] as $type => $keys) {
				foreach ($keys as $key) {
					if (!empty($key['chosen'])) {
						$delete[$type][] = $key['content'];
					}
				}
			}

			foreach ($delete as $type => $keys) {
				$result = $this->SshKey->delete(array(
					'type' => $type, 'username' => $this->data['User']['username'],
					'content' => $keys
				));
			}
		}

		return true;
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function projects($user, $conditions = array()) {
		if ($user = $this->Permission->user($user)) {
			$projects = $this->Permission->find('all', array(
				'conditions' => array_merge($conditions, array('Permission.user_id' => $user, 'Project.name !=' => null))
			));
			$ids = array();
			if (!empty($projects)) {
				$ids = array_filter(Set::extract($projects, '/Project/id'));
			}
			return compact('ids', 'projects');
		}
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function groups($user) {
		if ($user = $this->Permission->user($user)) {
			$results = $this->Permission->find('all', array(
				'conditions' => array('Permission.user_id' => $user, 'Project.id !=' => null)
			));
			return array_filter(Set::combine($results, '/Project/id', '/Permission/group'));
		}
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function permit() {
		if (!empty($this->data['User']['group'])) {
			$data = array('Permission' => array(
				'user_id' => $this->id,
				'project_id' => $this->data['User']['project_id'],
				'group' => $this->data['User']['group']
			));
			$this->Permission->save($data);
		}
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function forgotten($data = array()) {
		$this->set($data);
		$this->recursive = -1;

		if (!empty($this->data['User']['username']) && empty($this->data['User']['email'])) {
			$this->data = $this->find(array('username' => $this->data['User']['username']));
		} else {
			$this->data = $this->find(array('email' => $this->data['User']['email']));
		}

		if (empty($this->data['User']['email'])) {
			$this->invalidate('email', 'Email could not be found');
			return false;
		}
		$this->data['User']['token'] = String::uuid();
		$this->data['User']['token_expires'] = date('Y-m-d', strtotime('+ 1 day'));

		$this->id = $this->data['User']['id'];
		if ($data = $this->save($this->data)) {
			return $data;
		}
		$this->invalidate('email', 'Email could not be sent');
		return false;
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function verify($data = array()) {
		$this->set($data);

		if (empty($this->data['User']['token'])) {
			$this->invalidate('token', 'token could not be found');
			return false;
		}
		$this->recursive = -1;
		$this->data = $this->find(array('token' => $this->data['User']['token']));

		if (empty($this->data['User']['email'])) {
			$this->invalidate('token', 'token does not match');
			return false;
		}

		$password = $this->__generatePassword();
		$this->data['User']['tmp_pass'] = Security::hash($password, null, true);
		$this->data['User']['token'] = null;
		$this->data['User']['token_expires'] = null;

		$this->id = $this->data['User']['id'];
		if ($data = $this->save($this->data)) {
			$data['User']['tmp_pass'] = $password;
			return $data;
		}

		$this->invalidate('password', 'Password could not be reset');
		return false;
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function __generatePassword($length = 10) {
		srand((double)microtime() * 1000000);
		$password = '';
		$vowels = array('a', 'e', 'i', 'o', 'u');
		$cons = array('b', 'c', 'd', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'u', 'v', 'w', 'tr', 'cr', 'br', 'fr', 'th', 'dr', 'ch', 'ph', 'wr', 'st', 'sp', 'sw', 'pr', 'sl', 'cl');
		for ($i = 0; $i < $length; $i++) {
			$password .= $cons[mt_rand(0, 31)] . $vowels[mt_rand(0, 4)];
		}
		return substr($password, 0, $length);
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function paginateCount($conditions, $recursive, $extra) {
		$fields = array('fields' => 'DISTINCT User.username');
		return count($this->find('all', compact('conditions', 'fields', 'recursive', 'extra')));
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function isUnique($data, $options = array()) {
		if (!empty($data['username'])) {
			$this->recursive = -1;
			if ($result = $this->findByUsername($data['username'])) {
				if ($this->id === $result['User']['id']) {
					return true;
				}
				return false;
			}
			return true;
		}
		if (!empty($data['email'])) {
			$this->recursive = -1;
			if ($result = $this->findByEmail($data['email'])) {
				if ($this->id === $result['User']['id']) {
					return true;
				}
				return false;
			}
			return true;
		}
	}
}
?>