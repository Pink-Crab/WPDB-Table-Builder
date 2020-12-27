<?php

declare(strict_types=1);

/**
 * Abstract class to base all admin pages from.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\ExtLibs\Admin_Pages
 */

namespace PinkCrab\Modules\Admin_Pages;

use ReflectionClass;
use PinkCrab\Modules\Admin_Pages\Page_Collection;

use PinkCrab\Core\Services\Registration\Loader;

use PinkCrab\Core\Application\App;
use PinkCrab\Modules\Admin_Pages\ACF_Page;
use InvalidArgumentException;
use PinkCrab\Core\Interfaces\Registerable;
use PinkCrab\Core\Interfaces\Renderable;
use PinkCrab\Modules\Admin_Pages\Page_Validator;

abstract class Menu_Page_Group implements Registerable {

	public $key;
	public $menu_title;
	public $capability = 'manage_options';
	public $icon_url   = 'dashicons-admin-generic';
	public $position   = 85;

	protected $app;

	/**
	 * The parent/primary page object.
	 *
	 * @var Page
	 */
	protected $parent;

	/**
	 * All child pages in group.
	 *
	 * @var Page_Collection
	 */
	protected $children;

	/**
	 * The current view driver.
	 *
	 * @var Renderable
	 */
	protected $view;

	/**
	 * Validates the page properties.
	 *
	 * @var Page_Validator
	 */
	protected $page_validator;

	public function __construct( App $app, Renderable $view, Page_Validator $page_validator ) {
		$this->view           = $view;
		$this->page_validator = $page_validator;

		// Set the parent page.
		$this->parent = $this->set_parent_page(
			Page::create_page(
				$this->key,
				$this->menu_title,
				$this->key
			)
		);

		$this->children = $this->set_child_pages(
			$this->app::make( Page_Collection::class, array( $this->key ) )
		);
	}

	/**
	 * Called before the page is registered
	 *
	 * @return void
	 */
	public function setUp() {}

	/**
	 * Default call/return if not defined in child.
	 * Sets the parent pages.
	 *
	 * @param Page $parent_page
	 * @return Page
	 */
	public function set_parent_page( Page $parent_page ): Page {
		return $parent_page;
	}

	/**
	 * Default call/return if not defined in child.
	 * Sets the child pages
	 *
	 * @param Page_Collection $children
	 * @return Page_Collection
	 */
	public function set_child_pages( Page_Collection $children ): Page_Collection {
		return $children;
	}

	/**
	 * Validates a page using the page validator.
	 *
	 * @throws InvalidArgumentException
	 * @param Page $page
	 * @return void
	 */
	private function validate_page( Page $page ): void {
		$this->page_validator->validate_page( $page );
		if ( $this->page_validator->has_errors() ) {
			throw new InvalidArgumentException( $this->page_validator->get_error_messages() );
		}
	}

	/**
	 * Registers the admin pages.
	 *
	 * @return void
	 */
	public function register( Loader $loader ): void {

		// Register primary page.
		$this->validate_page( $this->parent );
		$this->register_parent_page( $this->parent, $loader );

		// Register any child pages.
		if ( ! $this->children->is_empty() ) {
			$this->children->register_child_pages(
				function( Page $page ) use ( $loader ): void {
					$this->validate_page( $page );
					$this->register_child_page( $page, $loader );
				}
			);
		}
	}

	/**
	 * Registers the parent page, based on type.
	 *
	 *  @param Page|ACF_Page $page
	 * @param Loader $loader
	 * @return void
	 */
	protected function register_parent_page( Page $page, Loader $loader ): void {
		if ( $page instanceof ACF_Page ) {
			$loader->admin_action(
				'acf/init',
				function() use ( $page ) {
					/ acf_add_options_page(
						array(
							'page_title' => $page->title,
							'menu_title' => $page->menu_title,
							'menu_slug'  => $this->key,
							'capability' => $this->capability,
							'redirect'   => false,
							'position'   => $this->position,
							'icon_url'   => $this->icon_url,
						)
					);
				}
			);
		} else {
			$loader->admin_action(
				'admin_menu',
				function() use ( $page ) {
					add_menu_page(
						$this->parent->title,
						$this->menu_title ?? $this->parent->menu_title,
						$this->capability,
						$this->key,
						$this->parent->compose_view( $this->view ),
						$this->icon_url,
						$this->position
					);
				}
			);
		}
	}

	/**
	 * Registers all child pages.
	 *
	 * @param Page|ACF_Page $page
	 * @param Loader $loader
	 * @return void
	 */
	protected function register_child_page( Page $page, Loader $loader ) {
		if ( $page instanceof ACF_Page && function_exists( 'acf_add_options_sub_page' ) ) {
			$loader->admin_action(
				'acf/init',
				function() use ( $page ) {
					acf_add_options_sub_page(
						array(
							'page_title'  => $page->title,
							'menu_title'  => $page->menu_title,
							'parent_slug' => $this->key,
							'position'    => 2,
						)
					);
				}
			);
		} else {
			$loader->admin_action(
				'admin_menu',
				function() use ( $page ) {
					add_submenu_page(
						$this->key,
						$page->title,
						$page->menu_title,
						$this->capability,
						$page->key,
						$page->compose_view( $this->view ),
						rand( 0, 100 )
					);
				}
			);
		}
	}
}
