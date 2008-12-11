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
 * @subpackage		chaw.config
 * @since			Chaw 0.1
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 */
	Router::parseExtensions('rss', 'tar');


	/* Base Routes */
	Router::connect('/', array('controller' => 'wiki', 'action' => 'index'));
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
	Router::connect('/start', array('controller' => 'pages', 'action' => 'start'));
	Router::connect('/dashboard', array('controller' => 'dashboard', 'action' => 'index'));

	/* Admin Routes */
	Router::connect('/admin/:controller', array('admin'=> true, 'controller' => 'dashboard'), array('admin' => true));
	Router::connect('/admin/:controller/:action/*', array('admin'=> true, 'controller' => 'dashboard'), array('admin' => true));

	Router::connect('/forks/:fork/:project/admin/:controller', array('admin'=> true, 'controller' => 'dashboard'));
	Router::connect('/forks/:fork/:project/admin/:controller/:action/*', array('admin'=> true, 'controller' => 'dashboard'));

	Router::connect('/:project/admin/:controller', array('admin'=> true, 'controller' => 'dashboard'));
	Router::connect('/:project/admin/:controller/:action/*', array('admin'=> true, 'controller' => 'dashboard'));


	/* Specific Routes */
	Router::connect('/:project/fork', array('controller' => 'projects', 'action' => 'fork'), array('action' => 'fork'));
	Router::connect('/download/:project', array('controller' => 'projects', 'action' => 'index'));
	Router::connect('/download/forks/:fork/:project', array('controller' => 'projects', 'action' => 'index'));

	Router::connect('/browser/*', array('controller' => 'browser', 'action' => 'index'));
	Router::connect('/wiki/add/*', array('controller' => 'wiki', 'action' => 'add'), array('action' => 'add'));
	Router::connect('/wiki/edit/*', array('controller' => 'wiki', 'action' => 'add'), array('action' => 'edit'));
	Router::connect('/wiki/*', array('controller' => 'wiki', 'action' => 'index'));

	Router::connect('/forks/:fork/:project/browser/*', array('controller' => 'browser', 'action' => 'index'));
	Router::connect('/forks/:fork/:project/wiki/add/*', array('controller' => 'wiki', 'action' => 'add'), array('action' => 'add'));
	Router::connect('/forks/:fork/:project/wiki/edit/*', array('controller' => 'wiki', 'action' => 'add'), array('action' => 'edit'));
	Router::connect('/forks/:fork/:project/wiki/*', array('controller' => 'wiki', 'action' => 'index'));

	Router::connect('/:project/browser/*', array('controller' => 'browser', 'action' => 'index'));
	Router::connect('/:project/wiki/add/*', array('controller' => 'wiki', 'action' => 'add'), array('action' => 'add'));
	Router::connect('/:project/wiki/edit/*', array('controller' => 'wiki', 'action' => 'add'), array('action' => 'edit'));
	Router::connect('/:project/wiki/*', array('controller' => 'wiki', 'action' => 'index'));


	/* General Routes */
	Router::connect('/:controller', array(), array(
		'controller' => 'commits|tickets|timeline|versions|users|projects',
		'action' => 'index')
	);
	Router::connect('/:controller/:action/*', array(), array(
		'controller' => 'commits|tickets|timeline|versions|users|projects',
		'action' => 'history|view|add|edit|modify|delete|remove|login|account|logout')
	);
	Router::connect('/:controller/*', array(), array(
		'controller' => 'commits|tickets|timeline|versions|users|projects',
		'action' => 'index')
	);


	/* Genral Fork Routes */
	Router::connect('/forks/:fork/:project/', array('controller' => 'browser', 'action' => 'index'));
	Router::connect('/forks/:fork/:project/:controller', array('action' => 'index'), array('action' => 'index'));
	Router::connect('/forks/:fork/:project/:controller/:action/*', array(), array(
		'controller' => 'commits|tickets|timeline|versions|users|projects',
		'action' => 'history|view|add|edit|modify|delete|remove')
	);
	Router::connect('/forks/:fork/:project/:controller/*', array(), array(
		'controller' => 'commits|tickets|timeline|versions|users|projects',
		'action' => 'index')
	);


	/* Genral Project Routes */
	Router::connect('/:project', array('controller' => 'browser', 'action' => 'index'));
	Router::connect('/:project/:controller', array('action' => 'index'), array('action' => 'index'));
	Router::connect('/:project/:controller/:action/*', array(), array(
		'controller' => 'commits|tickets|timeline|versions|users|projects',
		'action' => 'history|view|add|edit|modify|delete|remove')
	);
	Router::connect('/:project/:controller/*', array(), array(
		'controller' => 'commits|tickets|timeline|versions|users|projects',
		'action' => 'index')
	);
