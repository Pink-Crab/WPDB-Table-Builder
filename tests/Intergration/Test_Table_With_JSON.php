<?php

declare(strict_types=1);

/**
 * Tests a table with JSON column
 *
 * @since 1.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\WPDB_Table_Builder
 */

namespace PinkCrab\Table_Builder\Tests;

use WP_UnitTestCase;
use PinkCrab\Table_Builder\Schema;
use PinkCrab\Table_Builder\Builder;
use PinkCrab\Table_Builder\Engines\WPDB_DB_Delta\DB_Delta_Engine;

class Test_Table_With_JSON extends WP_UnitTestCase {



	/**
	 * WPDB
	 *
	 * @var wpdb
	 */
	protected $wpdb;

	/**
	 * Holds the tables schema
	 *
	 * @var \PinkCrab\Table_Builder\Interfaces\SQL_Schema
	 */
	protected $schema;


	public function setUp(): void {
		parent::setup();

		global $wpdb;
		$this->wpdb = $wpdb;

		$this->schema = new Schema( 'table_with_json' );
			$this->schema->column( 'id' )
				->type( 'INT' )
				->auto_increment()
				->nullable( false )
				->unsigned();

			$this->schema->column( 'json_man' )
				->type( 'json' )
				->nullable( true );

			$this->schema->column( 'json_helper_no_def' )
				->json();

			$this->schema->index( 'id' )->primary();
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
		$table_details = $this->wpdb->get_results( 'SHOW COLUMNS FROM table_with_json;' );
		// $this->assertCount( 5, $table_details );

		// Expected results.
		$expected = array(
			'id'           => array(
				'Type'    => ['int(10) unsigned'],
				'Null'    => 'NO',
				'Key'     => 'PRI',
				'Default' => null,
				'Extra'   => 'auto_increment',
			),
			'json_man'   => array(
				'Type'  => ['longtext', 'json'], // MYSQL uses JSON, MariaDB uses longtext
				'Null'  => 'YES',
				'Key'   => '',
				'Extra' => '',
			),
			'json_helper_no_def' => array(
				'Type'  => ['longtext', 'json'], // MYSQL uses JSON, MariaDB uses longtext
				'Null'  => 'NO',
				'Key'   => '',
				'Extra' => '',
			),
		);
		
		foreach ( $table_details as $column ) {
			$this->assertArrayHasKey( $column->Field, $expected );
			$this->assertContains( $column->Type, $expected[ $column->Field ]['Type'] );
			$this->assertEquals( $expected[ $column->Field ]['Null'], $column->Null );
			$this->assertEquals( $expected[ $column->Field ]['Key'], $column->Key );
			if ( isset( $expected[ $column->Field ]['Default'] ) ) { // Due to differences in mysql ver cant test datetimes current_timestamp() vs CURRENT_TIMESTAMP
				$this->assertEquals( $expected[ $column->Field ]['Default'], $column->Default );
			}
			$this->assertEquals( $expected[ $column->Field ]['Extra'], $column->Extra );
		}
	}

	/** @testdox It should be possible to use a JSON column as a json column */
	public function test_can_user_JSON_functions_in_query() : void
	{
		$builder = new Builder( new DB_Delta_Engine( $this->wpdb ) );
		$builder->create_table( $this->schema );
		
		$this->wpdb->insert('table_with_json',[
			'json_man' => json_encode(['key' => 'json_man']),
			'json_helper_no_def' => json_encode(['key' => 'json_helper_no_def']),
		]);

		$where_not = "WHERE NOT JSON_UNQUOTE(JSON_EXTRACT(json_man, \"$.key\")) = 'json_man_NOT'";
		$where = "WHERE JSON_UNQUOTE(JSON_EXTRACT(json_helper_no_def, \"$.key\")) = 'json_helper_no_def'";

		$this->assertNotEmpty($this->wpdb->get_results("SELECT * FROM  table_with_json {$where}" ));
		$this->assertNotEmpty($this->wpdb->get_results("SELECT * FROM  table_with_json {$where_not}" ));
	}
}
