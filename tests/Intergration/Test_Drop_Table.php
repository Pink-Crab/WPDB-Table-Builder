<?php

declare(strict_types=1);

/**
 * Tests creating a simple table with only a primary column
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\WPDB_Table_Builder
 */

namespace PinkCrab\Table_Builder\Tests;

use WP_UnitTestCase;
use PinkCrab\Table_Builder\Schema;
use PinkCrab\Table_Builder\Builder;
use PinkCrab\Table_Builder\Engines\WPDB_DB_Delta\DB_Delta_Engine;

class Test_Drop_Table extends WP_UnitTestCase {

    /** @testdox It should be possible to drop/remove a table based on a passed schema. */
    public function test_drop_table_with_valid_schema(): void
    {
        $this->setOutputCallback(function() {});
        
        $schema = new Schema('test_drop_table', function(Schema $schema): void{
            $schema->column('id')->unsigned_int(11)->auto_increment();
            $schema->index('id')->primary();
        });

        global $wpdb;
        $builder = new Builder( new DB_Delta_Engine( $wpdb ) );
		$builder->create_table( $schema );

        // Check the table exists, generate an error otherwise
        if(empty($wpdb->get_results( 'SHOW COLUMNS FROM test_drop_table;' ))){
            $this->fail("FAILED TO CREATE TABLE TO BE REMOVED");
        }

        // Remove table
        $builder->drop_table($schema);
        $table_details = $wpdb->get_results( 'SHOW COLUMNS FROM test_drop_table;' );
        $this->assertEmpty($table_details);
    }
}