<?php

declare(strict_types=1);
/**
 * Interface to define a schema for SQL database tables.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @package PinkCrab\Core
 */

namespace PinkCrab\Table_Builder\Interfaces;

use PinkCrab\Table_Builder\Interfaces\SQL_Builder;

interface SQL_Schema {

	/**
	 * Constructs the table.
	 *
	 * @since 0.1.0
	 * @param \PinkCrab\Table_Builder\Interfaces\SQL_Builder $builder
	 * @return void
	 */
	public function create_table( SQL_Builder $builder ): void;

	/**
	 * Gets the defined table name
	 *
	 * @since 0.2.0
	 * @return string
	 */
	public function get_table_name(): string;

	/**
	 * Gets the defined primary key
	 *
	 * @since 0.2.0
	 * @return string
	 */
	public function get_primary_key(): string;

	/**
	 * Returns all the defined indexes.
	 *
	 * @since 0.2.0
	 * @return array<int, \PinkCrab\Table_Builder\Table_Index>
	 */
	public function get_indexes(): array;

	/**
	 * Returns all the defined columns
	 *
	 * @since 0.2.0
	 * @return array<string, array>
	 */
	public function get_columns(): array;

}
