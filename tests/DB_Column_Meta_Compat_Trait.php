<?php
/**
 * Normalises `SHOW COLUMNS` / `DESCRIBE` output across MySQL and MariaDB
 * versions for integration-test assertions.
 *
 * Two quirks we currently cover:
 *
 * 1. Integer display widths.
 *    Older MySQL (< 8.0) and all current MariaDB versions still emit a width
 *    in `Type`, e.g. `int(11) unsigned`. MySQL 8.0+ drops it and returns
 *    `int unsigned`.
 *
 * 2. Empty-default reported as literal `'NULL'` in the `Extra` column.
 *    MySQL 8.0+ returns the literal string `'NULL'` in `Extra` for some
 *    nullable columns where older MySQL and MariaDB return an empty string.
 *
 * =====================================================================
 * WHEN CAN THIS TRAIT BE REMOVED?
 * ---------------------------------------------------------------------
 * Once the minimum supported database for this package is MySQL 8.0+
 * AND no MariaDB release is supported — i.e. the CI matrix no longer
 * contains any DB that emits integer display widths OR empty-string
 * `Extra` values — both helpers here become identity operations and
 * the trait can be deleted, along with the calls to `int_type()` and
 * `extra_empty()` in the integration tests.
 *
 * Practically: drop this trait in the same release that removes support
 * for all MariaDB versions below 12.0 (assuming MariaDB 12.0+ aligns with
 * MySQL 8.0's behaviour) and MySQL < 8.0.
 * =====================================================================
 *
 * @package PinkCrab\Table_Builder
 */

declare(strict_types=1);

namespace PinkCrab\Table_Builder\Tests;

trait DB_Column_Meta_Compat_Trait {

	/**
	 * Cached detection: whether the running DB still emits integer widths.
	 *
	 * @var bool|null
	 */
	protected $db_keeps_int_width = null;

	/**
	 * Cached detection: whether the running DB reports the literal string
	 * `'NULL'` in `Extra` for nullable columns without an explicit default.
	 *
	 * @var bool|null
	 */
	protected $db_emits_null_extra = null;

	/**
	 * Returns true when the running database emits integer display widths
	 * (e.g. `int(11)`) from `SHOW COLUMNS`.
	 *
	 * @return bool
	 */
	protected function db_has_int_display_width(): bool {
		if ( null !== $this->db_keeps_int_width ) {
			return $this->db_keeps_int_width;
		}

		global $wpdb;
		$version = (string) $wpdb->get_var( 'SELECT VERSION()' );

		if ( false !== stripos( $version, 'mariadb' ) ) {
			$this->db_keeps_int_width = true;
			return true;
		}

		// Pure MySQL: 8.0+ dropped widths.
		$this->db_keeps_int_width = version_compare( $version, '8.0', '<' );
		return $this->db_keeps_int_width;
	}

	/**
	 * Returns true when the running database reports the literal string
	 * `'NULL'` in the `Extra` column for a nullable column without an
	 * explicit default.
	 *
	 * @return bool
	 */
	protected function db_emits_literal_null_in_extra(): bool {
		if ( null !== $this->db_emits_null_extra ) {
			return $this->db_emits_null_extra;
		}

		global $wpdb;
		$version = (string) $wpdb->get_var( 'SELECT VERSION()' );

		if ( false !== stripos( $version, 'mariadb' ) ) {
			$this->db_emits_null_extra = false;
			return false;
		}

		// Pure MySQL: 8.0+ emits literal 'NULL'.
		$this->db_emits_null_extra = version_compare( $version, '8.0', '>=' );
		return $this->db_emits_null_extra;
	}

	/**
	 * Builds the expected `Type` string for an integer column, adapting to
	 * whether the running DB emits a display width.
	 *
	 * @param string $base     Base type, e.g. 'int', 'bigint'.
	 * @param int    $width    Display width to include when the DB supports it.
	 * @param bool   $unsigned Append ' unsigned' when true.
	 * @return string
	 */
	protected function int_type( string $base, int $width, bool $unsigned = false ): string {
		$type = $this->db_has_int_display_width()
			? sprintf( '%s(%d)', $base, $width )
			: $base;
		return $unsigned ? $type . ' unsigned' : $type;
	}

	/**
	 * Returns the expected `Extra` value for a nullable column with no
	 * explicit default — either `''` (older MySQL / all MariaDB) or
	 * the literal `'NULL'` (MySQL 8.0+).
	 *
	 * @return string
	 */
	protected function extra_empty(): string {
		return $this->db_emits_literal_null_in_extra() ? 'NULL' : '';
	}
}
