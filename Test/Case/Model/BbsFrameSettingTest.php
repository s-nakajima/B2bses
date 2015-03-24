<?php
/**
 * B2bsFrameSetting Model Test Case
 *
 * @property B2bsFrameSetting $B2bsFrameSetting
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('B2bsFrameSetting', 'B2bses.Model');
App::uses('B2bsAppModelTest', 'B2bses.Test/Case/Model');

/**
 * B2bsFrameSetting Model Test Case
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\B2bses\Test\Case\Model
 */
class B2bsFrameSettingTest extends B2bsAppModelTest {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.b2bses.b2bs_frame_setting',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->B2bsFrameSetting = ClassRegistry::init('B2bses.B2bsFrameSetting');
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
