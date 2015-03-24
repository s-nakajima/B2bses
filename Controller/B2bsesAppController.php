<?php
/**
 * B2bsesApp Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppController', 'Controller');

/**
 * B2bsesApp Controller
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\B2bses\Controller
 */
class B2bsesAppController extends AppController {

/**
 * use component
 *
 * @var array
 */
	public $components = array(
		'Security'
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.Date',
	);

/**
 * redirectBackUrl
 *
 * @throws BadRequestException
 * @return mixed status on success, false on error
 */
	public function redirectBackUrl() {
		$backUrl = CakeSession::read('backUrl');
		CakeSession::delete('backUrl');
		$this->redirect($backUrl);
	}

/**
 * Parse content status from request
 *
 * @throws BadRequestException
 * @return mixed status on success, false on error
 */
	public function parseStatus() {
		if ($matches = preg_grep('/^save_\d/', array_keys($this->data))) {
			list(, $status) = explode('_', array_shift($matches));
		} else {
			if ($this->request->is('ajax')) {
				$this->renderJson(
					['error' => ['validationErrors' => ['status' => __d('net_commons', 'Invalid request.')]]],
					__d('net_commons', 'Bad Request'), 400
				);
			} else {
				throw new BadRequestException(__d('net_commons', 'Bad Request'));
			}
			return false;
		}
		return $status;
	}

/**
 * setAddSaveData
 *
 * @param array $postData post data
 * @param int $status b2bsPosts.status
 * @param int $parentId b2bsPosts.id
 * @return array
 */
	public function setAddSaveData($postData, $status, $parentId = '') {
		$data = Hash::merge(
			$postData,
			['B2bsPost' => ['status' => $status]]
		);

		//新規登録のため、データ生成
		$comment = $this->B2bsPost->create(['key' => Security::hash('b2bsPost' . mt_rand() . microtime(), 'md5')]);

		$comment['B2bsPost']['b2bs_key'] = $data['B2bs']['key'];

		if ($parentId) {
			//コメント番号取得のために親記事情報取得
			$conditions['b2bs_key'] = $data['B2bs']['key'];
			$conditions['id'] = $parentId;
			$parentPosts = $this->B2bsPost->getOnePosts(
					false,
					false,
					false,
					$conditions
				);

			$comment['B2bsPost']['comment_index'] = $parentPosts['B2bsPost']['comment_index'];
		}

		return Hash::merge($comment, $data);
	}

/**
 * setEditSaveData
 *
 * @param array $postData post data
 * @param int $postId b2bsPosts.id
 * @return array
 */
	public function setEditSaveData($postData, $postId) {
		if (! $status = $this->parseStatus()) {
			return false;
		}

		$data = Hash::merge(
			$postData,
			['B2bsPost' => ['status' => $status]]
		);

		//編集データ取得
		$conditions['b2bs_key'] = $data['B2bs']['key'];
		$conditions['id'] = $postId;
		$b2bsPosts = $this->B2bsPost->getOnePosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$conditions
			);

		//更新時間をセット
		$b2bsPosts['B2bsPost']['modified'] = date('Y-m-d H:i:s');

		$results = Hash::merge($b2bsPosts, $data);

		//UPDATE
		$results['B2bsPost']['id'] = $b2bsPosts['B2bsPost']['id'];

		return $results;
	}

/**
 * Handle validation error
 *
 * @param array $errors validation errors
 * @return bool true on success, false on error
 */
	public function handleValidationError($errors) {
		if (is_array($errors)) {
			$this->validationErrors = $errors;
			if ($this->request->is('ajax')) {
				$results = ['error' => ['validationErrors' => $errors]];
				$this->renderJson($results, __d('net_commons', 'Bad Request'), 400);
			}
			return false;
		}
		return true;
	}

/**
 * setB2bs method
 *
 * @return void
 */
	public function setB2bs() {
		//掲示板の表示設定情報を取得
		$b2bsSettings = $this->B2bsFrameSetting->getB2bsSetting(
										$this->viewVars['frameKey']);
		$this->set(array(
			'b2bsSettings' => $b2bsSettings['B2bsFrameSetting'],
		));

		//ログインユーザIDを取得し、Viewにセット
		$this->set('userId', $this->Session->read('Auth.User.id'));

		$blockId = isset($this->viewVars['blockId'])? $this->viewVars['blockId'] : null;

		//掲示板データを取得
		if (! $b2bs = $this->B2bs->getB2bs($blockId)) {
			$b2bs = $this->initB2bs();
		}

		$this->set(array(
			'b2bses' => $b2bs['B2bs']
		));
	}

/**
 * initB2bs method
 *
 * @return void
 */
	public function initB2bs() {
		//掲示板が作成されていない場合
		$b2bs = $this->B2bs->create(['key' => Security::hash('b2bs' . mt_rand() . microtime(), 'md5')]);

		$b2bs['B2bs'] = array(
			'name' => '掲示板_' . date('YmdHis'),
			'use_comment' => true,
			'auto_approval' => false,
			'use_like_button' => true,
			'use_unlike_button' => true,
			'post_create_authority' => true,
			'editor_publish_authority' => false,
			'general_publish_authority' => false,
			'comment_create_authority' => true,
		);

		return $b2bs;
	}

