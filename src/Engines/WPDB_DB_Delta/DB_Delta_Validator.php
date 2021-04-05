<?php

declare(strict_types=1);

/**
 * WPDB DB_Delta table validator.
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
 * @since 0.3.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Table_Builder
 */

namespace PinkCrab\Table_Builder\Engines\WPDB_DB_Delta;

use PinkCrab\Table_Builder\Index;
use PinkCrab\Table_Builder\Column;
use PinkCrab\Table_Builder\Schema;
use PinkCrab\Table_Builder\Engines\Schema_Validator;

class DB_Delta_Validator implements Schema_Validator {

	/**
	 * All errors found while validating
	 *
	 * @var array<string>
	 */
	protected $errors = array();

	/**
	 * Valdiates the schema passed.
	 *
	 * @param Schema $schema
	 * @return bool
	 */
	public function validate( Schema $schema ): bool {
		// Reset errors.
		$this->errors = array();

		// Validate the columns.
		$this->validate_columns( $schema );
		$this->validate_primary_key( $schema );

		// Validate only a single primary key.
		dump( $this );
		return $this->has_errors();
	}

	/**
	 * Returns all errors encountered during validation.
	 *
	 * @return array<string>
	 */
	public function get_errors(): array {
		return $this->errors;
	}

	/**
	 * Checks if any error generated, validating.
	 *
	 * @return bool
	 */
	public function has_errors(): bool {
		return count( $this->errors ) !== 0;
	}

	/**
	 * Ensure all defined columns have unique names and have types defined.
	 *
	 * @return void
	 */
	public function validate_columns( Schema $schema ): void {
		$result = array_reduce(
			$schema->get_columns(),
			function( array $result, Column $column ): array {
				if ( is_null( $column->get_type() ) ) {
					$result[] = $column;
				}
				return $result;
			},
			array()
		);

		// If we have errors, add to error log.
		if ( count( $result ) !== 0 ) {
			$this->errors = array_merge(
				$this->errors,
				array_map(
					function( Column $column ): string {
						return \sprintf( 'Column "%s" has no type defined', $column->get_name() );
					},
					$result
				)
			);
		}
	}

	/** @testdox When defining a primary key, the column used must exist and only a single primary key can be set. */
	public function validate_primary_key( Schema $schema ): void {
		// Pass all current column names for checking against.
		$column_names = array_map(
			function( Column $column ):string {
				return $column->get_name();
			},
			$schema->get_columns()
		);

		$result = array_reduce(
			$schema->get_indexes(),
			function( array $result, Index $index ) use ( $column_names ): array {
				if ( $index->is_primary() ) {
					// Check column exists.
					if ( ! in_array( $index->get_column(), $column_names, true ) ) {
						$result['missing_columns'] = $index->get_column();
					}

					// Mark the index as primary key.
					$result['primary_key'][] = $index;
				}
			},
			array(

				'missing_columns'  => array(),
				'primary_keys' => array(),
			)
		);

		dump( $result );
	}
}
