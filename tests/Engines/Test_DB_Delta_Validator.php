<?php

declare(strict_types=1);

/**
 * Tests for the DB_Delta Validator.
 *
 * @since 0.3.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\WPDB_Table_Builder
 */

namespace PinkCrab\Table_Builder\Tests;

use WP_UnitTestCase;
use PinkCrab\Table_Builder\Schema;
use PinkCrab\Table_Builder\Engines\WPDB_DB_Delta\DB_Delta_Validator;

class Test_DB_Delta_Validator  extends WP_UnitTestCase {

	/** @testdox A schema with a column which has no defined type, should generate an error when validating. */
	public function test_returns_error_if_column_has_no_type(): void {
		$schema = new Schema(
			'table',
			function( Schema $schema ): void {
				$schema->column( 'invalid' );
			}
		);

		$valiadtor = new DB_Delta_Validator();
		$valiadtor->validate( $schema );

		$this->assertTrue( $valiadtor->has_errors() );
		$this->assertEquals( 'Column "invalid" has no type defined', $valiadtor->get_errors()[0] );
	}

	/**@testdox A schema with more than a single primary key added, should fail validation before being created. */
	public function test_returns_error_if_multiple_primary_keys_set(): void {
		$schema = new Schema(
			'table',
			function( Schema $schema ): void {
				$schema->column( 'id' )->unsigned_int( 11 );
				$schema->column( 'ref' )->varchar( 16 );

				$schema->index( 'id' )->primary();
				$schema->index( 'ref' )->primary();
			}
		);

		$valiadtor = new DB_Delta_Validator();
		$valiadtor->validate( $schema );

		$this->assertTrue( $valiadtor->has_errors() );
		$this->assertStringContainsString( '2 Primary keys are defined in schema', $valiadtor->get_errors()[0] );
	}

	/** @testdox A schema which has indexes that are defined on colums which do not exist as part of the schema should fail validation. */
	public function test_returns_error_if_index_defined_on_none_existing_column(): void {
		$schema = new Schema(
			'table',
			function( Schema $schema ): void {
				$schema->column( 'real_col' )->unsigned_int( 11 );

				$schema->index( 'fake_col' );
			}
		);

		$valiadtor = new DB_Delta_Validator();
		$valiadtor->validate( $schema );

		$this->assertTrue( $valiadtor->has_errors() );
		$this->assertStringContainsString( 'Index column fake_col not defined', $valiadtor->get_errors()[0] );
	}


	/** @testdox A schema which has foreign keyes that are defined on colums which do not exist as part of the schema should fail validation. */
	public function test_returns_error_if_foreign_key_defined_on_none_existing_column(): void {
		$schema = new Schema(
			'table',
			function( Schema $schema ): void {
				$schema->column( 'real_col' )->unsigned_int( 11 );

				$schema->foreign_key( 'fake_col' )->reference( 'table', 'col' );
			}
		);

		$valiadtor = new DB_Delta_Validator();
		$valiadtor->validate( $schema );

		$this->assertTrue( $valiadtor->has_errors() );
		$this->assertStringContainsString( 'Foreign Keys column fake_col not defined', $valiadtor->get_errors()[0] );
	}

	/** @testdox A schema which has a Foreign Key defined which doesnt have any reference details, should fail validation */
	public function test_retruns_error_if_foreign_key_is_missing_both_reference_table_and_reference_column(): void {
		$schema = new Schema(
			'table',
			function( Schema $schema ): void {
				$schema->column( 'no_reference' )->unsigned_int( 11 );
				$schema->foreign_key( 'no_reference' );
			}
		);

		$valiadtor = new DB_Delta_Validator();
		$valiadtor->validate( $schema );

		$this->assertTrue( $valiadtor->has_errors() );
		$this->assertStringContainsString( 'Foreign Keys column fk_no_reference has missing reference table or column details', $valiadtor->get_errors()[0] );

	}

	/** @testdox A schema which has a Foreign Key defined which doesnt have a reference column defined, should fail validation */
	public function test_retruns_error_if_foreign_key_is_missing__reference_column(): void {
		$schema = new Schema(
			'table',
			function( Schema $schema ): void {
				$schema->column( 'missing_column' )->unsigned_int( 11 );
				$schema->foreign_key( 'missing_column' )->reference_table( 'table' );
			}
		);

		$valiadtor = new DB_Delta_Validator();
		$valiadtor->validate( $schema );

		$this->assertTrue( $valiadtor->has_errors() );
		$this->assertStringContainsString( 'Foreign Keys column fk_missing_column has missing reference table or column details', $valiadtor->get_errors()[0] );

	}

	/** @testdox A schema which has a Foreign Key defined which doesnt have a reference table defined, should fail validation */
	public function test_retruns_error_if_foreign_key_is_missing__reference_table(): void {
		$schema = new Schema(
			'table',
			function( Schema $schema ): void {
				$schema->column( 'missing_table' )->unsigned_int( 11 );
				$schema->foreign_key( 'missing_table' )->reference_column( 'column' );
			}
		);

		$valiadtor = new DB_Delta_Validator();
		$valiadtor->validate( $schema );

		$this->assertTrue( $valiadtor->has_errors() );
		$this->assertStringContainsString( 'Foreign Keys column fk_missing_table has missing reference table or column details', $valiadtor->get_errors()[0] );
	}

	/** @testdox A schema which has valid columns, indexes, or foreign keys should pass validation */
	public function test_valid_schema_passes_validation(): void {
		$schema = new Schema(
			'table',
			function( Schema $schema ): void {
				$schema->column( 'id' )->unsigned_int( 11 )->auto_increment();
				$schema->column( 'unique_ref' )->varchar( 16 );
				$schema->column( 'f_key_column' )->varchar( 16 );

				$schema->index( 'id' )->primary();
				$schema->index( 'unique_ref' )->unique();
				$schema->foreign_key( 'f_key_column' )->reference( 'other_table', 'column' );
			}
		);

		$valiadtor = new DB_Delta_Validator();
		$valiadtor->validate( $schema );
		$this->assertFalse( $valiadtor->has_errors() );
	}
}
