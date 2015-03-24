<?php
/**
 * B2bsPostsUser Model
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
 * B2bsPostsUser Model
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\B2bses\Model
 */
class B2bsPostsUser extends B2bsesAppModel {

/**
 * use tables
 *
 * @var string
 */
	public $useTable = 'b2bs_posts_users';

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
		'B2bsPost' => array(
			'className' => 'B2bses.B2bsPost',
			'foreignKey' => 'post_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'Users.User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
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
			'post_id' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				)
			),
			'user_id' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				)
			),
			'likes_flag' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				)
			),
			'unlikes_flag' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				)
			),
		));
		return parent::beforeValidate($options);
	}

/**
 * getReadPostStatus
 *
 * @param int $postId b2bsPosts.id
 * @param int $userId users.id
 * @return array or not find data is false
 */
	public function getPostsUsers($postId, $userId) {
		$conditions = array(
			'post_id' => $postId,
			'user_id' => $userId,
		);

		if (! $postsUsers = $this->find('first', array(
				'recursive' => -1,
				'conditions' => $conditions,
			))
		) {
			return false;

		}
		return $postsUsers;
	}

/**
 * getLikes
 *
 * @param int $postId b2bsPosts.id
 * @param int $userId users.id
 * @return array
 */
	public function getLikes($postId, $userId) {
		if (! $likesPosts = $this->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'post_id' => $postId,
					'likes_flag' => true,
				),
			))
		) {
			$results['likesNum'] = 0;

		} else {
			$results['likesNum'] = count($likesPosts);

		}

		if (! $unlikesPosts = $this->find('all', array(
				'recursive' => -1,
				'conditions' => array(
					'post_id' => $postId,
					'unlikes_flag' => true,
				),
			))
		) {
			$results['unlikesNum'] = 0;

		} else {
			$results['unlikesNum'] = count($unlikesPosts);

		}

		if (! $postsUsers = $this->getPostsUsers($postId, $userId)) {
			$results['likesFlag'] = false;
			$results['unlikesFlag'] = false;

		} else {
			$results['likesFlag'] = $postsUsers['B2bsPostsUser']['likes_flag'];
			$results['unlikesFlag'] = $postsUsers['B2bsPostsUser']['unlikes_flag'];

		}

		return $results;
	}

/**
 * save b2bs
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function savePostsUsers($data) {
		$this->loadModels([
			'B2bsPostsUser' => 'B2bses.B2bsPostsUser',
		]);

		//トランザクションBegin
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		try {
			if (! $this->validateB2bsPostsUser($data)) {
				return false;
			}

			$b2bsPostsUser = $this->save(null, false);
			if (! $b2bsPostsUser) {
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
		return $b2bsPostsUser;
	}

/**
 * validate announcement
 *
 * @param array $data received post data
 * @return bool True on success, false on error
 */
	public function validateB2bsPostsUser($data) {
		$this->set($data);
		$this->validates();
		return $this->validationErrors ? false : true;
	}
}