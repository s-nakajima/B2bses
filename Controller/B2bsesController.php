<?php
/**
 * B2bses Controller
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
class B2bsesController extends B2bsesAppController {

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'Frames.Frame',
		'B2bses.B2bs',
		'B2bses.B2bsFrameSetting',
		'B2bses.B2bsPost',
		'B2bses.B2bsPostsUser',
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
				'contentPublishable' => array('edit'),
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
 * index method
 *
 * @param int $frameId frames.id
 * @param int $currentPage currentPage
 * @param int $sortParams sortParameter
 * @param int $visiblePostRow visiblePostRow
 * @param int $narrowDownParams narrowDownParameter
 * @return void
 */
	public function index($frameId, $currentPage = '', $sortParams = '',
								$visiblePostRow = '', $narrowDownParams = '') {
		$this->view = 'B2bses/view';
		$this->view($frameId, $currentPage, $sortParams, $visiblePostRow, $narrowDownParams);
	}

/**
 * index method
 *
 * @param int $frameId frames.id
 * @param int $currentPage currentPage
 * @param int $sortParams sortParameter
 * @param int $visiblePostRow visiblePostRow
 * @param int $narrowDownParams narrowDownParameter
 * @return void
 */
	public function view($frameId, $currentPage = '', $sortParams = '',
								$visiblePostRow = '', $narrowDownParams = '') {
		//一覧ページのURLをBackURLに保持
		if ($this->request->isGet()) {
				CakeSession::write('backUrl', Router::url(null, true));
		}

		//コメント表示数/掲示板名等をセット
		$this->setB2bs();

		//フレーム置いた直後
		if (! isset($this->viewVars['b2bses']['id'])) {
			if ((int)$this->viewVars['rolesRoomId'] === 0) {
				$this->autoRender = false;
				return;
			}
			$this->view = 'B2bses/notCreateB2bs';
			return;
		}

		//各パラメータをセット
		$this->initParams($currentPage, $sortParams, $narrowDownParams);

		//表示件数を設定
		$visiblePostRow = ($visiblePostRow === '')?
				$this->viewVars['b2bsSettings']['visible_post_row'] : $visiblePostRow;
		$this->set('currentVisibleRow', $visiblePostRow);

		//記事一覧情報取得
		$this->__setPost();

		//ページング情報取得
		$this->setPagination();

		//記事数取得
		$this->__setPostNum();
	}

/**
 * edit method
 *
 * @return void
 */
	public function add() {
		$this->view = 'b2bsPosts/edit';
		$this->view();
	}

/**
 * edit method
 *
 * @return void
 */
	public function edit() {
		$this->setB2bs();

		if ($this->request->isGet() &&
				! strstr($this->request->referer(), '/b2bses')) {
			CakeSession::write('backUrl', $this->request->referer());
		}

		if (! $this->request->isPost()) {
			return;
		}

		$data = $this->__setEditSaveData($this->data);

		if (!$b2bs = $this->B2bs->saveB2bs($data)) {
			if (!$this->handleValidationError($this->B2bs->validationErrors)) {
				return;
			}
		}

		$this->set('blockId', $b2bs['B2bs']['block_id']);

		if (! $this->request->is('ajax')) {
			$this->redirectBackUrl();
		}
	}

/**
 * __setPost method
 *
 * @return void
 */
	private function __setPost() {
		//ソート条件をセット
		$sortOrder = $this->setSortOrder($this->viewVars['sortParams']);

		//取得条件をセット
		$conditions['b2bs_key'] = $this->viewVars['b2bses']['key'];
		$conditions['parent_id'] = null;

		//絞り込み条件をセット
		$conditions = $this->setNarrowDown($conditions, $this->viewVars['narrowDownParams']);

		if (! $b2bsPosts = $this->B2bsPost->getPosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				$sortOrder,								//order by指定
				$this->viewVars['currentVisibleRow'],	//limit指定
				$this->viewVars['currentPage'],			//ページ番号指定
				$conditions								//検索条件をセット
		)) {
			$b2bsPosts = $this->B2bsPost->create();
			$results = array(
					'b2bsPosts' => $b2bsPosts['B2bsPost'],
					'b2bsPostNum' => 0,
				);

		} else {
			$results = $this->__setPostRelatedData($b2bsPosts);

		}
		$this->set($results);
	}

