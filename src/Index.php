<?php

declare(strict_types=1);

/**
 * Table Index definition
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

class Index {

	/**
	 * Index name
	 *
	 * @since 0.1.0
	 * @var string
	 */
	protected $keyname;

	/**
	 * Column referenced
	 *
	 * @since 0.1.0
	 * @var string
	 */
	protected $column;

	/**
	 * Unique index
	 *
	 * @since 0.1.0
	 * @var bool
	 */
	protected $unique = false;

	/**
	 * Allow full text index.
	 *
	 * @since 0.2.0
	 * @var bool
	 */
	protected $full_text = false;

	/**
	 * is primary key
	 *
	 * @since 0.3.0
	 * @var bool
	 */
	protected $primary = false;

	public function __construct( string $column, ?string $keyname = null ) {
		$this->keyname = $keyname ?? 'ix_' . $column;
		$this->column  = $column;
	}

	/**
	 * Set is primary key
	 *
	 * @param bool $primary  is primary key
	 * @return self
	 */
	public function primary( bool $primary = true ): self {
		$this->primary = $primary;
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
	 * Get index name
	 *
	 * @return string
	 */
	public function get_keyname(): string {
		return $this->keyname;
	}

	/**
	 * Exports the index as a stdClass
	 *
	 * @return object
	 */
	public function export() {
		return (object) array(
			'keyname'   => $this->keyname,
			'column'    => $this->column,
			'primary'   => $this->primary,
			'unique'    => $this->unique,
			'full_text' => $this->full_text,
		);
	}


	/**
	 * Get column referenced
	 *
	 * @return string
	 */
	public function get_column(): string {
		return $this->column;
	}

	/**
	 * Checks if using a using HASH
	 *
	 * @return bool
	 */
	public function is_primary(): bool {
		return $this->primary;
	}

	/**
	 * Checks if using a unique index
	 *
	 * @return bool
	 */
	public function is_unique(): bool {
		return $this->unique;
	}

	/**
	 * Checks if using afull text index.
	 *
	 * @return bool
	 */
	public function is_full_text(): bool {
		return $this->full_text;
	}

	/**
	 * Retuns the index type.
	 *
	 * @return string
	 */
	public function get_type(): string {
		if ( $this->is_primary() ) {
			return 'primary';
		} elseif ( $this->is_unique() ) {
			return 'unique';
		} elseif ( $this->is_full_text() ) {
			return 'fulltext';
		} else {
			return '';
		}
	}
}
