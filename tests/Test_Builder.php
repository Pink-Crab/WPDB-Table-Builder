<?php

declare(strict_types=1);

/**
 * Tests for the Builder class
 *
 * @since 0.3.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\WPDB_Table_Builder
 */

namespace PinkCrab\Table_Builder\Tests;

use WP_UnitTestCase;
use PinkCrab\Table_Builder\Builder;
use PinkCrab\Table_Builder\Engines\Engine;

class Test_Builder extends WP_UnitTestCase {

	/** @testdox It snould be possible to configure the Engine used when constructing the Builder. */
	public function test_can_configure_builder_on_construct(): void {
		$mock_engine = $this->createMock( Engine::class );
		$builder     = new Builder(
			$mock_engine,
			function( Engine $engine ) use ( $mock_engine ) {
				$this->assertSame( $mock_engine, $engine );
			}
		);
	}
}
