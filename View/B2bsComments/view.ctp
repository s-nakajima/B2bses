<?php echo $this->Html->script('/net_commons/base/js/workflow.js', false); ?>
<?php echo $this->Html->script('/net_commons/base/js/wysiwyg.js', false); ?>
<?php echo $this->Html->script('/b2bses/js/b2bses.js', false); ?>

<div id="nc-b2bs-comment-view-<?php echo (int)$frameId; ?>"
		ng-controller="B2bsComment"
		ng-init="initialize(<?php echo h(json_encode($b2bsPosts)); ?>,
							<?php echo h(json_encode($b2bsComments)); ?>)">

<!-- パンくずリスト -->
<ol class="breadcrumb">
	<li><a href="<?php echo $this->Html->url(
				'/b2bses/b2bses/index/' . $frameId) ?>">
		<?php echo $b2bses['name']; ?></a>
	</li>
	<li>
		<a href="<?php echo $this->Html->url(
				'/b2bses/b2bsPosts/view/' . $frameId . '/' . $b2bsPosts['id']) ?>">
			<?php echo h(mb_strcut(strip_tags($b2bsPosts['title']), 0, B2bsPost::DISPLAY_MAX_TITLE_LENGTH, 'UTF-8')); ?>
			<?php echo (h(mb_strcut(strip_tags($b2bsPosts['title']), B2bsPost::DISPLAY_MAX_TITLE_LENGTH, null, 'UTF-8')) === false)? '' : '...'; ?>
		</a>
	</li>
	<li class="active">
		<?php echo h(mb_strcut(strip_tags($b2bsCurrentComments['title']), 0, B2bsPost::DISPLAY_MAX_TITLE_LENGTH, 'UTF-8')); ?>
			<?php echo (h(mb_strcut(strip_tags($b2bsCurrentComments['title']), B2bsPost::DISPLAY_MAX_TITLE_LENGTH, null, 'UTF-8')) === false)? '' : '...'; ?>
	</li>
</ol>

<div class="text-left">
		<!-- 記事タイトル -->
		<h3><a href="<?php echo $this->Html->url(
					'/b2bses/b2bsPosts/view/' . $frameId . '/' . $b2bsPosts['id']) ?>">
			<?php echo $b2bsPosts['title']; ?></a>に戻る</h3>
</div>