/**
 * setComment method
 *
 * @param array $conditions condition for search
 * @return void
 */
	public function setComment($conditions) {
		//ソート条件をセット
		$sortOrder = $this->setSortOrder($this->viewVars['sortParams']);

		//絞り込み条件をセット
		$conditions = $this->setNarrowDown($conditions, $this->viewVars['narrowDownParams']);

		$conditions['b2bs_key'] = $this->viewVars['b2bses']['key'];

		$b2bsCommnets = $this->B2bsPost->getPosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$sortOrder,									//order by指定
				$this->viewVars['currentVisibleRow'],		//limit指定
				$this->viewVars['currentPage'],				//ページ番号指定
				$conditions
			);

		//コメントなしの場合
		if (empty($b2bsCommnets)) {
			$this->set('b2bsComments', false);
			return;
		}

		foreach ($b2bsCommnets as $b2bsComment) {
			$conditions = '';
			$conditions['b2bs_key'] = $this->viewVars['b2bses']['key'];
			$conditions['id'] = $b2bsComment['B2bsPost']['parent_id'];
			//親記事の記事取得
			$parentPosts = $this->B2bsPost->getOnePosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$conditions
			);

			//評価を取得
			$likes = $this->B2bsPostsUser->getLikes(
						$b2bsComment['B2bsPost']['id'],
						$this->viewVars['userId']
					);

			//取得した記事の作成者IDからユーザ情報を取得
			$user = $this->User->find('first', array(
					'recursive' => -1,
					'conditions' => array(
						'id' => $b2bsComment['B2bsPost']['created_user'],
					)
				)
			);
			//取得した情報を配列に追加
			$b2bsComment['B2bsPost']['parent_comment_index'] = $parentPosts['B2bsPost']['comment_index'];
			$b2bsComment['B2bsPost']['username'] = $user['User']['username'];
			$b2bsComment['B2bsPost']['userId'] = $user['User']['id'];
			$b2bsComment['B2bsPost']['likesNum'] = $likes['likesNum'];
			$b2bsComment['B2bsPost']['unlikesNum'] = $likes['unlikesNum'];
			$b2bsComment['B2bsPost']['likesFlag'] = $likes['likesFlag'];
			$b2bsComment['B2bsPost']['unlikesFlag'] = $likes['unlikesFlag'];
			$results[] = $b2bsComment['B2bsPost'];
		}
		$this->set('b2bsComments', $results);
	}

/**
 * setPagination method
 *
 * @param array $conditions condition for search
 * @param int $postId b2bsPosts.id
 * @return void
 */
	public function setPagination($conditions = '', $postId = '') {
		//ソート条件をセット
		$sortOrder = $this->setSortOrder($this->viewVars['sortParams']);

		if (! $postId) {
			$conditions['parent_id'] = null;
		}
		//取得条件をセット
		$conditions['b2bs_key'] = $this->viewVars['b2bses']['key'];

		//絞り込み条件をセット
		$conditions = $this->setNarrowDown($conditions, $this->viewVars['narrowDownParams']);

		//前のページがあるか取得
		if ($this->viewVars['currentPage'] === 1) {
			$this->set('hasPrevPage', false);

		} else {
			$this->set('hasPrevPage',
					$this->B2bsPost->getPosts(
						$this->viewVars['userId'],
						$this->viewVars['contentEditable'],
						$this->viewVars['contentCreatable'],
						$sortOrder,								//order by指定
						$this->viewVars['currentVisibleRow'],	//limit指定
						$this->viewVars['currentPage'] - 1,		//前のページ番号指定
						$conditions
				)? true : false);
		}

		//次のページがあるか取得
		$this->set('hasNextPage',
				$this->B2bsPost->getPosts(
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$sortOrder,								//order by指定
					$this->viewVars['currentVisibleRow'],	//limit指定
					$this->viewVars['currentPage'] + 1,		//次のページ番号指定
					$conditions
			)? true : false);

		$this->setPaginationRelatedData($sortOrder, $conditions);
	}

/**
 * setPaginationRelatedData method
 *
 * @param array $sortOrder sort order for search
 * @param array $conditions conditions for search
 * @return void
 */
	public function setPaginationRelatedData($sortOrder, $conditions) {
		//2ページ先のページがあるか取得
		$this->set('hasNextSecondPage',
				$this->B2bsPost->getPosts(
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$sortOrder,								//order by指定
					$this->viewVars['currentVisibleRow'],	//limit指定
					$this->viewVars['currentPage'] + 2,		//2ページ先の番号指定
					$conditions
			)? true : false);

		//4ページがあるか取得（モックとしてとりあえず）
		$this->set('hasFourPage',
				$this->B2bsPost->getPosts(
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$sortOrder,								//order by指定
					$this->viewVars['currentVisibleRow'],	//limit指定
					4,										//4ページ先の番号指定
					$conditions
			)? true : false);

		//5ページがあるか取得（モックとしてとりあえず）
		$this->set('hasFivePage',
				$this->B2bsPost->getPosts(
					$this->viewVars['userId'],
					$this->viewVars['contentEditable'],
					$this->viewVars['contentCreatable'],
					$sortOrder,								//order by指定
					$this->viewVars['currentVisibleRow'],	//limit指定
					5,										//5ページ先の番号指定
					$conditions
			)? true : false);
	}

