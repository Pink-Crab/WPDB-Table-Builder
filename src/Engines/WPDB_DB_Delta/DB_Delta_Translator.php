<?php

declare(strict_types=1);

/**
 * Interface for the translators.
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

class DB_Delta_Translator {

	/**
	 * Returns the parsed strings for all columns based on the schema passed.
	 *
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @return array<string>
	 */
	public function translate_columns( Schema $schema ): array {
		return array_map(
			function( Column $column ): string {

				return sprintf(
					'%s %s%s%s%s%s',
					$column->get_name(),
					$this->type_mapper( $column->get_type() ?? '', $column->get_length() ),
					$column->is_unsigned() ? ' UNSIGNED' : '',
					$column->is_nullable() ? ' NULL' : ' NOT NULL',
					$column->is_auto_increment() ? ' AUTO_INCREMENT' : '',
					$this->parse_default( $column->get_type() ?? '', $column->get_default() )
				);
			},
			$schema->get_columns()
		);
	}

	/**
	 * Returns the parsed strings for all indexes based on the schema passed.
	 * Should translate all Primary, Indexes and Foreign Keys
	 *
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @return array<string>
	 */
	public function translate_indexes( Schema $schema ): array {
		return array_merge(
			$this->transform_primary( $schema ),
			$this->transform_indexes( $schema ),
			$this->transform_foreign_keys( $schema )
		);
	}

		/**
	 * Maps types with length if set.
	 *
	 * @param string $type
	 * @param int|null $length
	 * @return string
	 */
	protected function type_mapper( string $type, ?int $length ): string {
		$type = strtoupper( $type );
		switch ( $type ) {
			// With length
			case 'CHAR':
			case 'VARCHAR':
			case 'BINARY':
			case 'VARBINARY':
			case 'TEXT':
			case 'BLOB':
				// Int
			case 'BIT':
			case 'TINYINT':
			case 'SMALLINT':
			case 'MEDIUMINT':
			case 'INT':
			case 'INTEGER':
			case 'BIGINT':
				// Floats
			case 'FLOAT':
			case 'DOUBLE':
			case 'DOUBLE PRECISION':
			case 'DECIMAL':
			case 'DEC':
				// Date
			case 'DATETIME':
			case 'TIMESTAMP':
			case 'TIME':
				return is_null( $length ) ? $type : "{$type}({$length})";

			default:
				return $type;
		}
	}

	/**
	 * Parses the default value based on column type.
	 *
	 * @param string $type
	 * @param string|null $defualt
	 * @return string
	 */
	protected function parse_default( string $type, ?string $defualt ): string {
		if ( is_null( $defualt ) ) {
			return '';
		}

		$type = strtoupper( $type );

		// String values.
		if ( in_array( $type, array( 'CHAR', 'VARCHAR', 'BINARY', 'VARBINARY', 'TEXT', 'BLOB' ), true ) ) {
			return " DEFAULT '{$defualt}'";
		}

		return " DEFAULT {$defualt}";
	}

		/**
	 * Parses the primary key from the defined schema.
	 *
	 * This should only return an array with a single value if any.
	 *
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @return array<string>
	 */
	protected function transform_primary( Schema $schema ): array {
		return array_map(
			function( $index ) {
				return "PRIMARY KEY  ({$index->get_column()})";
			},
			array_filter(
				$schema->get_indexes(),
				function( $index ): bool {
					return $index->is_primary();
				}
			)
		);
	}

	/**
	 * Parses the indexes into a SQL strings.
	 *
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @return array<string>
	 */
	protected function transform_indexes( Schema $schema ): array {
		return array_map(
			/** @param Index[] $index_group  */
			function( array $index_group ): string {

				// Extract all parts from group.
				$key_name   = $index_group[0]->get_keyname();
				$index_type = $index_group[0]->get_type();
				$columns    = array_map(
					function( $e ) {
						return $e->get_column();
					},
					$index_group
				);

				return sprintf(
					'%sINDEX %s (%s)',
					\strlen( $index_type ) !== 0 ? \strtoupper( $index_type ) . ' ' : '',
					$key_name,
					join( ', ', $columns )
				);

			},
			$this->group_indexes( $schema )
		);
	}

	/**
	 * Parses the foreign key index to SQL strings
	 *
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @return array<string>
	 */
	protected function transform_foreign_keys( Schema $schema ): array {
		return array_map(
			function( Foreign_Key $foreign_key ): string {
				return \sprintf(
					'FOREIGN KEY %s(%s) REFERENCES %s(%s)%s%s',
					$foreign_key->get_keyname(),
					$foreign_key->get_column(),
					$foreign_key->get_reference_table(),
					$foreign_key->get_reference_column(),
					\strlen( $foreign_key->get_on_update() ) ? " ON UPDATE {$foreign_key->get_on_update()}" : '',
					\strlen( $foreign_key->get_on_delete() ) ? " ON DELETE {$foreign_key->get_on_delete()}" : ''
				);
			},
			$schema->get_foreign_keys()
		);
	}

	/**
	 * Groups the indexes by keyname and type.
	 *
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @return array<string>
	 */
	protected function group_indexes( Schema $schema ): array {
		return array_reduce(
			$schema->get_indexes(),
			function( array $carry, Index $index ): array {
				// Remove all primiary keys.
				if ( $index->is_primary() ) {
					return $carry;
				}

				$carry[ $index->get_keyname() . '_' . $index->get_type() ][] = $index;

				return $carry;
			},
			array()
		);
	}
}
