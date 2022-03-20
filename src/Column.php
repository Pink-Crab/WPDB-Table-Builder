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

use PinkCrab\Table_Builder\Column_Types;

class Column {

	/**
	 * Gives access to various wrappers for
	 * types
	 *
	 * @method varchar( ?int $length = null )
	 * @method text( ?int $length = null )
	 * @method int( ?int $length = null )
	 * @method float( ?int $length = null )
	 * @method double( ?int $length = null )
	 * @method datetime( ?string $default = null )
	 * @method timestamp( ?string $default = null )
	 */
	use Column_Types;

	/**
	 * Column name
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The column type
	 *
	 * @var string|null
	 */
	protected $type = null;

	/**
	 * The column length
	 *
	 * @var int|null
	 */
	protected $length = null;

	/**
	 * The column precision
	 * Used for floating point values
	 *
	 * @var int|null
	 */
	protected $precision = null;

	/**
	 * Denotes if the column is nullable
	 *
	 * @var bool|null
	 */
	protected $nullable = null;

	/**
	 * The columns default value
	 *
	 * @var string|null
	 */
	protected $default = null;

	/**
	 * If the column has the auto incrememnt flag.
	 *
	 * @var bool|null
	 */
	protected $auto_increment = null;

	/**
	 * Is the columns value unsigned
	 *
	 * @var bool|null
	 */
	protected $unsigned = null;

	public function __construct( string $name ) {
		$this->name = $name;
	}

	/**
	 * Sets the columns type
	 *
	 * @param string $type
	 * @return self
	 */
	public function type( string $type ): self {
		$this->type = $type;
		return $this;
	}

	/**
	 * Sets the column length
	 *
	 * @param integer $length
	 * @return self
	 */
	public function length( int $length ): self {
		$this->length = $length;
		return $this;
	}

	/**
	 * Sets the column precision
	 *
	 * Only used for floating point numbers
	 *
	 * @param integer $precision
	 * @return self
	 */
	public function precision( int $precision ): self {
		$this->precision = $precision;
		return $this;
	}

	/**
	 * Denotes if the column is nullable
	 *
	 * @param boolean $nullable
	 * @return self
	 */
	public function nullable( bool $nullable = true ): self {
		$this->nullable = $nullable;
		return $this;
	}

	/**
	 * Sets the default value
	 *
	 * @param mixed $default
	 * @return self
	 */
	public function default( $default ): self {
		$this->default = $default;
		return $this;
	}

	/**
	 * Sets the default value
	 *
	 * @param boolean $auto_increment
	 * @return self
	 */
	public function auto_increment( bool $auto_increment = true ): self {
		$this->auto_increment = $auto_increment;
		return $this;
	}

	/**
	 * Denotes if the column is unsigned.
	 *
	 * @param boolean $unsigned
	 * @return self
	 */
	public function unsigned( bool $unsigned = true ): self {
		$this->unsigned = $unsigned;
		return $this;
	}

	/**
	 * Returns the column details as a stdClass
	 *
	 * @return object{
	 *  name:string,
	 *  type:string,
	 *  length:int|null,
	 *  precision:int|null,
	 *  nullable:bool,
	 *  default:string|int|float,
	 *  unsigned:bool,
	 *  auto_increment:bool,
	 *}
	 */
	public function export() {
		return (object) array(
			'name'           => $this->name,
			'type'           => $this->type,
			'length'         => $this->length,
			'precision'      => $this->precision,
			'nullable'       => $this->nullable ?? false,
			'default'        => $this->default,
			'unsigned'       => $this->unsigned ?? false,
			'auto_increment' => $this->auto_increment ?? false,
		);
	}

	/**
	 * Get column name
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Get the column type
	 *
	 * @return string|null
	 */
	public function get_type(): ?string {
		return $this->type;
	}

	/**
	 * Get the column length
	 *
	 * @return int|null
	 */
	public function get_length(): ?int {
		return $this->length;
	}

	/**
	 * Get used for floating point values
	 *
	 * @return int|null
	 */
	public function get_precision(): ?int {
		return $this->precision;
	}

	/**
	 * Get denotes if the column is nullable
	 * Returns false if column not set.
	 *
	 * @return bool
	 */
	public function is_nullable(): bool {
		return $this->nullable ?? false;
	}

	/**
	 * Get the columns default value
	 *
	 * @return mixed|null
	 */
	public function get_default() {
		return $this->default;
	}

	/**
	 * Get if the column has the auto increment flag.
	 * False if not set.
	 *
	 * @return bool
	 */
	public function is_auto_increment(): bool {
		return $this->auto_increment ?? false;
	}

	/**
	 * Get is the columns value unsigned
	 * False if not set.
	 *
	 * @return bool
	 */
	public function is_unsigned(): bool {
		return $this->unsigned ?? false;
	}

}
