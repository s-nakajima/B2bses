<?php
/**
 * B2bsPost Model
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
 * B2bsPost Model
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\B2bses\Model
 */
class B2bsPost extends B2bsesAppModel {

	const DISPLAY_MAX_TITLE_LENGTH = '50';
	const DISPLAY_MAX_CONTENT_LENGTH = '200';

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		//'NetCommons.Publishable',
		'Tree',
	);

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
		'B2bs' => array(
			'className' => 'B2bses.B2bs',
			'foreignKey' => 'b2bs_key',
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
			'key' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				)
			),
			'b2bs_key' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				)
			),

			//status to set in PublishableBehavior.
			'status' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
				),
				'range' => array(
					'rule' => array('range', 0, 5),
					'message' => __d('net_commons', 'Invalid request.'),
				),
			),

			'is_auto_translated' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
				)
			),
			'title' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('b2bses', 'Title')),
					'required'
					=> true
				),
			),
			'content' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('b2bses', 'Content')),
					'required' => true
				),
			),
		));
		return parent::beforeValidate($options);
	}

/**
 * get b2bs data
 *
 * @param int $userId users.id
 * @param bool $contentEditable true can edit the content, false not can edit the content.
 * @param bool $contentCreatable true can create the content, false not can create the content.
 * @param array $conditions databese find condition
 * @return array
 */
	public function getOnePosts($userId, $contentEditable, $contentCreatable, $conditions) {
		if (! $conditions['id']) {
			return;
		}

		//作成権限まで
		if ($contentCreatable && ! $contentEditable) {
			//自分で書いた記事と公開中の記事を取得
			$conditions['or']['created_user'] = $userId;
			$conditions['or']['status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
		}

		//作成・編集権限なし:公開中の記事のみ取得
		if (! $contentCreatable && ! $contentEditable) {
			$conditions['status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
		}

		//対象記事のみ取得
		if (! $b2bsPosts = $this->find('first', array(
				'recursive' => -1,
				'conditions' => $conditions,
		))) {
			return false;

		}

		return $b2bsPosts;
	}

/**
 * get b2bs data
 *
 * @param int $userId users.id
 * @param bool $contentEditable true can edit the content, false not can edit the content.
 * @param bool $contentCreatable true can create the content, false not can create the content.
 * @param array $sortOrder databese find condition
 * @param int $visiblePostRow databese find condition
 * @param int $currentPage databese find condition
 * @param array $conditions databese find condition
 * @return array
 */
	public function getPosts($userId, $contentEditable, $contentCreatable,
				$sortOrder = '', $visiblePostRow = '', $currentPage = '', $conditions = '') {
		//他人の編集中の記事・コメントが見れない人
		if ($contentCreatable && ! $contentEditable) {
			$conditions['or']['status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
			$conditions['or']['and']['created_user'] = $userId;
			$conditions['or']['and']['status <>'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
		}

		//公開中の記事・コメントしか見れない人
		if (! $contentCreatable && ! $contentEditable) {
			$conditions['status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
		}

		//記事一覧取得
		$group = array('B2bsPost.key');
		$params = array(
				'conditions' => $conditions,
				'recursive' => -1,
				'order' => $sortOrder,
				'group' => $group,
				'limit' => $visiblePostRow,
				'page' => $currentPage,
			);

		if (! $b2bsPosts = $this->find('all', $params)) {
			return false;
		}

		return $b2bsPosts;
	}

/**
 * save posts
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function savePost($data) {
		$this->loadModels([
			'B2bsPost' => 'B2bses.B2bsPost',
			'Comment' => 'Comments.Comment',
		]);

		//トランザクションBegin
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		try {
			if (!$this->validatePost($data)) {
				return false;
			}
			if (!$this->Comment->validateByStatus($data, array('caller' => $this->name))) {
				$this->validationErrors = Hash::merge($this->validationErrors, $this->Comment->validationErrors);
				return false;
			}

			$b2bsPost = $this->save(null, false);
			if (!$b2bsPost) {
				// @codeCoverageIgnoreStart
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				// @codeCoverageIgnoreEnd
			}

			//コメントの登録
			if ($this->Comment->data) {
				if (! $this->Comment->save(null, false)) {
					// @codeCoverageIgnoreStart
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
					// @codeCoverageIgnoreEnd
				}
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
		return $b2bsPost;
	}

/**
 * save posts
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function saveComment($data) {
		$this->loadModels([
			'B2bsPost' => 'B2bses.B2bsPost',
		]);

		//トランザクションBegin
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		try {
			if (!$this->validatePost($data)) {
				return false;
			}

			$comments = $this->save(null, false);

			if (! $comments) {
				// @codeCoverageIgnoreStart
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				// @codeCoverageIgnoreEnd
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
		return $comments;
	}

/**
 * validate post
 *
 * @param array $data received post data
 * @return bool|array True on success, validation errors array on error
 */
	public function validatePost($data) {
		$this->set($data);
		$this->validates();
		return $this->validationErrors ? false : true;
	}

}