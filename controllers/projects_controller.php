<?php
/**
 * Short description
 *
 * Long description
 *
 *
 * Copyright 2008, Garrett J. Woodworth <gwoo@cakephp.org>
 * Licensed under The MIT License
 * Redistributions of files must retain the copyright notice.
 *
 * @copyright		Copyright 2008, Garrett J. Woodworth
 * @package			chaw
 * @subpackage		chaw.controllers
 * @since			Chaw 0.1
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
class ProjectsController extends AppController {

	var $name = 'Projects';

	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('index');
	}

	function index() {
		if ($this->RequestHandler->ext == 'tar') {
			$this->set(array(
				'project' => basename($this->Project->Repo->working),
				'working' => $this->Project->Repo->working
			));

			$this->render('package');
		}

		$this->Project->recursive = 0;

		if ($this->params['isAdmin'] === false) {
			$this->paginate = array(
				'conditions' => array(
					'Project.private' => 0, 'Project.active' => 1, 'Project.approved' => 1
				),
				'order' => 'Project.users_count DESC, Project.created ASC'
			);
		}

		$this->paginate['conditions']['Project.fork'] = null;

		if(!empty($this->passedArgs['type'])) {
			if ($this->passedArgs['type'] == 'fork') {
				$this->paginate['conditions']['Project.fork !='] = null;
			}
			unset($this->paginate['conditions']['Project.fork']);
		}
		
		$this->set('projects', $this->paginate());
		
		$this->set('rssFeed', array('controller' => 'projects'));
	}

	function view($url  = null) {
		$project = $this->Project->config;
		if (empty($this->params['project']) && $url == null && $project['id'] != 1) {
			$project = $this->Project->findByUrl($url);
		}

		$this->set('project', array('Project' => $project));
	}

	function fork() {
		if ($this->Project->Repo->type == 'svn') {
			$this->Session->setFlash('You cannot fork an svn project yet');
			$this->redirect($this->referer());
		}

		if (!empty($this->data)) {
			$this->Project->create(array_merge(
				$this->Project->config,
				array(
					'user_id' => $this->Auth->user('id'),
					'fork' => $this->Auth->user('username'),
					'approved' => 1,
				)
			));
			if ($data = $this->Project->fork()) {
				if (empty($data['Project']['approved'])) {
					$this->Session->setFlash('Project is awaiting approval');
				} else {
					$this->Session->setFlash('Project was created');
				}
				$this->redirect(array(
					'fork' => $data['Project']['fork'],
					'controller' => 'browser', 'action' => 'index',
				));
			} else {
				$this->Session->setFlash('Project was NOT created');
			}
		}
	}

	function add() {

		$this->pageTitle = 'Project Setup';

		if (!empty($this->data)) {
			$this->Project->create(array(
				'user_id' => $this->Auth->user('id'),
				'username' => $this->Auth->user('username'),
				'approved' => $this->params['isAdmin']
			));
			if ($data = $this->Project->save($this->data)) {
				if (empty($data['Project']['approved'])) {
					$this->Session->setFlash('Project is awaiting approval');
				} else {
					$this->Session->setFlash('Project was created');
				}
				$this->redirect(array('project' => $data['Project']['url'], 'controller' => 'timeline', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Project was NOT created');
			}
		}

		$this->data = array_merge((array)$this->data, array('Project' => $this->Project->config));
		if (!empty($this->data['Project']['id'])) {
			unset($this->data['Project']['id'], $this->data['Project']['name'], $this->data['Project']['description']);
		}

		$this->set('repoTypes', $this->Project->repoTypes());

		$this->set('messages', $this->Project->messages);

		$this->render('add');
	}

	function edit() {

		$this->pageTitle = 'Update Project';

		if (!empty($this->data)) {
			if ($data = $this->Project->save($this->data)) {
				$this->Session->setFlash('Project was updated');
			} else {
				$this->Session->setFlash('Project was NOT updated');
			}
		}

		$this->data = $this->Project->read();

		$this->set('repoTypes', $this->Project->repoTypes());

		$this->set('messages', $this->Project->messages);

		$this->render('edit');
	}

	function admin_index() {
		if ($this->Project->id !== '1' || $this->params['isAdmin'] === false) {
			$this->redirect($this->referer());
		}
		$this->Project->recursive = 0;
		$this->set('projects', $this->paginate());
	}

	function admin_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('The project was invalid');
			$this->redirect(array('action' => 'index'));
		}

		$this->pageTitle = 'Project Admin';

		if ($this->Project->id !== '1' || $this->params['isAdmin'] === false) {
			$this->redirect($this->referer());
		}

		$this->Project->id = $id;

		if (!empty($this->data)) {
			if ($data = $this->Project->save($this->data)) {
				$this->Session->setFlash('Project was updated');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Project was NOT updated');
			}
		}

		$this->data = $this->Project->read();

		$this->set('repoTypes', $this->Project->repoTypes());

		$this->set('messages', $this->Project->messages);

		$this->render('edit');
	}

	function admin_approve($id = null) {
		if ($id) {
			$this->Project->id = $id;
			if ($this->Project->save(array('approved' => 1))) {
				$this->Session->setFlash('The project was approved');
			} else {
				$this->Session->setFlash('The project was NOT approved');
			}
		} else {
			$this->Session->setFlash('The project was invalid');
		}
		$this->redirect(array('action' => 'index'));
	}

	function admin_reject($id = null) {
		if ($id) {
			$this->Project->id = $id;
			if ($this->Project->save(array('approved' => 0))) {
				$this->Session->setFlash('The project was rejected');
			} else {
				$this->Session->setFlash('The project was NOT rejected');
			}
		} else {
			$this->Session->setFlash('The project was invalid');
		}
		$this->redirect(array('action' => 'index'));
	}

	function admin_activate($id = null) {
		if ($id) {
			$this->Project->id = $id;
			if ($this->Project->save(array('active' => 1))) {
				$this->Session->setFlash('The project was activated');
			} else {
				$this->Session->setFlash('The project was NOT activated');
			}
		} else {
			$this->Session->setFlash('The project was invalid');
		}
		$this->redirect(array('action' => 'index'));
	}

	function admin_deactivate($id = null) {
		if ($id) {
			$this->Project->id = $id;
			if ($this->Project->save(array('active' => 0))) {
				$this->Session->setFlash('The project was deactivated');
			} else {
				$this->Session->setFlash('The project was NOT deactivated');
			}
		} else {
			$this->Session->setFlash('The project was invalid');
		}
		$this->redirect(array('action' => 'index'));
	}
}
?>