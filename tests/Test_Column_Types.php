<?php

declare(strict_types=1);

/**
 * Tests for Column type shortcuts
 *
 * @since 0.3.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\WPDB_Table_Builder
 */

namespace PinkCrab\Table_Builder\Tests;

use WP_UnitTestCase;
use PinkCrab\Table_Builder\Column;

class Test_Column_Types extends WP_UnitTestCase {

	/** @testdox It should be possible to set common types such as INT and it legnth in a simple and short fashion */
	public function test_int_type(): void {
		$column_no_length = new Column( 'no_length' );
		$column_no_length->int();

		$this->assertEquals( 'int', $column_no_length->get_type() );
		$this->assertEquals( null, $column_no_length->get_length() );

		$column_with_length = new Column( 'with_length' );
		$column_with_length->int( 12 );

		$this->assertEquals( 'int', $column_with_length->get_type() );
		$this->assertEquals( 12, $column_with_length->get_length() );
	}

	/** @testdox It should be possible to set common types such as VARCHAR and it legnth in a simple and short fashion */
	public function test_varchar_type(): void {
		$column_no_length = new Column( 'no_length' );
		$column_no_length->varchar();

		$this->assertEquals( 'varchar', $column_no_length->get_type() );
		$this->assertEquals( null, $column_no_length->get_length() );

		$column_with_length = new Column( 'with_length' );
		$column_with_length->varchar( 12 );

		$this->assertEquals( 'varchar', $column_with_length->get_type() );
		$this->assertEquals( 12, $column_with_length->get_length() );
	}

	/** @testdox It should be possible to set common types such as TEXT and it legnth in a simple and short fashion */
	public function test_text_type(): void {
		$column_no_length = new Column( 'no_length' );
		$column_no_length->text();

		$this->assertEquals( 'text', $column_no_length->get_type() );
		$this->assertEquals( null, $column_no_length->get_length() );

		$column_with_length = new Column( 'with_length' );
		$column_with_length->text( 12 );

		$this->assertEquals( 'text', $column_with_length->get_type() );
		$this->assertEquals( 12, $column_with_length->get_length() );
	}

	/** @testdox It should be possible to set common types such as FLOAT and it legnth in a simple and short fashion */
	public function test_float_type(): void {
		$column_no_length = new Column( 'no_length' );
		$column_no_length->float();

		$this->assertEquals( 'float', $column_no_length->get_type() );
		$this->assertEquals( null, $column_no_length->get_length() );

		$column_with_length = new Column( 'with_length' );
		$column_with_length->float( 12 );

		$this->assertEquals( 'float', $column_with_length->get_type() );
		$this->assertEquals( 12, $column_with_length->get_length() );
	}

	/** @testdox It should be possible to set common types such as DOUBLE and it legnth in a simple and short fashion */
	public function test_double_type(): void {
		$column_no_length = new Column( 'no_length' );
		$column_no_length->double();

		$this->assertEquals( 'double', $column_no_length->get_type() );
		$this->assertEquals( null, $column_no_length->get_length() );

		$column_with_length = new Column( 'with_length' );
		$column_with_length->double( 12 );

		$this->assertEquals( 'double', $column_with_length->get_type() );
		$this->assertEquals( 12, $column_with_length->get_length() );
	}

	/** @testdox It should be possible to set common types such as UNSIGNED INT and it legnth in a simple and short fashion */
	public function test_unsigned_int_type(): void {
		$column_no_length = new Column( 'no_length' );
		$column_no_length->unsigned_int();

		$this->assertEquals( 'int', $column_no_length->get_type() );
		$this->assertEquals( null, $column_no_length->get_length() );
		$this->assertTrue( $column_no_length->is_unsigned() );

		$column_with_length = new Column( 'with_length' );
		$column_with_length->unsigned_int( 12 );

		$this->assertEquals( 'int', $column_with_length->get_type() );
		$this->assertTrue( $column_no_length->is_unsigned() );
		$this->assertEquals( 12, $column_with_length->get_length() );
	}

	/** @testdox It should be possible to set common types such as UNSIGNED MEDIUM and it legnth in a simple and short fashion */
	public function test_unsigned_medium_type(): void {
		$column_no_length = new Column( 'no_length' );
		$column_no_length->unsigned_medium();

		$this->assertEquals( 'mediumint', $column_no_length->get_type() );
		$this->assertEquals( null, $column_no_length->get_length() );
		$this->assertTrue( $column_no_length->is_unsigned() );

		$column_with_length = new Column( 'with_length' );
		$column_with_length->unsigned_medium( 12 );

		$this->assertEquals( 'mediumint', $column_with_length->get_type() );
		$this->assertTrue( $column_no_length->is_unsigned() );
		$this->assertEquals( 12, $column_with_length->get_length() );
	}

	/** @testdox It should be possible to set common types such as DATETIME and its default in a simple and short fashion */
	public function test_datetime_type(): void {
		$column_no_default = new Column( 'with_no_length' );
		$column_no_default->datetime();

		$this->assertEquals( 'datetime', $column_no_default->get_type() );
		$this->assertEquals( null, $column_no_default->get_default() );

		$column_with_default = new Column( 'with_default' );
		$column_with_default->datetime( 'NOW()' );

		$this->assertEquals( 'datetime', $column_with_default->get_type() );
		$this->assertEquals( 'NOW()', $column_with_default->get_default() );
	}

    /** @testdox It should be possible to set common types such as TIMESTAMP and its default in a simple and short fashion */
	public function test_timestamp_type(): void {
		$column_no_default = new Column( 'with_no_length' );
		$column_no_default->timestamp();

		$this->assertEquals( 'timestamp', $column_no_default->get_type() );
		$this->assertEquals( null, $column_no_default->get_default() );

		$column_with_default = new Column( 'with_default' );
		$column_with_default->timestamp( 'NOW()' );

		$this->assertEquals( 'timestamp', $column_with_default->get_type() );
		$this->assertEquals( 'NOW()', $column_with_default->get_default() );
	}
}
