<?php

declare(strict_types=1);

/**
 * WPDB DB_Delta table builder.
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

namespace PinkCrab\Table_Builder\Engines\WPDB_DB_Delta;

use PinkCrab\Table_Builder\Schema;
use PinkCrab\Table_Builder\Engines\Engine;
use PinkCrab\Table_Builder\Engines\Schema_Validator;
use PinkCrab\Table_Builder\Engines\Schema_Translator;
use PinkCrab\Table_Builder\Exceptions\Engine_Exception;
use PinkCrab\Table_Builder\Engines\WPDB_DB_Delta\DB_Delta_Validator;
use PinkCrab\Table_Builder\Engines\WPDB_DB_Delta\DB_Delta_Translator;

class DB_Delta_Engine implements Engine {

	/**
	 * The engines validator class name.
	 *
	 * @var Schema_Validator
	 */
	protected $validator;

	/**
	 * The WPDB Translator for SQL
	 *
	 * @var Schema_Translator
	 */
	protected $translator;

	/**
	 * Access to wpdb
	 *
	 * @var \wpdb
	 */
	protected $wpdb;


	/**
	 * Holds access to the Schema definition
	 *
	 * @var Schema
	 */
	protected $schema;

	public function __construct( \wpdb $wpdb, ?callable $configure = null ) {
		$this->wpdb = $wpdb;

		if ( $configure ) {
			$configure( $this );
		}

		// Set fallback validator.
		if ( ! is_a( $this->validator, Schema_Validator::class ) ) {
			$this->validator = new DB_Delta_Validator();
		}

		// Set fallback translator.
		if ( ! is_a( $this->translator, Schema_Translator::class ) ) {
			$this->translator = new DB_Delta_Translator();
		}

	}

	/**
	 * Sets a validator to the builder
	 *
	 * @param \PinkCrab\Table_Builder\Engines\Schema_Validator $validator
	 * @return self
	 * @throws Engine_Exception Code 1 If a validator is already set.
	 */
	public function set_validator( Schema_Validator $validator ): self {
		if ( is_a( $this->validator, Schema_Validator::class ) ) {
			throw Engine_Exception::valdidator_already_defined();
		}

		$this->validator = $validator;
		return $this;
	}

	/**
	 * Sets a translator to the builder
	 *
	 * @param \PinkCrab\Table_Builder\Engines\Schema_Translator $translator
	 * @return self
	 * @throws Engine_Exception Code 2 If a translator is already set.
	 */
	public function set_translator( Schema_Translator $translator ): self {
		if ( is_a( $this->translator, Schema_Translator::class ) ) {
			throw Engine_Exception::translator_already_defined();
		}

		$this->translator = $translator;
		return $this;
	}

	/**
	 * Returns an intance of the valditor.
	 *
	 * @return \PinkCrab\Table_Builder\Engines\Schema_Validator
	 */
	public function get_validator(): Schema_Validator {
		return $this->validator;
	}

	/**
	 * Create the table based on the schema passed.
	 *
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @return bool
	 */
	public function create_table( Schema $schema ): bool {
		$this->schema = $schema;
		if ( ! $this->validator->validate( $schema ) ) {
			return false;
		}
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $this->compile_create_sql_query() );
		dump( $this->compile_create_sql_query(), $this->wpdb );

		return true;
	}

	public function drop_table( Schema $schema ): bool {
		$this->schema = $schema;
		if ( ! $this->validator->validate( $schema ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Compiles the SQL query used to create a table.
	 *
	 * @return string
	 */
	protected function compile_create_sql_query(): string {

		// Compile query partials.
		$table   = $this->schema->get_table_name();
		$body    = join(
			',' . PHP_EOL,
			array_merge(
				$this->translator->translate_columns( $this->schema ),
				$this->translator->translate_indexes( $this->schema )
			)
		);
		$collate = $this->wpdb->collate;

		return <<<SQL
CREATE TABLE $table (
$body ) COLLATE $collate 
SQL;
	}


}
