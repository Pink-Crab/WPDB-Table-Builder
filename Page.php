<?php declare(strict_types=1);
/**
 * The parent page.
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

use PinkCrab\Core\Interfaces\Renderable;

class Page {

	/**
	 * The pages key/slug
	 *
	 * @var string
	 */
	public $key;

	/**
	 * THe pages menu title
	 *
	 * @var string
	 */
	public $menu_title;

	/**
	 * Parent pages slug
	 *
	 * @var string
	 */
	public $parent_slug;

	/**
	 * Pages position
	 *
	 * @var int
	 */
	protected $postition = 1;

	/**
	 * The pages title
	 *
	 * @var string
	 */
	public $title;

	/**
	 * The template to render.
	 *
	 * @var string
	 */
	public $view_template = '';

	/**
	 * Array of data to pass to view.
	 *
	 * @var array
	 */
	public $view_data = array();

	/**
	 * All metaboxes for this page.
	 *
	 * @var array
	 */
	protected $meta_boxes = array();

	/**
	 * Creates an instance of the page.
	 *
	 * @param string|null $key
	 */
	public function __construct( ?string $key = null ) {
		$this->key = $key;
	}

	/**
	 * Create a new page.
	 *
	 * @param string $key
	 * @param string $menu_title
	 * @param string $parent_slug
	 * @return void
	 */
	public static function create_page( string $key, string $menu_title, ?string $parent_slug = null ): Page {
		$page              = new static( $key );
		$page->menu_title  = $menu_title;
		$page->parent_slug = $parent_slug;
		return $page;
	}

	/**
	 * Set the page title.
	 *
	 * @param string $title
	 * @return self
	 */
	public function title( string $title ): Page {
		$this->title = $title;
		return $this;
	}

	/**
	 * Set the value of view_template
	 *
	 * @return  self
	 */
	public function view_template( string $view_template ): Page {
		$this->view_template = $view_template;
		return $this;
	}

	/**
	 * Set the value of view_data
	 *
	 * @return  self
	 */
	public function view_data( $view_data ): Page {
		$this->view_data = $view_data;
		return $this;
	}

	/**
	 * Sets the pages position.
	 *
	 * @param int $postition
	 * @return Page
	 */
	public function position( int $position ): Page {
		$this->position = $position;
		return $this;
	}

	/**
	 * Renders the passed view and data using the current view engine.
	 *
	 * @param Renderable $view
	 * @return callable
	 */
	public function compose_view( Renderable $view ): callable {
		return function() use ( $view ) {
			$view->render( $this->view_template, $this->view_data );
		};
	}
}
