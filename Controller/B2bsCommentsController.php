<?php
/**
 * B2bsComments Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('B2bsesAppController', 'B2bses.Controller');

/**
 * B2bses Controller
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\B2bses\Controller
 */
class B2bsCommentsController extends B2bsesAppController {

/**
 * use helpers
 *
 * @var array
 */
	public $useTable = array(
		'b2bs_posts'
	);

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'Users.User',
		'B2bses.B2bs',
		'B2bses.B2bsFrameSetting',
		'B2bses.B2bsPost',
		'B2bses.B2bsPostsUser',
		'Comments.Comment',
	);

/**
 * use components
 *
 * @var array
 */
	public $components = array(
		'NetCommons.NetCommonsBlock',
		'NetCommons.NetCommonsFrame',
		'NetCommons.NetCommonsRoomRole' => array(
			//コンテンツの権限設定
			'allowedActions' => array(
				'contentEditable' => array('add', 'edit', 'delete'),
				'contentCreatable' => array('add', 'edit', 'delete'),
			),
		),
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.Token'
	);

/**
 * view method
 *
 * @param int $frameId frames.id
 * @param int $postId b2bsPosts.id
 * @param int $commentId b2bsPosts.id
 * @param int $currentPage currentPage
 * @param int $sortParams sortParameter
 * @param int $visibleCommentRow visibleCommentRow
 * @param int $narrowDownParams narrowDownParameter
 * @throws BadRequestException throw new
 * @return void
 */
	public function view($frameId, $postId = '', $commentId = '', $currentPage = '',
				$sortParams = '', $visibleCommentRow = '', $narrowDownParams = '') {
		if (! $postId || ! $commentId) {
			BadRequestException(__d('net_commons', 'Bad Request'));
		}

		if ($this->request->isGet()) {
			CakeSession::write('backUrl', $this->request->referer());
		}

		//コメント表示数/掲示板名等をセット
		$this->setB2bs();

		//親記事情報をセット
		$this->__setPost($postId);

		//選択したコメントをセット
		$this->__setCurrentComment($commentId);

		//各パラメータをセット
		$this->initParams($currentPage, $sortParams, $narrowDownParams);

		//表示件数をセット
		$visibleCommentRow =
			($visibleCommentRow === '')? $this->viewVars['b2bsSettings']['visible_comment_row'] : $visibleCommentRow;
		$this->set('currentVisibleRow', $visibleCommentRow);

		//Treeビヘイビアのlft,rghtカラムを利用して対象記事のコメントのみ取得
		$conditions['and']['lft >'] = $this->viewVars['b2bsCurrentComments']['lft'];
		$conditions['and']['rght <'] = $this->viewVars['b2bsCurrentComments']['rght'];
		//レスデータをセット
		$this->setComment($conditions);

		//Treeビヘイビアのlft,rghtカラムを利用して対象記事のコメントのみ取得
		$conditions['and']['lft >'] = $this->viewVars['b2bsCurrentComments']['lft'];
		$conditions['and']['rght <'] = $this->viewVars['b2bsCurrentComments']['rght'];
		//ページング情報取得
		$this->setPagination($conditions, $commentId);

		//コメント数をセットする
		$this->setCommentNum(
				$this->viewVars['b2bsCurrentComments']['lft'],
				$this->viewVars['b2bsCurrentComments']['rght']
			);

		//コメント作成権限をセットする
		//$this->setCommentCreateAuth();
		if (((int)$this->viewVars['rolesRoomId'] !== 0 &&
				(int)$this->viewVars['rolesRoomId'] < 4) ||
				($this->viewVars['b2bses']['comment_create_authority'] &&
				$this->viewVars['contentCreatable'])) {

			$this->set('commentCreatable', true);

		} else {
			$this->set('commentCreatable', false);

		}
	}

/**
 * view method
 *
 * @param int $frameId frames.id
 * @param int $parentId b2bsPosts.id
 * @param int $postId b2bsPosts.id
 * @return void
 */
	public function add($frameId, $parentId, $postId) {
		//引用フラグをURLパラメータからセット
		$this->set('quotFlag', $this->params->query['quotFlag']);

		$this->setB2bs();

		//根記事をセット
		$this->__setPost($parentId);

		//対象のコメントをセット
		$this->__setCurrentComment($postId);

		$this->__initComment();

		if ($this->request->isGet()) {
			CakeSession::write('backUrl', $this->request->referer());
		}

		if (! $this->request->isPost()) {
			return;
		}

		if (! $status = $this->parseStatus()) {
			return;
		}

		$data = $this->setAddSaveData($this->data, $status, $parentId);

		if (! $this->B2bsPost->saveComment($data)) {
			if (!$this->handleValidationError($this->B2bsPost->validationErrors)) {
				return;
			}
		}

		//根記事の公開中のコメント数更新
		if (! $this->__updateCommentNum($data['B2bs']['key'], $parentId)) {
			return;
		}

		//根記事のコメント番号更新
		if (! $this->__updateCommentIndex($data['B2bs']['key'], $parentId)) {
			return;
		}

		if (! $this->request->is('ajax')) {
			$this->redirectBackUrl();
		}
	}

