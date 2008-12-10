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
 * @subpackage		chaw.models
 * @since			Chaw 0.1
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
class Project extends AppModel {
/**
 * undocumented class variable
 *
 * @var string
 **/
	var $name = 'Project';
/**
 * undocumented class variable
 *
 * @var string
 **/
	var $config = array();
/**
 * undocumented class variable
 *
 * @var string
 **/
	var $Repo;
/**
 * undocumented class variable
 *
 * @var string
 **/
	var $hooks = array(
		'git' => array('pre-receive', 'post-receive'),
		'svn' => array('pre-commit', 'post-commit')
	);
/**
 * undocumented class variable
 *
 * @var string
 **/
	var $repoTypes = array('Git', 'Svn');
/**
 * undocumented class variable
 *
 * @var string
 **/
	var $messages = array('response' => null, 'debug' => null);
/**
 * undocumented class variable
 *
 * @var string
 **/
	var $validate = array(
		'name' => array(
			'required' => array(
				'rule' => 'notEmpty',
			),
			'unique' => array(
				'rule' => 'isUnique'
			)
		),
		'user_id' => array('notEmpty')
	);
/**
 * undocumented class variable
 *
 * @var string
 **/
	var $belongsTo = array('User');
/**
 * undocumented class variable
 *
 * @var string
 **/
	var $hasMany = array('Permission');
/**
 * undocumented class variable
 *
 * @var string
 **/
	var $__created = false;
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function initialize($params = array()) {
		$this->recursive = -1;
		$this->config = Configure::read('Project');

		$duration = '+99 days';
		if (Configure::read() > 1) {
			$duration = '+15 seconds';
		}

		Cache::set(array('prefix' => 'config_', 'duration' => $duration, 'path' => CACHE . 'persistent'));

		if (!empty($this->data['Project']['url'])) {
			$params['project'] = $this->data['Project']['url'];
		}
		if (!empty($this->data['Project']['fork'])) {
			$params['fork'] = $this->data['Project']['fork'];
		}

		if (!empty($params['project'])) {
			$conditions['url'] = $params['project'];
			$conditions['fork'] = null;
			if (!empty($params['fork'])) {
				$conditions['fork'] = $params['fork'];
			}
			$key = join('_', $conditions);
			$project = Cache::read($key);
			if (empty($project)) {
				$project = $this->find($conditions);
				if (!empty($project)) {
					Cache::write($key, $project);
				}
			}
		}
		if (empty($project)) {
			$key = Configure::read('App.dir');
			$project = Cache::read($key);
			if (empty($project)) {
				$project = $this->find('first');
				if (!empty($project)) {
					Cache::write($key, $project);
				}
			}
		}

		if (!empty($this->data['Project'])) {
			if (empty($project)) {
				$project = array('Project' => array());
			}
			$project['Project'] = array_merge($project['Project'], $this->data['Project']);
		}

		if (empty($project['Project'])) {
			Configure::write('Project', $this->config);
			return false;
		}

		$this->config = array_merge($this->config, $project['Project']);

		$repoType = strtolower($this->config['repo_type']);

		if (!empty($this->data['Project']['remote'])) {
			$this->config['remote'][$repoType] = $this->data['Project']['remote'];
		}
		if (is_string($this->config['remote'])) {
			$this->config['remote'][$repoType] = $this->config['remote'];
		}

		$path = Configure::read("Content.{$repoType}");

		$fork = null;
		if (!empty($this->config['fork'])) {
			$fork = 'forks' . DS . $this->config['fork'] . DS;
		}

		$this->config['repo'] = array(
			'class' => 'repo.' . $this->config['repo_type'],
			'type' => $repoType,
			'chmod' => 0777,
			'path' => $path . 'repo' . DS . $fork . $this->config['url'],
			'working' => $path . 'working' . DS . $fork . $this->config['url'],
			'remote' => is_string($this->config['remote']) ? $this->config['remote'] : $this->config['remote'][$repoType]
		);

		if ($repoType == 'git') {
			$this->config['repo']['path'] .= '.git';
		}

		$this->id = $this->config['id'];
		Configure::write('Project', $this->config);

		$this->Repo = ClassRegistry::init($this->config['repo']);
		return true;
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function beforeSave() {
		if (!empty($this->data['Project']['name']) && empty($this->data['Project']['url'])) {
			$this->data['Project']['url'] = Inflector::slug(strtolower($this->data['Project']['name']));
		}

		if ($this->id &&
			!empty($this->data['Project']['repo_type']) &&
			!empty($this->config['repo_type']) &&
			$this->data['Project']['repo_type'] == $this->config['repo_type']
		) {
			unset($this->data['Project']['repo_type']);
		}

		if (!empty($this->data['Project']['approved'])) {
			if ($this->initialize() === false) {
				return false;
			}

			if (!file_exists($this->config['repo']['path']) || !file_exists($this->config['repo']['working'])) {
				$this->__created = $this->Repo->create(array(
					'remote' => $this->config['repo']['remote'],
				));

				if ($this->__created !== true) {
					$this->invalidate('repo_type', 'the repo could not be created');
					return false;
				}
			}
		}

		if ($this->__created && (empty($this->data['Project']['username']) || empty($this->data['Project']['user_id']))) {
			$this->invalidate('user', 'Invalid user');
			return false;
		}

		return true;
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function afterSave($created) {
		if (!empty($this->data['Project']['approved'])) {

			$this->config['id'] = $this->id;

			$hooksCreated = $this->createHooks($this->hooks[$this->Repo->type], array(
				'project' => $this->data['Project']['url'],
				'fork' => (!empty($this->data['Project']['fork'])) ? $this->data['Project']['fork'] : false,
				'root' => Configure::read('Content.base')
			));

			if ($this->__created && $hooksCreated) {
				foreach ($this->hooks[$this->Repo->type] as $hook) {
					if ($this->__created) {
						if ($hook === 'post-commit') {
							$this->Repo->execute("env - {$this->Repo->path}/hooks/{$hook} {$this->Repo->path} 1");
						}

						if ($hook === 'post-receive') {
							$this->Repo->execute("env - {$this->Repo->path}/hooks/{$hook} refs/heads/master");
						}
					}
				}
			}

			$this->messages = array('response' => $this->Repo->response, 'debug' => $this->Repo->debug);

			$Wiki = ClassRegistry::init('Wiki');
			if (!$Wiki->field('slug', array('slug' => 'home', 'project_id' => $this->id))) {
				$Wiki->create(array(
					'slug' => 'home', 'active' => 1,
					'project_id' => $this->id,
					'last_changed_by' => $this->config['user_id'],
					'content' => "##The home page for " . $this->config['name']
						. "\n\n" . $this->config['description']
				));
				$Wiki->save();
			}

			$this->Permission->config($this->config);
			if ($this->Permission->exists() !== true) {
				$this->Permission->saveFile(array('Permission' => array(
					'username' => @$this->data['Project']['username']
				)));
			}

			$this->permit(array(
				'user' => $this->config['user_id'],
				'group' => 'admin',
				'count' => 1
			));
		}

		$this->__created = false;
		$this->createShell();
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function createHooks($hooks, $options = array()) {
		extract(array_merge(array('project' => null, 'fork' => null, 'root' => null), $options));
		$result = array();
		if (!empty($hooks)) {
			foreach ((array)$hooks as $hook) {
				if (!file_exists("{$this->Repo->path}/hooks/{$hook}")) {
					$result[] = $this->Repo->hook($hook, array('project' => $project, 'fork' => $fork, 'root' => $root));
				}
			}
		}
		return $result;
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function createShell($data = array()) {

		$template = CONFIGS . 'templates' . DS;
		$chaw = Configure::read('Content.base');

		if (file_exists($template . 'chaw') && !file_exists($chaw . 'chaw')) {
			$console = array_pop(Configure::corePaths('cake')) . 'console' . DS;
			ob_start();
			include($template . 'chaw');
			$data = ob_get_clean();

			$File = new File($chaw . 'chaw', true, 0775);
			chmod($File->pwd(), 0775);
			return $File->write($data);
		}

		return true;
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function fork($data = array()) {
		$this->set($data);

		if (empty($this->Repo)) {
			return false;
		}

		if ($this->Repo->fork($this->data['Project']['fork'], array('remote' => $this->config['remote']))) {
			$this->__created = true;
			$this->data['Project']['project_id'] = $this->id;
			$this->data['Project']['name'] = $this->data['Project']['fork'] . "'s fork of " . $this->data['Project']['name'];
			$this->data['Project']['username'] = $this->data['Project']['fork'];
			$this->data['Project']['users_count'] = 1;
		}

		if (!empty($this->data['Project']['id'])) {
			$this->id = null;
			unset($this->data['Project']['id'], $this->data['Project']['created'], $this->data['Project']['modified']);
		}

		if ($data = $this->save()) {
			$this->id = $data['Project']['project_id'];
			$this->permit(array(
				'user' => $data['Project']['user_id'],
				'id' => $data['Project']['project_id'],
			));
			return $data;
		}
		return false;
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function permit($user, $group = null) {
		$id = $this->id;
		$count = 'Project.users_count + 1';

		if (is_array($user)) {
			extract($user);
		}

		if (!is_numeric($user)) {
			$user = $this->Permission->User->field('id', array('username' => $user));
		}

		if (!$user || !$id) {
			return false;
		}

		if (!$this->Permission->field('id', array('project_id' => $id, 'user_id' => $user))) {
			$this->Permission->create(array(
				'project_id' => $id,
				'user_id' => $user,
				'group' => $group
			));
			$this->Permission->save();

			$this->recursive = -1;
			$this->updateAll(
				array('Project.users_count' => $count),
				array('Project.id' => $id)
			);
		}
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function isUnique($data, $options = array()) {
		if (!empty($data['name'])) {
			$data = $this->findByUrl(Inflector::slug(strtolower($data['name'])));
			if (!empty($test) && $test['Project']['id'] !== $this->id) {
				return false;
			}
			return true;
		}
		if (!empty($data['url'])) {
			$reserved = array('forks');
			$test = $this->findByUrl($data['url']);
			if (in_array($data['url'], $reserved) || !empty($data) && $test['Project']['id'] !== $this->id) {
				$this->invalidate('name');
				return false;
			}
			return true;
		}
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function ticket($key = null) {
		switch ($key) {
			case 'types':
				$types = array_map('trim', explode(',', $this->config['ticket_types']));
				return array_combine($types, $types);
			break;
			case 'priorities':
				$priorities = array_map('trim', explode(',', $this->config['ticket_priorities']));
				return array_combine($priorities, $priorities);
			break;
			case 'statuses':
				$statuses = array_map('trim', explode(',', $this->config['ticket_statuses']));
				return array_combine($statuses, $statuses);
			break;
		}
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function groups($key = null) {
		$Inflector = Inflector::getInstance();
		$groups = explode(',', $this->config['groups']);
		$groups = array_map(array($Inflector, 'slug'), $groups, array_fill(0, count($groups), '-'));
		return array_combine($groups, $groups);
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function repoTypes() {
		return array_combine($this->repoTypes, $this->repoTypes);
	}
/**
 * undocumented function
 *
 * @return void
 *
 **/
	function from() {
		$baseDomain = env('HTTP_BASE');
		if ($baseDomain[0] === '.') {
			$baseDomain = substr($baseDomain, 1);
		}
		$from = sprintf('<noreply@%s>', $baseDomain);
		return $from;
	}
}
?>