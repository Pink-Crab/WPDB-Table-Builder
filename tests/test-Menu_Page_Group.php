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
class Menu_Page_Group_Valid_Test extends WP_UnitTestCase {

	protected $group;

	protected $loader;

	public function setUp() {
		if ( ! $this->group ) {
			$view_engine   = new PHP_Engine();
			$view          = new View( $view_engine );
			$pageValidator = new Page_Validator();
			$this->group   = new Mock_Menu_Page_Group_Valid( $view, $pageValidator );
			$this->loader  = new Loader();
		}
	}

	/**
	 * Test that we have a Menu_Page_Group obejct.
	 *
	 * @return void
	 */
	public function testWeCanCreateGroup() {
		$this->assertInstanceOf( Menu_Page_Group::class, $this->group );
	}

	public function testParentPageDetails() {
		$parent = getPrivateProperty( $this->group, 'parent' );
		$this->assertInstanceOf( Page::class, $parent );
		$this->assertEquals( 'Test Page', $parent->title );
		$this->assertEquals( 'Test', $parent->menu_title );
		$this->assertEquals( 'pc_framework_admin_page_group', $parent->key );
		$this->assertContains( 'bar', $parent->view_data );
		$this->assertContains( 'foo', $parent->view_data );
	}

	/**
	 * Test we have all correct data for child pages.
	 *
	 * @return void
	 */
	public function testChildPageDetails() {
		// Get the pages
		$pages  = $this->getChildPageCollectionDeque();
		$page_2 = $pages->pop();
		$page_1 = $pages->pop();

		// Page 1
		$this->assertInstanceOf( Page::class, $page_1 );
		$this->assertEquals( 'Sub Page 1', $page_1->title );
		$this->assertEquals( 'sub_page_1', $page_1->key );
		$this->assertEquals( 'pc_framework_admin_page_group', $page_1->parent_slug );
		$this->assertContains( 'bar1', $page_1->view_data );
		$this->assertContains( 'foo1', $page_1->view_data );

		// Page 2
		$this->assertInstanceOf( Page::class, $page_2 );
		$this->assertEquals( 'Sub Page 2', $page_2->title );
		$this->assertEquals( 'sub_page_2', $page_2->key );
		$this->assertEquals( 'pc_framework_admin_page_group', $page_2->parent_slug );
		$this->assertContains( 'bar2', $page_2->view_data );
		$this->assertContains( 'foo2', $page_2->view_data );
	}

	public function testCanRegisterPages() {

		$this->populateLoader();

		$adminHooks = getPrivateProperty( $this->loader, 'admin' );
		$adminHooks->apply(
			function( $hook ) {
				$fn    = new ReflectionFunction( is_array( $hook['method'] ) ? call_user_func( $hook['method'] ) : $hook['method'] );
				$class = $fn->getClosureThis();
				$this->assertEquals( 'Mock_Menu_Page_Group_Valid', get_class( $class ) );
				$this->assertEquals( 'pc_framework_admin_page_group', $class->key );
				$this->assertEquals( 'Test', $class->menu_title );
			}
		);
	}

	protected function populateLoader(): void {
		$this->group->setup();
		$this->group->register( $this->loader );
	}


	/**
	 * Use reflection to extrace the child page deque.
	 *
	 * @return \PinkCrab\Core\DataStructures\Deque
	 */
	protected function getChildPageCollectionDeque(): Deque {
		$children_collection = getPrivateProperty( $this->group, 'children' );
		return getPrivateProperty( $children_collection, 'pages' );
	}
}
