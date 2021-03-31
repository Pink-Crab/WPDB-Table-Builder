<?php

declare(strict_types=1);

/**
 * Tests the Table_Schema class
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Table_Builder\Tests;

use Exception;
use WP_UnitTestCase;
use PinkCrab\Table_Builder\Column;
use PinkCrab\Table_Builder\Schema;


class Test_Schema extends WP_UnitTestCase {

	/** @testdox When creating a new Schema definition, the table name should be passed onlong with a helper function for setting data. */
	public function test_can_create_schema(): void {
		$schema = new Schema(
			'table',
			function( $schema ) {
				$this->assertInstanceOf( Schema::class, $schema );
			}
		);

		$this->assertSame( 'table', $schema->get_table_name() );
	}

	/** @testdox When creating a new schema definition, it should be possible to define the columns within the constructor. */
	public function test_can_set_column_on_schema_construct(): void {
		$schema = new Schema(
			'table',
			function( $schema ) {
				$schema->column( 'test_col' );
			}
		);

		$this->assertCount( 1, $schema->get_columns() );

		$this->assertTrue( $schema->has_column( 'test_col' ) );

		foreach ( $schema->get_columns() as $name => $column ) {
			$this->assertInstanceOf( Column::class, $column );
		}
	}

	/** @testdox When creating a new schema definition, you should not be able to define 2 columns with the same name */
	public function test_set_column_with_name_as_key() {
		$schema = new Schema(
			'table',
			function( $schema ) {
				$schema->column( 'test_col' )->int(11)->nullable(false);
				$schema->column( 'test_col' )->text()->default('n/a');
			}
		);

		$this->assertCount( 1, $schema->get_columns() );
	}

	/** @testdox It should be possible to check if a table has a table name prefix defined. */
	public function test_define_table_name_with_prefix(): void {
		$schema_with_prefix = new Schema(
			'table',
			function( $schema ) {
				$schema->prefix( 'prefix_' );
			}
		);

		$schema_without_prefix = new Schema( 'table', function( $schema ) {} );

		$this->assertTrue( $schema_with_prefix->has_prefix() );
		$this->assertFalse( $schema_without_prefix->has_prefix() );
	}

	/** @testdox When getting the table name from a Schema definiton, if a prefix is set, the table name should have it included. */
	public function test_table_name_with_prefix_if_set(): void {
		$schema_with_prefix = new Schema(
			'table',
			function( $schema ) {
				$schema->prefix( 'prefix_' );
			}
		);

		$schema_without_prefix = new Schema( 'table', function( $schema ) {} );

		$this->assertEquals( 'prefix_table', $schema_with_prefix->get_table_name() );
		$this->assertEquals( 'table', $schema_without_prefix->get_table_name() );
	}

	/** @testdox It should be possible to remove a column from the defined schema. */
	public function test_can_remove_column(): void {
		$schema = new Schema(
			'table',
			function( $schema ) {
				$schema->column( 'a' );
				$schema->column( 'b' );
			}
		);

		$schema->remove_column( 'a' );
		$this->assertCount( 1, $schema->get_columns() );
	}

    /** @testdox Attempting to remove a column from a schema which has not been defined, should result in an error and abort the operation. */
	public function test_throws_exception_attempting_to_remove_none_set_column(): void {
		$this->expectException( Exception::class );
		$schema = new Schema(
			'table',
			function( $schema ) {
				$schema->column( 'b' );
			}
		);
		$schema->remove_column( 'a' );
	}

	public function test_d(Type $var = null)
	{
		$schema = new Schema(
			'table',
			function( $schema ) {
				$schema->index( 'a' )->unique();
				$schema->column( 'b' );
				$schema->foreign_key('c')->reference_table('bar')
				->reference_column('id');
			}
		);
		dump($schema);
	}
}
