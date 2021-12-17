<?php

declare(strict_types=1);

/**
 * Tests for the DB_Delta Engine.
 *
 * @since 0.3.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\WPDB_Table_Builder
 */

namespace PinkCrab\Table_Builder\Tests;

use WP_UnitTestCase;
use PinkCrab\Table_Builder\Schema;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Table_Builder\Engines\WPDB_DB_Delta\DB_Delta_Engine;
use PinkCrab\Table_Builder\Engines\WPDB_DB_Delta\DB_Delta_Validator;
use PinkCrab\Table_Builder\Engines\WPDB_DB_Delta\DB_Delta_Translator;

class Test_DB_Delta_Engine extends WP_UnitTestCase {

	/** @testdox When creating an instance of the DB_Delta builder, the validator and translator should be present. */
	public function test_construct_with_fallbacks(): void {
		$builder = new DB_Delta_Engine( $this->createMock( \wpdb::class ) );

		$this->assertInstanceOf( DB_Delta_Validator::class, Objects::get_property( $builder, 'validator' ) );
		$this->assertInstanceOf( DB_Delta_Translator::class, Objects::get_property( $builder, 'translator' ) );
	}

	/** @testdox It should be possible to get the query used to drop a table based on its schema. */
	public function test_can_generate_drop_table_query() {
		$builder = new DB_Delta_Engine( $this->createMock( \wpdb::class ) );
		$schema  = new Schema(
			'test_drop_table',
			function( Schema $schema ): void {
				$schema->column( 'id' )->unsigned_int( 11 )->auto_increment();
				$schema->index( 'id' )->primary();
			}
		);

		$query = $builder->drop_table_query( $schema );
		$this->assertEquals( 'DROP TABLE IF EXISTS test_drop_table;', $query );
	}

	/** @testdox It should be possible to get the query used to create a table based on its schema. */
	public function test_can_generate_create_table_query(): void {
		$builder = new DB_Delta_Engine( $this->createMock( \wpdb::class ) );
		$schema  = new Schema(
			'test_drop_table',
			function( Schema $schema ): void {
				$schema->column( 'id' )->unsigned_int( 11 )->auto_increment();
				$schema->index( 'id' )->primary();
			}
		);

		$query = $builder->create_table_query( $schema );
		$this->assertStringContainsString( 'CREATE TABLE test_drop_table', $query );
		$this->assertStringContainsString( 'id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT', $query );
		$this->assertStringContainsString( 'PRIMARY KEY  (id)', $query );

	}

}
