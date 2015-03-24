<div class="form-group">
	<label class="control-label">
		<?php echo __d('b2bses', 'Title'); ?>
	</label>
	<?php echo $this->element('NetCommons.required'); ?>

	<div  class="nc-b2bs-add-post-title-alert">
		<?php echo $this->Form->input('title',
					array(
						'label' => false,
						'class' => 'form-control',
						'ng-model' => 'b2bsPosts.title',
						'required' => 'required',
						'autofocus' => true,
					)) ?>
	</div>

	<div class="has-error">
		<?php if (isset($this->validationErrors['B2bsPost']['title'])): ?>
		<?php //foreach ($this->validationErrors['B2bsPost']['title'] as $message): ?>
			<div class="help-block">
				<?php //echo $message ?>
			</div>
		<?php //endforeach; ?>
		<?php else : ?>
			<br />
		<?php endif; ?>
	</div>
</div>

<div class="form-group">
	<label class="control-label">
		<?php echo __d('b2bses', 'Content'); ?>
	</label>
	<?php echo $this->element('NetCommons.required'); ?>

	<div class="nc-wysiwyg-alert">
		<?php echo $this->Form->textarea('content',
					array(
						'label' => false,
						'class' => 'form-control',
						'ui-tinymce' => 'tinymce.options',
						'ng-model' => 'b2bsPosts.content',
						'rows' => 5,
						'required' => 'required',
					)) ?>
	</div>

	<div class="has-error">
		<?php if (isset($this->validationErrors['B2bsPost']['content'])): ?>
		<?php foreach ($this->validationErrors['B2bsPost']['content'] as $message): ?>
			<div class="help-block">
				<?php echo $message; ?>
			</div>
		<?php endforeach; ?>
		<?php else : ?>
			<br />
		<?php endif; ?>
	</div>
</div>