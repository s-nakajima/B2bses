<?php
/**
 * B2bsPosts Controller
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
class B2bsPostsController extends B2bsesAppController {

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
				'contentEditable' => array('add', 'edit', 'delete', 'likes', 'unlikes'),
				'contentCreatable' => array('add', 'edit', 'delete', 'likes', 'unlikes'),
			),
		),
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.Token',
	);

/**
 * view method
 *
 * @param int $frameId frames.id
 * @param int $postId posts.id
 * @param int $currentPage currentPage
 * @param int $sortParams sortParameter
 * @param int $visibleCommentRow visibleCommentRow
 * @param int $narrowDownParams narrowDownParameter
 * @throws BadRequestException throw new
 * @return void
 */
	public function view($frameId, $postId = '', $currentPage = '', $sortParams = '',
							$visibleCommentRow = '', $narrowDownParams = '') {
		if (! $postId) {
			BadRequestException(__d('net_commons', 'Bad Request'));
		}

		if ($this->request->isGet()) {
			CakeSession::write('backUrl', $this->request->referer());
		}

		//コメント表示数/掲示板名等をセット
		$this->setB2bs();

		//選択した記事をセット
		$this->__setPost($postId);

		//各パラメータをセット
		$this->initParams($currentPage, $sortParams, $narrowDownParams);

		//表示件数をセット
		$visibleCommentRow =
			($visibleCommentRow === '')? $this->viewVars['b2bsSettings']['visible_comment_row'] : $visibleCommentRow;
		$this->set('currentVisibleRow', $visibleCommentRow);

		//Treeビヘイビアのlft,rghtカラムを利用して対象記事のコメントのみ取得
		$conditions['and']['lft >'] = $this->viewVars['b2bsPosts']['lft'];
		$conditions['and']['rght <'] = $this->viewVars['b2bsPosts']['rght'];
		//記事に関するコメントをセット
		$this->setComment($conditions);

		//Treeビヘイビアのlft,rghtカラムを利用して対象記事のコメントのみ取得
		$conditions['and']['lft >'] = $this->viewVars['b2bsPosts']['lft'];
		$conditions['and']['rght <'] = $this->viewVars['b2bsPosts']['rght'];
		//ページング情報取得
		$this->setPagination($conditions, $postId);

		//コメント数をセットする
		$this->setCommentNum(
				$this->viewVars['b2bsPosts']['lft'],
				$this->viewVars['b2bsPosts']['rght']
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

		//既読情報を登録
		$this->__saveReadStatus($postId);
	}

/**
 * add method
 *
 * @param int $frameId frames.id
 * @return void
 */
	public function add($frameId) {
		//掲示板名を取得
		$this->setB2bs();

		//記事初期データを取得
		$this->__initPost();

		if ($this->request->isGet()) {
			CakeSession::write('backUrl', $this->request->referer());
		}

		if (! $this->request->isPost()) {
			return;
		}

		if (! $status = $this->parseStatus()) {
			return;
		}

		$data = $this->setAddSaveData($this->data, $status);

		if (! $this->B2bsPost->savePost($data)) {
			if (!$this->handleValidationError($this->B2bsPost->validationErrors)) {
				return;
			}
		}

		if (! $this->request->is('ajax')) {
			$this->redirectBackUrl();
		}
	}

/**
 * edit method
 *
 * @param int $frameId frames.id
 * @param int $postId b2bsPosts.id
 * @return void
 */
	public function edit($frameId, $postId) {
		//掲示板名を取得
		$this->setB2bs();

		//編集する記事を取得
		$this->__setPost($postId);

		if ($this->request->isGet()) {
			CakeSession::write('backUrl', $this->request->referer());
		}

		if (! $this->request->isPost()) {
			return;
		}

		if (! $data = $this->setEditSaveData($this->data, $postId)) {
			return;
		}

		if (! $this->B2bsPost->savePost($data)) {
			if (! $this->handleValidationError($this->B2bsPost->validationErrors)) {
				return;
			}
		}

		if (! $this->request->is('ajax')) {
			$this->redirectBackUrl();
		}
	}

/**
 * delete method
 *
 * @param int $frameId frames.id
 * @param int $postId postId
 * @throws BadRequestException
 * @return void
 */
	public function delete($frameId, $postId) {
		//確認ダイアログ経由

		if (! $this->request->isPost()) {
			return;
		}

		if ($this->B2bsPost->delete($postId)) {
			//記事一覧へリダイレクト
			$this->redirect(array(
					'controller' => 'b2bses',
					'action' => 'view',
					$frameId,
				));
		}

		throw new BadRequestException(__d('net_commons', 'Bad Request'));
	}

/**
 * likes method
 *
 * @param int $frameId frames.id
 * @param int $postId b2bsPosts.id
 * @param int $userId users.id
 * @param bool $likesFlag likes flag
 * @return void
 */
	public function likes($frameId, $postId, $userId, $likesFlag) {
		if (! $this->request->isPost()) {
			return;
		}

		CakeSession::write('backUrl', $this->request->referer());

		if (! $postsUsers = $this->B2bsPostsUser->getPostsUsers(
				$postId,
				$userId
		)) {
			//データがなければ登録
			$default = $this->B2bsPostsUser->create();
			$default['B2bsPostsUser'] = array(
						'post_id' => $postId,
						'user_id' => $userId,
						'likes_flag' => (int)$likesFlag,
				);
			$this->B2bsPostsUser->savePostsUsers($default);

		} else {
			$postsUsers['B2bsPostsUser']['likes_flag'] = (int)$likesFlag;
			$this->B2bsPostsUser->savePostsUsers($postsUsers);

		}
		$backUrl = CakeSession::read('backUrl');
		CakeSession::delete('backUrl');
		$this->redirect($backUrl);
	}

/**
 * unlikes method
 *
 * @param int $frameId frames.id
 * @param int $postId b2bsPosts.id
 * @param int $userId users.id
 * @param bool $unlikesFlag unlikes flag
 * @return void
 */
	public function unlikes($frameId, $postId, $userId, $unlikesFlag) {
		if (! $this->request->isPost()) {
			return;
		}

		CakeSession::write('backUrl', $this->request->referer());

		if (! $postsUsers = $this->B2bsPostsUser->getPostsUsers(
				$postId,
				$userId
		)) {
			//データがなければ登録
			$default = $this->B2bsPostsUser->create();
			$default['B2bsPostsUser'] = array(
						'post_id' => $postId,
						'user_id' => $userId,
						'unlikes_flag' => (int)$unlikesFlag,
				);
			$this->B2bsPostsUser->savePostsUsers($default);

		} else {
			$postsUsers['B2bsPostsUser']['unlikes_flag'] = (int)$unlikesFlag;
			$this->B2bsPostsUser->savePostsUsers($postsUsers);

		}
		$backUrl = CakeSession::read('backUrl');
		CakeSession::delete('backUrl');
		$this->redirect($backUrl);
	}

/**
 * __initPost method
 *
 * @return void
 */
	private function __initPost() {
		//新規記事データセット
		$b2bsPosts = $this->B2bsPost->create();

		//新規の記事名称
		$b2bsPosts['B2bsPost']['title'] = '新規記事_' . date('YmdHis');

		$comments = $this->Comment->getComments(
			array(
				'plugin_key' => 'b2bsPosts',
				'content_key' => isset($b2bsPosts['B2bsPost']['key']) ? $b2bsPosts['B2bsPost']['key'] : null,
			)
		);
		$results['comments'] = $comments;
		$results = $this->camelizeKeyRecursive($results);
		$results['b2bsPosts'] = $b2bsPosts['B2bsPost'];
		$results['contentStatus'] = null;
		$this->set($results);
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

		//評価を取得
		$likes = $this->B2bsPostsUser->getLikes(
					$b2bsPosts['B2bsPost']['id'],
					$this->viewVars['userId']
				);

		//取得した記事の作成者IDからユーザ情報を取得
		$user = $this->User->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'id' => $b2bsPosts['B2bsPost']['created_user'],
				)
			)
		);

		$comments = $this->Comment->getComments(
			array(
				'plugin_key' => 'b2bsPosts',
				'content_key' => isset($b2bsPosts['B2bsPost']['key']) ? $b2bsPosts['B2bsPost']['key'] : null,
			)
		);
		$results['comments'] = $comments;
		$results = $this->camelizeKeyRecursive($results);
		$results['b2bsPosts'] = $b2bsPosts['B2bsPost'];
		$results['contentStatus'] = $b2bsPosts['B2bsPost']['status'];

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
 * __saveReadStatus method
 *
 * @param int $postId b2bsPosts.id
 * @return void
 */
	private function __saveReadStatus($postId) {
		//既読情報がなければデータ登録
		if (! $this->B2bsPostsUser->getPostsUsers(
				$postId,
				$this->viewVars['userId']
		)) {
			$default = $this->B2bsPostsUser->create();
			$default['B2bsPostsUser'] = array(
						'post_id' => $postId,
						'user_id' => $this->viewVars['userId'],
						'likes_flag' => false,
						'unlikes_flag' => false,
				);
			$this->B2bsPostsUser->savePostsUsers($default);
		}
	}
}
