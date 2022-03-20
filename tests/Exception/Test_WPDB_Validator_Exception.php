<?php

declare(strict_types=1);

/**
 * Tests for the WPDB Validator Exceptions
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\WPDB_Table_Builder
 */

namespace PinkCrab\Table_Builder\Tests\Exception;

use WP_UnitTestCase;
use PinkCrab\Table_Builder\Schema;
use PinkCrab\Table_Builder\Exception\WPDB_DB_Delta\WPDB_Validator_Exception;

class Test_WPDB_Validator_Exception extends WP_UnitTestCase {

	/**
	 * Returns a basic schema.
	 *
	 * @return \PinkCrab\Table_Builder\Schema
	 */
	public function get_schema(): Schema {
		return new Schema(
			'print_error',
			function( $schema ) {
				$schema->column( 'id' )
					->unsigned_int( 11 )
					->auto_increment();
				$schema->index( 'id' )->primary();
			}
		);
	}

	/** @testdox It should be possible to access the schema that generated the exception. */
	public function test_exception_can_access_schema(): void {
		$schema    = $this->get_schema();
		$exception = new WPDB_Validator_Exception( $schema, array( 'error' ) );
		$this->assertSame( $schema, $exception->get_schema() );
	}

	/** @testdox It should be possible to access the errors that generated the exception. */
	public function test_exception_can_access_errors(): void {
		$exception = new WPDB_Validator_Exception( $this->get_schema(), array( 'error' ) );
		$this->assertSame( array( 'error' ),  $exception->get_validation_errors() );
	}
}
