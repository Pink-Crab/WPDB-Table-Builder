<?php

declare(strict_types=1);

/**
 * Schema tests
 *
 * @since 0.3.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\WPDB_Table_Builder
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
				$schema->column( 'test_col' )->int( 11 )->nullable( false );
				$schema->column( 'test_col' )->text()->default( 'n/a' );
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

	/** @testdox It should be possible to define an index when setting up schema. */
	public function test_can_create_index(): void {
		$schema = new Schema(
			'table',
			function( $schema ) {
				$schema->column( 'a' );
				$schema->index( 'a' )->unique();
			}
		);

		$indexes = $schema->get_indexes();
		$this->assertCount( 1, $indexes );

		$indexes = array_values( $indexes );
		$a_index = $indexes[0]->export();

		$this->assertEquals( 'ix_a', $a_index->key_name );
		$this->assertEquals( 'a', $a_index->column );
		$this->assertTrue( $a_index->unique );
		$this->assertFalse( $a_index->full_text );
	}

	/** @testdox It should be possibe to defined a foreign key index for a column */
	public function test_can_create_foreign_key(): void {
		$schema = new Schema(
			'table',
			function( $schema ) {
				$schema->column( 'a' );
				$schema->foreign_key( 'a', 'named_fk' )
					->reference_table( 'foo' )
					->reference_column( 'bar' )
					->on_update( 'update_go' )
					->on_delete( 'delete_go' );
			}
		);

		$f_keys = $schema->get_foreign_keys();
		$this->assertCount( 1, $f_keys );

		$f_keys   = array_values( $f_keys );
		$named_fk = $f_keys[0]->export();

		$this->assertEquals( 'named_fk', $named_fk->key_name );
		$this->assertEquals( 'a', $named_fk->column );
		$this->assertEquals( 'foo', $named_fk->reference_table );
		$this->assertEquals( 'bar', $named_fk->reference_column );
		$this->assertEquals( 'update_go', $named_fk->on_update );
		$this->assertEquals( 'delete_go', $named_fk->on_delete );
	}

	/** @testdox It should be possible to check if a schema has any indexes or not. */
	public function test_has_indexes(): void {
		$schema_with = new Schema(
			'table',
			function( $schema ) {
				$schema->column( 'a' );
				$schema->index( 'a' )->unique();
			}
		);

		$schema_without = new Schema(
			'table',
			function( $schema ) {
				$schema->column( 'a' );
			}
		);

		$this->assertFalse( $schema_without->has_indexes() );
		$this->assertTrue( $schema_with->has_indexes() );
	}

		/** @testdox It should be possible to check if a schema has any forien_keys or not. */
	public function test_has_forien_keys(): void {
		$schema_with = new Schema(
			'table',
			function( $schema ) {
				$schema->column( 'a' );
				$schema->foreign_key( 'a', 'named_fk' )
					->reference_table( 'foo' )
					->reference_column( 'bar' )
					->on_update( 'update_go' )
					->on_delete( 'delete_go' );
			}
		);

		$schema_without = new Schema(
			'table',
			function( $schema ) {
				$schema->column( 'a' );
			}
		);

		$this->assertFalse( $schema_without->has_foreign_keys() );
		$this->assertTrue( $schema_with->has_foreign_keys() );
	}
}
