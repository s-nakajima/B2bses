<?php
/**
 * B2bs Model Test Case
 *
 * @property B2bs $B2bs
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('B2bs', 'B2bses.Model');
App::uses('B2bsAppModelTest', 'B2bses.Test/Case/Model');

/**
 * B2bs Model Test Case
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\B2bses\Test\Case\Model
 */
class B2bsTest extends B2bsAppModelTest {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.b2bses.b2bs',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->B2bs = ClassRegistry::init('B2bs');
	}

/**
 * test method
 *
 * @return void
 */
	public function test() {
		$this->assertTrue(true);
	}

/**
 * test method
 *
 * @return void
 */
	public function testGetB2bs() {
		/*$blockId = 1;
		$result = $this->B2bs->getB2bs($blockId);

		$expected = array(
			'B2bs' => array(
				'id' => '1',
				'block_id' => $blockId,
				'name' => 'テスト掲示板1',
				'key' => 'b2bs_1',
			),
		);*/

		/*$this->_assertArray(null, $expected, $result);*/
	}
}