<?php if ($b2bsCurrentComments['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>

	<div class="btn-group text-left">
		<button type="button" class="btn btn-default">
			<?php echo $narrowDown; ?>
		</button>
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			<span class="caret"></span>
			<span class="sr-only">Toggle Dropdown</span>
		</button>
		<ul class="dropdown-menu" role="menu">
			<!-- URL:controller:B2bsCommentsController action:view -->
			<!--     argument:frameId, postId(記事), $b2bsCurrentComments(対象コメント) -->
			<!--     pageNumber(コメント一覧ページ番号), sortParams(ソート), visibleRow(表示件数), narrowDown(絞り込み) -->
			<li>
				<a href="<?php echo $this->Html->url('/' . $baseUrl . '/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $b2bsCurrentComments['id'] . '/' . 1 . '/' . $sortParams . '/' . $currentVisibleRow . '/' . 5); ?>">
					<?php echo __d('b2bses', 'Display all posts'); ?>
				</a>
			</li>
			<li>
				<a href="<?php echo $this->Html->url('/' . $baseUrl . '/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $b2bsCurrentComments['id'] . '/' . 1 . '/' . $sortParams . '/' . $currentVisibleRow . '/' . NetCommonsBlockComponent::STATUS_PUBLISHED); ?>">
					<?php echo __d('b2bses', 'Published'); ?>
				</a>
			</li>
			<li>
				<a href="<?php echo $this->Html->url('/' . $baseUrl . '/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $b2bsCurrentComments['id'] . '/' . 1 . '/' . $sortParams . '/' . $currentVisibleRow . '/' . NetCommonsBlockComponent::STATUS_IN_DRAFT); ?>">
					<?php echo __d('net_commons', 'Temporary'); ?>
				</a>
			</li>
			<li>
				<a href="<?php echo $this->Html->url('/' . $baseUrl . '/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $b2bsCurrentComments['id'] . '/' . 1 . '/' . $sortParams . '/' . $currentVisibleRow . '/' . NetCommonsBlockComponent::STATUS_APPROVED); ?>">
					<?php echo __d('b2bses', 'Disapproval'); ?>
				</a>
			</li>
		</ul>
	</div>

	<div class="text-left" style="float:right;">
		<!-- コメント数 -->
		<span class="glyphicon glyphicon-comment"><?php echo $commentNum; ?></span>
		<small><?php echo __d('b2bses', 'Comments'); ?></small>&nbsp;

		<!-- ソート用プルダウン -->
		<div class="btn-group">
			<button type="button" class="btn btn-default">
				<?php echo $currentSortOrder; ?>
			</button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
				<span class="caret"></span>
				<span class="sr-only">Toggle Dropdown</span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<!-- URL:controller:B2bsCommentsController action:view -->
				<!--     argument:frameId, postId(記事), $b2bsCurrentComments(対象コメント) -->
				<!--     pageNumber(コメント一覧ページ番号), sortParams(ソート), visibleRow(表示件数), narrowDown(絞り込み) -->
				<li>
					<a href="<?php echo $this->Html->url('/' . $baseUrl . '/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $b2bsCurrentComments['id'] . '/' . 1 . '/' . 1 . '/' . $currentVisibleRow . '/' . $narrowDownParams); ?>">
						<?php echo __d('b2bses', 'Latest post order'); ?>
					</a>
				</li>
				<li>
					<a href="<?php echo $this->Html->url('/' . $baseUrl . '/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $b2bsCurrentComments['id'] . '/' . 1 . '/' . 2 . '/' . $currentVisibleRow . '/' . $narrowDownParams); ?>">
						<?php echo __d('b2bses', 'Older post order'); ?>
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
				<!-- URL:controller:B2bsCommentsController action:view -->
				<!--     argument:frameId, postId(記事), $b2bsCurrentComments(対象コメント) -->
				<!--     pageNumber(コメント一覧ページ番号), sortParams(ソート), visibleRow(表示件数), narrowDown(絞り込み) -->
				<li>
					<a href="<?php echo $this->Html->url('/' . $baseUrl . '/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $b2bsCurrentComments['id'] . '/' . 1 . '/' . $sortParams . '/' . 1 . '/' . $narrowDownParams); ?>">
						<?php echo '1' . "件"; ?>
					</a>
				</li>
				<li>
					<a href="<?php echo $this->Html->url('/' . $baseUrl . '/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $b2bsCurrentComments['id'] . '/' . 1 . '/' . $sortParams . '/' . 5 . '/' . $narrowDownParams); ?>">
						<?php echo '5' . "件"; ?>
					</a>
				</li>
				<li>
					<a href="<?php echo $this->Html->url('/' . $baseUrl . '/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $b2bsCurrentComments['id'] . '/' . 1 . '/' . $sortParams . '/' . 10 . '/' . $narrowDownParams); ?>">
						<?php echo '10' . "件"; ?>
					</a>
				</li>
				<li>
					<a href="<?php echo $this->Html->url('/' . $baseUrl . '/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $b2bsCurrentComments['id'] . '/' . 1 . '/' . $sortParams . '/' . 20 . '/' . $narrowDownParams); ?>">
						<?php echo '20' . "件"; ?>
					</a>
				</li>
				<li>
					<a href="<?php echo $this->Html->url('/' . $baseUrl . '/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $b2bsCurrentComments['id'] . '/' . 1 . '/' . $sortParams . '/' . 50 . '/' . $narrowDownParams); ?>">
						<?php echo '50' . "件"; ?>
					</a>
				</li>
				<li>
					<a href="<?php echo $this->Html->url('/' . $baseUrl . '/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $b2bsCurrentComments['id'] . '/' . 1 . '/' . $sortParams . '/' . 100 . '/' . $narrowDownParams); ?>">
						<?php echo '100' . "件"; ?>
					</a>
				</li>
			</ul>
		</div>
	</div>
	<br />
<?php endif; ?>

<br />

<!-- 親記事 -->
<div class="panel-group">
	<div class="panel panel-info">
		<div class="panel-heading">
			<span class="text-left">
				<!-- アバター表示 -->
				<span>
					<?php echo $this->Html->image('/b2bses/img/avatar.PNG', array('alt' => 'アバターが設定されていません')); ?>
				</span>

				<!-- ユーザ情報 -->
				<span>
					<a href="" ng-click="user.showUser(<?php echo $b2bsPosts['userId'] ?>)">
						<?php echo $b2bsPosts['username']; ?>
					</a>
				</span>
			</span>
			<!-- 右に表示 -->
			<span class="text-left" style="float:right;">
				<!-- 作成時間 -->
				<span><?php echo $this->Date->dateFormat($b2bsPosts['created']); ?></span>
			</span>
		</div>
		<div class="panel-body">
			<!-- 本文 -->
			<div><?php echo $b2bsPosts['content']; ?></div>
		</div>
		<div class="panel-footer">
			<!-- いいね！ -->
			<span class="text-left">
				<?php if ($b2bsPosts['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>
					<?php if ($b2bses['use_like_button']) : ?>
						<!-- いいね -->
						<span class="text-left" style="float:left;">
							<!-- general user以上 -->
							<?php if ((int)$this->viewVars['rolesRoomId'] !== 0 && (int)$this->viewVars['rolesRoomId'] < 5) : ?>
								<?php $likesParams = ($b2bsPosts['likesFlag'])? 0 : 1; ?>
								<?php echo $this->Form->create('',
										array(
											'type' => 'post',
											'url' => '/b2bses/b2bsPosts/likes/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $userId . '/' . $likesParams,
										)); ?>

										<?php echo $this->Form->button('<span class="glyphicon glyphicon-thumbs-up">' . $b2bsPosts['likesNum'] . '</span>',
												array(
													'type' => 'submit',
													'class' => 'btn btn-link',
													'style' => 'padding: 0;',
													'tooltip' => ($b2bsPosts['likesFlag'])? __d('b2bses', 'Remove likes') : __d('b2bses', 'Do likes'),
												)); ?>
										&nbsp;
								<?php echo $this->Form->end(); ?>

							<!-- visitor -->
							<?php else : ?>
								<span class="glyphicon glyphicon-thumbs-up"><?php echo $b2bsPosts['likesNum']; ?></span>
								&nbsp;
							<?php endif; ?>
						</span>
					<?php endif; ?>
					<?php if ($b2bses['use_unlike_button']) : ?>
						<!-- よくないね -->
						<span class="text-left" style="float:left;">
							<!-- general user以上 -->
							<?php if ((int)$this->viewVars['rolesRoomId'] !== 0 && (int)$this->viewVars['rolesRoomId'] < 5) : ?>
								<?php $unlikesParams = ($b2bsPosts['unlikesFlag'])? 0 : 1; ?>
								<?php echo $this->Form->create('',
										array(
											'type' => 'post',
											'url' => '/b2bses/b2bsPosts/unlikes/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $userId . '/' . $unlikesParams,
										)); ?>

										<?php echo $this->Form->button('<span class="glyphicon glyphicon-thumbs-up">' . $b2bsPosts['unlikesNum'] . '</span>',
												array(
													'type' => 'submit',
													'class' => 'btn btn-link',
													'style' => 'padding: 0;',
													'tooltip' => ($b2bsPosts['unlikesFlag'])? __d('b2bses', 'Remove unlikes') : __d('b2bses', 'Do unlikes'),
												)); ?>
								<?php echo $this->Form->end(); ?>

							<!-- visitor -->
							<?php else : ?>
								<span class="glyphicon glyphicon-thumbs-up"><?php echo $b2bsPosts['unlikesNum']; ?></span>
							<?php endif; ?>
						</span>
					<?php endif; ?>
				<?php endif; ?>
				&nbsp;
			</span>

			<!-- 右に表示 -->
			<span class="text-left" style="float:right;">
				<?php if (($contentCreatable && $b2bsPosts['created_user'] === $userId
								&& $b2bsPosts['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED) ||
							($contentEditable
								&& $b2bsPosts['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED) ||
							$contentPublishable) : ?>

					<!-- 削除 -->
					<span class="text-left" style="float:right;">
						<?php echo $this->Form->create('',
							array(
								'div' => false,
								'type' => 'post',
								'url' => '/b2bses/b2bsPosts/delete/' . $frameId . '/' . $b2bsPosts['id']
							)); ?>

							<?php echo $this->Form->button('<span class="glyphicon glyphicon-trash"></span>',
									array(
										'label' => false,
										'div' => false,
										'type' => 'submit',
										'class' => 'btn btn-danger btn-xs',
										'tooltip' => __d('b2bses', 'Delete'),
									)); ?>
						<?php echo $this->Form->end(); ?>
					</span>

					<!-- 編集 -->
					<span class="text-left" style="float:right;">

						<a href="<?php echo $this->Html->url(
										'/b2bses/b2bsPosts/edit' . '/' . $frameId . '/' . $b2bsPosts['id']); ?>"
										class="btn btn-primary btn-xs" tooltip="<?php echo __d('b2bses', 'Edit'); ?>">
										<span class="glyphicon glyphicon-edit"></span>
						</a>
						&ensp;
					</span>
				<?php endif; ?>
			</span>

			<!-- Warn:style 一行に表示するため指定 -->
			<span class="text-left" style="float:right;">
				<?php if ($commentCreatable && $b2bses['use_comment']
							&& $b2bsPosts['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>

					<?php echo $this->Form->create('',
								array(
									'div' => false,
									'type' => 'get',
									'url' => '/b2bses/b2bsComments/add/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $b2bsPosts['id']
								)); ?>

						<label>
							<!-- 引用するか否か -->
							<?php echo $this->Form->input('quotFlag',
										array(
											'label' => false,
											'div' => false,
											'type' => 'checkbox',
										)); ?>
							<?php echo __d('b2bses', 'Quote this posts'); ?>
						</label>
						&nbsp;

						<?php echo $this->Form->button('<span class="glyphicon glyphicon-comment"></span>',
								array(
									'label' => false,
									'div' => false,
									'type' => 'submit',
									'class' => 'btn btn-success btn-xs',
									'tooltip' => __d('b2bses', 'Write comment'),
								)); ?>
						&ensp;

					<?php echo $this->Form->end(); ?>
				<?php endif; ?>
			</span>
		</div>
	</div>
</div>

<!-- 対象の記事 -->
<div class="panel-group">
	<div class="panel panel-success">
		<div class="panel-heading">
			<span class="text-left">
				<!-- id -->
				<?php echo $b2bsCurrentComments['comment_index'] . '.'; ?>
				<span><?php echo $this->Html->image('/b2bses/img/avatar.PNG', array('alt' => 'アバターが設定されていません')); ?></span>
				<!-- ユーザ情報 -->
				<span>
					<a href="" ng-click="user.showUser(<?php echo $b2bsCurrentComments['userId'] ?>)">
						<?php echo $b2bsCurrentComments['username']; ?>
					</a>
				</span>

				<!-- タイトル※対象記事のためリンクを貼らない -->
				<h4 style="display:inline;"><strong><?php echo $b2bsCurrentComments['title']; ?></strong></h4>
				<!-- ステータス -->
				<span><?php echo $this->element('B2bses.comment_status_label',
									array('status' => $b2bsCurrentComments['status'])); ?></span>
			</span>

			<span class="text-left" style="float:right;">
				<!-- 作成時間 -->
				<span><?php echo $this->Date->dateFormat($b2bsCurrentComments['created']); ?></span>
			</span>
		</div>
		<div class="panel-body">
			<!-- 本文 -->
			<?php if ($b2bsPosts['id'] !== $b2bsCurrentComments['parent_id']) : ?>
				<div><a href="<?php echo $this->Html->url(
							'/b2bses/b2bsComments/view' . '/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $b2bsCurrentComments['parent_id']); ?>">
						<?php echo '>>' . $b2bsCurrentComments['parent_comment_index']; ?></a></div>
			<?php endif; ?>
			<div><?php echo $b2bsCurrentComments['content']; ?></div>
		</div>

		<!-- フッター -->
		<div class="panel-footer">
			<!-- いいね！ -->
			<span class="text-left">
				<?php if ($b2bsCurrentComments['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>
					<?php if ($b2bses['use_like_button']) : ?>
						<!-- いいね -->
						<span class="text-left" style="float:left;">
							<!-- general user以上 -->
							<?php if ((int)$this->viewVars['rolesRoomId'] !== 0 && (int)$this->viewVars['rolesRoomId'] < 5) : ?>
								<?php $likesParams = ($b2bsCurrentComments['likesFlag'])? 0 : 1; ?>
								<?php echo $this->Form->create('',
										array(
											'type' => 'post',
											'url' => '/b2bses/b2bsPosts/likes/' . $frameId . '/' . $b2bsCurrentComments['id'] . '/' . $userId . '/' . $likesParams,
										)); ?>

										<?php echo $this->Form->button('<span class="glyphicon glyphicon-thumbs-up">' . $b2bsCurrentComments['likesNum'] . '</span>',
												array(
													'type' => 'submit',
													'class' => 'btn btn-link',
													'style' => 'padding: 0;',
													'tooltip' => ($b2bsCurrentComments['likesFlag'])? __d('b2bses', 'Remove likes') : __d('b2bses', 'Do likes'),
												)); ?>
										&nbsp;
								<?php echo $this->Form->end(); ?>

							<!-- visitor -->
							<?php else : ?>
								<span class="glyphicon glyphicon-thumbs-up"><?php echo $b2bsCurrentComments['likesNum']; ?></span>
								&nbsp;
							<?php endif; ?>
						</span>
					<?php endif; ?>
					<?php if ($b2bses['use_unlike_button']) : ?>
						<!-- よくないね -->
						<span class="text-left" style="float:left;">
							<!-- general user以上 -->
							<?php if ((int)$this->viewVars['rolesRoomId'] !== 0 && (int)$this->viewVars['rolesRoomId'] < 5) : ?>
								<?php $unlikesParams = ($b2bsCurrentComments['unlikesFlag'])? 0 : 1; ?>
								<?php echo $this->Form->create('',
										array(
											'type' => 'post',
											'url' => '/b2bses/b2bsPosts/unlikes/' . $frameId . '/' . $b2bsCurrentComments['id'] . '/' . $userId . '/' . $unlikesParams,
										)); ?>

										<?php echo $this->Form->button('<span class="glyphicon glyphicon-thumbs-up">' . $b2bsCurrentComments['unlikesNum'] . '</span>',
												array(
													'type' => 'submit',
													'class' => 'btn btn-link',
													'style' => 'padding: 0;',
													'tooltip' => ($b2bsCurrentComments['unlikesFlag'])? __d('b2bses', 'Remove unlikes') : __d('b2bses', 'Do unlikes'),
												)); ?>
								<?php echo $this->Form->end(); ?>

							<!-- visitor -->
							<?php else : ?>
								<span class="glyphicon glyphicon-thumbs-up"><?php echo $b2bsCurrentComments['unlikesNum']; ?></span>
							<?php endif; ?>
						</span>
					<?php endif; ?>
				<?php endif; ?>
				&nbsp;
			</span>

			<!-- 右に表示 -->
			<?php if (($contentCreatable && $b2bsCurrentComments['created_user'] === $userId
							&& $b2bsCurrentComments['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED) ||
						($contentEditable
							&& $b2bsCurrentComments['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED) ||
						$contentPublishable) : ?>

				<!-- 削除 -->
				<span class="text-left" style="float:right;">
					<?php echo $this->Form->create('',
						array(
							'div' => false,
							'type' => 'post',
							'url' => '/b2bses/b2bsComments/delete/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $b2bsCurrentComments['id']
						)); ?>

						<?php echo $this->Form->button('<span class="glyphicon glyphicon-trash"></span>',
								array(
									'label' => false,
									'div' => false,
									'type' => 'submit',
									'class' => 'btn btn-danger btn-xs',
									'tooltip' => __d('b2bses', 'Delete'),
								)); ?>
					<?php echo $this->Form->end(); ?>
				</span>

				<!-- 編集 -->
				<span class="text-left" style="float:right;">
					<a href="<?php echo $this->Html->url(
							'/b2bses/b2bsComments/edit' . '/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $b2bsCurrentComments['id']); ?>"
							class="btn btn-primary btn-xs" tooltip="<?php echo __d('b2bses', 'Edit'); ?>">
							<span class="glyphicon glyphicon-edit"></span>
					</a>
					&ensp;
				</span>
			<?php endif; ?>

			<!-- Warn:style 一行に表示するため指定 -->
			<span class="text-left" style="float:right;">
				<?php if ($commentCreatable && $b2bses['use_comment']
							&& $b2bsCurrentComments['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>

					<?php echo $this->Form->create('',
								array(
									'div' => false,
									'type' => 'get',
									'url' => '/b2bses/b2bsComments/add/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $b2bsCurrentComments['id']
								)); ?>

						<label>
							<!-- 引用するか否か -->
							<?php echo $this->Form->input('quotFlag',
										array(
											'label' => false,
											'div' => false,
											'type' => 'checkbox',
										)); ?>
							<?php echo __d('b2bses', 'Quote this posts'); ?>
						</label>
						&nbsp;

						<?php echo $this->Form->button('<span class="glyphicon glyphicon-comment"></span>',
								array(
									'label' => false,
									'div' => false,
									'type' => 'submit',
									'class' => 'btn btn-success btn-xs',
									'tooltip' => __d('b2bses', 'Write comment'),
								)); ?>
						&ensp;

					<?php echo $this->Form->end(); ?>
				<?php endif; ?>

				<!-- 承認ボタン -->
				<?php if ($b2bsCurrentComments['status'] === NetCommonsBlockComponent::STATUS_APPROVED &&
							$contentPublishable) : ?>
					<!-- 承認するボタン -->
					<?php echo $this->element('approving_buttons', array(
										'parentId' => $b2bsPosts['id'],
										'comment' => $b2bsCurrentComments
						)); ?>
					&emsp;
				<?php endif; ?>
			</span>
		</div>
	</div>
</div>

<?php if ($b2bsCurrentComments['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>

		<!-- 記事が公開されていない場合 -->
		<div class="col-md-offset-1 col-xs-offset-1">
			<hr />
			<?php echo __d('b2bses', 'This comments has not yet been published'); ?>
		</div>

<?php elseif (! $b2bsComments) : ?>

		<!-- コメントがない場合 -->
		<div class="col-md-offset-1 col-xs-offset-1">
			<hr />
			<?php echo __d('b2bses', 'There are not comments'); ?>
		</div>

<?php else : ?>

		<!-- 全体の段落下げ -->
		<?php foreach ($b2bsComments as $comment) { ?>
		<div class="panel-group col-md-offset-1 col-md-offset-1 col-xs-offset-1 col-sm-13 col-sm-13 col-xs-13">
			<div class="panel panel-default">
				<div class="panel-heading">

					<span class="text-left">
						<!-- id -->
						<?php echo $comment['comment_index'] . '.'; ?>
						<span><?php echo $this->Html->image('/b2bses/img/avatar.PNG', array('alt' => 'アバターが設定されていません')); ?></span>
						<!-- ユーザ情報 -->
						<span>
							<a href="" ng-click="user.showUser(<?php echo $comment['userId'] ?>)">
								<?php echo $comment['username']; ?>
							</a>
						</span>
						<!-- タイトル -->
						<a href="<?php echo $this->Html->url(
										'/b2bses/b2bsComments/view' . '/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $comment['id']); ?>">
										<h4 style="display:inline;"><strong><?php echo $comment['title']; ?></strong></h4></a>
						<!-- ステータス -->
						<span><?php echo $this->element('B2bses.comment_status_label',
											array('status' => $comment['status'])); ?></span>
					</span>

					<span class="text-left" style="float:right;">
						<!-- 時間 -->
						<span><?php echo $this->Date->dateFormat($comment['created']); ?></span>
					</span>

				</div>
				<!-- 本文 -->
				<div class="panel panel-body">
					<?php if ($b2bsCurrentComments['id'] !== $comment['parent_id']) : ?>
						<div><a href="<?php echo $this->Html->url(
									'/b2bses/b2bsComments/view' . '/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $comment['parent_id']); ?>">
								<?php echo '>>' . $comment['parent_comment_index']; ?></a></div>
					<?php endif; ?>
					<div><?php echo $comment['content']; ?></div>
				</div>
				<!-- フッター -->
				<div class="panel-footer">
					<!-- いいね！ -->
					<span class="text-left">
						<?php if ($comment['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>
							<?php if ($b2bses['use_like_button']) : ?>
								<!-- いいね -->
								<span class="text-left" style="float:left;">
									<!-- general user以上 -->
									<?php if ((int)$this->viewVars['rolesRoomId'] !== 0 && (int)$this->viewVars['rolesRoomId'] < 5) : ?>
										<?php $likesParams = ($comment['likesFlag'])? 0 : 1; ?>
										<?php echo $this->Form->create('',
												array(
													'type' => 'post',
													'url' => '/b2bses/b2bsPosts/likes/' . $frameId . '/' . $comment['id'] . '/' . $userId . '/' . $likesParams,
												)); ?>

												<?php echo $this->Form->button('<span class="glyphicon glyphicon-thumbs-up">' . $comment['likesNum'] . '</span>',
														array(
															'type' => 'submit',
															'class' => 'btn btn-link',
															'style' => 'padding: 0;',
															'tooltip' => ($comment['likesFlag'])? __d('b2bses', 'Remove likes') : __d('b2bses', 'Do likes'),
														)); ?>
												&nbsp;
										<?php echo $this->Form->end(); ?>

									<!-- visitor -->
									<?php else : ?>
										<span class="glyphicon glyphicon-thumbs-up"><?php echo $comment['likesNum']; ?></span>
										&nbsp;
									<?php endif; ?>
								</span>
							<?php endif; ?>
							<?php if ($b2bses['use_unlike_button']) : ?>
								<!-- よくないね -->
								<span class="text-left" style="float:left;">
									<!-- general user以上 -->
									<?php if ((int)$this->viewVars['rolesRoomId'] !== 0 && (int)$this->viewVars['rolesRoomId'] < 5) : ?>
										<?php $unlikesParams = ($comment['unlikesFlag'])? 0 : 1; ?>
										<?php echo $this->Form->create('',
												array(
													'type' => 'post',
													'url' => '/b2bses/b2bsPosts/unlikes/' . $frameId . '/' . $comment['id'] . '/' . $userId . '/' . $unlikesParams,
												)); ?>

												<?php echo $this->Form->button('<span class="glyphicon glyphicon-thumbs-up">' . $comment['unlikesNum'] . '</span>',
														array(
															'type' => 'submit',
															'class' => 'btn btn-link',
															'style' => 'padding: 0;',
															'tooltip' => ($comment['unlikesFlag'])? __d('b2bses', 'Remove unlikes') : __d('b2bses', 'Do unlikes'),
														)); ?>
										<?php echo $this->Form->end(); ?>

									<!-- visitor -->
									<?php else : ?>
										<span class="glyphicon glyphicon-thumbs-up"><?php echo $comment['unlikesNum']; ?></span>
									<?php endif; ?>
								</span>
							<?php endif; ?>
						<?php endif; ?>
						&nbsp;
					</span>

					<span class="text-left" style="float:right;">
						<!-- コメント編集/削除 -->
						<?php if (($contentCreatable && $comment['created_user'] === $userId
										&& $comment['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED) ||
									($contentEditable
										&& $comment['status'] !== NetCommonsBlockComponent::STATUS_PUBLISHED) ||
									$contentPublishable) : ?>

							<!-- 削除 -->
							<span class="text-left" style="float:right;">
								<?php echo $this->Form->create('',
									array(
										'div' => false,
										'type' => 'post',
										'url' => '/b2bses/b2bsComments/delete/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $b2bsCurrentComments['id'] . '/' . $comment['id']
									)); ?>

									<?php echo $this->Form->button('<span class="glyphicon glyphicon-trash"></span>',
											array(
												'label' => false,
												'div' => false,
												'type' => 'submit',
												'class' => 'btn btn-danger btn-xs',
												'tooltip' => __d('b2bses', 'Delete'),
											)); ?>
								<?php echo $this->Form->end(); ?>
							</span>

							<!-- 編集 -->
							<span class="text-left" style="float:right;">
								<a href="<?php echo $this->Html->url(
										'/b2bses/b2bsComments/edit' . '/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $comment['id']); ?>"
										class="btn btn-primary btn-xs" tooltip="<?php echo __d('b2bses', 'Edit'); ?>">
										<span class="glyphicon glyphicon-edit"></span>
								</a>
								&ensp;
							</span>
						<?php endif; ?>
					</span>

					<!-- コメント作成 -->
					<!-- Warn:style 一行に表示するため指定 -->
					<span class="text-left" style="float:right;">
						<?php if ($commentCreatable && $b2bses['use_comment']
									&& $comment['status'] === NetCommonsBlockComponent::STATUS_PUBLISHED) : ?>

							<?php echo $this->Form->create('',
										array(
											'div' => false,
											'type' => 'get',
											'url' => '/b2bses/b2bsComments/add/' . $frameId . '/' . $b2bsPosts['id'] . '/' . $comment['id']
										)); ?>

								<label>
									<!-- 引用するか否か -->
									<?php echo $this->Form->input('quotFlag',
												array(
													'label' => false,
													'div' => false,
													'type' => 'checkbox',
												)); ?>
									<?php echo __d('b2bses', 'Quote this posts'); ?>
								</label>
								&nbsp;

								<?php echo $this->Form->button('<span class="glyphicon glyphicon-comment"></span>',
										array(
											'label' => false,
											'div' => false,
											'type' => 'submit',
											'class' => 'btn btn-success btn-xs',
											'tooltip' => __d('b2bses', 'Write comment'),
										)); ?>
								&ensp;

							<?php echo $this->Form->end(); ?>
						<?php endif; ?>

						<!-- 承認ボタン -->
						<?php if ($comment['status'] === NetCommonsBlockComponent::STATUS_APPROVED &&
									$contentPublishable) : ?>
							<!-- 承認するボタン -->
							<?php echo $this->element('approving_buttons', array(
												'parentId' => $b2bsPosts['id'],
												'comment' => $comment
								)); ?>
							&emsp;
						<?php endif; ?>
					</span>
				</div>
			</div>
		</div>
		<?php } ?>

		<!-- ページャーの表示 -->
		<div class="text-center">
			<?php echo $this->element('B2bsComments/pager'); ?>
		</div>

<?php endif; ?>

</div>