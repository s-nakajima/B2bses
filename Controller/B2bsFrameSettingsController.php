<?php
/**
 * B2bsFrameSettings Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('B2bsesAppController', 'B2bses.Controller');

/**
 * B2bsFrameSettings Controller
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\B2bses\Controller
 */
class B2bsFrameSettingsController extends B2bsesAppController {

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
		//掲示板の表示設定情報を取得
		$b2bsSettings = $this->B2bsFrameSetting->getB2bsSetting(
										$this->viewVars['frameKey']);
		$results = array(
			'b2bsSettings' => $b2bsSettings['B2bsFrameSetting'],
		);
		$this->set($results);

		if (! $this->request->isPost()) {
			return;
		}

		$data = $this->data;

		if (! $b2bsSetting = $this->B2bsFrameSetting->getB2bsSetting(
			isset($data['Frame']['key']) ? $data['Frame']['key'] : null
		)) {
			//b2bsFrameSettingテーブルデータ生成
			$b2bsSetting = $this->B2bsFrameSetting->create();
		}

		//作成時間,更新時間を再セット
		unset($b2bsSetting['B2bsFrameSetting']['created'], $b2bsSetting['B2bsFrameSetting']['modified']);
		$data = Hash::merge($b2bsSetting, $data);

		if (! $b2bsSetting = $this->B2bsFrameSetting->saveB2bsSetting($data)) {
			if (! $this->handleValidationError($this->B2bsFrameSetting->validationErrors)) {
				return;
			}
		}

		$this->set('frameKey', $b2bsSetting['B2bsFrameSetting']['frame_key']);

		if (!$this->request->is('ajax')) {
			$this->redirectBackUrl();
		}
	}
}
