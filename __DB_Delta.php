<?php declare(strict_types=1);
/**
 * The DB Delta builder for use with WPDB
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
 * @package PinkCrab\Table_Builder
 */

namespace PinkCrab\Table_Builder\Builders;

use wpdb;
use Exception;
use PinkCrab\Table_Builder\Interfaces\SQL_Schema;
use PinkCrab\Table_Builder\Interfaces\SQL_Builder;

class DB_Delta implements SQL_Builder {

	/**
	 * Holds the schema
	 *
	 * @var \PinkCrab\Table_Builder\Interfaces\SQL_Schema $schema
	 */
	protected $schema;

	/**
	 * Instance of wpdb
	 *
	 * @var wpdb
	 */
	protected $wpdb;

	/**
	 * Create instance of DB_Delta with WPDB injected.
	 *
	 * @param wpdb $wpdb
	 */
	public function __construct( wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	/**
	 * Builds a table based on a passed schema.
	 *
	 * @param \PinkCrab\Table_Builder\Interfaces\SQL_Schema $schema
	 * @return void
	 */
	public function build( SQL_Schema $schema ): void {
		$this->schema = $schema;
		$this->validate_schema();
		$this->render();
	}

	/**
	 * Drops a table if it exists, based on the schema passed.
	 *
	 * @param SQL_Schema $schema
	 * @return void
	 */
	public function drop( SQL_Schema $schema ): void {
		$this->validate_schema();
		$this->wpdb->get_results( "DROP TABLE IF EXISTS {$schema->get_table_name()};" ); // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared 
	}

	/**
	 * Checks all required values are defined.
	 *
	 * @return void
	 * @throws Exception
	 */
	protected function validate_schema(): void {
		// Check has table name.
		if ( ! is_string( $this->schema->get_table_name() ) || empty( $this->schema->get_table_name() ) ) {
			throw new Exception( 'Table name not defined in schema' );
		}

		// Check all columns have required fields
		$invalid_columns = array_filter(
			$this->schema->get_columns(),
			function( array $col ): bool {
				return ! \array_key_exists( 'key', $col )
					|| ! \array_key_exists( 'null', $col )
					|| ! \array_key_exists( 'type', $col )
					|| ! \array_key_exists( 'length', $col );
			}
		);
		if ( ! empty( $invalid_columns ) ) {
			throw new Exception( sprintf( 'Columns in %s schema are missing required properties.', $this->schema->get_table_name() ) );
		}

		if ( ! empty( $this->schema->get_indexes() ) ) {
			$this->validate_indexes();
		}
	}

	/**
	 * Validates indexes if set.
	 *
	 * @return void
	 * @throws Exception
	 */
	protected function validate_indexes(): void {
		foreach ( $this->schema->get_indexes() as $key => $index ) {
			if ( $index->foreign_key === true && (
				empty( $index->reference_table ) || empty( $index->reference_column )
			) ) {
				throw new Exception(
					sprintf(
						'Foreign key index "%s" requires both reference table(%s) and reference column(%s) defined.',
						$index->keyname,
						! empty( $index->reference_table ) ? $index->reference_table : 'UNDEFINED',
						! empty( $index->reference_column ) ? $index->reference_column : 'UNDEFINED'
					)
				);
			}
		}
	}

	/**
	 * Builds the table use dbDelta();
	 *
	 * @return void
	 */
	protected function render(): void {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $this->compile_create_sql() );
	}

