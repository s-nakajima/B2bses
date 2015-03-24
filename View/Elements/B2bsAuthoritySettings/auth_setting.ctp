<div class="form-group">
	<?php
		echo $this->Form->label(__d('b2bses', 'Post creatable authority'));
	?>

	<ul class="list-inline" style="margin-left:20px">
		<li>
		<?php
			echo $this->Form->input('', array(
						'label' => __d('b2bses', 'Room administrator'),
						'div' => false,
						'type' => 'checkbox',
						'checked' => true,
						'disabled' => true
				));
		?>
		</li>
		<li>
		<?php
			echo $this->Form->input('', array(
						'label' => __d('b2bses', 'Cheif editor'),
						'div' => false,
						'type' => 'checkbox',
						'checked' => true,
						'disabled' => true
				));
		?>
		</li>
		<li>
		<?php
			echo $this->Form->input('', array(
						'label' => __d('b2bses', 'Editor'),
						'div' => false,
						'type' => 'checkbox',
						'checked' => true,
						'disabled' => true
				));
		?>
		</li>
		<li>
		<?php
			echo $this->Form->input('B2bs.post_create_authority', array(
						'label' => __d('b2bses', 'General'),
						'div' => false,
						'type' => 'checkbox',
						'ng-model' => 'b2bses.post_create_authority',
						'autofocus' => true,
					)
				);
		?>
		</li>
	</ul>
</div>

<div class="form-group">
	<?php
		echo $this->Form->label(__d('b2bses', 'Post publishable authority'));
	?>

	<ul class="list-inline" style="margin-left:20px">
		<li>
		<?php
			echo $this->Form->input('', array(
						'label' => __d('b2bses', 'Room administrator'),
						'div' => false,
						'type' => 'checkbox',
						'checked' => true,
						'disabled' => true
				));
		?>
		</li>
		<li>
		<?php
			echo $this->Form->input('', array(
						'label' => __d('b2bses', 'Cheif editor'),
						'div' => false,
						'type' => 'checkbox',
						'checked' => true,
						'disabled' => true
				));
		?>
		</li>
		<li>
		<?php
			echo $this->Form->input('B2bs.editor_publish_authority', array(
						'label' => __d('b2bses', 'Editor'),
						'div' => false,
						'type' => 'checkbox',
						'ng-model' => 'b2bses.editor_publish_authority',
						'ng-click' => "checkAuth()",
				));
		?>
		</li>
		<li>
		<?php
			echo $this->Form->input('B2bs.general_publish_authority', array(
						'label' => __d('b2bses', 'General'),
						'div' => false,
						'type' => 'checkbox',
						'ng-model' => 'b2bses.general_publish_authority',
						'ng-click' => "checkAuth()",
					)
				);
		?>
		</li>
	</ul>
</div>

<div class="form-group">
	<?php
		echo $this->Form->label(__d('b2bses', 'Comment creatable authority'));
	?>

	<ul class="list-inline" style="margin-left:20px">
		<li>
		<?php
			echo $this->Form->input('', array(
						'label' => __d('b2bses', 'Room administrator'),
						'div' => false,
						'type' => 'checkbox',
						'checked' => true,
						'disabled' => true
				));
		?>
		</li>
		<li>
		<?php
			echo $this->Form->input('', array(
						'label' => __d('b2bses', 'Cheif editor'),
						'div' => false,
						'type' => 'checkbox',
						'checked' => true,
						'disabled' => true
				));
		?>
		</li>
		<li>
		<?php
			echo $this->Form->input('', array(
						'label' => __d('b2bses', 'Editor'),
						'div' => false,
						'type' => 'checkbox',
						'checked' => true,
						'disabled' => true
				));
		?>
		</li>
		<li>
		<?php
			echo $this->Form->input('B2bs.comment_create_authority', array(
						'label' => __d('b2bses', 'General'),
						'div' => false,
						'type' => 'checkbox',
						'ng-model' => 'b2bses.comment_create_authority',
					)
				);
		?>
		</li>
	</ul>
</div>