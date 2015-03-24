<?php echo $this->Form->create('B2bsPost', array(
		'div' => false,
		'type' => 'post',
		'url' => '/b2bses/b2bsComments/edit/' . $frameId . '/' . $parentId . '/' . $comment['id'] . '/' . '1',
		'style' => 'float:left;'
	)); ?>

	<?php echo $this->Form->hidden('id'); ?>

	<?php echo $this->Form->hidden('B2bs.id', array(
		'value' => $b2bses['id'],
	)); ?>

	<?php echo $this->Form->hidden('User.id', array(
		'value' => $userId,
	)); ?>

	<?php echo $this->Form->hidden('B2bs.key', array(
		'value' => $b2bses['key'],
	)); ?>

	<?php echo $this->Form->hidden('title', array(
		'value' => $comment['title'],
	)); ?>

	<?php echo $this->Form->hidden('content', array(
		'value' => $comment['content'],
	)); ?>

	<?php echo $this->Form->button('<span class="glyphicon glyphicon-ok"></span>', array(
		'label' => false,
		'type' => 'submit',
		'class' => 'btn btn-warning btn-xs',
		'tooltip' => __d('b2bses', 'Approving'),
		'name' => 'save_' . NetCommonsBlockComponent::STATUS_PUBLISHED,
	)); ?>

<?php echo $this->Form->end();
