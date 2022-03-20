<?php

declare(strict_types=1);

/**
 * Tests creating a simple table with only a primary column
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\WPDB_Table_Builder
 */

namespace PinkCrab\Table_Builder\Tests;

use WP_UnitTestCase;
use PinkCrab\Table_Builder\Schema;
use PinkCrab\Table_Builder\Builder;
use PinkCrab\Table_Builder\Engines\WPDB_DB_Delta\DB_Delta_Engine;

class Test_Simple_Table extends WP_UnitTestCase {



	/**
	 * WPDB
	 *
	 * @var wpdb
	 */
	protected $wpdb;

	/**
	 * Undocumented variable
	 *
	 * @var \PinkCrab\Table_Builder\Interfaces\SQL_Schema
	 */
	protected $schema;


	public function setUp(): void {
		parent::setup();

		global $wpdb;
		$this->wpdb = $wpdb;

		$this->schema = new Schema(
			'simple_table',
			function( Schema $schema ) {

				$schema->column( 'id' )
					->unsigned_int( 11 )
					->auto_increment();

				$schema->column( 'name' )
					->varchar( 255 )
					->nullable()
					->default( 'no_name' );

				$schema->column( 'date' )
					->datetime( 'CURRENT_TIMESTAMP' );

				$schema->index( 'id' )->primary();
			}
		);

	}


	/**
	 * Test that the table is created.
	 *
	 * @return void
	 */
	public function test_can_create_table_with_wpdb_delta(): void {

		// Create table
		$builder = new Builder( new DB_Delta_Engine( $this->wpdb ) );
		$builder->create_table( $this->schema );

		// Grab the table column info. If not created, will fail.
		$table_details = $this->wpdb->get_results( 'SHOW COLUMNS FROM simple_table;' );
		$this->assertCount( 3, $table_details );

		// Expected results.
		$expected = array(
			'id'   => array(
				'Type'  => 'int(11) unsigned',
				'Null'  => 'NO',
				'Key'   => 'PRI',
				'Extra' => 'auto_increment',
			),
			'name' => array(
				'Type'  => 'varchar(255)',
				'Null'  => 'YES',
				'Key'   => '',
				'Extra' => '',
			),
			'date' => array(
				'Type'  => 'datetime',
				'Null'  => 'NO',
				'Key'   => '',
				'Extra' => '',
			),
		);

		foreach ( $table_details as $column ) {
			$this->assertArrayHasKey( $column->Field, $expected );
			$this->assertEquals( $expected[ $column->Field ]['Type'], $column->Type );
			$this->assertEquals( $expected[ $column->Field ]['Null'], $column->Null );
			$this->assertEquals( $expected[ $column->Field ]['Key'], $column->Key );
			$this->assertEquals( $expected[ $column->Field ]['Extra'], $column->Extra );
		}

		$this->assert_defaults();
	}

	/**
	 * Test the defaults are used.
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
