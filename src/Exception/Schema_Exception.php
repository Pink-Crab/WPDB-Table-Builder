<?php

declare(strict_types=1);

/**
 * Exception for the schema model.
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
 * @since 1.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Table_Builder
 */

namespace PinkCrab\Table_Builder\Exception;

use Exception;
use Throwable;
use PinkCrab\Table_Builder\Schema;

class Schema_Exception extends Exception {

	/**
	 * The table schema.
	 *
	 * @var Schema|null
	 */
	private $schema;

	public function __construct(
		?Schema $schema = null,
		string $message = '',
		int $code = 0,
		?Throwable $previous = null
	) {
		$this->schema = $schema;
		parent::__construct( $message, $code, $previous );
	}

	/**
	 * Get the table schema.
	 *
	 * @return Schema|null
	 */
	public function get_schema(): ?Schema {
		return $this->schema;
	}

	/**
	 * Throw an exception when a column doesn't exist.
	 *
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @param string $column
	 * @return Schema_Exception
	 */
	public static function column_not_exist( Schema $schema, string $column ): Schema_Exception {
		return new Schema_Exception(
			$schema,
			\sprintf( 'column with name %s is not currently defined', $column ),
			301
		);
	}

}
