<?php declare(strict_types=1);
/**
 * A class based table builder.
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
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Modules\Table_Builder
 */

namespace PinkCrab\Modules\Table_Builder;

use Exception;
use PinkCrab\Modules\Table_Builder\Table_Index;
use PinkCrab\Modules\Table_Builder\Interfaces\SQL_Schema;
use PinkCrab\Modules\Table_Builder\Interfaces\SQL_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

final class Table_Schema implements SQL_Schema {

	/**
	 * The tablename
	 *
	 * @since 0.1.0
	 * @var string
	 */
	protected $table_name;

	/**
	 * The primary index
	 *
	 * @since 0.1.0
	 * @var string
	 */
	protected $primary_key;

	/**
	 * All table indexes
	 *
	 * @since 0.1.0
	 * @var array<int, Table_Index>
	 */
	protected $indexes = array();

	/**
	 * All columns
	 *
	 * @since 0.1.0
	 * @var array<string, array>
	 */
	protected $columns = array();

	/**
	 * Allows the settings of the table name.
	 * Should not be called if created statically as table_name is defined in init.
	 *
	 * @since 0.1.0
	 * @param string $table_name
	 * @return self
	 */
	public function table( string $table_name ): self {
		$this->table_name = $table_name;
		return $this;
	}

	/**
	 * Creates a self instace of the class
	 *
	 * @since 0.1.0
	 * @param string $table_name
	 * @return self
	 */
	public static function create( string $table_name ): self {
		$new = new self();
		$new->table( $table_name );
		return $new;
	}

	/**
	 * Set the primary key.
	 *
	 * @since 0.1.0
	 * @param string $key
	 * @return self
	 */
	public function primary( string $key ) {
		$this->primary_key = $key;
		return $this;
	}

	/**
	 * Adds an key
	 *
	 * @since 0.1.0
	 * @param Table_Index $index If the index unique.
	 * @return self
	 */
	public function index( Table_Index $index ): self {
		array_push( $this->indexes, $index );
		return $this;
	}

	/**
	 * Starts new column with a few defaults.
	 *
	 * @since 0.1.0
	 * @param string $key
	 * @return self
	 */
	public function column( string $key ): self {
		$this->columns[ $key ] = array(
			'key'    => $key,
			'null'   => false,
			'length' => null,
		);
		return $this;
	}

	/**
	 * Sets the type to the last column added.
	 *
	 * @since 0.1.0
	 * @param string $type
	 * @return self
	 */
	public function type( string $type ): self {
		$this->push_to_last_column( 'type', $type );
		return $this;
	}

	/**
	 * Sets if the last column is nullable
	 *
	 * Changed to nullable in version 0.2.0
	 *
	 * @since 0.2.0
	 * @param boolean $null
	 * @return self
	 */
	public function nullable( bool $null = true ): self {
		$this->push_to_last_column( 'null', $null );
		return $this;
	}

	/**
	 * Sets if the last column is nullable
	 *
	 * NOW DEPRECATED, USED NULLABLE.
	 * WILL BE REMOVED IN FUTURE VERSIONS.
	 *
	 * @since 0.1.0
	 * @param boolean $null
	 * @return self
	 * @deprecated version 0.2.0
	 */
	public function null( bool $null = true ): self {
		// Trigger DEPRECATED notice
		trigger_error(
			'Method ' . __METHOD__ . ' is deprecated, pleae use nullable(bool): self in place. Will be removed in future versions.',
			E_USER_DEPRECATED
		);

		$this->push_to_last_column( 'null', $null );
		return $this;
	}

	/**
	 * Sets the length of the column (can be left blank if type doesnt need length)
	 *
	 * @since 0.1.0
	 * @param int $length
	 * @return self
	 */
	public function length( int $length ): self {
		$this->push_to_last_column( 'length', $length );
		return $this;
	}

	/**
	 * Sets the default value.
	 *
	 * @since 0.1.0
	 * @param string|null $default The default value, if null is passed with parse as NULL.
	 * @return self
	 */
	public function default( ?string $default = null ): self {
		$this->push_to_last_column( 'default', $default );
		return $this;
	}

