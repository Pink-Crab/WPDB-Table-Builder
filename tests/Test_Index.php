<?php

declare(strict_types=1);

/**
 * Tests for Index definitions
 *
 * @since 0.3.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Table_Builder\Tests;

use WP_UnitTestCase;
use PinkCrab\Table_Builder\Index;

class Test_Index extends WP_UnitTestCase {

	/** @testdox When creating a new table index, if no key name is passed, it should be inferred as ix_{column_name} */
	public function test_can_create_with_inferred_keyname(): void {
		$index = new Index( 'column' );
		$this->assertEquals( 'ix_column', $index->get_keyname() );
	}

	/** @testdox When creating an new table index, it should be possible to define the keyname. */
	public function test_can_create_index_with_defined_keyname(): void {
		$index = new Index( 'column', 'keyname' );
		$this->assertEquals( 'keyname', $index->get_keyname() );
	}

	/** @testdox It should be possible to get the column name that an index is applied to */
	public function test_can_get_column_name(): void {
		$index = new Index( 'column' );
		$this->assertEquals( 'column', $index->get_column() );
	}

	/** @testdox It should be possible to create an index to denote the primary key on the table */
	public function test_can_crate_primary_index(): void {
		$index = new Index( 'column' );
		$index->primary();

		$this->assertTrue( $index->is_primary() );
	}

	/** @testdox It should be possible to create an index to denote the unique key on the table */
	public function test_can_crate_unique_index(): void {
		$index = new Index( 'column' );
		$index->unique();

		$this->assertTrue( $index->is_unique() );
	}

	/** @testdox It should be possible to create an index to denote the full_text key on the table */
	public function test_can_crate_full_text_index(): void {
		$index = new Index( 'column' );
		$index->full_text();

		$this->assertTrue( $index->is_full_text() );
	}

	/** @testdox It should be possible to get the index type from an Index definition. */
	public function test_get_type(): void
	{
		$this->assertEquals(
			'unique', 
			( new Index('a') )->unique()->get_type()
		);

		$this->assertEquals(
			'primary', 
			( new Index('a') )->primary()->get_type()
		);

		$this->assertEquals(
			'fulltext', 
			( new Index('a') )->full_text()->get_type()
		);

		$this->assertEquals(
			'', 
			( new Index('a') )->get_type()
		);
	}

	/** @testdox It should be possible to export the current indexes details as a stdClass */
	public function test_can_export_index_as_stdClass(): void {
		$index = new Index( 'column', 'keyname' );
		$index->unique();
		$index->full_text( false );
		$index->primary( true );
		
        $export = $index->export();

		$this->assertEquals( 'keyname', $export->keyname );
		$this->assertEquals( 'column', $export->column );
		$this->assertTrue( $export->unique );
		$this->assertFalse( $export->full_text );
		$this->assertTrue( $export->primary );
	}
}
