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
use PinkCrab\Table_Builder\Engines\WPDB_DB_Delta\DB_Delta_Validator;
use PinkCrab\Table_Builder\Engines\WPDB_DB_Delta\DB_Delta_Translator;

class DB_Delta_Engine implements Engine {

	/**
	 * The engines validator class name.
	 *
	 * @var DB_Delta_Validator
	 */
	protected $validator;

	/**
	 * The WPDB Translator for SQL
	 *
	 * @var DB_Delta_Translator
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

	public function __construct( \wpdb $wpdb ) {
		$this->wpdb = $wpdb;

		// Set the translator and valiator
		$this->translator = new DB_Delta_Translator();
		$this->validator  = new DB_Delta_Validator();
	}

	/**
	 * Create the table based on the schema passed.
	 *
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @return bool
	 * @throws \Exception If fails validation.
	 */
	public function create_table( Schema $schema ): bool {
		$this->schema = $schema;

		if ( ! $this->validator->validate( $schema ) ) {
			throw new \Exception(
				sprintf(
					'Failed to create table %s as failed valiadtion: %s',
					$schema->get_table_name(),
					join( ', ' . PHP_EOL, $this->validator->get_errors() )
				),
				1
			);
		}

		// Include WP dbDelta.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $this->compile_create_sql_query() );

		return $this->wpdb->last_error === '';
	}

	/**
	 * Drops the table
	 *
	 *
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @return bool
	 */
	public function drop_table( Schema $schema ): bool {
		$this->schema = $schema;
		if ( ! $this->validator->validate( $this->schema ) ) {
			return false;
		}

		$this->wpdb->get_results( "DROP TABLE IF EXISTS {$this->schema->get_table_name()};" ); // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		return $this->wpdb->last_error === '';
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
