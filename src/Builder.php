<?php

declare(strict_types=1);

/**
 * The primary class for creating and dropping tables.
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

use PinkCrab\Table_Builder\Engines\Engine;
use PinkCrab\Table_Builder\Schema;


class Builder {

	/**
	 * The engine used to create the tables
	 *
	 * @var Engine
	 */
	protected $engine;

	public function __construct( Engine $engine, ?callable $engine_config = null ) {
		$this->engine = $engine_config
			? $engine_config( $engine )
			: $engine;
	}

	/**
	 * Creates the table
	 *
	 * @param Schema $schema
	 * @return bool
	 */
	public function create_table( Schema $schema ): bool {
		return $this->engine->create_table( $schema );
	}

	/**
	 * Drop a table
	 *
	 * @param Schema $schema
	 * @return bool
	 */
	public function drop_table( Schema $schema ): bool {
		return $this->engine->drop_table( $schema );
	}

	/**
	 * Access to the builders engine.
	 *
	 * @return Engine
	 */
	public function get_engine(): Engine {
		return $this->engine;
	}
}
