<?php

declare(strict_types=1);

/**
 * Tests for the DB_Delta Engine.
 *
 * @since 0.3.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\WPDB_Table_Builder
 */

namespace PinkCrab\Table_Builder\Tests;

use WP_UnitTestCase;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Table_Builder\Engines\WPDB_DB_Delta\DB_Delta_Engine;
use PinkCrab\Table_Builder\Engines\WPDB_DB_Delta\DB_Delta_Validator;
use PinkCrab\Table_Builder\Engines\WPDB_DB_Delta\DB_Delta_Translator;

class Test_DB_Delta_Engine extends WP_UnitTestCase {

	/** @testdox When creating an instance of the DB_Delta builder, the validator and translator should be present. */
	public function test_construct_with_fallbacks(): void {
		$builder = new DB_Delta_Engine( $this->createMock( \wpdb::class ) );

		$this->assertInstanceOf( DB_Delta_Validator::class, Objects::get_property( $builder, 'validator' ) );
		$this->assertInstanceOf( DB_Delta_Translator::class, Objects::get_property( $builder, 'translator' ) );
	}

}
