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

class Test_Docs_Examples extends WP_UnitTestCase {

	/** @testdox Shows all column properties that can be used on STRING based columns. Examples used in docs */
	public function test_string_columns(): void {
		$schema = new Schema(
			'ex_string_cols',
			function( $schema ) {
				// Length
				$schema->column( 'verbose_length' )->text()->length( 123 );
				$schema->column( 'shortcut_length' )->varchar( 255 );

				// Nullable
				$schema->column( 'nullable' )->text()->nullable( true );
				$schema->column( 'not_nullable' )->text()->nullable( false );

				// Default
				$schema->column( 'default_string' )->varchar( 255 )->default( 'HAPPY' );
			}
		);

		$builder = new Builder( new DB_Delta_Engine( $GLOBALS['wpdb'] ) );
		$builder->create_table( $schema );

		$generated_query = $GLOBALS['wpdb']->last_query;
		// Length
		$this->assertStringContainsString( 'verbose_length TEXT(123)', $generated_query );
		$this->assertStringContainsString( 'shortcut_length VARCHAR(255)', $generated_query );

		// Nullable
		$this->assertStringContainsString( 'nullable TEXT NULL', $generated_query );
		$this->assertStringContainsString( 'not_nullable TEXT NOT NULL', $generated_query );

		// Default
		$this->assertStringContainsString( "default_string VARCHAR(255) NOT NULL DEFAULT 'HAPPY'", $generated_query );
	}

	/** Shows all column properties that can be used on NUMERIC based columns. Examples used in docs */
	public function test_numeric_columns() {
		$schema = new Schema(
			'ex_numeric_cols',
			function( $schema ) {
				// Length
				$schema->column( 'verbose_length' )->type( 'mediumint' )->length( 123 );
				$schema->column( 'shortcut_length' )->int( 12 );

				// Length with Precision
				$schema->column( 'verbose_precision' )->type( 'double' )
                    ->length( 123 )->precision( 2 );
                $schema->column( 'assumed_precision' )->type( 'decimal' )
                    ->length( 3 );
				$schema->column( 'shortcut_precision' )->float( 12, 4 );

				// Unsigned
				$schema->column( 'is_unsigned' )->type( 'int' )->unsigned( true );
				$schema->column( 'not_unsigned' )->int()->unsigned( false );

				// Auto Increment
				$schema->column( 'increment' )
					->int( 11 )
					->auto_increment();
				$schema->index( 'increment' )->primary(); // Must be a key for increment.
			}
		);

		$builder = new Builder( new DB_Delta_Engine( $GLOBALS['wpdb'] ) );
		$builder->create_table( $schema );

		$generated_query = $GLOBALS['wpdb']->last_query;

		// Length
		$this->assertStringContainsString( 'verbose_length MEDIUMINT(123)', $generated_query );
		$this->assertStringContainsString( 'shortcut_length INT(12) ', $generated_query );

		// Length with Precision
		$this->assertStringContainsString( 'verbose_precision DOUBLE(123,2)', $generated_query );
		$this->assertStringContainsString( 'shortcut_precision FLOAT(12,4)', $generated_query );
		$this->assertStringContainsString( 'assumed_precision DECIMAL(3,1)', $generated_query );

		// Unsigned
		$this->assertStringContainsString( 'is_unsigned INT UNSIGNED', $generated_query );
		$this->assertStringContainsString( 'not_unsigned INT', $generated_query );

		// Auto Increment
		$this->assertStringContainsString( 'increment INT(11) NOT NULL AUTO_INCREMENT', $generated_query );
	}
}
