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
use PinkCrab\Table_Builder\Exception\Engine_Exception;
use PinkCrab\Table_Builder\Engines\WPDB_DB_Delta\DB_Delta_Engine;
use PinkCrab\Table_Builder\Exception\WPDB_DB_Delta\WPDB_Validator_Exception;

class Test_WPDB_Validator_Exception extends WP_UnitTestCase {

	private $backup_wpdb;

	public function setUp(): void {
		$this->backup_wpdb = clone $GLOBALS['wpdb'];
	}

	public function tearDown(): void {
		$GLOBALS['wpdb'] = clone $this->backup_wpdb;
	}

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



	/** @testdox When drop a table, if an error is printed, throw exception. */
	public function test_throw_exception_drop_when_prints_error() {

		$GLOBALS['wpdb'] = $this->createMock( \wpdb::class );
		// Mock methods used for db_delta.
		$GLOBALS['wpdb']->method( 'get_results' )
			->will(
				$this->returnCallback(
					function( $query ) {
						echo 'Printed error';
					}
				)
			);

		$engine = new DB_Delta_Engine( $GLOBALS['wpdb'] );
		$schema = $this->get_schema();

		$this->expectException( Engine_Exception::class );
		$this->expectExceptionCode( 102 );
		$this->expectExceptionMessage( 'Printed error' );
		$engine->drop_table( $schema );
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
