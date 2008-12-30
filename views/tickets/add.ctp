<?php
$html->css('highlight/idea', null, null, false);
$javascript->link('highlight.pack', false);

$script = '
hljs.initHighlightingOnLoad();

$(document).ready(function(){
	$("#Preview").html(converter.makeHtml(jQuery.trim($("#TicketDescription").val())));
	$("#TicketDescription").bind("keyup", function() {
		$("#Preview").html(converter.makeHtml($(this).val()));
		hljs.initHighlighting.called = false;
		hljs.initHighlighting();
	});
});
';
$javascript->codeBlock($script, array('inline' => false));
?>
<div class="tickets add form">
<?php echo $form->create('Ticket');?>
	<fieldset class="main">
 		<legend><?php __('Add Ticket');?></legend>
		<?php
			echo $form->input('title');
			echo $form->input('description', array(
				'value' => "###What happened:\n- something\n\n\n###What was expected:\n- something else\n\n"
			));
		?>

		<div id="Preview" class="preview"></div>

	</fieldset>


	<fieldset class="options">
		<legend>Tags</legend>
		<?php
			echo $form->textarea('tags');
		?>
		comma separated
	</fieldset>


	<fieldset class="options">
		<legend>Options</legend>
		<?php
			if (!empty($versions)) {
				echo $form->input('version_id');
			}
			echo $form->input('owner');
			echo $form->input('type');
			echo $form->input('priority');
		?>
	</fieldset>

	<div class="help">
		<?php echo $this->element('markdown_help', array('short' => true)); ?>
	</div>

	<?php echo $form->submit('Submit')?>


<?php echo $form->end();?>
</div>