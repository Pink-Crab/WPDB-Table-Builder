<?php

declare(strict_types=1);

/**
 * Schema definition
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

namespace PinkCrab\Table_Builder;

use Exception;
use PinkCrab\Table_Builder\Index;
use PinkCrab\Table_Builder\Column;
use PinkCrab\Table_Builder\Foreign_Key;

class Schema {

	/**
	 * The table name
	 *
	 * @since 0.3.0
	 * @var string
	 */
	protected $table_name;

	/**
	 * The table name prefix
	 *
	 * @since 0.3.0
	 * @var string|null
	 */
	protected $prefix = null;

	/**
	 * Table columns
	 *
	 * @since 0.3.0
	 * @var array<Column>
	 */
	protected $columns = array();

	/**
	 * All table indexes
	 *
	 * @since 0.3.0
	 * @var array<Index>
	 */
	protected $indexes = array();

	/**
	 * All foreign key relations
	 *
	 * @since 0.3.0
	 * @var array<Foreign_Key>
	 */
	protected $foreign_keys = array();

	/**
	 * Creates an instance of Schema
	 *
	 * @since 0.3.0
	 * @param string $table_name
	 * @param callable(Schema):void|null $configure
	 */
	public function __construct( string $table_name, ?callable $configure = null ) {
		$this->table_name = $table_name;
		if ( is_callable( $configure ) ) {
			$configure( $this );
		}
	}

	/**
	 * Get the table name
	 *
	 * @since 0.3.0
	 * @return string
	 */
	public function get_table_name(): string {
		return \sprintf(
			'%s%s',
			$this->get_prefix(),
			$this->table_name
		);
	}

	/**
	 * Sets the table names prefix
	 *
	 * If null, will be treated as no preix.
	 *
	 * @since 0.3.0
	 * @param string|null $prefix
	 * @return self
	 */
	public function prefix( ?string $prefix = null ): self {
		$this->prefix = $prefix;
		return $this;
	}

	/**
	 * Checks if the table name should be prefixed.
	 *
	 * @since 0.3.0
	 * @return bool
	 */
	public function has_prefix(): bool {
		return $this->prefix !== null;
	}

	/**
	 * Get the table name prefix
	 *
	 * @since 0.3.0
	 * @return string
	 */
	public function get_prefix(): string {
		return $this->prefix ?? '';
	}


	/**
	 * Adds a new column to the schema
	 *
	 * @since 0.3.0
	 * @param string $name
	 * @return Column
	 */
	public function column( string $name ): Column {
		$column = new Column( $name );

		$this->columns[ $name ] = $column;
		return $column;
	}


	/**
	 * Get table colums
	 *
	 * @since 0.3.0
	 * @return array<Column>
	 */
	public function get_columns(): array {
		return $this->columns;
	}

	/**
	 * Checks if a column has been set based on name.
	 *
	 * @since 0.3.0
	 * @param string $name
	 * @return bool
	 */
	public function has_column( string $name ): bool {
		return count(
			array_filter(
				$this->get_columns(),
				function( Column $column ) use ( $name ): bool {
					return $column->get_name() === $name;
				}
			)
		) >= 1;
	}

	/**
	 * Removes a column from the stack based on its name.
	 *
	 * @since 0.3.0
	 * @param string $name
	 * @return self
	 * @throws Exception If columnn doesnt exist.
	 */
	public function remove_column( string $name ): self {
		if ( ! $this->has_column( $name ) ) {
			throw new Exception(
				sprintf(
					'%s doest exist in table %s',
					$name,
					$this->get_table_name()
				),
				1
			);
		}

		unset( $this->columns[ $name ] );

		return $this;
	}

	/**
	 * Sets an foreign key to the table
	 *
	 * @since 0.3.0
	 * @param string $column The column this FK index is set to.
	 * @param string|null $keyname if not set, will use the column name a tempalte.
	 * @return \PinkCrab\Table_Builder\Foreign_Key
	 */
	public function foreign_key( string $column, ?string $keyname = null ): Foreign_Key {
		$foreign_key = new Foreign_Key( $column, $keyname );

		$this->foreign_keys[] = $foreign_key;
		return $foreign_key;
	}

	/**
	 * Checks if foreign keys have been set.
	 *
	 * @return bool
	 */
	public function has_foreign_keys(): bool {
		return count( $this->foreign_keys ) !== 0;
	}

	/**
	 *
	 * Returns all the defined indexes.
	 *
	 * @since 0.3.0
	 * @return array<int, \PinkCrab\Table_Builder\Foreign_Key>
	 */
	public function get_foreign_keys(): array {
		return $this->foreign_keys;
	}

	/**
	 * Checks if foreign keys have been set.
	 *
	 * @return bool
	 */
	public function has_indexes(): bool {
		return count( $this->indexes ) !== 0;
	}

	/**
	 * Sets an index to the table.
	 *
	 * @since 0.3.0
	 * @param string $keyname
	 * @return \PinkCrab\Table_Builder\Index
	 */
	public function index( string $column, ?string $keyname = null ): Index {
		$index = new Index( $column, $keyname );

		$this->indexes[] = $index;
		return $index;
	}

	/**
	 * Returns all the defined indexes.
	 *
	 * @since 0.2.0
	 * @return array<int, \PinkCrab\Table_Builder\Index>
	 */
	public function get_indexes(): array {
		return $this->indexes;
	}


}
