<?php

declare(strict_types=1);
/**
 * SQL Builder interface. Used to build SQL_Schema compliant objects.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @package PinkCrab\Core
 */

namespace PinkCrab\Table_Builder\Interfaces;

use PinkCrab\Table_Builder\Interfaces\SQL_Schema;


interface SQL_Builder {

	/**
	 * Builds the passed schema.
	 *
	 * @since 0.1.0
	 * @param SQL_Schema $schema
	 * @return void
	 */
	public function build( SQL_Schema $schema ): void;

	/**
	 * Method for dropping the table.
	 *
	 * @since 0.2.1
	 * @param SQL_Schema $schema
	 * @return void
	 */
	public function drop( SQL_Schema $schema ): void;

}
