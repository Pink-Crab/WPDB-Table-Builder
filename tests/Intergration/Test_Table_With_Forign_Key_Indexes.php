<?php

declare(strict_types=1);

/**
 * Tests a table with multiple Forign_Key indexes.
 *
 * @since 0.2.1
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Table_Builder\Tests;

use WP_UnitTestCase;
use PinkCrab\Table_Builder\Table_Index;
use PinkCrab\PHPUnit_Helpers\Reflection;
use PinkCrab\Table_Builder\Table_Schema;
use PinkCrab\Table_Builder\Builders\DB_Delta;

class Test_Table_With_Forign_Key_Indexes extends WP_UnitTestCase {



	/**
	 * WPDB
	 *
	 * @var wpdb
	 */
	protected $wpdb;

	/**
	 * The test tables schema
	 *
	 * @var \PinkCrab\Table_Builder\Interfaces\SQL_Schema
	 */
	protected $schema;

	/**
	 * The test reference tables schema
	 *
	 * @var \PinkCrab\Table_Builder\Interfaces\SQL_Schema
	 */
	protected $refence_table_parent;


	public function setUp(): void {
		parent::setup();

		global $wpdb;
		$this->wpdb = $wpdb;

		$this->schema = Table_Schema::create( 'table_with_indexes' )
			->column( 'id' )
				->type( 'INT' )
				->auto_increment()
				->nullable( false )
				->unsigned()
			->column( 'user_id' )
				->type( 'varchar' )
				->length( 16 )
			->column( 'user_email' )
				->type( 'varchar' )
				->length( 256 )
			->column( 'created_on' )
				->type( 'datetime' )
				->default( 'CURRENT_TIMESTAMP' )
			->column( 'last_updated' )
				->type( 'datetime' )
				->default( 'CURRENT_TIMESTAMP' )
			->index(
				Table_Index::name( 'user_id' )->column( 'user_id' )->foreign_key()
					->reference_table( 'parent_table' )->reference_column( 'id' )
					->on_delete( 'delete' )->on_update( 'update' ) // Include these to check they are called in tests.
			)
			->primary( 'id' );

		$this->refence_table_parent = Table_Schema::create( 'parent_table' )
			->column( 'id' )->int( 10 )->auto_increment()->unsigned()
			->column( 'name' )->varchar( 128 )
			->primary( 'id' );
	}


	/**
	 * Test that the table is created.
	 *
	 * @return void
	 */
	public function test_foreign_query_is_composed(): void {

		// Create table
		$builder = new DB_Delta( $this->wpdb );

		// Create the reference table.
		$this->refence_table_parent->create_table( $builder );

		// Sets schema to the builder
		Reflection::set_private_property( $builder, 'schema', $this->schema );
		// Call the internal methods that compile the query string.
		$indexes        = Reflection::invoke_private_method( $builder, 'compile_indexes' );
		$grouped_forign = Reflection::invoke_private_method( $builder, 'parseForeignTableQuery', array( $indexes['is_foreign'] ) );

		// Check the index references the correct column
		$this->assertStringContainsString( 'INDEX', $grouped_forign['index'] );
		$this->assertStringContainsString( '(user_id)', $grouped_forign['index'] );

		// Check the formign key references the right column
		$this->assertStringContainsString( 'FOREIGN KEY', $grouped_forign['key'] );
		$this->assertStringContainsString( '(user_id)', $grouped_forign['key'] );

		// Check references point to the parent table.
		$this->assertStringContainsString( 'REFERENCES', $grouped_forign['key'] );
		$this->assertStringContainsString( 'parent_table(id)', $grouped_forign['key'] );
	}

}
