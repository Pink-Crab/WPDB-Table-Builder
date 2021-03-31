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
	 * @var string
	 */
	protected $table_name;

	/**
	 * The table name prefix
	 *
	 * @var string|null
	 */
	protected $prefix = null;

	/**
	 * Table colums
	 *
	 * @var array<Column>
	 */
	protected $columns;

	/**
	 * All table indexes
	 *
	 * @var array<Index>
	 */
	protected $indexes;

	/**
	 * All foreign key relations
	 *
	 * @var array<Foreign_Key>
	 */
	protected $foreign_keys;

	/**
	 * Creates an instance of Schema
	 *
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
	 * Sets the table names prefix
	 *
	 * If null, will be treated as no preix.
	 *
	 * @param string|null $prefix
	 * @return self
	 */
	public function prefix( ?string $prefix = null ): self {
		$this->prefix = $prefix;
		return $this;
	}

	/**
	 * Adds a new column to the schema
	 *
	 * @param string $name
	 * @return Column
	 */
	public function column( string $name ): Column {
		$column                 = new Column( $name );
		$this->columns[ $name ] = $column;
		return $column;
	}

	/**
	 * Sets an index to the table.
	 *
	 * @param string $index_key
	 * @return \PinkCrab\Table_Builder\Index
	 */
	public function index( string $index_key ): Index {
		$index                       = new Index();
		$this->indexes[ $index_key ] = $index;
		return $index;
	}

	/** GETTERS */

	/**
	 * Get the table name
	 *
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
	 * Get table colums
	 *
	 * @return array<Column>
	 */
	public function get_columns(): array {
		return $this->columns;
	}

	/**
	 * Checks if a column has been set based on name.
	 *
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
	 * Checks if the table name should be prefixed.
	 *
	 * @return bool
	 */
	public function has_prefix(): bool {
		return $this->prefix !== null;
	}

	/**
	 * Get the table name prefix
	 *
	 * @return string
	 */
	public function get_prefix(): string {
		return $this->prefix ?? '';
	}


}
