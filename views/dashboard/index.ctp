<h2><?php __('Dashboard');?></h2>

<div class="page-navigation">
	<?php
		echo $html->link('View Account', array('controller' => 'users', 'action' => 'account')) .' | ';

		echo $html->link(
			$html->image('feed-icon.png', array(
				'width' => 14, 'height' => 14
			)),
			$rssFeed, array(
			'title' => "{$CurrentUser->username}'s Feed", 'class' => 'rss', 'escape'=> false
		));
	?>
</div>

<div class="dashboard index">

	<div class="panels">
		<?php echo $this->element('current_projects'); ?>

		<?php echo $this->element('recent/commits'); ?>

		<?php echo $this->element('recent/tickets'); ?>

		<?php echo $this->element('recent/comments'); ?>

		<?php echo $this->element('recent/wiki'); ?>
	</div>

</div>
