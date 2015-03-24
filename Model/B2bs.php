<?php
/**
 * B2bs Model
 *
 * @property Block $Block
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
App::uses('B2bsesAppModel', 'B2bses.Model');

/**
 * B2bs Model
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\B2bses\Model
 */
class B2bs extends B2bsesAppModel {

/**
 * use tables
 *
 * @var string
 */
	public $useTable = 'b2bses';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Block' => array(
			'className' => 'Blocks.Block',
			'foreignKey' => 'block_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'B2bsPost' => array(
			'className' => 'B2bses.B2bsPost',
			'foreignKey' => 'b2bs_key',
			'dependent' => true
		)
	);

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = array()) {
		$this->validate = Hash::merge($this->validate, array(
			'block_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					'allowEmpty' => false,
					'required' => true,
				)
			),
			'key' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				)
			),

			//status to set in PublishableBehavior.

			'is_auto_translated' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				)
			),
			'name' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('b2bses', 'B2bs name')),
					'required' => true
				),
			),
		));
		return parent::beforeValidate($options);
	}
/**
 * get b2bs data
 *
 * @param int $blockId blocks.id
 * @return array
 */
	public function getB2bs($blockId = '') {
		$conditions = array(
			'block_id' => $blockId,
		);

		$b2bs = $this->find('first', array(
				'recursive' => -1,
				'conditions' => $conditions,
				'order' => 'B2bs.id DESC',
			)
		);

		return $b2bs;
	}

/**
 * save b2bs
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function saveB2bs($data) {
		$this->loadModels([
			'B2bs' => 'B2bses.B2bs',
			'Block' => 'Blocks.Block',
		]);

		//トランザクションBegin
		$dataSource = $this->getDataSource();
		$dataSource->begin();

		try {
			if (!$this->validateB2bs($data)) {
				return false;
			}
			//ブロックの登録
			$block = $this->Block->saveByFrameId($data['Frame']['id'], false);

			//掲示板の登録
			$this->data['B2bs']['block_id'] = (int)$block['Block']['id'];
			$b2bs = $this->save(null, false);
			if (!$b2bs) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			//トランザクションCommit
			$dataSource->commit();
		} catch (Exception $ex) {
			//トランザクションRollback
			$dataSource->rollback();
			//エラー出力
			CakeLog::write(LOG_ERR, $ex);
			throw $ex;
		}
		return $b2bs;
	}

/**
 * validate announcement
 *
 * @param array $data received post data
 * @return bool True on success, false on error
 */
	public function validateB2bs($data) {
		$this->set($data);
		$this->validates();
		return $this->validationErrors ? false : true;
	}

}