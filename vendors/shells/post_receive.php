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
 * @subpackage		chaw.vendors.shells
 * @since			Chaw 0.1
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
class PostReceiveShell extends Shell {

	var $uses = array( 'Project', 'Commit');

	function _welcome() {}

	function main() {
		return $this->commit();
	}

	function commit() {

		$project = str_replace('.git', '', trim(@$this->args[0], "'"));
		$refname = @$this->args[1];
		$oldrev = @$this->args[2];
		$newrev = @$this->args[3];

		$fork = (!empty($this->params['fork']) && $this->params['fork'] != 1 && $this->params['fork'] != true) ? $this->params['fork'] : null;

		if ($this->Project->initialize(compact('project', 'fork')) === false || $this->Project->config['url'] !== $project) {
			$this->err('Invalid project');
			return 1;
		}

		if (!isset($refname)) {
			$refname = 'refs/heads/master';
		}

		$data = $this->Project->Repo->read($newrev, false);

		if (!empty($data)) {

			$data['project_id'] = $this->Project->id;

			$this->Commit->create($data);

			if (!$this->Commit->save()) {
				return false;
			}
		}

		return;
	}

}