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
	 * Using HASH
	 *
	 * @since 0.1.0
	 * @var bool
	 */
	protected $hash = false;

	public function __construct( string $column, ?string $keyname = null ) {
		$this->keyname = $keyname ?? 'ix_' . $column;
        $this->column = $column;
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

}
