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

use Exception;
use PinkCrab\Table_Builder\Schema;
use PinkCrab\Table_Builder\Engines\Engine;
use PinkCrab\Table_Builder\Exception\Engine_Exception;
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
	 * Validate and set the schema
	 *
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @return void
	 * @throws \Exception If fails validation. (code 1)
	 */
	private function set_query_for_create( Schema $schema ): void {
		$this->schema = $schema;

		if ( ! $this->validator->validate( $schema ) ) {
			throw new \Exception(
				sprintf(
					'Failed to create table %s as failed validation: %s',
					$schema->get_table_name(),
					join( ', ' . PHP_EOL, $this->validator->get_errors() )
				),
				1
			);
		}

	}

	/**
	 * Create the table based on the schema passed.
	 *
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @return bool
	 * @throws \Exception If fails validation. (code 1)
	 */
	public function create_table( Schema $schema ): bool {

		// Generate the query from the passed schema.
		$query = $this->create_table_query( $schema );

		// Include WP dbDelta.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		\ob_start();
		dbDelta( $query );
		$output = \ob_get_clean();

		// If output captured, throw.
		if ( '' !== $output ) {
			throw Engine_Exception::create_table( $schema, $output ?: '' );
		}

		// Throw if WPDB has errors.
		if ( '' !== $this->wpdb->last_error ) {
			throw Engine_Exception::create_table( $schema, $this->wpdb->last_error );
		}

		return true;
	}

	/**
	 * Returns the table query generated for creating a table
	 *
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @return string
	 * @throws \Exception If fails validation. (code 1)
	 */
	public function create_table_query( Schema $schema ): string {
		$this->set_query_for_create( $schema );
		return $this->compile_create_sql_query();
	}

	/**
	 * Validate and set the schema
	 *
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @return void
	 * @throws \Exception If fails validation. (code 2)
	 */
	private function set_query_for_drop( Schema $schema ): void {
		$this->schema = $schema;

		if ( ! $this->validator->validate( $schema ) ) {
			throw new \Exception(
				sprintf(
					'Failed to drop table %s as failed validation: %s',
					$schema->get_table_name(),
					join( ', ' . PHP_EOL, $this->validator->get_errors() )
				),
				2
			);
		}
	}

	/**
	 * Drops the table
	 *
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @return bool
	 * @throws \Exception If fails validation. (code 2)
	 * @throws Engine_Exception If error thrown dropping table. (code 102)
	 */
	public function drop_table( Schema $schema ): bool {

		$query = $this->drop_table_query( $schema );

		\ob_start();
		$this->wpdb->get_results( $query ); // phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
		$output = \ob_get_clean();

		// If output captured, throw.
		if ( '' !== $output ) {
			throw Engine_Exception::drop_table( $schema, $output ?: '' );
		}

		// Throw if WPDB has errors.
		if ( '' !== $this->wpdb->last_error ) {
			throw Engine_Exception::drop_table( $schema, $this->wpdb->last_error );
		}

		return true;
	}

	/**
	 * Returns the query for dropping the curren table.
	 *
	 * @param \PinkCrab\Table_Builder\Schema $schema
	 * @return string
	 * @throws \Exception If fails validation. (code 2)
	 */
	public function drop_table_query( Schema $schema ): string {
		$this->set_query_for_drop( $schema );
		return "DROP TABLE IF EXISTS {$this->schema->get_table_name()};";
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