	/**
	 * Compiles the SQL query for creating table
	 *
	 * @return string
	 */
	protected function compile_create_sql(): string {
		return "CREATE TABLE {$this->schema->get_table_name()} (
{$this->parse_columns()},
{$this->parse_primary_key()}{$this->parse_indexes()})
COLLATE {$this->wpdb->collate} ";
	}

	/**
	 * Parses the primary key, if set.
	 *
	 * @return string
	 */
	public function parse_primary_key(): string {
		return ! empty( $this->schema->get_primary_key() )
		? "PRIMARY KEY  ({$this->schema->get_primary_key()})" : '';
	}

	/**
	 * Parses the columns to a string
	 *
	 * @return string The query string for parsing the coloumns.
	 */
	protected function parse_columns(): string {
		return implode(
			',' . PHP_EOL,
			array_map(
				function( $col ) {
					$string = "{$col['key']} {$this->parse_type($col['type'], $col['length'])}";
					// If Unsigned
					if ( ! empty( $col['default'] ) ) {
						$string .= $this->parse_default( $col );
					}

					// If Unsigned
					if ( ! empty( $col['unsigned'] ) ) {
						$string .= ' UNSIGNED';
					}

					// If null
					if ( ! $col['null'] ) {
						$string .= ' NOT NULL';
					}

					// If auto increment
					if ( ! empty( $col['auto_increment'] ) ) {
						$string .= ' AUTO_INCREMENT';
					}

					return $string;
				},
				$this->schema->get_columns()
			)
		);
	}

	/**
	 * Parses the DEFUALT value in the column definition.
	 *
	 * @param array<string, mixed> $column
	 * @return string
	 */
	protected function parse_default( array $column ): string {
		$protected_constants = array( 'CURRENT_TIMESTAMP' );

		return in_array( $column['default'], $protected_constants, true )
		? ' DEFAULT ' . $column['default']
		: " DEFAULT '{$column['default']}'";
	}

	/**
	 * Parses the indexes as either
	 *
	 * @return string The parse indexes string.
	 */
	protected function parse_indexes() : string {

		$indexes = $this->compile_indexes();

		$new_line = ',' . PHP_EOL;

		$indexes['formatted'] = array_merge(
			array_map(
				function( $index ) {
					$unique = $index->unique ? 'UNIQUE ' : '';
					$hash   = $index->hash ? ' USING HASH' : '';
					return "{$unique}INDEX {$index->keyname} ({$index->column}){$hash}";
				},
				$indexes['is_simple']
			),
			$this->parseForeignTableQuery( $indexes['is_foreign'] )
		);

		// Remove all empty indexes.
		$indexes['formatted'] = array_filter(
			$indexes['formatted'],
			function( $e ) {
				return ! empty( $e );
			}
		);

		return ( ! empty( $indexes['formatted'] ) ? $new_line : '' )
		. implode( $new_line, $indexes['formatted'] );
	}

	/**
	 * Composes the simple and complex indexes into a single array of query strings.
	 *
	 * @param array<string, \PinkCrab\Table_Builder\Table_Index> $indexes All indexes for the table.
	 * @return array<string, mixed>
	 */
	protected function parseForeignTableQuery( array $indexes ):array {
		// Group into index and key groups.
		$grouped_indexes = array_reduce(
			$indexes,
			function( $carry, $table ) {
				$carry['index'] = "INDEX ({$this->index_local_table( $table, 'column' )})";
				$carry['key']   = $this->compile_foreign_key_query( $table );
				return $carry;
			},
			array(
				'index' => array(),
				'key'   => array(),
			)
		);
		return $grouped_indexes;
	}

	/**
	 * Compiles the (foreign) key query.
	 * Its a bit knarly i know.
	 *
	 * @param array<string, \PinkCrab\Table_Builder\Table_Index> $table Each related tables indexes.
	 * @return string
	 */
	protected function compile_foreign_key_query( array $table ): string {
		$remote_table = array_values(
			array_unique(
				array_map(
					function( $e ) {
						return $e->reference_table;
					},
					$table
				)
			)
		);

		$query = "FOREIGN KEY ({$this->index_local_table( $table, 'column' )})
REFERENCES {$remote_table[0]}({$this->index_local_table( $table, 'reference_column' )})";

		// Add "ON UPDATE XX"
		foreach ( $table as $value ) {
			if ( ! empty( $value->on_update ) ) {
				$query .= ' ' . $value->on_update;
				break;
			}
		}

		// Add "ON DELETE XX"
		foreach ( $table as $value ) {
			if ( ! empty( $value->on_delete ) ) {
				$query .= ' ' . $value->on_delete;
				break;
			}
		}
		return $query . "\n";

	}

	/**
	 * Returns a comma seperated string of any column from a tables refernces.
	 *
	 * @param array<string, \PinkCrab\Table_Builder\Table_Index> $table A tables indexes.
	 * @param string $column The column to return.
	 * @return string
	 */
	protected function index_local_table( $table, string $column ): string {
		return implode(
			', ',
			array_map(
				function( $e ) use ( $column ) {
					return $e->{$column};
				},
				$table
			)
		);
	}

	/**
	 * Compiles the indexes into a forign and simple.
	 *
	 * @return array<string, mixed> Array of both simple and complex indexes (complex grouped by referenced table.)
	 */
	protected function compile_indexes(): array {
		return array_reduce(
			$this->schema->get_indexes(),
			function( $carry, $index ) {
				if ( $index->foreign_key ) {
					$carry['is_foreign'][ $index->reference_table ][ $index->keyname ] = $index;
				} else {
					$carry['is_simple'][ $index->keyname ] = $index;
				}
				return $carry;
			},
			array(
				'is_foreign' => array(),
				'is_simple'  => array(),
			)
		);
	}

	// /**
	//  * Return the string for NULL or NOT NULL.
	//  *
	//  * @param bool|null $null If true set to NULL.
	//  * @return string
	//  * @deprecated 0.2.1 Not actually used.....
	//  */
	// protected function parse_null( ?bool $null ): string {
	// 	// If null
	// 	if ( is_null( $null ) ) {
	// 		return '';
	// 	}
	// 	return $null ? 'NULL' : 'NOT NULL';
	// }

	/**
	 * Parses the type based on its type and length passed.
	 *
	 * @param string|null $type The column type.
	 * @param int|null $length The allowed length.
	 * @return string The parse column type(length).
	 */
	protected function parse_type( ?string $type = '', ?int $length = null ): string {
		$type = strtoupper( $type ?? '' );
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
				return empty( $length ) ? $this->type_alias( $type ) : "{$this->type_alias($type)}({$length})";

			default:
				return $this->type_alias( $type );
		}
	}

	/**
	 * Replaces the values entered with alias suited for dbDelta.
	 *
	 * @param string $type The defined column type.
	 * @return string The replaced value.
	 */
	protected function type_alias( string $type ): string {
		// This is current unused, but kept incase mapping is needed later.
		return $type;
	}
}
