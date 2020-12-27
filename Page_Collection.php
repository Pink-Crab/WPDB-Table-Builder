<?php declare(strict_types=1);
/**
 * A colletion of child pages.
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

use PinkCrab\Modules\Admin_Pages\Page;
use PinkCrab\Core\Collection\Collection;
use PinkCrab\Modules\Admin_Pages\ACF_Page;

class Page_Collection {

	/**
	 * Holds the sub pages.
	 *
	 * @var \PinkCrab\Core\Collection\Collection
	 */
	protected $pages = array();

	/**
	 * Key of parent page
	 *
	 * @var string
	 */
	protected $parent_key;

	public function __construct( string $parent_key ) {
		$this->parent_key = $parent_key;
		$this->pages      = new Collection();
	}

	public function is_empty(): bool {
		return empty( $this->pages );
	}

	/**
	 * Returns an new page instance, populated with the parent key.
	 *
	 * @return Page
	 */
	public function child_page_factory( string $menu_title, string $key ): Page {
		return Page::create_page(
			$key,
			$menu_title,
			$this->parent_key
		);
	}

	/**
	 * Returns an new page instance, populated with the parent key.
	 *
	 * @return Page
	 */
	public function child_acf_page_factory( string $menu_title, string $key ): ACF_Page {
		return ACF_Page::create_page(
			$key,
			$menu_title,
			$this->parent_key
		);
	}

	/**
	 * Used to add
	 *
	 * @param Page ...$pages
	 * @return self
	 */
	public function add( Page $page ): self {
		if ( $page instanceof Page ) {
			$this->pages->push( $page );
		}
		return $this;
	}

	/**
	 * Uses a passed callable to register the child page.s
	 *
	 * @param callable $function
	 * @return void
	 */
	public function register_child_pages( callable $function ): void {
		$this->pages->apply(
			function( Page $page ) use ( $function ): void {
				$function( $page );
			}
		);
	}
}
