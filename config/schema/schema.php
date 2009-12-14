<?php
/**
 * Chaw : source code and project management
 *
 * @copyright  Copyright 2009, Garrett J. Woodworth (gwoohoo@gmail.com)
 * @license    GNU AFFERO GENERAL PUBLIC LICENSE v3 (http://opensource.org/licenses/agpl-v3.html)
 *
 */

class ChawSchema extends CakeSchema {
	var $name = 'Chaw';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $comments = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'model' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 40),
		'foreign_key' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'reason' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100),
		'changes' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'body' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $commits = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'project_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'branch' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 200),
		'revision' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'author' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 200),
		'committer' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 200),
		'commit_date' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'message' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'changes' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $permissions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'project_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'group' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'project_id' => array('column' => 'project_id', 'unique' => 0), 'user_id' => array('column' => 'user_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $projects = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'url' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 200),
		'approved' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'private' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'active' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'fork' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 200),
		'project_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 200),
		'repo_type' => array('type' => 'string', 'null' => false, 'default' => 'git', 'length' => 10),
		'config' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'users_count' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'ohloh_project' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 200),
		'description' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'date', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'date', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $tags = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'key' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'key' => 'index'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'key' => array('column' => 'key', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $tags_tickets = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'tag_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'ticket_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $tickets = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'number' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'project_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'version_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'reporter' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'owner' => array('type' => 'integer', 'null' => false, 'default' => '0'),
		'type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'status' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'resolution' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'priority' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 200),
		'description' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $timeline = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'project_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'model' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'event' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50),
		'data' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 200),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'active' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'key' => 'index'),
		'username' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 40, 'key' => 'index'),
		'password' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 40),
		'email' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 200, 'key' => 'index'),
		'ohloh_account' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 200),
		'last_login' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'tmp_pass' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'token' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40),
		'token_expires' => array('type' => 'date', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'active' => array('column' => 'active', 'unique' => 0), 'username' => array('column' => 'username', 'unique' => 0), 'email' => array('column' => 'email', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $versions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'project_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'slug' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 200),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'description' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'due_date' => array('type' => 'date', 'null' => true, 'default' => NULL),
		'completed' => array('type' => 'boolean', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
	var $wiki = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'project_id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'index'),
		'path' => array('type' => 'string', 'null' => false, 'default' => '/', 'length' => 200, 'key' => 'index'),
		'slug' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 200, 'key' => 'index'),
		'active' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'read_only' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'last_changed_by' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'content' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'path' => array('column' => 'path', 'unique' => 0), 'slug' => array('column' => 'slug', 'unique' => 0), 'project_id' => array('column' => 'project_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);
}
?>