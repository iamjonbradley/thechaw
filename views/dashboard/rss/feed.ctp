<?php
Configure::write('debug', 0);

$this->set('channel', array(
	'title' =>"{$CurrentUser->username}'s Feed",
	'link' => $rssFeed
));

foreach ($feed as $data) {
	$type = $data['Timeline']['model'];

	if (empty($data[$type])) {
		return array();
	}

	switch ($type) {
		case 'Commit':
			$title = "{$type}: " . $data[$type]['revision']; //$chaw->commit($commit['Commit']['revision'], $commit['Project'])
			$link = array('controller' => 'commits', 'action' => 'view', $data[$type]['revision']);
			$pubDate = $data[$type]['commit_date'];
			$description = $data[$type]['message'];
			$author = !empty($data['User']['username']) ? $data['User']['username'] : $data['Commit']['author'];
		break;
		case 'Wiki':
			$title = "{$type}: " . Inflector::humanize($data[$type]['slug']);
			$link = array('controller' => 'wiki', 'action' => 'index', $data[$type]['path'], $data[$type]['slug']);
			$pubDate = $data[$type]['modified'];
			$description = $text->truncate(nl2br($page['Wiki']['content']), 400, '...', false, true);
			$author = $data['User']['username'];
		break;
		case 'Ticket':
			$title = "{$type}: " . $data[$type]['title'];
			$link = array('controller' => 'tickets', 'action' => 'view', $data[$type]['number']);
			$pubDate = $data[$type]['modified'];
			$description = $text->truncate(nl2br($data[$type]['description']), 200, '...', false, true);
			$author = $data['Reporter']['username'];
		break;
		case 'Comment':
			$title = "{$type}: " . $data['Ticket']['title'];
			$link = array(
				'controller' => 'tickets', 'action' => 'view', $data['Ticket']['number'],
				'#' => 'c'.$data['Comment']['id']
			);;
			$pubDate = $data['Ticket']['modified'];
			$description = $text->truncate(nl2br($data['Comment']['body']), 200, '...', false, true);
			$author = $data['User']['username'];

			if (!empty($data['Ticket']['Project'])) {
				$data['Project'] = $data['Ticket']['Project'];
			}
		break;

	}

	$project = null;
	if (!empty($data['Project'])) {
		$link = $chaw->url($data['Project'], $link);
		$project = ' in '. $data['Project']['name'];
	}
	$title .= $project;

	$pubDate = date('r', strtotime($pubDate));

	echo $rss->item(null, compact('title', 'link', 'pubDate', 'description', 'author'));
}