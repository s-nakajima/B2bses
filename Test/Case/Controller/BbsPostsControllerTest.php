<?php
/**
 * B2bsPostsController Test Case
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('B2bsPostsController', 'B2bses.Controller');

/**
 * B2bsPostsController Test Case
 */
class B2bsPostsControllerTest extends ControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.b2bses.b2bs',
		'plugin.b2bses.b2bs_frame_setting',
		'plugin.b2bses.b2bs_post',
		'plugin.b2bses.b2bs_posts_user',
		'plugin.b2bses.site_setting'
	);

/**
 * test method
 *
 * @return void
 */
	public function test() {
		$this->assertTrue(true);
	}
}