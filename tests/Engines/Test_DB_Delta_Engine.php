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
use PinkCrab\Table_Builder\Engines\Schema_Validator;
use PinkCrab\Table_Builder\Engines\Schema_Translator;
use PinkCrab\Table_Builder\Exceptions\Engine_Exception;
use PinkCrab\Table_Builder\Engines\WPDB_DB_Delta\DB_Delta_Engine;
use PinkCrab\Table_Builder\Engines\WPDB_DB_Delta\DB_Delta_Validator;
use PinkCrab\Table_Builder\Engines\WPDB_DB_Delta\DB_Delta_Translator;

class Test_DB_Delta_Engine extends WP_UnitTestCase {

	/** @testdox When creating a instance of the builder you sould not need to define the Valdiator or Translator as the defualts will be used as a fallback. */
	public function test_construct_with_fallbacks(): void {
		$wpdb = $this->createMock( \wpdb::class );

		$builder = new DB_Delta_Engine( $wpdb );

		$this->assertInstanceOf( DB_Delta_Validator::class, Objects::get_property( $builder, 'validator' ) );
		$this->assertInstanceOf( DB_Delta_Translator::class, Objects::get_property( $builder, 'translator' ) );
	}

	/** @testdox When creating an instance of a builder it should be possible to define the valditor and translator at the time of construction. */
	public function test_setting_validator_and_translator_using_config_callable_on_contstructor(): void {
		$wpdb            = $this->createMock( \wpdb::class );
		$mock_translator = $this->createMock( Schema_Translator::class );
		$mock_validator  = $this->createMock( Schema_Validator::class );

		$builder = new DB_Delta_Engine(
			$wpdb,
			function( $builder ) use ( $mock_translator, $mock_validator ) {
				$builder->set_translator( $mock_translator );
				$builder->set_validator( $mock_validator );
			}
		);

		$this->assertSame( $mock_validator, Objects::get_property( $builder, 'validator' ) );
		$this->assertSame( $mock_translator, Objects::get_property( $builder, 'translator' ) );
	}

	/** @testdox It should not be possible to set the validator once it has been set.  */
	public function test_exception_thrown_if_trying_redefine_the_validator(): void {
		$this->expectException( Engine_Exception::class );
		$this->expectExceptionCode( 1 );
		$builder = new DB_Delta_Engine( $this->createMock( \wpdb::class ) );
		$builder->set_validator( $this->createMock( Schema_Validator::class ) );
	}

	 /** @testdox It should not be possible to set the translator once it has been set.  */
	public function test_exception_thrown_if_trying_redefine_the_translator(): void {
		$this->expectException( Engine_Exception::class );
		$this->expectExceptionCode( 2 );
		$builder = new DB_Delta_Engine( $this->createMock( \wpdb::class ) );
		$builder->set_translator( $this->createMock( Schema_Translator::class ) );
	}

	/** @testdox It should be possible to access the builders Validator to check for errors from outside the builders internal scope. */
	public function test_get_validator(): void {
		$builder = new DB_Delta_Engine( $this->createMock( \wpdb::class ) );
		$this->assertInstanceOf( DB_Delta_Validator::class, $builder->get_validator() );
	}
}
