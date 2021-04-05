<?php

declare(strict_types=1);

/**
 * Tests the Table_Schema class
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\WPDB_Table_Builder
 */

namespace PinkCrab\Table_Builder\Tests;

use WP_UnitTestCase;
use PinkCrab\PHPUnit_Helpers\Objects;
use PinkCrab\Table_Builder\Table_Index;
use PinkCrab\Table_Builder\Table_Schema;

class Test_Tabe_Index extends WP_UnitTestCase {



	/**
	 * WPDB
	 *
	 * @var wpdb
	 */
	protected $wpdb;

	/**
	 * Ensure we have wpdb instance.
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setup();

		if ( ! $this->wpdb ) {
			global $wpdb;
			$this->wpdb = $wpdb;
		}
	}

    /**
     * Tests can use helper to set full_text
     *
     * @return void
     */
	public function test_sets_index_as_full_text(): void {
		$index = new Table_Index( 'test' );
		$index->full_text( true );
		$this->assertTrue( $index->full_text );
	}

    /**
     * Tests can use helper to set hash
     *
     * @return void
     */
	public function test_sets_index_as_hash(): void {
		$index = new Table_Index( 'test' );
		$index->hash( true );
		$this->assertTrue( $index->hash );
	}

    /**
     * Tests can use helper to set foreign_key
     *
     * @return void
     */
	public function test_sets_index_as_foreign_key(): void {
		$index = new Table_Index( 'test' );
		$index->foreign_key( true );
		$this->assertTrue( $index->foreign_key );
	}

    /**
     * Tests can use helper to set unique
     *
     * @return void
     */
	public function test_sets_index_as_unique(): void {
		$index = new Table_Index( 'test' );
		$index->unique( true );
		$this->assertTrue( $index->unique );
	}


    /**
     * Tests can use helper to set reference_table
     *
     * @return void
     */
	public function test_sets_index_as_reference_table(): void {
		$index = new Table_Index( 'test' );
		$index->reference_table( 'true' );
		$this->assertEquals( 'true', $index->reference_table );
	}

     /**
     * Tests can use helper to set reference_column
     *
     * @return void
     */
	public function test_sets_index_as_reference_column(): void {
		$index = new Table_Index( 'test' );
		$index->reference_column( 'true' );
		$this->assertEquals( 'true', $index->reference_column );
	}

    /**
     * Tests can use helper to set on_update
     *
     * @return void
     */
	public function test_sets_index_as_on_update(): void {
		$index = new Table_Index( 'test' );
		$index->on_update( 'DO ACTION' );
		$this->assertEquals( 'ON UPDATE ' . 'DO ACTION', $index->on_update );
	}

    /**
     * Tests can use helper to set on_delete
     *
     * @return void
     */
	public function test_sets_index_as_on_delete(): void {
		$index = new Table_Index( 'test' );
		$index->on_delete( 'DO ACTION' );
		$this->assertEquals( 'ON DELETE ' . 'DO ACTION', $index->on_delete );
	}


}
