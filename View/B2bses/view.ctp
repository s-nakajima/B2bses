<div id="nc-b2bs-index-<?php echo (int)$frameId; ?>">

	<?php if ($contentPublishable) : ?>
		<div class="text-right">
			<a href="<?php echo $this->Html->url(
				'/b2bses/b2bses/edit/' . $frameId) ?>" class="btn btn-default">
				<span class="glyphicon glyphicon-cog"> </span>
			</a>
		</div>
	<?php endif; ?>

	<div class="text-left">
		<strong><?php echo $b2bses['name']; ?></strong>
	</div>

	<span class="text-left">
		<?php if ($contentCreatable) : ?>
			<span class="nc-tooltip" tooltip="<?php echo __d('b2bses', 'Create post'); ?>">
				<a href="<?php echo $this->Html->url(
						'/b2bses/b2bsPosts/add' . '/' . $frameId); ?>" class="btn btn-success">
					<span class="glyphicon glyphicon-plus"> </span></a>
			</span>

			<?php if ($narrowDownParams !== '5' || $b2bsPostNum) : ?>
			<span class="btn-group">
				<button type="button" class="btn btn-default">
					<?php echo $narrowDown; ?>
				</button>
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					<span class="caret"></span>
					<span class="sr-only">Toggle Dropdown</span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li>
						<a href="<?php echo $this->Html->url(
							'/b2bses/b2bses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . $currentVisibleRow . '/' . 5); ?>">
								<?php echo __d('b2bses', 'Display all posts'); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo $this->Html->url(
							'/b2bses/b2bses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . $currentVisibleRow . '/' . 6); ?>">
								<?php echo __d('b2bses', 'Unread'); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo $this->Html->url(
							'/b2bses/b2bses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . $currentVisibleRow . '/' . NetCommonsBlockComponent::STATUS_PUBLISHED); ?>">
								<?php echo __d('b2bses', 'Published'); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo $this->Html->url(
							'/b2bses/b2bses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . $currentVisibleRow . '/' . NetCommonsBlockComponent::STATUS_IN_DRAFT); ?>">
								<?php echo __d('net_commons', 'Temporary'); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo $this->Html->url(
							'/b2bses/b2bses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . $currentVisibleRow . '/' . NetCommonsBlockComponent::STATUS_DISAPPROVED); ?>">
								<?php echo __d('b2bses', 'Remand'); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo $this->Html->url(
							'/b2bses/b2bses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . $currentVisibleRow . '/' . NetCommonsBlockComponent::STATUS_APPROVED); ?>">
								<?php echo __d('net_commons', 'Approving'); ?>
						</a>
					</li>
				</ul>
			</span>
			<?php endif; ?>
		<?php endif; ?>
	</span>

<!-- 記事なし -->
<?php if ($b2bsPostNum) : ?>

	<!-- 右に表示 -->
	<span class="text-left" style="float:right;">

		<!-- 記事件数の表示 -->
		<span class="glyphicon glyphicon-duplicate"><?php echo $postNum; ?></span>
		<small><?php echo __d('b2bses', 'Posts'); ?></small>&nbsp;

		<!-- ソート -->
		<div class="btn-group">
			<button type="button" class="btn btn-default">
				<?php echo $currentSortOrder; ?>
			</button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				<span class="caret"></span>
				<span class="sr-only">Toggle Dropdown</span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li>
					<a href="<?php echo $this->Html->url(
						'/b2bses/b2bses/view' . '/' . $frameId . '/' . 1 . '/' . 1 . '/' . $currentVisibleRow . '/' . $narrowDownParams) ?>">
							<?php echo __d('b2bses', 'Latest post order'); ?>
					</a>
				</li>
				<li>
					<a href="<?php echo $this->Html->url(
						'/b2bses/b2bses/view' . '/' . $frameId . '/' . 1 . '/' . 2 . '/' . $currentVisibleRow . '/' . $narrowDownParams); ?>">
							<?php echo __d('b2bses', 'Older post order'); ?>
					</a>
				</li>
				<li>
					<a href="<?php echo $this->Html->url(
						'/b2bses/b2bses/view' . '/' . $frameId . '/' . 1 . '/' . 3 . '/' . $currentVisibleRow . '/' . $narrowDownParams); ?>">
							<?php echo __d('b2bses', 'Descending order of comments'); ?>
					</a>
				</li>
			</ul>
		</div>

		<!-- 表示件数 -->
		<div class="btn-group">
			<button type="button" class="btn btn-default">
				<?php echo $currentVisibleRow . B2bsFrameSetting::DISPLAY_NUMBER_UNIT; ?>
			</button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				<span class="caret"></span>
				<span class="sr-only">Toggle Dropdown</span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li>
					<a href="<?php echo $this->Html->url(
						'/b2bses/b2bses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . 1 . '/' . $narrowDownParams); ?>">
							<?php echo '1' . "件"; ?>
					</a>
				</li>
				<li>
					<a href="<?php echo $this->Html->url(
						'/b2bses/b2bses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . 5 . '/' . $narrowDownParams); ?>">
							<?php echo '5' . "件"; ?>
					</a>
				</li>
				<li>
					<a href="<?php echo $this->Html->url(
						'/b2bses/b2bses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . 10 . '/' . $narrowDownParams); ?>">
							<?php echo '10' . "件"; ?>
					</a>
				</li>
				<li>
					<a href="<?php echo $this->Html->url(
						'/b2bses/b2bses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . 20 . '/' . $narrowDownParams); ?>">
							<?php echo '20' . "件"; ?>
					</a>
				</li>
				<li>
					<a href="<?php echo $this->Html->url(
						'/b2bses/b2bses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . 50 . '/' . $narrowDownParams); ?>">
							<?php echo '50' . "件"; ?>
					</a>
				</li>
				<li>
					<a href="<?php echo $this->Html->url(
						'/b2bses/b2bses/view' . '/' . $frameId . '/' . 1 . '/' . $sortParams . '/' . 100 . '/' . $narrowDownParams); ?>">
							<?php echo '100' . "件"; ?>
					</a>
				</li>
			</ul>
		</div>
	</span>
	<br /><br />

	<table class="table table-striped">
		<?php foreach ($b2bsPosts as $b2bsPost) : ?>
			<tr>
				<td>
					<?php echo ($b2bsPost['readStatus'])? '' : '<strong>'; ?>
						<a href="<?php echo $this->Html->url(
									'/b2bses/b2bsPosts/view/' . $frameId . '/' . $b2bsPost['id']); ?>"
						   class="text-left">

							<!-- 記事のタイトル -->
							<?php echo h(mb_strcut(strip_tags($b2bsPost['title']), 0, B2bsPost::DISPLAY_MAX_TITLE_LENGTH, 'UTF-8')); ?>
							<?php echo (h(mb_strcut(strip_tags($b2bsPost['title']), B2bsPost::DISPLAY_MAX_TITLE_LENGTH, null, 'UTF-8')) === false)? '' : '...'; ?>

							<!-- コメント数 -->
							<?php if ($b2bsPost['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>
								<span class="glyphicon glyphicon-comment"></span>
								<span tooltip="<?php echo __d('b2bses', 'Publishing comments'); ?>"
									  ><?php echo $b2bsPost['comment_num']; ?></span>&nbsp;
								<span tooltip="<?php echo __d('b2bses', 'Edit status of comments'); ?>"
									  ><?php echo ($contentCreatable)? '(' . $b2bsPost['editCommentNum'] . ')' : ''; ?></span>

							<?php endif; ?>
						</a>
					<?php echo ($b2bsPost['readStatus'])? '' : '</strong>'; ?>

					<!-- ステータス -->
					<?php echo $this->element('NetCommons.status_label',
								array('status' => $b2bsPost['status'])); ?>

					<!-- 作成日時 -->
					<div class="text-left" style="float:right;">
						<?php echo $this->Date->dateFormat($b2bsPost['created']); ?>
					</div>

					<!-- 本文 -->
					<p>
						<?php echo mb_strcut(strip_tags($b2bsPost['content']), 0, B2bsPost::DISPLAY_MAX_CONTENT_LENGTH, 'UTF-8'); ?>
						<?php echo (mb_strcut(strip_tags($b2bsPost['content']), B2bsPost::DISPLAY_MAX_CONTENT_LENGTH, null, 'UTF-8') === false)? '' : '...'; ?>

					</p>

					<!-- フッター -->
					<span class="text-left">
						<?php if ($b2bsPost['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>
							<?php if ($b2bses['use_like_button']) : ?>
								<span class="glyphicon glyphicon-thumbs-up"><?php echo $b2bsPost['likesNum']; ?></span>&nbsp;
							<?php endif; ?>
							<?php if ($b2bses['use_unlike_button']) : ?>
								<span class="glyphicon glyphicon-thumbs-down"><?php echo $b2bsPost['unlikesNum']; ?></span>
							<?php endif; ?>
						<?php endif ?>
						&nbsp;
					</span>

					<!-- 公開権限があれば編集／削除できる -->
					<!-- もしくは　編集権限があり、公開されていなければ、編集／削除できる -->
					<!-- もしくは 作成権限があり、自分の書いた記事で、公開されていなければ、編集／削除できる -->
					<?php if ($contentPublishable ||
							($contentEditable && $b2bsPost['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED) ||
							($contentCreatable && $b2bsPost['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED) && $b2bsPost['created_user'] === $userId): ?>

						<!-- 編集 -->
						<span class="text-left" style="float:right;">
							<a href="<?php echo $this->Html->url(
									'/b2bses/b2bsPosts/edit' . '/' . $frameId . '/' . $b2bsPost['id']); ?>"
									class="btn btn-primary btn-xs" tooltip="<?php echo __d('b2bses', 'Edit'); ?>">
									<span class="glyphicon glyphicon-edit"></span>
							</a>
							&nbsp;
						</span>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>

	<!-- ページャーの表示 -->
	<div class="text-center">
	<?php echo $this->element('B2bses/pager'); ?>
	</div>

<?php else : ?>

		<hr />
		<!-- メッセージの表示 -->
		<div class="text-left col-md-offset-1 col-xs-offset-1">
			<?php echo __d('b2bses', 'There are not posts'); ?>
		</div>

<?php endif; ?>

</div>
