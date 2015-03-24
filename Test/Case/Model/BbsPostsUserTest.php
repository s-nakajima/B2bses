<?php
/**
 * B2bsPostsUser Model Test Case
 *
 * @property B2bsPostsUser $B2bsPostsUser
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('B2bsPostsUser', 'B2bses.Model');
App::uses('B2bsAppModelTest', 'B2bses.Test/Case/Model');

/**
 * B2bsPostsUser Model Test Case
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\B2bses\Test\Case\Model
 */
class B2bsPostsUserTest extends B2bsAppModelTest {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.b2bses.b2bs_posts_user',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->B2bsPostsUser = ClassRegistry::init('B2bses.B2bsPostsUser');
	}

/**
 * test method
 *
 * @return void
 */
	public function test() {
		$this->assertTrue(true);
	}
}
