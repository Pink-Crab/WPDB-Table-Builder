<?php

declare(strict_types=1);

/**
 * Tests for Foreign Key definitions
 *
 * @since 0.3.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\WPDB_Table_Builder
 */

namespace PinkCrab\Table_Builder\Tests;

use WP_UnitTestCase;
use PinkCrab\Table_Builder\Foreign_Key;

class Test_Foreign_Key extends WP_UnitTestCase {

	/** @testdox When creating a new Foreign_Key, if no key name is passed, it should be inferred as ix_{column_name} */
	public function test_can_create_with_inferred_keyname(): void {
		$f_key = new Foreign_Key( 'column' );
		$this->assertEquals( 'fk_column', $f_key->get_keyname() );
	}

	/** @testdox When creating an new Foreign_Key, it should be possible to define the keyname. */
	public function test_can_create_index_with_defined_keyname(): void {
		$f_key = new Foreign_Key( 'column', 'keyname' );
		$this->assertEquals( 'keyname', $f_key->get_keyname() );
	}

	/** @testdox It should be possible to get the column that the Foreign Key is attached to */
	public function test_can_get_the_column_name(): void {
		$f_key = new Foreign_Key( 'column' );
		$this->assertEquals( 'column', $f_key->get_column() );
	}

	/** @testdox It should be possible to get the reference table from a Forgien Key */
	public function test_refernce_table(): void {
		$f_key = new Foreign_Key( 'column' );
		$f_key->reference_table( 'some_table' );
		$this->assertEquals( 'some_table', $f_key->get_reference_table() );
	}

	/** @testdox It should be possible to get the reference column from a Forgien Key */
	public function test_refernce_column(): void {
		$f_key = new Foreign_Key( 'column' );
		$f_key->reference_column( 'some_column' );
		$this->assertEquals( 'some_column', $f_key->get_reference_column() );
	}

	/** @testdox It should be possible to set both the reference table and column in a sinle method */
	public function test_reference(): void
	{
		$f_key = new Foreign_Key( 'column' );
		$f_key->reference( 'some_table', 'some_column' );
		$this->assertEquals( 'some_table', $f_key->get_reference_table() );
		$this->assertEquals( 'some_column', $f_key->get_reference_column() );
	}

	/** @testdox If ether reference colum or table are defined, null should be reutrned as the value to denote not set. */
	public function test_reference_table_column_return_null_if_not_set(): void {
		$f_key = new Foreign_Key( 'column' );
		$this->assertNull( $f_key->get_reference_column() );
		$this->assertNull( $f_key->get_reference_table() );
	}

    /** @testdox It should be possible to get the defined on update action for the Foreign Key */
	public function test_get_on_update() {
		$f_key = new Foreign_Key( 'column' );
		$f_key->on_update( 'update' );

		$this->assertEquals( 'update', $f_key->get_on_update() );
	}

    /** @testdox It should be possible to get the defined on delete action for the Foreign Key */
	public function test_get_on_delete() {
		$f_key = new Foreign_Key( 'column' );
		$f_key->on_delete( 'delete' );

		$this->assertEquals( 'delete', $f_key->get_on_delete() );
	}
}
