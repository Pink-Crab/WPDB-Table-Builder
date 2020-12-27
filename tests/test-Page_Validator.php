<?php

use PinkCrab\Core\View\View;
use PinkCrab\Core\View\PHP_Engine;
use PinkCrab\Core\Registration\Loader;
use PinkCrab\Modules\Admin_Pages\Page;
use PinkCrab\Core\DataStructures\Deque;
use PinkCrab\Modules\Admin_Pages\Page_Validator;
use PinkCrab\Modules\Admin_Pages\Menu_Page_Group;
use PinkCrab\Modules\Admin_Pages\Page_Collection;

require_once __DIR__ . '/mocks/mock-menu-page-group-valid.php';

/**
 * Tests if the valid product group can be registered.
 */
class Page_Validator_Test extends WP_UnitTestCase {

	public function testFailsWithoutKey() {
		$page      = new Page();
		$validator = new Page_Validator();
		$validator->validate_page( $page );
		$this->assertTrue( $validator->has_errors() );
		$this->assertStringContainsString( 'Page key not defined', $validator->get_error_messages() );
		$this->assertStringContainsString( 'NO KEY', $validator->get_error_messages() );
	}

	public function testFailsWithINvlidKey() {
		$page      = new Page( 'INVALID KEY @' );
		$validator = new Page_Validator();
		$validator->validate_page( $page );
		$this->assertTrue( $validator->has_errors() );
		$this->assertStringContainsString( 'Page key is invalid', $validator->get_error_messages() );
		$this->assertStringContainsString( 'INVALID KEY @', $validator->get_error_messages() );
	}

	public function testFailsWithoutMenuTitle() {
		$page      = new Page( 'some_key' );
		$validator = new Page_Validator();
		$validator->validate_page( $page );
		$this->assertTrue( $validator->has_errors() );
		$this->assertStringContainsString( 'Menu title not defined', $validator->get_error_messages() );
		$this->assertStringContainsString( 'NO MENU TITLE', $validator->get_error_messages() );
	}

	public function testFailsWithViewDataButNoTemplate() {
		$page             = new Page( 'some_key' );
		$page->menu_title = 'Some Page';
		$page->view_data( array( 'some' => 'data' ) );
		$validator = new Page_Validator();
		$validator->validate_page( $page );
		$this->assertTrue( $validator->has_errors() );
		$this->assertStringContainsString( 'View template not defined', $validator->get_error_messages() );
		$this->assertStringContainsString( 'Some Page', $validator->get_error_messages() );
	}

	public function testPassesWithTextbookPage() {

		// Create instance of group.
		$view_engine   = new PHP_Engine();
		$view          = new View( $view_engine );
		$pageValidator = new Page_Validator();
		$page          = new Mock_Menu_Page_Group_Valid( $view, $pageValidator );

		$parent = getPrivateProperty( $page, 'parent' );
		$pageValidator->validate_page( $parent );
		$this->assertFalse( $pageValidator->has_errors() );

	}
}
