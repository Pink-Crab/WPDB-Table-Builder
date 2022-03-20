<?php

declare(strict_types=1);

/**
 * Exception for WPDB DB Delta validator.
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

namespace PinkCrab\Table_Builder\Exception\WPDB_DB_Delta;

use Exception;
use Throwable;
use PinkCrab\Table_Builder\Schema;

class WPDB_Validator_Exception extends Exception {

	/**
	 * Array of errors during validation.
	 *
	 * @var string[]
	 */
	private $validation_errors;

	/**
	 * Schema
	 *
	 * @var Schema
	 */
	private $schema;

	/**
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @param string[] $validation_errors
	 * @param string $message
	 * @param int $code
	 * @param \Throwable|null $previous
	 */
	public function __construct(
		Schema $schema,
		array $validation_errors,
		string $message = '',
		int $code = 0,
		?Throwable $previous = null
	) {
		$this->schema            = $schema;
		$this->validation_errors = $validation_errors;
		parent::__construct( $message, $code, $previous );
	}

	/**
	 * Returns an exception for a schema failing validation
	 *
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @param string[] $errors
	 * @return WPDB_Validator_Exception
	 * @code 201
	 */
	public static function failed_validation( Schema $schema, array $errors ): WPDB_Validator_Exception {
		return new WPDB_Validator_Exception(
			$schema,
			$errors,
			sprintf( '%s failed with %d errors', $schema->get_table_name(), count( $errors ) ),
			201
		);
	}

	/**
	 * Get the table schema.
	 *
	 * @return Schema
	 */
	public function get_schema(): Schema {
		return $this->schema;
	}

	/**
	 * Get array of errors during validation.
	 *
	 * @return string[]
	 */
	public function get_validation_errors(): array {
		return $this->validation_errors;
	}
}
