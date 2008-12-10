<?php
/* SVN FILE: $Id$ */
/* Wiki Test cases generated on: 2008-10-10 16:10:08 : 1223680508*/
App::import('Model', 'Wiki');

class WikiTestCase extends CakeTestCase {
	var $Wiki = null;
	var $fixtures = array(
		'app.project', 'app.permission', 'app.user', 'app.wiki',
		'app.timeline', 'app.comment', 'app.ticket', 'app.version',
		'app.tag', 'app.tags_tickets', 'app.commit'
	);

	function start() {
		parent::start();
		$this->Wiki = ClassRegistry::init('Wiki');
	}

	function testWikiInstance() {
		$this->assertTrue(is_a($this->Wiki, 'Wiki'));
	}

	function testSave() {
		$this->Wiki->create(array(
			'slug' => 'home',
			'path' => '',
		));
		$this->assertTrue($this->Wiki->save());

		$this->Wiki->create(array(
			'slug' => 'setup',
			'path' => 'guides',
		));
		$this->assertTrue($this->Wiki->save());

		$this->Wiki->create(array(
			'slug' => 'keygen',
			'path' => 'guides/ssh',
		));
		$this->assertTrue($this->Wiki->save());

	}


	function testPath() {
		$this->Wiki->create(array(
			'slug' => 'home',
			'path' => '/',
		));
		$this->assertTrue($this->Wiki->save());

		$this->Wiki->create(array(
			'slug' => 'setup',
			'path' => '/guides',
		));
		$this->assertTrue($this->Wiki->save());

		$this->Wiki->create(array(
			'slug' => 'keygen',
			'path' => '/guides/ssh',
		));
		$this->assertTrue($this->Wiki->save());


		$results = $this->Wiki->find('all', array('conditions' => array('Wiki.path' => '/')));
		$this->assertEqual(count($results), 3);
		$this->assertEqual($results[0]['Wiki']['path'], '/');
		$this->assertEqual($results[1]['Wiki']['path'], '/guides');
		$this->assertEqual($results[2]['Wiki']['path'], '/guides/ssh');


		$results = $this->Wiki->find('all', array('conditions' => array('Wiki.path' => '/guides')));
		$this->assertEqual(count($results), 2);
		$this->assertEqual($results[0]['Wiki']['path'], '/guides');
		$this->assertEqual($results[1]['Wiki']['path'], '/guides/ssh');

		$results = $this->Wiki->find('all', array('conditions' => array('Wiki.path' => '/guides/ssh')));
		$this->assertEqual(count($results), 1);
		$this->assertEqual($results[0]['Wiki']['path'], '/guides/ssh');
	}
}
?>