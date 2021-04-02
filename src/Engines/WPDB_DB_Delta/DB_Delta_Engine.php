<?php

declare(strict_types=1);

/**
 * WPDB DB_Delta table builder.
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
use PinkCrab\Table_Builder\Engines\Engine;
use PinkCrab\Table_Builder\Engines\Schema_Validator;
use PinkCrab\Table_Builder\Engines\WPDB_DB_Delta\DB_Delta_Validator;

class DB_Delta_Engine implements Engine {

	/**
	 * The engines validator class name.
	 *
	 * @var Schema_Validator
	 */
	protected $validator;

	/**
	 * Access to wpdb
	 *
	 * @var \wpdb
	 */
	protected $wpdb;


	/**
	 * Holds access to the Schema definition
	 *
	 * @var Schema
	 */
	protected $schema;

	public function __construct( \wpdb $wpdb, ?callable $configure = null ) {
		$this->wpdb = $wpdb;

		if ( $configure ) {
			$configure( $this );
		}

		// Set vaidator.
		$this->validator = new DB_Delta_Validator();
	}

	/**
	 * Returns an intance of the valditor.
	 *
	 * @return \PinkCrab\Table_Builder\Engines\Schema_Validator
	 */
	public function get_validator(): Schema_Validator {
		return $this->validator;
	}

	/**
	 * Create the table based on the schema passed.
	 *
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @return bool
	 */
	public function create_table( Schema $schema ): bool {
		$this->schema = $schema;
		if ( ! $this->validator->validate( $schema ) ) {
			return false;
		}
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $this->compile_create_sql_query() );
		dump( $this->compile_create_sql_query(), $this->wpdb );

		return true;
	}

	public function drop_table( Schema $schema ): bool {
		$this->schema = $schema;
		if ( ! $this->validator->validate( $schema ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Compiles the SQL query used to create a table.
	 *
	 * @return string
	 */
	protected function compile_create_sql_query(): string {

		// Compile query partials.
		$table   = $this->schema->get_table_name();
		$body    = join(
			',' . PHP_EOL,
			array_merge(
				$this->transform_columns(),
				$this->transform_primary(),
				$this->transform_indexes(),
				$this->transform_foreign_keys()
			)
		);
		$collate = $this->wpdb->collate;

		return <<<SQL
CREATE TABLE $table (
$body ) COLLATE $collate
SQL;
	}

	/**
	 * Renders the array of columns into an array of string represenetations.
	 *
	 * @return array<string>
	 */
	protected function transform_columns(): array {
		return array_map(
			function( Column $column ): string {
				$column_data = $column->export();

				return sprintf(
					'%s %s%s%s%s%s',
					$column_data->name,
					$this->type_mapper( $column_data->type, $column_data->length ),
					$column_data->unsigned ? ' UNSIGNED' : '',
					$column_data->nullable ? ' NULL' : ' NOT NULL',
					$column_data->auto_increment ? ' AUTO_INCREMENT' : '',
					$this->parse_default( $column_data->type, $column_data->default )
				);
			},
			$this->schema->get_columns()
		);
	}
	/**
	 * Maps types with length if set.
	 *
	 * @param string $type
	 * @param [type] $length
	 * @return string
	 */
	protected function type_mapper( string $type, $length ): string {
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
				return empty( $length ) ? $type : "{$type}({$length})";

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
	 * @return array<string>
	 */
	protected function transform_primary(): array {
		return array_map(
			function( $index ) {
				return "PRIMARY KEY  ({$index->get_column()})";
			},
			array_filter(
				$this->schema->get_indexes(),
				function( $index ): bool {
					return $index->is_primary();
				}
			)
		);
	}

	/**
	 * Parses the indexes into a SQL strings.
	 *
	 * @return array<string>
	 */
	protected function transform_indexes(): array {
		return array_map(
			/** @param array<Index> */
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
			$this->group_indexes()
		);
	}

	/**
	 * Parses the foreign key index to SQL strings
	 *
	 * @return array
	 */
	protected function transform_foreign_keys(): array {
		return array_map(
			function( Foreign_Key $foreign_key ): string {
				return \sprintf(
					'FOREIGN KEY %s(%s) REFERENCES %s(%s)%s%s',
					$foreign_key->get_keyname(),
					$foreign_key->get_column(),
					$foreign_key->get_reference_table(),
					$foreign_key->get_reference_column(),
					\strlen( $foreign_key->get_on_update() ) ? " ON UPDATE {$foreign_key->get_on_update()}" : '',
					\strlen( $foreign_key->get_on_delete() ) ? " ON DELETE {$foreign_key->get_on_delete()}" : '',
				);
			},
			$this->schema->get_foreign_keys()
		);
	}

	/**
	 * Groups the indexes by keyname and type.
	 *
	 * @return array
	 */
	protected function group_indexes(): array {
		return array_reduce(
			$this->schema->get_indexes(),
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
