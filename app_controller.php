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
 * @subpackage		chaw
 * @since			Chaw 0.1
 * @license			commercial
 *
 */
class AppController extends Controller {

	var $components = array('Access', 'Auth', 'RequestHandler');

	var $helpers = array(
		'Html', 'Form', 'Javascript', 'Chaw'
	);

	var $uses = array('Project');
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function beforeFilter() {
		$this->Auth->loginAction = '/users/login';
		$this->Auth->mapActions(array(
			'modify' => 'update',
			'remove' => 'delete'
		));
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function beforeRender() {
		if ($this->params['isAdmin'] !== true) {
			//$this->params['admin'] = false;
		}
		if (!empty($this->params['admin'])) {
			$this->layout = 'admin';
		}

		$this->params['project'] = null;
		if (!empty($this->Project->config) && $this->Project->config['id'] !== '1') {
			$this->params['project'] = $this->Project->config['url'];
		}

		if (isset($this->viewVars['rssFeed'])) {
			$this->viewVars['rssFeed'] = array_merge(
				array(
				'controller' => 'timeline', 'action' => 'index', 'ext' => 'rss'
				),
				$this->viewVars['rssFeed']
			);
		}

		$this->set('CurrentUser', Set::map($this->Auth->user()));
		$this->set('CurrentProject', Set::map(Configure::read('Project'), true));
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function redirect($url = array(), $status = null, $exit = true) {
		if (is_array($url)) {
			if (!empty($this->params['project'])) {
				$url = array_merge(array('project' => $this->params['project']), $url);
			}
			if (!empty($this->params['fork'])) {
				$url = array_merge(array('fork' => $this->params['fork']), $url);
			}
		}
		parent::redirect($url, $status, $exit);
	}
}
?>