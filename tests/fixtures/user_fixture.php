<?php 
/* SVN FILE: $Id$ */
/* User Fixture generated on: 2008-10-16 10:10:23 : 1224177023*/

class UserFixture extends CakeTestFixture {
	var $name = 'User';
	var $fields = array(
			'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
			'username' => array('type'=>'string', 'null' => false, 'length' => 40),
			'password' => array('type'=>'string', 'null' => false, 'length' => 40),
			'group_id' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 100),
			'email' => array('type'=>'string', 'null' => false, 'length' => 200),
			'last_login' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'created' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'modified' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
			);
}
?>