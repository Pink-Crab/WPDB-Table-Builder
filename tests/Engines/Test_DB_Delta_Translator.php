<?php

declare(strict_types=1);

/**
 * Tests for the WPDB Builder
 *
 * @since 0.3.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\WPDB_Table_Builder
 */

namespace PinkCrab\Table_Builder\Tests;

use WP_UnitTestCase;
use PinkCrab\Table_Builder\Schema;
use PinkCrab\Table_Builder\Engines\WPDB_DB_Delta\DB_Delta_Translator;

class Test_DB_Delta_Translator extends WP_UnitTestCase {

	/** @testdox It should be possible to extract the columns from a valid schema and translate into an arryay of valid SQL commands for the WordPress DbDelta function. */
	public function test_translate_columns(): void {
		$schema = new Schema( 'test' );
		$schema->column( 'text_column' )->text()->nullable()->default( 'text default' );
		$schema->column( 'varchar_column' )->varchar( 2 );
		$schema->column( 'int_column' )->int( 24 );
		$schema->column( 'unsigned_column' )->unsigned_int( 11 )->auto_increment();
		$schema->column( 'float_column' )->float( 11 )->default( '123.50' );
		$schema->column( 'timestamp_column' )->timestamp( 'NOW()' );
		$schema->column( 'longblob_column' )->type( 'LONGBLOB' );

		$translator = new DB_Delta_Translator();
		$columns    = $translator->translate_columns( $schema );

		// Check all columns still exist.
		$this->assertArrayHasKey( 'text_column', $columns );
		$this->assertArrayHasKey( 'varchar_column', $columns );
		$this->assertArrayHasKey( 'int_column', $columns );
		$this->assertArrayHasKey( 'unsigned_column', $columns );
		$this->assertArrayHasKey( 'float_column', $columns );
		$this->assertArrayHasKey( 'timestamp_column', $columns );
		$this->assertArrayHasKey( 'longblob_column', $columns );

		// Text column
		$this->assertStringContainsString( 'TEXT', $columns['text_column'] );
		$this->assertStringContainsString( 'NULL', $columns['text_column'] );
		$this->assertStringContainsString( 'DEFAULT \'text default\'', $columns['text_column'] );

		// varchar column
		$this->assertStringContainsString( 'VARCHAR(2)', $columns['varchar_column'] );
		$this->assertStringContainsString( 'NOT NULL', $columns['varchar_column'] );

		// int column
		$this->assertStringContainsString( 'INT(24)', $columns['int_column'] );
		$this->assertStringContainsString( 'NOT NULL', $columns['int_column'] );

		// unsigned int column
		$this->assertStringContainsString( 'INT(11)', $columns['unsigned_column'] );
		$this->assertStringContainsString( 'NOT NULL', $columns['unsigned_column'] );
		$this->assertStringContainsString( 'UNSIGNED', $columns['unsigned_column'] );
		$this->assertStringContainsString( 'AUTO_INCREMENT', $columns['unsigned_column'] );

		// float column
		$this->assertStringContainsString( 'FLOAT(11)', $columns['float_column'] );
		$this->assertStringContainsString( 'NOT NULL', $columns['float_column'] );
		$this->assertStringContainsString( 'DEFAULT 123.50', $columns['float_column'] );

		// timestamp column
		$this->assertStringContainsString( 'TIMESTAMP', $columns['timestamp_column'] );
		$this->assertStringContainsString( 'NOT NULL', $columns['timestamp_column'] );
		$this->assertStringContainsString( 'DEFAULT NOW()', $columns['timestamp_column'] );

		// Long Blob
		$this->assertStringContainsString( 'LONGBLOB', $columns['longblob_column'] );
		$this->assertStringContainsString( 'NOT NULL', $columns['longblob_column'] );
	}

	/** @testdox If an index has been created for a priamry key, it should be parsed as a valid SQL expression (for DbDeleta) to create the index. */
	public function test_can_translate_primary_key(): void {
		$schema = new Schema( 'test' );
		$schema->column( 'id' )->unsigned_int( 11 )->auto_increment();
		$schema->index( 'id' )->primary();

		$translator = new DB_Delta_Translator();
		$indexes    = $translator->translate_indexes( $schema );

		$this->assertCount( 1, $indexes );
		$this->assertEquals( 'PRIMARY KEY  (id)', $indexes[0] );
	}

	/** @testdox It should be possible to define multiple indexes in a schema and have the translator return each index as a valid SQL expression (for DbDeleta) */
	public function test_can_translate_indexes(): void {
		$schema = new Schema( 'test' );
		$schema->column( 'text_column' )->text()->nullable()->default( 'text default' );
		$schema->column( 'varchar_column' )->varchar( 2 );
		$schema->index( 'test_column' )->unique();
		$schema->index( 'varchar_column' )->full_text();

		$translator = new DB_Delta_Translator();
		$indexes    = $translator->translate_indexes( $schema );

		$this->assertCount( 2, $indexes );
		$this->assertArrayHasKey( 'ix_test_column_unique', $indexes );
		$this->assertArrayHasKey( 'ix_varchar_column_fulltext', $indexes );

		$this->assertEquals( 'UNIQUE INDEX ix_test_column (test_column)', $indexes['ix_test_column_unique'] );
		$this->assertEquals( 'FULLTEXT INDEX ix_varchar_column (varchar_column)', $indexes['ix_varchar_column_fulltext'] );
	}

	/** @testdox It should be possible to has multiple indexes using the same keyname, be expresses as a single SQL expression. */
	public function test_can_group_indexes_by_keyname(): void {
		$schema = new Schema( 'test' );
		$schema->column( 'col1' )->text()->nullable()->default( 'text default' );
		$schema->column( 'col2' )->varchar( 2 );
		$schema->index( 'col1', 'unique_cols' )->unique();
		$schema->index( 'col2', 'unique_cols' )->unique();

		$translator = new DB_Delta_Translator();
		$indexes    = $translator->translate_indexes( $schema );

		$this->assertCount( 1, $indexes );
		$this->assertArrayHasKey( 'unique_cols_unique', $indexes );
		$this->assertEquals( 'UNIQUE INDEX unique_cols (col1, col2)', $indexes['unique_cols_unique'] );
	}

	/** @testdox It should be possible to define multiple Foreign Key indexes in a schema and have the translator return each index as a valid SQL expression (for DbDeleta)   */
	public function test_can_translate_foreign_keys(): void {
		$schema = new Schema( 'test' );
		$schema->column( 'text_column' )->text()->nullable()->default( 'text default' );
		$schema->column( 'varchar_column' )->varchar( 2 );
		$schema->foreign_key( 'col1' )->reference_column( 'cola' )->reference_table( 'table' )->on_delete( 'CASCADE' );
		$schema->foreign_key( 'col2' )->reference_column( 'colb' )->reference_table( 'table' )->on_update( 'CASCADE' );

        $translator = new DB_Delta_Translator();
		$indexes    = $translator->translate_indexes( $schema );

		$this->assertCount( 2, $indexes );

        // Col1
		$this->assertStringContainsString( 'FOREIGN KEY fk_col1(col1)', $indexes[0] );
		$this->assertStringContainsString( 'REFERENCES table(cola)', $indexes[0] );
		$this->assertStringContainsString( 'ON DELETE CASCADE', $indexes[0] );

         // Col2
		$this->assertStringContainsString( 'FOREIGN KEY fk_col2(col2)', $indexes[1] );
		$this->assertStringContainsString( 'REFERENCES table(colb)', $indexes[1] );
		$this->assertStringContainsString( 'ON UPDATE CASCADE', $indexes[1] );
	}
}
