<?php

declare(strict_types=1);
/**
 * An abstract class for defninig migrations
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
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Modules\Table_Builder
 */

namespace PinkCrab\Modules\Table_Builder;

use wpdb;
use Exception;
use PinkCrab\Modules\Table_Builder\Interfaces\SQL_Builder;
use PinkCrab\Modules\Table_Builder\Interfaces\SQL_Schema;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

abstract class Migration {


	/**
	 * The table builder.
	 *
	 * @var \PinkCrab\Modules\Table_Builder\Interfaces\SQL_Builder
	 */
	protected $builder;

	/**
	 * The tables schema.
	 *
	 * @var \PinkCrab\Modules\Table_Builder\Interfaces\SQL_Schema
	 */
	protected $schema;

	/**
	 * Access to wpdb
	 *
	 * @var wpdb
	 */
	protected $wpdb;

	public function __construct( SQL_Builder $builder, wpdb $wpdb ) {
		$this->builder = $builder;
		$this->wpdb    = $wpdb;
	}

	/**
	 * Used to either import or define the schema.
	 *
	 * @return void
	 */
	abstract public function set_schema(): void;

	/**
	 * Method is called after the table is created.
	 * Can be overwritten to insert inital data etc.
	 *
	 * @return void
	 */
	protected function after_creation(): void {}

	/**
	 * Used to create the table.
	 *
	 * @return void
	 */
	public function execute() {
		// Set the schema.
		$this->set_schema();

		if ( ! is_a( $this->schema, SQL_Schema::class ) ) {
			throw new Exception( 'No valid schema suppled' );
		}

		$this->builder->build( $this->schema );

		// Allow hook in create inital data.
		$this->after_creation();
	}


}
