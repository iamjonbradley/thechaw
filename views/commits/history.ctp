<?php
$script = '
$(document).ready(function(){
	converter = new Showdown.converter("' . $this->webroot . '");
	$(".message").each(function () {
		$(this).html(converter.makeHtml(jQuery.trim($(this).text())))
	});
});
';
$javascript->codeBlock($script, array('inline' => false));
?>
<h2>
	History
</h2>
<div class="commits history">
	<h3>
		<?php
			$title = null;
			if (!empty($CurrentProject->fork)) {
				$title = "forks / {$CurrentProject->fork} / ";
			}
			$title .= $CurrentProject->url;
			echo $html->link($title, array('controller' => 'browser', 'action' => 'index'));
		?>
		<?php
			$path = '/';
			foreach ((array)$args as $part):
				$path .= $part . '/';
				echo '/' . $html->link(' ' . $part . ' ', array($path));
			endforeach;
			echo '/ ' . $current;
		?>
	</h3>

	<?php foreach ((array)$commits as $commit):?>

		<div class="commit">

			<h4>
				<?php echo $chaw->commit($commit['Repo']['revision']);?>
			</h4>

			<p>
				<strong>Author:</strong> <?php echo $commit['Repo']['author'];?>
			</p>

			<p>
				<strong>Date:</strong> <?php echo $commit['Repo']['commit_date'];?>
			</p>

			<p class="message">
				<?php echo $commit['Repo']['message'];?>
			</p>

			<?php
				if(!empty($commit['Repo']['changes'])):
			?>
				<div class="changes">
					<strong>Changes:</strong>
					<ul>
					<?php
						foreach ($commit['Repo']['changes'] as $changed) :
							echo $html->tag('li', $changed);
						endforeach;
					?>
					</ul>
				</div>
			<?php endif?>

		</div>

	<?php endforeach;?>

</div>