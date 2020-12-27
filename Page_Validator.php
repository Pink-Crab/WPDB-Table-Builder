<?php declare(strict_types=1);
/**
 * The validator for Admin Pages
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
use PinkCrab\Modules\Admin_Pages\ACF_Page;

class Page_Validator {

	protected const WP_PAGE  = 'wp';
	protected const ACF_PAGE = 'acf';

	/**
	 * The page to be validated.
	 *
	 * @var \PinkCrab\Modules\Admin_Pages\Page
	 */
	protected $page;

	/**
	 * Page has errors flag.
	 *
	 * @var bool
	 */
	protected $error = false;

	/**
	 * Messages of errors.
	 *
	 * @var array
	 */
	protected $messages = array();

	/**
	 * Denotes the current page type
	 * Can be wp or acf
	 *
	 * @var string
	 */
	protected $page_type = self::WP_PAGE;

	/**
	 * Entry point for running a test.
	 * Resets from previous tests.
	 *
	 * @param \PinkCrab\Modules\Admin_Pages\Page $page
	 * @return void
	 */
	public function validate_page( Page $page ): void {
		$this->error    = false;
		$this->messages = array();
		$this->page     = $page;

		// Set the flag is an acf page.
		if ( $page instanceof ACF_Page ) {
			$this->page_type = self::ACF_PAGE;
		}

		$this->run_checks();
	}

	/**
	 * Marks as error.
	 *
	 * @param mixed $data
	 * @return void
	 */
	protected function error_log( $data ) {
		$this->error      = true;
		$this->messages[] = array(
			'page'    => $this->page,
			'message' => $data,
		);
	}

	/**
	 * Runs the checks.
	 *
	 * @return void
	 */
	protected function run_checks(): void {
		$this->check_keys();
		$this->check_titles();

		if ( $this->page_type === self::WP_PAGE ) {
			$this->check_view();
		} elseif ( $this->page_type === self::ACF_PAGE ) {
			$this->check_acf_fields();
		}
	}

	/**
	 * Checks the key is valid.
	 *
	 * @return void
	 */
	protected function check_keys(): void {
		if ( empty( $this->page->key ) ) {
			$this->error_log( array( 'Page key not defined' ) );
		} elseif ( ! preg_match( '/^[a-z][-a-z0-9_]*$/', $this->page->key ) ) {
			$this->error_log( array( 'Page key is invalid' ) );
		}
	}

	/**
	 * Checks the menu title is set.
	 *
	 * @return void
	 */
	protected function check_titles(): void {
		if ( empty( $this->page->menu_title ) ) {
			$this->error_log( array( 'Menu title not defined' ) );
		}
	}

	/**
	 * Checks the page has a view tempalte.
	 *
	 * @return void
	 */
	protected function check_view() {
		if ( ! empty( $this->page->view_data ) && empty( $this->page->view_template ) ) {
			$this->error_log( array( 'View template not defined, but data passed' ) );
		}
	}

	/**
	 * Checks that the fields are set correctly for ACF.
	 *
	 * @return void
	 */
	protected function check_acf_fields() {
		# code...
	}

	/**
	 * Returns if the page has any errors.
	 *
	 * @return bool
	 */
	public function has_errors(): bool {
		return $this->error;
	}

	/**
	 * Returns the current error messages
	 *
	 * @return string
	 */
	public function get_error_messages(): string {

		$start = array(
			sprintf(
				'Errors thrown validating %s : %s',
				$this->page->key ?? 'NO KEY',
				$this->page->menu_title ?? 'NO MENU TITLE'
			),
		);

		$messages = array_map(
			function( $e ) {
				return join( '\\n', $e['message'] ?? array() );
			},
			$this->messages
		);

		return join( ', ', array_merge( $start, $messages ) );
	}
}