/**
 * edit method
 *
 * @param int $frameId frames.id
 * @param int $parentId b2bsPosts.id
 * @param int $postId b2bsPosts.id
 * @param bool $isApproval true is approving action, false is normal edit action.
 * @return void
 */
	public function edit($frameId, $parentId, $postId, $isApproval = 0) {
		$this->setB2bs();

		//根記事をセット
		$this->__setPost($parentId);

		//対象のコメントをセット
		$this->__setCurrentComment($postId);

		if ($this->request->isGet() || (int)$isApproval) {
			CakeSession::write('backUrl', $this->request->referer());
		}

		if (! $this->request->isPost()) {
			return;
		}

		if (! $data = $this->setEditSaveData($this->data, $postId)) {
			return;
		}

		if (! $this->B2bsPost->saveComment($data)) {
			if (! $this->handleValidationError($this->B2bsPost->validationErrors)) {
				return;
			}
		}

		//親記事の公開中のコメント数更新
		if (! $this->__updateCommentNum($data['B2bs']['key'], $parentId)) {
			return;
		}

		if (!$this->request->is('ajax')) {
			$this->redirectBackUrl();
		}
	}

/**
 * delete method
 *
 * @param int $frameId frames.id
 * @param int $postId b2bsPosts.id
 * @param int $parentId b2bsPosts.id
 * @param int $commentId b2bsPosts.id
 * @throws BadRequestException
 * @return void
 */
	public function delete($frameId, $postId, $parentId, $commentId = '') {
		//b2bses.keyをセット
		$this->setB2bs();

		//確認ダイアログ経由

		if (! $this->request->isPost()) {
			return;
		}

		if ($this->B2bsPost->delete(($commentId)? $commentId : $parentId)) {
			//根記事の公開中のコメント数更新
			$this->__updateCommentNum($this->viewVars['b2bses']['key'], $postId);

			//記事一覧orコメント一覧へリダイレクト
			$this->redirect(array(
				'controller' => ($commentId)? 'b2bsComments' : 'b2bsPosts',
				'action' => 'view',
				$frameId,
				$postId,
				($commentId)? $parentId : '',
			));
		}

		throw new BadRequestException(__d('net_commons', 'Bad Request'));
	}

/**
 * __setPost method
 *
 * @param int $postId b2bsPosts.id
 * @throws BadRequestException
 * @return void
 */
	private function __setPost($postId) {
		$conditions['b2bs_key'] = $this->viewVars['b2bses']['key'];
		$conditions['id'] = $postId;

		if (! $b2bsPosts = $this->B2bsPost->getOnePosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$conditions
		)) {
			throw new BadRequestException(__d('net_commons', 'Bad Request'));

		}

		//取得した記事の作成者IDからユーザ情報を取得
		$user = $this->User->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $b2bsPosts['B2bsPost']['created_user'],
				)
			)
		);

		//評価を取得
		$likes = $this->B2bsPostsUser->getLikes(
					$b2bsPosts['B2bsPost']['id'],
					$this->viewVars['userId']
				);

		$results = array(
			'b2bsPosts' => $b2bsPosts['B2bsPost'],
			'contentStatus' => $b2bsPosts['B2bsPost']['status']
		);

		//ユーザ名、ID、評価をセット
		$results['b2bsPosts']['username'] = $user['User']['username'];
		$results['b2bsPosts']['userId'] = $user['User']['id'];
		$results['b2bsPosts']['likesNum'] = $likes['likesNum'];
		$results['b2bsPosts']['unlikesNum'] = $likes['unlikesNum'];
		$results['b2bsPosts']['likesFlag'] = $likes['likesFlag'];
		$results['b2bsPosts']['unlikesFlag'] = $likes['unlikesFlag'];
		$this->set($results);
	}

/**
 * __initPost method
 *
 * @return void
 */
	private function __initComment() {
		//新規記事データセット
		$comment = $this->B2bsPost->create();

		//新規の記事名称
		$comment['B2bsPost']['title'] = '新規コメント_' . date('YmdHis');

		$results = array(
				'b2bsComments' => $comment['B2bsPost'],
				'contentStatus' => null,
			);
		$this->set($results);
	}

