<?php

declare(strict_types=1);

/**
 * Schema definition
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @since 0.3.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Table_Builder
 */

namespace PinkCrab\Table_Builder;

class Schema {

	/**
	 * The table name
	 *
	 * @var string
	 */
	protected $table_name;

	/**
	 * The table name prefix
	 *
	 * @var string|null
	 */
	protected $prefix = null;

	/**
	 * Table colums
	 *
	 * @var array<Column>
	 */
	protected $columns;

	/**
	 * All table indexes
	 *
	 * @var array<Index>
	 */
	protected $indexes;

	/**
	 * All foreign key relations
	 *
	 * @var array<Foreign_Key>
	 */
	protected $foreign_keys;

	/**
	 * Creates an instance of Schema
	 *
	 * @param string $table_name
	 * @param callable(Schema):void $configure
	 */
	public function __construct( string $table_name, callable $configure ) {
		$this->table_name = $table_name;
		$configure( $this );
	}

	/**
	 * Adds a new column to the schema
	 *
	 * @param string $name
	 * @return Column
	 */
	public function column( string $name ): Column {
		$column          = new Column( $name );
		$this->columns[$name] = $column;
		return $column;
	}


}