	/**
	 * Should the column auto increment
	 *
	 * @since 0.1.0
	 * @param boolean $auto_increment
	 * @return self
	 */
	public function auto_increment( bool $auto_increment = true ): self {
		$this->push_to_last_column( 'auto_increment', $auto_increment );
		return $this;
	}

	/**
	 * Should the column be an unsigned (int)
	 *
	 * @since 0.1.0
	 * @param boolean $unsigned
	 * @return self
	 */
	public function unsigned( bool $unsigned = true ): self {
		$this->push_to_last_column( 'unsigned', $unsigned );
		return $this;
	}

	/**
	 * Pushes a key => value to the last entry of the columns array.
	 *
	 * @since 0.1.0
	 * @param string $key The key of the pair
	 * @param mixed  $value The value of the pair.
	 * @return void
	 * @throws Exception If no columns yet defined.
	 */
	private function push_to_last_column( string $key, $value ): void {
		if ( empty( $this->columns ) ) {
			throw new Exception( 'No columns defined, please define a column before adding its properties.' );

		}
		$this->columns[ array_key_last( $this->columns ) ][ $key ] = $value;
	}

	/**
	 * Used to build the table.
	 *
	 * @since 0.1.0
	 * @param SQL_Builder $table_builder
	 * @return void
	 */
	public function create_table( SQL_Builder $table_builder ): void {
		$table_builder->build( $this );
	}

	/**
	 * Gets the defined table name
	 *
	 * @since 0.2.0
	 * @return string
	 */
	public function get_table_name(): string {
		return $this->table_name;
	}

	/**
	 * Gets the defined primary key
	 *
	 * @since 0.2.0
	 * @return string
	 */
	public function get_primary_key(): string {
		return $this->primary_key;
	}

	/**
	 * Returns all the defined indexes.
	 *
	 * @since 0.2.0
	 * @return array<int, \PinkCrab\Modules\Table_Builder\Table_Index>
	 */
	public function get_indexes(): array {
		return $this->indexes;
	}

	/**
	 * Returns all the defined columns
	 *
	 * @since 0.2.0
	 * @return array<int, array>
	 */
	public function get_columns(): array {
		return $this->columns;
	}

	/** TYPE SETTERS */

	/**
	 * Sets column as varchar wtih a definied length.
	 *
	 * @since 0.2.0
	 * @param int|null $length
	 * @return self
	 */
	public function varchar( ?int $length = null ): self {
		$this->type( 'varchar' );
		if ( $length ) {
			$this->length( $length );
		}
		return $this;
	}

	/**
	 * Sets column as text wtih a definied length.
	 *
	 * @since 0.2.0
	 * @param int|null $length
	 * @return self
	 */
	public function text( ?int $length = null ): self {
		$this->type( 'text' );
		if ( $length ) {
			$this->length( $length );
		}
		return $this;
	}

	/**
	 * Sets column as int wtih a definied length.
	 *
	 * @since 0.2.0
	 * @param int|null $length
	 * @return self
	 */
	public function int( ?int $length = null ): self {
		$this->type( 'int' );
		if ( $length ) {
			$this->length( $length );
		}
		return $this;
	}

	/**
	 * Sets column as float wtih a definied length.
	 *
	 * @since 0.2.0
	 * @param int|null $length
	 * @return self
	 */
	public function float( ?int $length = null ): self {
		$this->type( 'float' );
		if ( $length ) {
			$this->length( $length );
		}
		return $this;
	}

	/**
	 * Sets column as double wtih a definied length.
	 *
	 * @since 0.2.0
	 * @param int|null $length
	 * @return self
	 */
	public function double( ?int $length = null ): self {
		$this->type( 'double' );
		if ( $length ) {
			$this->length( $length );
		}
		return $this;
	}

	/**
	 * Sets column as datetime with an optional default.
	 *
	 * @since 0.2.0
	 * @param int|null $length
	 * @return self
	 */
	public function datetime( ?string $default = null ): self {
		$this->type( 'datetime' );
		if ( $default ) {
			$this->default( $default );
		}
		return $this;
	}

	/**
	 * Sets column as timestamp with an optional default.
	 *
	 * @since 0.2.0
	 * @param int|null $default
	 * @return self
	 */
	public function timestamp( ?string $default = null ): self {
		$this->type( 'timestamp' );
		if ( $default ) {
			$this->default( $default );
		}
		return $this;
	}
}
