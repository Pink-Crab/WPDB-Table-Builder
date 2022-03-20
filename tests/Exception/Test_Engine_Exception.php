<?php

declare(strict_types=1);

/**
 * Tests for the Engine Exceptions
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\WPDB_Table_Builder
 */

namespace PinkCrab\Table_Builder\Tests\Exception;

use WP_UnitTestCase;
use PinkCrab\Table_Builder\Schema;
use PinkCrab\Table_Builder\Builder;
use PinkCrab\Table_Builder\Exception\Engine_Exception;
use PinkCrab\Table_Builder\Engines\WPDB_DB_Delta\DB_Delta_Engine;

class Test_Engine_Exception extends WP_UnitTestCase {

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

	/** @testdox When creating a table, if an error is printed, throw exception. */
	public function test_throw_exception_creating_when_prints_error() {

		$GLOBALS['wpdb'] = $this->createMock( \wpdb::class );
		// Mock methods used for db_delta.
		$GLOBALS['wpdb']->method( 'query' )
			->will(
				$this->returnCallback(
					function( $query ) {
						echo 'Printed error';
					}
				)
			);
		$GLOBALS['wpdb']->method( 'tables' )
			->will( $this->returnValue( array() ) );

		$engine = new DB_Delta_Engine( $GLOBALS['wpdb'] );
		$schema = $this->get_schema();

		$this->expectException( Engine_Exception::class );
		$this->expectExceptionCode( 101 );
		$this->expectExceptionMessage( 'Printed error' );
		$engine->create_table( $schema );
	}

	/** @testdox When creating a table, if an error is set to wpdb, throw exception. */
	public function test_throw_exception_creating_when_setting_error() {

		$GLOBALS['wpdb'] = $this->createMock( \wpdb::class );
		// Mock methods used for db_delta.
		$GLOBALS['wpdb']->method( 'query' )
			->will(
				$this->returnCallback(
					function( $query ) {
						$GLOBALS['wpdb']->last_error = 'Error set in WPDB';
					}
				)
			);
		$GLOBALS['wpdb']->method( 'tables' )
			->will( $this->returnValue( array() ) );

		$engine = new DB_Delta_Engine( $GLOBALS['wpdb'] );
		$schema = $this->get_schema();

		$this->expectException( Engine_Exception::class );
		$this->expectExceptionCode( 101 );
		$this->expectExceptionMessage( 'Error set in WPDB' );
		$engine->create_table( $schema );
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

	/** @testdox When drop a table, if an error is printed, throw exception. */
	public function test_throw_exception_drop_when_setting_error() {

		$GLOBALS['wpdb'] = $this->createMock( \wpdb::class );
		// Mock methods used for db_delta.
		$GLOBALS['wpdb']->method( 'get_results' )
			->will(
				$this->returnCallback(
					function( $query ) {
						$GLOBALS['wpdb']->last_error = 'Error set in WPDB';
					}
				)
			);

		$engine = new DB_Delta_Engine( $GLOBALS['wpdb'] );
		$schema = $this->get_schema();

		$this->expectException( Engine_Exception::class );
		$this->expectExceptionCode( 102 );
		$this->expectExceptionMessage( 'Error set in WPDB' );
		$engine->drop_table( $schema );
	}

	/** @testdox It should be possible to access the schema that generated the exception. */
	public function test_exception_can_access_schema(): void {
		$schema    = $this->get_schema();
		$exception = new Engine_Exception( $schema );
		$this->assertSame( $schema, $exception->get_schema() );
	}
}
