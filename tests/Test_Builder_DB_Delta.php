<?php

declare(strict_types=1);

/**
 * Covers a few branches not covered by the Intergration tests (most)
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Table_Builder\Tests;

use Exception;
use WP_UnitTestCase;
use PinkCrab\PHPUnit_Helpers\Output;
use PinkCrab\PHPUnit_Helpers\Objects;
use PinkCrab\Table_Builder\Table_Index;
use PinkCrab\PHPUnit_Helpers\Reflection;
use PinkCrab\Table_Builder\Table_Schema;
use PinkCrab\Table_Builder\Builders\DB_Delta;

class Test_Builder_DB_Delta extends WP_UnitTestCase {

	protected $db_delta;
	protected $schema;
	protected $wpdb;

	public function setUp(): void {
		parent::setUp();
		global $wpdb;
		$this->wpdb = $wpdb;

		// Creates simple instance of DB delta for testing against.
		$this->db_delta = new DB_Delta( $wpdb );
		$this->schema   = Table_Schema::create( 'db_delta_tests' )
			->column( 'id' )->int( 11 )->unsigned()->auto_increment()
			->primary( 'id' );
		Reflection::set_private_property( $this->db_delta, 'schema', $this->schema );
	}

	/**
	 * Test that types are parse with length if defined and accepted value.
	 *
	 * @return void
	 */
	public function test_parse_type(): void {
		$cases = array(
			array(
				'input'    => 'smallInt',
				'length'   => 12,
				'expected' => 'SMALLINT(12)',
			),
			array(
				'input'    => 'mediumint',
				'length'   => null,
				'expected' => 'MEDIUMINT',
			),
			array(
				'input'    => 'int',
				'length'   => null,
				'expected' => 'INT',
			),
			array(
				'input'    => 'integer',
				'length'   => null,
				'expected' => 'INTEGER',
			),
			array(
				'input'    => 'bigInt',
				'length'   => 2,
				'expected' => 'BIGINT(2)',
			),
			array(
				'input'    => 'Float',
				'length'   => null,
				'expected' => 'FLOAT',
			),
			array(
				'input'    => 'double',
				'length'   => null,
				'expected' => 'DOUBLE',
			),
			array(
				'input'    => 'double PRECISION',
				'length'   => 32,
				'expected' => 'DOUBLE PRECISION(32)',
			),
			array(
				'input'    => 'decimal',
				'length'   => null,
				'expected' => 'DECIMAL',
			),
			array(
				'input'    => 'dec',
				'length'   => null,
				'expected' => 'DEC',
			),
			array(
				'input'    => 'datetime',
				'length'   => null,
				'expected' => 'DATETIME',
			),
			array(
				'input'    => 'timeStamp',
				'length'   => null,
				'expected' => 'TIMESTAMP',
			),
			array(
				'input'    => 'time',
				'length'   => null,
				'expected' => 'TIME',
			),
			array(
				'input'    => 'LongText',
				'length'   => 999,
				'expected' => 'LONGTEXT',
			),
		);

		foreach ( $cases as  $case ) {
			$this->assertEquals(
				$case['expected'],
				Reflection::invoke_private_method(
					$this->db_delta,
					'parse_type',
					array( $case['input'], $case['length'] )
				)
			);
		}
	}

	/**
	 * Test that the table can be dropped.
	 *
	 * @return void
	 */
	public function test_can_drop_table(): void {
		// Build current temp table.
		$this->db_delta->build( $this->schema );

		$table_details = $this->wpdb->get_results( 'SHOW COLUMNS FROM db_delta_tests;' );
		if ( empty( $table_details ) ) {
			\throwException( new Exception( 'Didnt create table on Test_Builder_DB_Delta::test_can_drop_table()' ) );
		}

		// Remove table.
		$this->db_delta->drop( $this->schema );

		// Attempt to get table details and catch error
		$error = Output::buffer(
			function () {
				$this->wpdb->get_results( 'SHOW COLUMNS FROM db_delta_tests;' );
			}
		);

		$this->assertStringContainsString(
			"db_delta_tests' doesn't exist",
			htmlspecialchars_decode( $error, \ENT_QUOTES )
		);
	}

	/**
	 * Tests that an exception is thrown attempting to build a table with
	 * no name.
	 *
	 * @return void
	 */
	public function test_throws_exception_if_no_table_name(): void {
		Reflection::set_private_property( $this->schema, 'table_name', '' );
		$this->expectException( Exception::class );
		$this->db_delta->build( $this->schema );
	}

	/**
	 * Test throws for invalid colums array
	 * Reuired, key, null, type and legnth properties.
	 *
	 * @return void
	 */
	public function test_throws_exception_with_invalid_columns(): void {
		$incorrect_columns = array(
			'id' => array(
				'key'    => 'correct',
				'nul'    => 'should be null',
				'type'   => 'correct',
				'length' => 'correct',
			),
		);
		Reflection::set_private_property( $this->schema, 'columns', $incorrect_columns );
		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Columns in db_delta_tests schema are missing required properties.' );
		$this->db_delta->build( $this->schema );
	}

	public function test_throws_exception_with_missing_foreign_key_references(): void {
		$index = Table_Index::name( 'test' )
			->foreign_key()
			->reference_column( 'some_col' );
		Reflection::set_private_property( $this->schema, 'indexes', array( $index ) );
        		
        $this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Foreign key index "test" requires both reference table(UNDEFINED) and reference column(some_col) defined.' );
		$this->db_delta->build( $this->schema );

	}

}
