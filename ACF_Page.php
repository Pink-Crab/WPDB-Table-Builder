<?php declare(strict_types=1);
/**
 * Registers ACF option pages.
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
 *
 * @docs https://www.advancedcustomfields.com/resources/acf_add_options_page/
 */

namespace PinkCrab\Modules\Admin_Pages;

use PinkCrab\Modules\Admin_Pages\Page;
use PinkCrab\Core\Interfaces\Renderable;

class ACF_Page extends Page {

	/**
	 * Should this page just be a title holder?
	 * If set to true, will redirect to first child page if set.
	 *
	 * @var bool
	 */
	public $redirect = false;

	/**
	 * Denotes which page should be selected to save and load values from.
	 *
	 * @var int|string
	 */
	public $post_id = 'options';

	/**
	 * Should all defined options be autoloaded
	 *
	 * @var bool
	 */
	public $autoload = false;

	/**
	 * The message shown when a page is updated.
	 *
	 * @var string
	 */
	public $updated_message = 'Options Updated';


	/**
	 * Set the value of view_template
	 *
	 * This has been removed for ACF pages, as nothing to render.
	 *
	 * @return  self
	 */
	public function view_template( string $view_template ): Page {
		return $this;
	}

	/**
	 * Set the value of view_data
	 *
	 * This has been removed for ACF pages, as nothing to render.
	 *
	 * @return  self
	 */
	public function view_data( $view_data ): Page {
		return $this;
	}

	/**
	 * Renders the passed view and data using the current view engine.
	 *
	 * This has been removed for ACF pages, as nothing to render.
	 *
	 * @param Renderable $view
	 * @return callable
	 */
	public function compose_view( Renderable $view ):callable {
		return function() {};
	}
}
