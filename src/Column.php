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

	/**
	 * Is a unique columm
	 *
	 * @var bool|null
	 */
	protected $unique = null;

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
	 * @param string $default
	 * @return self
	 */
	public function default( string $default ): self {
		$this->default = $default;
		return $this;
	}

	/**
	 * Denotes if the column is unsigned.
	 *
	 * @param boolean $unsigned
	 * @return self
	 */
	public function unsigned( bool $unsigned ): self {
		$this->unsigned = $unsigned;
		return $this;
	}

	/**
	 * Set is a unique columm
	 *
	 * @param bool $unique  Is a unique columm
	 * @return self
	 */
	public function unique( bool $unique = true ): self {
		$this->unique = $unique;
		return $this;
	}

	/**
	 * Returns the column details as a stdClass
	 *
	 * @return object
	 */
	public function export() {
		return (object) array(
			'name'     => $this->name,
			'type'     => $this->type,
			'length'   => $this->length,
			'nullable' => $this->nullable,
			'default'  => $this->default,
			'unsigned' => $this->unsigned,
			'unique'   => $this->unique,
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
}
