<?php

declare(strict_types=1);

/**
 * Loader tests.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Modules\Registerables\Tests;

use WP_UnitTestCase;
use PinkCrab\Modules\Table_Builder\Table_Schema;
use PinkCrab\Modules\Table_Builder\Builders\DB_Delta;
use PinkCrab\Modules\Table_Builder\Interfaces\SQL_Schema;

class Test_General extends WP_UnitTestCase {



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
	 * Ensure null() is now Deprecated and thorws error.
	 *
	 * @return void
	 */
	public function test_null_is_deprecated(): void {
		$schema = new Table_Schema( 'test' );
		try {
			$schema->column( 'test' )->null();
		} catch ( \Throwable $th ) {
			$this->assertTrue(
				str_contains(
					$th->getMessage(),
					'null is deprecated, pleae use nullable(bool)'
				)
			);
		}
	}

	/**
	 * Ensure the int(), text(), float()....
	 *
	 * @return void
	 */
	public function test_type_shortcuts(): void {
		$schema = new Table_Schema( 'test' );
		$schema->column( 'int' )->int( 10 );
		$schema->column( 'float' )->float();
		$schema->column( 'double' )->double( 8 );
		$schema->column( 'varchar' )->varchar( 256 );
		$schema->column( 'datetime' )->datetime( 'CURRENT_TIMESTAMP' );
		$schema->column( 'timestamp' )->timestamp();

		// Ge the columns
		$columns = \_getPrivateProperty( $schema, 'columns' );

		// Int
		$this->assertEquals( 'int', $columns['int']['type'] );
		$this->assertEquals( 10, $columns['int']['length'] );

		// FLoat
		$this->assertEquals( 'float', $columns['float']['type'] );
		$this->assertEquals( null, $columns['float']['length'] );

		// Doubnle
		$this->assertEquals( 'double', $columns['double']['type'] );
		$this->assertEquals( 8, $columns['double']['length'] );

		// varchar
		$this->assertEquals( 'varchar', $columns['varchar']['type'] );
		$this->assertEquals( 256, $columns['varchar']['length'] );

		// Datetime
		$this->assertEquals( 'datetime', $columns['datetime']['type'] );
		$this->assertEquals( 'CURRENT_TIMESTAMP', $columns['datetime']['default'] );

		$this->assertEquals( 'timestamp', $columns['timestamp']['type'] );
		$this->assertArrayNotHasKey( 'default', $columns['timestamp'] );
	}

}
