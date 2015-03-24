<?php
/**
 * B2bsAuthoritySettings Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('B2bsesAppController', 'B2bses.Controller');

/**
 * B2bsAuthoritySettings Controller
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\B2bses\Controller
 */
class B2bsAuthoritySettingsController extends B2bsesAppController {

/**
 * use models
 *
 * @var array
 */
	public $uses = array(
		'B2bses.B2bs',
		'B2bses.B2bsFrameSetting',
		'B2bses.B2bsPost',
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
 * edit method
 *
 * @return void
 */
	public function edit() {
		$this->setB2bs();

		if (! $this->request->isPost()) {
			return;
		}

		$data = $this->__setEditSaveData($this->data);

		if (! $this->B2bs->saveB2bs($data)) {
			if (! $this->handleValidationError($this->B2bs->validationErrors)) {
				return;
			}
		}

		if (! $this->request->is('ajax')) {
			$this->redirectBackUrl();
		}
	}

/**
 * setEditSaveData
 *
 * @param array $postData post data
 * @return array
 */
	private function __setEditSaveData($postData) {
		$blockId = isset($this->data['Block']['id']) ? (int)$this->data['Block']['id'] : null;

		if (! $b2bs = $this->B2bs->getB2bs($blockId)) {
			//b2bsテーブルデータ作成とkey格納
			$b2bs = $this->initB2bs();
			$b2bs['B2bs']['block_id'] = 0;
		}

		$data['B2bs'] = $results = $this->__convertStringToBoolean($postData, $b2bs);

		$results = Hash::merge($postData, $b2bs, $data);

		//IDリセット
		unset($results['B2bs']['id']);

		return $results;
	}

/**
 * convertStringToBoolean
 *
 * @param array $data post data
 * @param array $b2bs b2bses
 * @return array
 */
	private function __convertStringToBoolean($data, $b2bs) {
		//boolean値が文字列になっているため個別で格納し直し
		return $data['B2bs'] = array(
				'post_create_authority' => ($data['B2bs']['post_create_authority'] === '1') ? true : false,
				'editor_publish_authority' => ($data['B2bs']['editor_publish_authority'] === '1') ? true : false,
				'general_publish_authority' => ($data['B2bs']['general_publish_authority'] === '1') ? true : false,
				'comment_create_authority' => ($data['B2bs']['comment_create_authority'] === '1') ? true : false,
			);
	}
}