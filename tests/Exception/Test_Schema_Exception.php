<?php

declare(strict_types=1);

/**
 * Tests for the Schema Exceptions
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\WPDB_Table_Builder
 */

namespace PinkCrab\Table_Builder\Tests\Exception;

use WP_UnitTestCase;
use PinkCrab\Table_Builder\Schema;
use PinkCrab\Table_Builder\Exception\Schema_Exception;

class Test_Schema_Exception extends WP_UnitTestCase {

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

	/** @testdox When attempting to remove a column that doesnt exist, an exception should be thrown. */
	public function test_throws_exception_when_trying_to_remove_undefined_column() {
		$this->expectException( Schema_Exception::class );
		$this->expectExceptionCode( 301 );
		$this->expectExceptionMessage( 'column with name FOO is not currently defined' );
		$this->get_schema()->remove_column( 'FOO' );

	}

	/** @testdox It should be possible to access the schema that generated the exception. */
	public function test_exception_can_access_schema(): void {
		$schema    = $this->get_schema();
		$exception = new Schema_Exception( $schema );
		$this->assertSame( $schema, $exception->get_schema() );
	}
}
