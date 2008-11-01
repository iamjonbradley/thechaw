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
 * @subpackage		chaw.controllers.components
 * @since			Chaw 0.1
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
class AccessComponent extends Object {

	var $isAllowed = false;
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function initialize(&$controller) {
		$controller->params['isAdmin'] = false;

		if ($controller->name == 'CakeError') {
			return false;
		}

		if (empty($controller->Project)) {
			if ($controller->params['url']['url'] != 'pages/home') {
				$controller->Session->write('Install', true);
				$controller->redirect(array('admin' => false, 'project' => null, 'controller' => 'pages', 'action'=> 'display', 'home'));
			} else {
				$controller->Auth->allow($controller->action);
			}
			return;
		}

		if ($controller->Project->initialize($controller->params) === false) {

			if ($controller->params['url']['url'] == 'users/logout') {
				$controller->Auth->allow($controller->action);
				return;
			}

			if ($controller->params['url']['url'] != 'pages/home' && !$controller->Session->read('Install')) {
				$controller->Session->write('Install', true);
				$controller->redirect(array('admin' => false, 'project' => null, 'controller' => 'pages', 'action'=> 'display', 'home'));
			}

			if (in_array($controller->params['url']['url'], array('pages/home', 'users/add', 'users/login'))) {
				$controller->Auth->allow($controller->action);
				return;
			}

			if ($controller->Auth->user()) {
				if (in_array($controller->params['url']['url'], array('install', 'admin/projects/add'))) {
					$controller->params['isAdmin'] = true;
					$controller->Auth->allow($controller->action);
				} else {
					$controller->Session->setFlash('Chaw needs to be installed');
					$controller->redirect(array('admin' => false, 'project' => null, 'controller' => 'pages', 'action'=> 'display', 'home'));
				}
			} else {
				if (in_array($controller->params['url']['url'], array('install', 'admin/projects/add'))) {
					$login =  Router::url(array('admin' => false, 'project' => null, 'controller' => 'users', 'action'=> 'login'));
					$controller->Session->setFlash("Chaw needs to be installed. Please <a href='{$login}'>Login</a> or Register");
					$controller->redirect(array('admin' => false, 'project' => null, 'controller' => 'users', 'action'=> 'add'));
				}
				$controller->Session->setFlash('Chaw needs to be installed');
				$controller->redirect(array('admin' => false, 'project' => null, 'controller' => 'pages', 'action'=> 'display', 'home'));
			}
		} else {
			$default = true;
			if (!empty($controller->Project->config['private'])) {
				$default = false;
			}
			$this->isAllowed = (
				in_array($controller->params['url']['url'], array('users/add', 'projects')) ||
				in_array($controller->action, $controller->Auth->allowedActions) ||
				(
					$controller->Project->Permission->check($controller->params['controller'], array('access' => 'r', 'default' => $default))
				)
			);

			if ($this->isAllowed) {
				$controller->Auth->allow($controller->action);
				return true;
			}
		}
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function startup(&$controller) {
		if (empty($controller->Project->Repo)) {
			return false;
		}

		$isOwner = ($controller->Auth->user('id') == $controller->Project->config['user_id']);
		if ($isOwner) {
			$controller->Auth->allow($controller->action);
			$controller->params['isAdmin'] = $this->isAllowed = true;
			return true;
		}

		$crud = $access = 'r';
		if (!empty($controller->Auth->actionMap[$controller->params['action']])) {
			$crud = $controller->Auth->actionMap[$controller->params['action']][0];
		}
		if (in_array($crud, array('c', 'u', 'd'))) {
			$access = 'w';
		}

		if ($this->isAllowed && $access == 'r') {
			return true;
		}

		$options = array(
			'user' => $controller->Auth->user('username'),
			'access' => array($access, $crud),
			'default' => false
		);

		$allowed = $controller->Project->Permission->check('admin', $options);

		if ($allowed === false && !empty($controller->Project->config['private'])) {
			if ($controller->params['url']['url'] !== '/') {
				$controller->Session->setFlash('Select a Project');
			}
			if ($controller->params['url']['url'] != 'projects') {
				$controller->redirect(array('admin' => false, 'controller' => 'projects'));
			}
		} else if ($allowed === true) {
			$controller->params['isAdmin'] = true;
			return true;
		}

		$options = array(
			'user' => $controller->Auth->user('username'),
			'access' => array($access, $crud),
			'default' => true
		);

		$allowed = $controller->Project->Permission->check($controller->params['controller'], $options);

		if ($allowed === true) {
			return true;
		}

		$controller->Session->setFlash('You are not authorized to access that location');
		$controller->redirect($controller->referer());
		return false;
	}
}