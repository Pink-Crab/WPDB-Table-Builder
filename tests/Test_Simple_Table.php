<?php

declare(strict_types=1);

/**
 * Loader tests.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Modules\Registerables\Tests;

use WP_UnitTestCase;
use PinkCrab\Modules\Table_Builder\Table_Schema;
use PinkCrab\Modules\Table_Builder\Builders\DB_Delta;
use PinkCrab\Modules\Table_Builder\Interfaces\SQL_Schema;

class Test_Simple_Table extends WP_UnitTestCase {



	/**
	 * WPDB
	 *
	 * @var wpdb
	 */
	protected $cpt;

	/**
	 * Undocumented variable
	 *
	 * @var \PinkCrab\Modules\Table_Builder\Interfaces\SQL_Schema
	 */
	protected $schema;


	public function setUp(): void {
		parent::setup();

		global $wpdb;
		$this->wpdb = $wpdb;

		$this->schema = Table_Schema::create( 'simple_table' )
			->column( 'id' )
				->int( 11 )
				->auto_increment()
				->unsigned()
			->column( 'name' )
				->type( 'text' )
				->nullable()
				->default( 'no_name' )
			->column( 'date' )
				->datetime( 'CURRENT_TIMESTAMP' )
			->primary( 'id' );
	}

	/**
	 * Tests the class is created and name, primary are set and indexes not.
	 *
	 * @return void
	 */
	public function test_created_with_basics(): void {
		$this->assertInstanceOf( Table_Schema::class, $this->schema );
		$this->assertEquals( 'simple_table', $this->schema->get_table_name() );
		$this->assertEquals( 'id', $this->schema->get_primary_key() );
		$this->assertEmpty( $this->schema->get_indexes() );
		$this->assertCount( 3, $this->schema->get_columns() );
	}

	/**
	 * Tests all values for id.
	 *
	 * @return void
	 */
	public function test_id_column(): void {
		$this->assertArrayHasKey( 'id', $this->schema->get_columns() );
		$this->assertArrayNotHasKey( 'default', $this->schema->get_columns()['id'] );
		$this->assertEquals( 'id', $this->schema->get_columns()['id']['key'] );
		$this->assertEquals( 11, $this->schema->get_columns()['id']['length'] );
		$this->assertTrue( $this->schema->get_columns()['id']['auto_increment'] );
		$this->assertTrue( $this->schema->get_columns()['id']['unsigned'] );
		$this->assertFalse( $this->schema->get_columns()['id']['null'] );
	}

	/**
	 * Test that the table is created.
	 *
	 * @return void
	 */
	public function test_can_create_table_with_wpdb_delta(): void {

		// Create table
		$builder = new DB_Delta( $this->wpdb );
		$this->schema->create_table( $builder );

		// Grab the table column info. If not created, will fail.
		$table_details = $this->wpdb->get_results( 'SHOW COLUMNS FROM simple_table;' );
		$this->assertCount( 3, $table_details );

		// Expected results.
		$expected = array(
			'id'   => array(
				'Type'    => 'int(11) unsigned',
				'Null'    => 'NO',
				'Key'     => 'PRI',
				'Default' => null,
				'Extra'   => 'auto_increment',
			),
			'name' => array(
				'Type'    => 'text',
				'Null'    => 'YES',
				'Key'     => '',
				'Default' => "'no_name'",
				'Extra'   => '',
			),
			'date' => array(
				'Type'    => 'datetime',
				'Null'    => 'NO',
				'Key'     => '',
				'Default' => 'current_timestamp()',
				'Extra'   => '',
			),
		);

		foreach ( $table_details as $column ) {
			$this->assertArrayHasKey( $column->Field, $expected );
			$this->assertEquals( $expected[ $column->Field ]['Type'], $column->Type );
			$this->assertEquals( $expected[ $column->Field ]['Null'], $column->Null );
			$this->assertEquals( $expected[ $column->Field ]['Key'], $column->Key );
			$this->assertEquals( $expected[ $column->Field ]['Default'], $column->Default );
			$this->assertEquals( $expected[ $column->Field ]['Extra'], $column->Extra );
		}

		$this->assert_defaults();
	}

	/**
	 * Test the defualts are used.
	 * Called from same class, as the temp transactions are not fun.
	 *
	 * @return void
	 */
	public function assert_defaults(): void {

		// With default on name.
		$expected_date = date( 'Y-m-d H:i:s', 0 );
		$expected_name = 'Testy Test-Face';

		/** row 0*/
		$this->wpdb->insert( 'simple_table', array( 'date' => $expected_date ), array( '%s' ) );
		/** row 1*/
		$this->wpdb->insert( 'simple_table', array( 'name' => $expected_name ), array( '%s' ) );
		$results = $this->wpdb->get_results( 'SELECT * FROM simple_table' );

		// Test with expected date & default name.
		$this->assertEquals( 'no_name', $results[0]->name );
		$this->assertEquals( $expected_date, $results[0]->date );

		// Test with expected name and default date.
		$this->assertEquals( $expected_name, $results[1]->name );
		$this->assertTrue( str_contains( $results[1]->date, date( 'Y-m-d' ) ) ); // Should use current date, so check date (not time)

	}
}