/**
 * initParams method
 *
 * @param int $currentPage currentPage
 * @param int $sortParams sortParameter
 * @param int $narrowDownParams narrowDownParameter
 * @return void
 */
	public function initParams($currentPage = '', $sortParams = '', $narrowDownParams = '') {
		$baseUrl = Inflector::variable($this->plugin) . '/' .
				Inflector::variable($this->name) . '/' . $this->action;
		$this->set('baseUrl', $baseUrl);

		//現在の一覧表示ページ番号をセット
		$currentPage = ($currentPage === '')? 1 : (int)$currentPage;
		$this->set('currentPage', $currentPage);

		//現在のソートパラメータをセット
		$sortParams = ($sortParams === '')? '1' : $sortParams;
		$this->set('sortParams', $sortParams);

		//現在の絞り込みをセット
		$narrowDownParams = ($narrowDownParams === '')? '5' : $narrowDownParams;
		$this->set('narrowDownParams', $narrowDownParams);
	}

/**
 * setCommentNum method
 *
 * @param int $lft b2bsPosts.lft
 * @param int $rght b2bsPosts.rght
 * @return void
 */
	public function setCommentNum($lft, $rght) {
		$conditions['and']['lft >'] = $lft;
		$conditions['and']['rght <'] = $rght;
		if (! $comments = $this->B2bsPost->getPosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				false,
				false,
				false,
				$conditions
		)) {
			$this->set('commentNum', 0);

		} else {
			$this->set('commentNum', count($comments));

		}
	}

/**
 * setSortOrder method
 *
 * @param int $sortParams sortParams
 * @return string order for search
 */
	public function setSortOrder($sortParams) {
		switch ($sortParams) {
		case '1':
		default :
				//最新の投稿順
				$sortStr = __d('b2bses', 'Latest post order');
				$this->set('currentSortOrder', $sortStr);
				return array('B2bsPost.created DESC', 'B2bsPost.title');

		case '2':
				//古い投稿順
				$sortStr = __d('b2bses', 'Older post order');
				$this->set('currentSortOrder', $sortStr);
				return array('B2bsPost.created ASC', 'B2bsPost.title');

		case '3':
				//コメントの多い順
				$sortStr = __d('b2bses', 'Descending order of comments');
				$this->set('currentSortOrder', $sortStr);
				return array('B2bsPost.comment_num DESC', 'B2bsPost.title');

		}
	}

/**
 * setNarrowDown method
 *
 * @param array $conditions find conditions
 * @param int $narrowDownParams narrowDownParams
 * @return array order conditions for narrow down, or void
 */
	public function setNarrowDown($conditions, $narrowDownParams) {
		switch ($narrowDownParams) {
		case '1':
				//公開
				$narrowDownStr = __d('b2bses', 'Published');
				$this->set('narrowDown', $narrowDownStr);
				$conditions['status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
				return $conditions;

		case '2':
				//承認待ち/未承認
				$narrowDownStr = ($this->name !== 'B2bses')? __d('b2bses', 'Disapproval') : __d('net_commons', 'Approving');
				$this->set('narrowDown', $narrowDownStr);
				$conditions['status'] = NetCommonsBlockComponent::STATUS_APPROVED;
				return $conditions;

		case '3':
				//一時保存
				$narrowDownStr = __d('net_commons', 'Temporary');
				$this->set('narrowDown', $narrowDownStr);
				$conditions['status'] = NetCommonsBlockComponent::STATUS_IN_DRAFT;
				return $conditions;

		case '4':
				//差し戻し
				$narrowDownStr = __d('b2bses', 'Remand');
				$this->set('narrowDown', $narrowDownStr);
				$conditions['status'] = NetCommonsBlockComponent::STATUS_DISAPPROVED;
				return $conditions;

		case '5':
		default :
				//全件表示
				$narrowDownStr = __d('b2bses', 'Display all posts');
				$this->set('narrowDown', $narrowDownStr);
				$conditions['and']['status >='] = '1';
				$conditions['and']['status <='] = '4';
				return $conditions;

		case '6':
				//未読
				$narrowDownStr = __d('b2bses', 'Unread');
				$this->set('narrowDown', $narrowDownStr);
				$conditions['and']['status >='] = '1';
				$conditions['and']['status <='] = '4';
				//未読or既読セット中に未読のみ取得する
				return $conditions;
		}
	}

}
