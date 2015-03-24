<div class="form-group">
	<label class="control-label">
		<?php echo __d('b2bses', 'B2bs name'); ?>
	</label>
	<?php echo $this->element('NetCommons.required'); ?>

	<?php echo $this->Form->input('B2bs.name',
				array(
					'label' => false,
					'class' => 'form-control',
					'ng-model' => 'b2bses.name',
					'required' => 'required',
					'autofocus' => true,
				)) ?>

	<div class="has-error">
		<?php if ($this->validationErrors['B2bs']): ?>
			<?php foreach ($this->validationErrors['B2bs']['name'] as $message): ?>
				<div class="help-block">
					<?php echo $message; ?>
				</div>
			<?php endforeach; ?>
		<?php else : ?>
			<br />
		<?php endif; ?>
	</div>
</div>

<div class="form-group">
	<?php
		echo $this->Form->input('B2bs.use_comment', array(
					'label' => __d('b2bses', 'Use comment'),
					'div' => false,
					'type' => 'checkbox',
					'ng-model' => 'b2bses.use_comment',
					'ng-click' => "initAutoApproval()",
				)
			);
	?>
</div>

	<div class="form-group col-sm-offset-1 col-xs-offset-1">
		<?php
			echo $this->Form->input('B2bs.auto_approval', array(
						'label' => __d('b2bses', 'Auto approval'),
						'div' => false,
						'type' => 'checkbox',
						'ng-disabled' => '! b2bses.use_comment',
						'ng-model' => 'b2bses.auto_approval',
					)
				);
		?>
	</div>

<div class="form-group">
	<?php
		echo $this->Form->input('B2bs.use_like_button', array(
					'label' => __d('b2bses', 'Use like button'),
					'div' => false,
					'type' => 'checkbox',
					'ng-model' => 'b2bses.use_like_button',
					'ng-click' => "initUnlikeButton()",
				)
			);
	?>
	<span class="glyphicon glyphicon-thumbs-up"></span>
</div>

	<div class="form-group col-sm-offset-1 col-xs-offset-1">
		<?php
			echo $this->Form->input('B2bs.use_unlike_button', array(
						'label' => __d('b2bses', 'Use unlike button'),
						'div' => false,
						'type' => 'checkbox',
						'ng-disabled' => '! b2bses.use_like_button',
						'ng-model' => 'b2bses.use_unlike_button',
					)
				);
		?>
		<span class="glyphicon glyphicon-thumbs-down"></span>
	</div>