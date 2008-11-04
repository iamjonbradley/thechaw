<?php
/* SVN FILE: $Id: default.ctp 6296 2008-01-01 22:18:17Z phpnut $ */
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.cake.console.libs.templates.skel.views.layouts
 * @since			CakePHP(tm) v 0.10.0.1076
 * @version			$Revision: 6296 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2008-01-01 14:18:17 -0800 (Tue, 01 Jan 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>
		<?php echo Configure::read('Project.name') .' : ' . $title_for_layout;?>
	</title>
	<?php
		echo $html->charset();
		echo $html->meta('icon');
		echo $html->css(array('generic', 'chaw'));
	?>
	<?php
		echo $javascript->link('jquery-1.2.6.min');
		echo $javascript->link('gshowdown');
		echo $scripts_for_layout;
	?>
</head>
<body>
	<div id="container">
		<div id="header">

			<h1><?php echo $html->link(Configure::read('Project.name'), array('controller' => 'wiki', 'action' => 'index'));?></h1>

			<div id="navigation">
				<ul>
					<li><?php echo $html->link('Wiki', array('controller' => 'wiki', 'action' => 'index'));?></li>
					<li><?php echo $html->link('Timeline', array('controller' => 'timeline', 'action' => 'index'));?></li>
					<li><?php echo $html->link('Tickets', array('controller' => 'tickets', 'action' => 'index'));?></li>
					<li><?php echo $html->link('Source', array('controller' => 'browser', 'action' => 'index'));?></li>
					<li><?php echo $html->link('Versions', array('controller' => 'versions', 'action' => 'index'));?></li>
					<li><?php echo $html->link('Projects', array('controller' => 'projects', 'action' => 'index'));?></li>
					<li><?php echo $admin->link('Admin', array('admin' => true, 'controller' => 'dashboard', 'action' => 'index'));?></li>
				</ul>
			</div>

		</div>

		<div id="content">
			<?php
				echo $this->element('current_user');

				$session->flash();
			?>
			<div id="page-content">
				<?php
					echo $content_for_layout;
				?>
			</div>

		</div>

		<div id="footer">
			<?php echo $html->link(
							$html->image('cake.power.gif', array('alt'=> __("CakePHP: the rapid development php framework", true), 'border'=>"0")),
							'http://www.cakephp.org/',
							array('target'=>'_new'), null, false
						);
			?>
		</div>
	</div>
	<?php echo $cakeDebug?>
</body>
</html>