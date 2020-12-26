<?php

declare(strict_types=1);

/**
 * Handles databse indexes.
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

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

final class Table_Index {

	/**
	 * Index name
	 *
	 * @since 0.1.0
	 * @var string
	 */
	public $keyname;

	/**
	 * Columns
	 *
	 * @since 0.1.0
	 * @var array<string, array>
	 */
	public $column = array();

	/**
	 * Sets the reference column
	 *
	 * @since 0.1.0
	 * @var array
	 */
	public $reference_column = array();

	/**
	 * The table used.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	public $reference_table;

	/**
	 * Uses a foreign key
	 *
	 * @since 0.1.0
	 * @var bool
	 */
	public $foreign_key = false;

	/**
	 * Unique index
	 *
	 * @since 0.1.0
	 * @var bool
	 */
	public $unique = false;

	/**
	 * Allow full text index.
	 *
	 * @since 0.2.0
	 * @var bool
	 */
	public  $full_text = false;

	/**
	 * Using HASH
	 *
	 * @since 0.1.0
	 * @var bool
	 */
	public $hash = false;

	/**
	 * Command to execute on update.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	public $on_update = '';

	/**
	 * Command to execute on delete.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	public $on_delete = '';


	public function __construct( string $keyname ) {
		$this->name = $keyname;
	}

	/**
	 * Creates a static instance with a defined name.
	 *
	 * @since 0.1.0
	 * @param string $keyname
	 * @return self
	 */
	public static function name( string $keyname ): self {
		$key = new static( $keyname );
		return $key;
	}

	/**
	 * Add columns to the key.
	 *
	 * @since 0.1.0
	 * @param string $column
	 * @return self
	 */
	public function column( string $column ): self {
		$this->column = $column;
		return $this;
	}

	/**
	 * Are the key unique
	 *
	 * @since 0.1.0
	 * @param boolean $unique
	 * @return self
	 */
	public function unique( bool $unique = true ): self {
		$this->unique = $unique;
		return $this;
	}

	/**
	 * Are the key unique
	 *
	 * @since 0.1.0
	 * @param boolean $full_text
	 * @return self
	 */
	public function full_text( bool $full_text = true ): self {
		$this->full_text = $full_text;
		return $this;
	}

	/**
	 * The column to use a the foreign key.
	 *
	 * @since 0.1.0
	 * @param boolean $foreign_key
	 * @return self
	 */
	public function foreign_key( bool $foreign_key = true ): self {
		$this->foreign_key = $foreign_key;
		return $this;
	}

	/**
	 * Sets the index as using hash not btree
	 *
	 * @since 0.1.0
	 * @param boolean $hash
	 * @return self
	 */
	public function hash( bool $hash = true ): self {
		$this->hash = $hash;
		return $this;
	}

	/**
	 * Set the reference table
	 *
	 * @since 0.1.0
	 * @param string $reference_table
	 * @return self
	 */
	public function reference_table( string $reference_table ): self {
		$this->reference_table = $reference_table;
		return $this;
	}

	/**
	 * Add reference_column to the key.
	 *
	 * @since 0.1.0
	 * @param string $columns
	 * @return self
	 */
	public function reference_column( string $reference_column ): self {
		$this->reference_column = $reference_column;
		return $this;
	}

	/**
	 * Sets the on update action.
	 *
	 * @since 0.1.0
	 * @param string $action
	 * @return self
	 */
	public function on_update( string $action ): self {
		$this->on_update = 'ON UPDATE ' . strtoupper( $action );
		return $this;
	}


	/**
	 * Sets the on update action.
	 *
	 * @since 0.1.0
	 * @param string $action
	 * @return self
	 */
	public function on_delete( string $action ): self {
		$this->on_delete = 'ON DELETE ' . strtoupper( $action );
		return $this;
	}
}
