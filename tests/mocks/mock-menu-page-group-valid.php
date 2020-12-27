<?php

declare(strict_types=1);

use PinkCrab\Modules\Admin_Pages\Page;
use PinkCrab\Modules\Admin_Pages\Menu_Page_Group;
use PinkCrab\Modules\Admin_Pages\Page_Collection;

/**
 * Testing example of the Admin_Pages Group.
 */

class Mock_Menu_Page_Group_Valid extends Menu_Page_Group {

	public $key        = 'pc_framework_admin_page_group';
	public $menu_title = 'Test';

	/**
	 * Register the parent/main page.
	 *
	 * @param \PinkCrab\Modules\Admin_Pages\Page $page
	 * @return \PinkCrab\Modules\Admin_Pages\Page $page
	 */
	public function set_parent_page( Page $page ): Page {
		return $page->title( 'Test Page' )
			->view_template( 'test_view' )
			->view_data(
				array(
					'foo' => 'foo',
					'bar' => 'bar',
				)
			);
	}

	/**
	 * Register all child pages.
	 *
	 * @param \PinkCrab\Modules\Admin_Pages\Page_Collection $children
	 * @return \PinkCrab\Modules\Admin_Pages\Page_Collection
	 */
	public function set_child_pages( Page_Collection $children ): Page_Collection {

		$children->add(
			$children->child_page_factory( 'Sub Page 1', 'sub_page_1' )
			->title( 'Sub Page 1' )
			->view_template( 'test_view' )
			->view_data(
				array(
					'foo' => 'foo1',
					'bar' => 'bar1',
				)
			)
		);

		$children->add(
			$children->child_page_factory( 'Sub Page 2', 'sub_page_2' )
				->title( 'Sub Page 2' )
				->view_template( 'test_view' )
				->view_data(
					array(
						'foo' => 'foo2',
						'bar' => 'bar2',
					)
				)
		);

		return $children;
	}
}
