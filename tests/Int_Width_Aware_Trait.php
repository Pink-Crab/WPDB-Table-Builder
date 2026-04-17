<?php
/**
 * Detects whether the running MySQL / MariaDB version emits integer display
 * widths (e.g. `int(11) unsigned`) from `SHOW COLUMNS` / `DESCRIBE`.
 *
 * MySQL 8.0+ silently drops the width and reports `int unsigned`.
 * MariaDB (through 11.x) still emits the width.
 *
 * Integration tests that assert exact column types should use the `$int_width`
 * flag (true = emits width) to conditionally build their expected values.
 *
 * @package PinkCrab\Table_Builder
 */

declare(strict_types=1);

namespace PinkCrab\Table_Builder\Tests;

trait Int_Width_Aware_Trait {

	/**
	 * Whether the running database still emits integer display widths.
	 *
	 * @var bool|null Lazily initialised.
	 */
	protected $int_width = null;

	/**
	 * Returns true if the running database still emits integer display widths.
	 *
	 * @return bool
	 */
	protected function db_has_int_display_width(): bool {
		if ( null !== $this->int_width ) {
			return $this->int_width;
		}

		global $wpdb;
		$version = (string) $wpdb->get_var( 'SELECT VERSION()' );

		// MariaDB (any version currently shipping) still emits widths.
		if ( false !== stripos( $version, 'mariadb' ) ) {
			$this->int_width = true;
			return true;
		}

		// Pure MySQL: 8.0+ dropped widths.
		$this->int_width = version_compare( $version, '8.0', '<' );
		return $this->int_width;
	}

	/**
	 * Returns the expected column-type string for an integer column,
	 * adapting to whether the current DB emits a display width.
	 *
	 * @param string $base     e.g. 'int' or 'bigint'.
	 * @param int    $width    Display width to emit when the DB supports one.
	 * @param bool   $unsigned Append ' unsigned' suffix when true.
	 * @return string
	 */
	protected function int_type( string $base, int $width, bool $unsigned = false ): string {
		$type = $this->db_has_int_display_width()
			? sprintf( '%s(%d)', $base, $width )
			: $base;
		return $unsigned ? $type . ' unsigned' : $type;
	}
}
