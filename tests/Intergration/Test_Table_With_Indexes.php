<?php

declare(strict_types=1);

/**
 * Tests a table with multiple unique indexes.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Table_Builder\Tests;

use WP_UnitTestCase;
use PinkCrab\Table_Builder\Table_Index;
use PinkCrab\Table_Builder\Table_Schema;
use PinkCrab\Table_Builder\Builders\DB_Delta;

class Test_Table_With_Indexes extends WP_UnitTestCase {



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
				Table_Index::name( 'user_id' )->column( 'user_id' )->unique()
			)
			->index(
				Table_Index::name( 'user_email' )->column( 'user_email' )->unique()
			)
			->primary( 'id' );
	}

	/**
	 * Tests the class is created and name, primary are set and indexes not.
	 *
	 * @return void
	 */
	public function test_created_with_basics(): void {
		$this->assertInstanceOf( Table_Schema::class, $this->schema );
		$this->assertEquals( 'table_with_indexes', $this->schema->get_table_name() );
		$this->assertEquals( 'id', $this->schema->get_primary_key() );
		$this->assertCount( 2, $this->schema->get_indexes() );
		$this->assertCount( 5, $this->schema->get_columns() );
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
		$table_details = $this->wpdb->get_results( 'SHOW COLUMNS FROM table_with_indexes;' );
		$this->assertCount( 5, $table_details );

		// Expected results.
		$expected = array(
			'id'           => array(
				'Type'    => 'int(10) unsigned',
				'Null'    => 'NO',
				'Key'     => 'PRI',
				'Default' => null,
				'Extra'   => 'auto_increment',
			),
			'user_id'      => array(
				'Type'    => 'varchar(16)',
				'Null'    => 'NO',
				'Key'     => 'UNI',
				'Default' => null,
				'Extra'   => '',
			),
			'user_email'   => array(
				'Type'    => 'varchar(256)',
				'Null'    => 'NO',
				'Key'     => 'UNI',
				'Default' => null,
				'Extra'   => '',
			),
			'created_on'   => array(
				'Type'    => 'datetime',
				'Null'    => 'NO',
				'Key'     => '',
				'Default' => 'current_timestamp()',
				'Extra'   => '',
			),
			'last_updated' => array(
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
	}
}
