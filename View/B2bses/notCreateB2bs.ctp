<!-- Todo:フレーム管理に移動する -->
<?php if ($contentPublishable) : ?>
	<div class="text-right">
		<a href="<?php echo $this->Html->url(
			'/b2bses/b2bses/edit/' . $frameId) ?>" class="btn btn-default">
			<span class="glyphicon glyphicon-cog"> </span>
		</a>
	</div>
<?php endif; ?>

<div class="text-left">
	<?php echo __d('b2bses', 'There are not published b2bs'); ?>
</div>