/**
 * Set current comment method
 *
 * @param int $postId b2bsPosts.id
 * @throws BadRequestException
 * @return void
 */
	private function __setCurrentComment($postId) {
		$conditions['b2bs_key'] = $this->viewVars['b2bses']['key'];
		$conditions['id'] = $postId;

		if (! $currentPosts = $this->B2bsPost->getOnePosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$conditions
		)) {
			throw new BadRequestException(__d('net_commons', 'Bad Request'));

		}

		$conditions = '';
		$conditions['b2bs_key'] = $this->viewVars['b2bses']['key'];
		$conditions['id'] = $currentPosts['B2bsPost']['parent_id'];
		//親記事の記事取得
		$parentPosts = $this->B2bsPost->getOnePosts(
			$this->viewVars['userId'],
			$this->viewVars['contentEditable'],
			$this->viewVars['contentCreatable'],
			$conditions
		);

		//評価を取得
		$likes = $this->B2bsPostsUser->getLikes(
					$currentPosts['B2bsPost']['id'],
					$this->viewVars['userId']
				);

		//取得した記事の作成者IDからユーザ情報を取得
		$user = $this->User->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $currentPosts['B2bsPost']['created_user'],
				)
			)
		);

		$results = array(
			'b2bsCurrentComments' => $currentPosts['B2bsPost'],
			'currentCommentStatus' => $currentPosts['B2bsPost']['status']
		);
		//取得した情報を配列に追加
		$results['b2bsCurrentComments']['parent_comment_index'] = $parentPosts['B2bsPost']['comment_index'];
		$results['b2bsCurrentComments']['username'] = $user['User']['username'];
		$results['b2bsCurrentComments']['userId'] = $user['User']['id'];
		$results['b2bsCurrentComments']['likesNum'] = $likes['likesNum'];
		$results['b2bsCurrentComments']['unlikesNum'] = $likes['unlikesNum'];
		$results['b2bsCurrentComments']['likesFlag'] = $likes['likesFlag'];
		$results['b2bsCurrentComments']['unlikesFlag'] = $likes['unlikesFlag'];
		$this->set($results);
	}

/**
 * updateParentPosts
 *
 * @param string $b2bsKey b2bses.key
 * @param int $parentId b2bsPosts.id
 * @return true is save successful, or false is failure
 */
	private function __updateCommentNum($b2bsKey, $parentId) {
		//親記事(lft,rghtカラム)取得
		$conditions['b2bs_key'] = $b2bsKey;
		$conditions['id'] = $parentId;
		$parentPosts = $this->B2bsPost->getOnePosts(
				false,
				false,
				false,
				$conditions
			);

		//条件初期化
		$conditions = null;

		//コメント一覧取得
		$conditions['b2bs_key'] = $b2bsKey;
		$conditions['and']['lft >'] = $parentPosts['B2bsPost']['lft'];
		$conditions['and']['rght <'] = $parentPosts['B2bsPost']['rght'];
		//公開されているコメントを取得
		if (! $b2bsComments = $this->B2bsPost->getPosts(
				false,
				false,
				false,
				false,
				false,
				false,
				$conditions
		)) {
			$parentPosts['B2bsPost']['comment_num'] = 0;

		} else {
			$parentPosts['B2bsPost']['comment_num'] = count($b2bsComments);

		}

		$parentPosts['B2bs']['key'] = $b2bsKey;
		$parentPosts['Comment']['comment'] = '';

		if (! $this->B2bsPost->savePost($parentPosts)) {
			if (! $this->handleValidationError($this->B2bsPost->validationErrors)) {
				return false;
			}
		}

		return true;
	}

/**
 * updateParentPosts
 *
 * @param string $b2bsKey b2bses.key
 * @param int $parentId b2bsPosts.id
 * @return true is save successful, or false is failure
 */
	private function __updateCommentIndex($b2bsKey, $parentId) {
		//親記事(lft,rghtカラム)取得
		$conditions['b2bs_key'] = $b2bsKey;
		$conditions['id'] = $parentId;
		$parentPosts = $this->B2bsPost->getOnePosts(
				false,
				false,
				false,
				$conditions
			);

		$parentPosts['B2bsPost']['comment_index'] = ++$parentPosts['B2bsPost']['comment_index'];
		$parentPosts['B2bs']['key'] = $b2bsKey;
		$parentPosts['Comment']['comment'] = '';

		if (! $this->B2bsPost->savePost($parentPosts)) {
			if (! $this->handleValidationError($this->B2bsPost->validationErrors)) {
				return false;
			}
		}

		return true;
	}
}
