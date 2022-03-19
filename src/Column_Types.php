<?php

declare(strict_types=1);

/**
 * Trait with a series of shortcut for types with lengths.
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

use PinkCrab\Table_Builder\Column;

trait Column_Types {

	/**
	 * Sets column as varchar wtih a definied length.
	 *
	 * @since 0.2.0
	 * @param int|null $length
	 * @return Column
	 */
	public function varchar( ?int $length = null ): Column {
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
	 * @return Column
	 */
	public function text( ?int $length = null ): Column {
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
	 * @return Column
	 */
	public function int( ?int $length = null ): Column {
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
	 * @return Column
	 */
	public function float( ?int $length = null ): Column {
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
	 * @return Column
	 */
	public function double( ?int $length = null ): Column {
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
	 * @param string|null $default
	 * @return Column
	 */
	public function datetime( ?string $default = null ): Column {
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
	 * @param string|null $default
	 * @return Column
	 */
	public function timestamp( ?string $default = null ): Column {
		$this->type( 'timestamp' );
		if ( $default ) {
			$this->default( $default );
		}
		return $this;
	}

	/**
	 * Sets column as an unsighed int wtih a definied length.
	 *
	 * @since 0.3.0
	 * @param int|null $length
	 * @return Column
	 */
	public function unsigned_int( ?int $length = null ): Column {
		$this->type( 'int' );
		$this->unsigned();
		if ( $length ) {
			$this->length( $length );
		}
		return $this;
	}

	/**
	 * Sets column as an unsighed mediumint wtih a definied length.
	 *
	 * @since 0.3.0
	 * @param int|null $length
	 * @return Column
	 */
	public function unsigned_medium( ?int $length = null ): Column {
		$this->type( 'mediumint' );
		$this->unsigned();
		if ( $length ) {
			$this->length( $length );
		}
		return $this;
	}

	/**
	 * Sets column as JSON with an optional default.
	 *
	 * @since 1.1.0
	 * @return Column
	 */
	public function json(): Column {
		$this->type( 'json' );
		return $this;
	}
}
