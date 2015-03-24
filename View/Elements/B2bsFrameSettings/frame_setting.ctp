<div class='form-group'>
	<?php
		echo $this->Form->label(__d('b2bses', 'Visible post row'));
	?>
	&nbsp;
	<?php
		echo $this->Form->input('B2bsFrameSetting.visible_post_row', array(
					'label' => false,
					'type' => 'select',
					'class' => 'form-control',
					'options' => B2bsFrameSetting::getDisplayNumberOptions(),
					'selected' => $b2bsSettings['visible_post_row'],
					'ng-model' => 'b2bsSettings.visible_post_row',
					'autofocus' => true,
				)
			);
	?>
</div>

<div class='form-group'>
	<?php
		echo $this->Form->label(__d('b2bses', 'Visible comment row'));
	?>
	&nbsp;
	<?php
		echo $this->Form->input('B2bsFrameSetting.visible_comment_row', array(
					'label' => false,
					'type' => 'select',
					'class' => 'form-control',
					'options' => B2bsFrameSetting::getDisplayNumberOptions(),
					'selected' => $b2bsSettings['visible_comment_row'],
					'ng-model' => 'b2bsSettings.visible_comment_row',
				)
			);
	?>
</div>
