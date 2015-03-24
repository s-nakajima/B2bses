<?php echo $this->Html->script('/net_commons/base/js/workflow.js', false); ?>
<?php echo $this->Html->script('/b2bses/js/b2bses.js', false); ?>

<div id="nc-b2bs-auth-setting-<?php echo (int)$frameId; ?>"
		ng-controller="B2bsAuthoritySettings"
		ng-init="initialize(<?php echo h(json_encode($b2bses)); ?>)">

<?php $formName = 'B2bsForm'; ?>

<?php $this->start('titleForModal'); ?>
<?php echo __d('b2bses', 'Plugin name'); ?>
<?php $this->end(); ?>

<?php $this->startIfEmpty('tabList'); ?>
<li>
	<a href="<?php echo $this->Html->url(
					'/b2bses/b2bses/edit' . '/' . $frameId); ?>">
		<?php echo __d('b2bses', 'B2bs edit'); ?>
	</a>
</li>
<li>
	<a href="<?php echo $this->Html->url(
					'/b2bses/b2bsFrameSettings/edit' . '/' . $frameId); ?>">
		<?php echo __d('b2bses', 'Display change'); ?>
	</a>
</li>
<li class="active">
	<a href="">
		<?php echo __d('b2bses', 'Authority setting'); ?>
	</a>
</li>
<?php $this->end(); ?>

<div class="modal-header">
	<?php $titleForModal = $this->fetch('titleForModal'); ?>
	<?php if ($titleForModal) : ?>
		<?php echo $titleForModal; ?>
	<?php else : ?>
		<br />
	<?php endif; ?>
</div>

<div class="modal-body">
	<?php $tabList = $this->fetch('tabList'); ?>
	<?php if ($tabList) : ?>
		<ul class="nav nav-tabs" role="tablist">
			<?php echo $tabList; ?>
		</ul>
		<br />
		<?php $tabId = $this->fetch('tabIndex'); ?>
		<div class="tab-content" ng-init="tab.setTab(<?php echo (int)$tabId; ?>)">
	<?php endif; ?>

	<div>
	<?php echo $this->Form->create('B2bs', array(
			'name' => 'form',
			'novalidate' => true,
		)); ?>
		<?php echo $this->Form->hidden('id'); ?>
		<?php echo $this->Form->hidden('Frame.id', array(
			'value' => $frameId,
		)); ?>
		<?php echo $this->Form->hidden('Block.id', array(
			'value' => $blockId,
		)); ?>

		<div class="panel panel-default" >
			<div class="panel-body has-feedback">
				<?php echo $this->element('B2bsAuthoritySettings/auth_setting'); ?>
			</div>

			<div class="panel-footer text-center">
				<?php echo $this->Form->button('<span class="glyphicon glyphicon-remove"></span>' . __d("net_commons", "Cancel"),
						array(
							'label' => false,
							'div' => false,
							'type' => 'post',
							'class' => 'btn btn-default',
							'ng-disabled' => 'sending',
							'url' => '/b2bses/b2bses/view/' . $frameId
						)); ?>

				<?php echo $this->Form->button(__d('net_commons', 'OK'),
								array(
									'class' => 'btn btn-primary',
									'name' => 'save_0',
								)) ?>
			</div>
		</div>

	<?php echo $this->Form->end(); ?>
</div>

</div>
