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
}
