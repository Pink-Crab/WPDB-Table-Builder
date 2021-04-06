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
use PinkCrab\Table_Builder\Foreign_Key;

class DB_Delta_Validator {

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

		$this->validate_columns( $schema );
		$this->validate_primary_key( $schema );
		$this->validate_index_columns( $schema );
		$this->validate_foreign_keys( $schema );

		return ! $this->has_errors();
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
	protected function validate_columns( Schema $schema ): void {
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

	/**
	 * Ensure that only a single primary key has been defined.
	 *
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @return void
	 */
	protected function validate_primary_key( Schema $schema ): void {

		$primary_keys = array_filter(
			$schema->get_indexes(),
			function( Index $index ): bool {
				return $index->is_primary();
			}
		);

		if ( count( $primary_keys ) > 1 ) {
			$this->errors[] = \sprintf( '%d Primary keys are defined in schema, only a single primary key can be set.', count( $primary_keys ) );
		}
	}

	/**
	 * Validate all indexes columnd are defined.
	 *
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @return void
	 */
	protected function validate_index_columns( Schema $schema ): void {
		// All defined colum names.
		$column_names = array_keys( $schema->get_columns() );

		/** @var array<Index> */
		$missing_columns = array_filter(
			$schema->get_indexes(),
			function( Index $index ) use ( $column_names ): bool {
				return ! in_array( $index->get_column(), $column_names, true );
			}
		);

		if ( count( $missing_columns ) > 0 ) {
			foreach ( $missing_columns as $missing_column ) {
				$this->errors[] = \sprintf( 'Index column %s not defined as a column in schema', $missing_column->get_column() );
			}
		}
	}

	/**
	 * Ensure all foreign keys are set again valid columns and have both refence table and column defined.
	 *
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @return void
	 */
	public function validate_foreign_keys( Schema $schema ): void {
		// All defined colum names.
		$column_names = array_keys( $schema->get_columns() );

		// Missing columns in local table

		/** @var array<Foreign_Key> */
		$missing_columns = array_filter(
			$schema->get_foreign_keys(),
			function( Foreign_Key $foreign_key ) use ( $column_names ): bool {
				return ! in_array( $foreign_key->get_column(), $column_names, true );
			}
		);

		if ( count( $missing_columns ) > 0 ) {
			foreach ( $missing_columns as $missing_column ) {
				$this->errors[] = \sprintf( 'Foreign Keys column %s not defined as a column in schema', $missing_column->get_column() );
			}
		}

		// Missing reference details.

		/** @var array<Foreign_Key> */
		$missing_references = array_filter(
			$schema->get_foreign_keys(),
			function( Foreign_Key $foreign_key ) : bool {
				return $foreign_key->get_reference_table() === null || $foreign_key->get_reference_column() === null;
			}
		);

		if ( count( $missing_references ) > 0 ) {
			foreach ( $missing_references as $missing_reference ) {
				$this->errors[] = \sprintf( 'Foreign Keys column %s has missing reference table or column details', $missing_reference->get_keyname() );
			}
		}
	}
}
