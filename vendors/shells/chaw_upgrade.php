<?php
App::import('Model', 'CakeSchema');
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
 * @subpackage		chaw.vendors.shells
 * @since			Chaw 0.1
 * @license			commercial
 *
 */
class ChawUpgradeShell extends Shell {

	var $uses = array('Project');

	function _welcome() {
		$this->out("Chaw Upgrade");
	}

	function main() {
		$choice = '';
		$Tasks = new Folder(dirname(__FILE__) . DS . 'tasks');
		list($folders, $files) = $Tasks->read();
		$i = 1;
		$choices = array();
		foreach ($files as $file) {
			if (preg_match('/upgrade_/', $file)) {
				$choices[$i] = basename($file, '.php');
				$this->out($i++ . '. ' . basename($file, '.php'));
			}
		}

		while ($choice == '') {
			$choice = $this->in("Enter a number from the list above, or 'q' to exit", null, 'q');

			if ($choice === 'q') {
				$this->out("Exit");
				$this->_stop();
			}

			if ($choice == '' || intval($choice) > count($choices)) {
				$this->err("The number you selected was not an option. Please try again.");
				$choice = '';
			}
		}

		if (intval($choice) > 0 && intval($choice) <= count($choices)) {
			$upgrade = Inflector::classify($choices[intval($choice)]);
		}

		$this->tasks = array($upgrade);
		$this->loadTasks();

		return $this->{$upgrade}->execute();
	}
	
	
	function execute() {
		$parentMethods = get_class_methods('ChawUpgradeShell');
		$methods = array_diff(
			get_class_methods($this),
			get_class_methods('ChawUpgradeShell')
		);

		foreach ($methods as $method) {
			if ($method != 'execute' && $method[0] != '_') {
				$this->{$method}();
			}
		}
	}


	function _updateSchema($table, $options = array()) {
		$defaults = array(
			'connection' => 'default', 
			'backup' => false,
			'new' => false,
		);
		extract(array_merge($defaults, $options));
		
		$Schema = new CakeSchema(array(
			'name' => 'Chaw',
		));
		$Schema = $Schema->load();

		$db = ConnectionManager::getDataSource($connection);
		$sql = array();
		if ($backup == true) {
			$oldTable = 'old_' . $table;
			$Schema->tables['old_' . $table] = $db->describe($table);
			$sql = array(
				$db->dropSchema($Schema, $oldTable),
				$db->createSchema($Schema, $oldTable)
			);
		}
		
		if ($new == true) {
			$newTable = 'new_' . $table;
			$Schema->tables['new_' . $table] = $Schema->tables[$table];
			$sql = array(
				$db->dropSchema($Schema, $newTable),
				$db->createSchema($Schema, $newTable)
			);
		}
		
		if ($new == false) {
			$db->cacheSources = false;

			$sql = array_merge($sql, array(
				$db->dropSchema($Schema, $table),
				$db->createSchema($Schema, $table)
			));
		}

		$result = false;
		foreach ($sql as $message => $query) {
			if (!empty($this->params['dry'])) {
				$this->out($query);
				continue;
			}
			if ($db->execute($query)) {
				$this->out($query);
				$result = true;
			} else {
				$result = false;
			}
		}

		return $result;
	}

}
