<?php echo $this->Html->script('/net_commons/base/js/workflow.js', false); ?>
<?php echo $this->Html->script('/net_commons/base/js/wysiwyg.js', false); ?>
<?php echo $this->Html->script('/b2bses/js/b2bses.js', false); ?>

<div id="nc-b2bs-add-<?php echo (int)$frameId; ?>"
		ng-controller="B2bsPost"
		ng-init="initialize(<?php echo h(json_encode($b2bsPosts)); ?>)">

<!-- パンくずリスト -->
<ol class="breadcrumb">
	<li><a href="<?php echo $this->Html->url(
				'/b2bses/b2bses/index/' . $frameId) ?>">
		<?php echo $b2bses['name']; ?></a>
	</li>
	<li class="active"><?php echo __d('b2bses', 'Create post'); ?></li>
</ol>

<div>
<?php echo $this->Form->create('B2bsPost', array(
		'name' => 'form',
		'novalidate' => true,
	)); ?>
	<?php echo $this->Form->hidden('id'); ?>
	<?php echo $this->Form->hidden('B2bs.key', array(
		'value' => $b2bses['key'],
	)); ?>
	<?php echo $this->Form->hidden('User.id', array(
		'value' => $userId,
	)); ?>

	<div class="panel panel-default">
		<div class="panel-body has-feedback">

			<?php echo $this->element('B2bsPosts/post_form'); ?>

			<hr />

			<?php echo $this->element('Comments.form'); ?>

		</div>
		<div class="panel-footer text-center">

			<?php echo $this->element('B2bses.post_workflow_buttons'); ?>

		</div>
	</div>
	<?php echo $this->element('Comments.index'); ?>

<?php echo $this->Form->end(); ?>
</div>

</div>