/**
 * __setPostRelatedData method
 *
 * @param array $b2bsPosts b2bsPosts
 * @return array
 */
	private function __setPostRelatedData($b2bsPosts) {
		//記事を$results['b2bsPosts']にセット
		foreach ($b2bsPosts as $b2bsPost) {

			//評価を取得
			$likes = $this->B2bsPostsUser->getLikes(
						$b2bsPost['B2bsPost']['id'],
						$this->viewVars['userId']
					);

			$b2bsPost['B2bsPost']['likesNum'] = $likes['likesNum'];
			$b2bsPost['B2bsPost']['unlikesNum'] = $likes['unlikesNum'];

			//未読or既読セット
			//$readStatus true:read, false:not read
			$readStatus = $this->B2bsPostsUser->getPostsUsers(
								$b2bsPost['B2bsPost']['id'],
								$this->viewVars['userId']
							);
			$b2bsPost['B2bsPost']['readStatus'] = $readStatus;

			//未読(narrowDownParams = '6')の場合、未読記事をセット
			//未読以外の場合は全ての記事をセット
			if (($this->viewVars['narrowDownParams'] === '6' && ! $readStatus) ||
					$this->viewVars['narrowDownParams'] !== '6') {

				//編集中のコメント数をセット
				$b2bsPost['B2bsPost']['editCommentNum'] =
					(int)$this->__setCommentNum($b2bsPost['B2bsPost']['lft'], $b2bsPost['B2bsPost']['rght'])
						- (int)$b2bsPost['B2bsPost']['comment_num'];

				//記事データを配列にセット
				$results['b2bsPosts'][] = $b2bsPost['B2bsPost'];

			}
		}

		//該当記事がない場合は空をセット
		if (! isset($results)) {
			$b2bsPosts = $this->B2bsPost->create();
			$results = array(
					'b2bsPosts' => $b2bsPosts['B2bsPost'],
					'b2bsPostNum' => 0,
				);

		} else {
			//記事数を$results['b2bsPostNum']セット
			$results['b2bsPostNum'] = count($results['b2bsPosts']);

		}

		return $results;
	}

/**
 * __setCommentNum method
 *
 * @param int $lft b2bsPosts.lft
 * @param int $rght b2bsPosts.rght
 * @return string order for search
 */
	private function __setCommentNum($lft, $rght) {
		//検索条件をセット
		$conditions['b2bs_key'] = $this->viewVars['b2bses']['key'];
		$conditions['and']['lft >'] = $lft;
		$conditions['and']['rght <'] = $rght;

		//公開データ以外も含めたコメント数を取得
		if (! $b2bsCommnets = $this->B2bsPost->getPosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				null,
				null,
				null,
				$conditions
		)) {
			return 0;
		}

		return count($b2bsCommnets);
	}

/**
 * __setPostNum method
 *
 * @return string order for search
 */
	private function __setPostNum() {
		$conditions['b2bs_key'] = $this->viewVars['b2bses']['key'];
		$conditions['parent_id'] = '';

		$b2bsPosts = $this->B2bsPost->getPosts(
				$this->viewVars['userId'],
				$this->viewVars['contentEditable'],
				$this->viewVars['contentCreatable'],
				null,
				null,
				null,
				$conditions
			);

		$results['postNum'] = count($b2bsPosts);
		$this->set($results);
	}

/**
 * setEditSaveData
 *
 * @param array $postData post data
 * @return array
 */
	private function __setEditSaveData($postData) {
		$blockId = isset($postData['Block']['id']) ? (int)$postData['Block']['id'] : null;

		if (! $b2bs = $this->B2bs->getB2bs($blockId)) {
			//b2bsテーブルデータ作成とkey格納
			$b2bs = $this->initB2bs();
			$b2bs['B2bs']['block_id'] = 0;
		}

		$data['B2bs'] = $this->__convertStringToBoolean($postData, $b2bs);

		$results = Hash::merge($postData, $b2bs, $data);

		//IDリセット
		unset($results['B2bs']['id']);

		return $results;
	}

/**
 * __convertStringToBoolean
 *
 * @param array $data post data
 * @param array $b2bs b2bses
 * @return array
 */
	private function __convertStringToBoolean($data, $b2bs) {
		//boolean値が文字列になっているため個別で格納し直し
		return $data['B2bs'] = array(
				'name' => $data['B2bs']['name'],
				'use_comment' => ($data['B2bs']['use_comment'] === '1') ? true : false,
				'auto_approval' => ($data['B2bs']['auto_approval'] === '1') ? true : false,
				'use_like_button' => ($data['B2bs']['use_like_button'] === '1') ? true : false,
				'use_unlike_button' => ($data['B2bs']['use_unlike_button'] === '1') ? true : false,
			);
	}
}
