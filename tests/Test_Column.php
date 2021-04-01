<?php

declare(strict_types=1);

/**
 * Tests for Column definitions
 *
 * @since 0.3.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Table_Builder\Tests;

use Exception;
use WP_UnitTestCase;
use PinkCrab\Table_Builder\Column;
use PinkCrab\Table_Builder\Schema;

class Test_Column extends WP_UnitTestCase {

	/** @testdox It should be possible to set a column using any type */
	public function test_can_manually_set_type(): void {
		$column_int = new Column( 'int' );
		$column_int->type( 'int' );
		$this->assertEquals( 'int', $column_int->export()->type );

		$column_text = new Column( 'text' );
		$column_text->type( 'text' );
		$this->assertEquals( 'text', $column_text->export()->type );
	}

	/** @testdox It should be possible to set the length of a columns value. */
	public function test_can_manually_set_length(): void {
		$column = new Column( 'int' );
		$column->type( 'int' );
		$column->length( 11 );
		$this->assertEquals( 11, $column->export()->length );

	}

	/** @testdox It should be possible to set a column as either Nullable or not.*/
	public function test_can_set_as_nullable(): void {
		$column_1 = new Column( 'foo' );
		$column_1->nullable();

		$this->assertTrue( $column_1->export()->nullable );

		$column_2 = new Column( 'foo' );
		$column_2->nullable( true );
		$this->assertTrue( $column_2->export()->nullable );

		$column_3 = new Column( 'foo' );
		$column_3->nullable( false );
		$this->assertFalse( $column_3->export()->nullable );
	}

	/** @testdox It should be possible to set the default value for a column. */
	public function test_can_set_default(): void {
		$column = new Column( 'foo' );
		$column->default( 'bar' );

		$this->assertEquals( 'bar', $column->export()->default );
	}

	/** @testdox It should be possible to set a column as either auto_increment or not.*/
	public function test_can_set_column_as_auto_increment(): void {
		$column_1 = new Column( 'foo' );
		$column_1->auto_increment();

		$this->assertTrue( $column_1->export()->auto_increment );

		$column_2 = new Column( 'foo' );
		$column_2->auto_increment( true );
		$this->assertTrue( $column_2->export()->auto_increment );

		$column_3 = new Column( 'foo' );
		$column_3->auto_increment( false );
		$this->assertFalse( $column_3->export()->auto_increment );
	}

	/** @testdox It should be possible to set a column as either unsigned or not.*/
	public function test_can_set_column_as_unsigned(): void {
		$column_1 = new Column( 'foo' );
		$column_1->unsigned();

		$this->assertTrue( $column_1->export()->unsigned );

		$column_2 = new Column( 'foo' );
		$column_2->unsigned( true );
		$this->assertTrue( $column_2->export()->unsigned );

		$column_3 = new Column( 'foo' );
		$column_3->unsigned( false );
		$this->assertFalse( $column_3->export()->unsigned );
	}

    /** @testdox It should be possible to export a columns data as a simple object */
	public function test_can_export_as_stdClass(): void {
		$column = ( new Column( 'foo' ) )
			->type( 'int' )
			->length( 11 )
			->nullable( false )
			->unsigned()
			->auto_increment()
			->default( '1' );

		$exported = $column->export();

		$this->assertEquals( 'int', $exported->type );
		$this->assertTrue( $exported->unsigned );
		$this->assertEquals( 11, $exported->length );
		$this->assertFalse( $exported->nullable );
		$this->assertEquals( '1', $exported->default );
		$this->assertTrue( $exported->auto_increment );
		$this->assertTrue( $exported->unsigned );
	}

}
