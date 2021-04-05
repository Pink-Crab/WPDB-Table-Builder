<?php

declare(strict_types=1);

/**
 * Interface for all Table Builder Engines.
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

namespace PinkCrab\Table_Builder\Engines;

use PinkCrab\Table_Builder\Exceptions\Engine_Exception;
use PinkCrab\Table_Builder\{Schema, Index,Column,Foreign_Key};
use PinkCrab\Table_Builder\Engines\{Schema_Validator,Schema_Translator};

interface Engine {

	/**
	 * Sets a validator to the builder
	 *
	 * @param \PinkCrab\Table_Builder\Engines\Schema_Validator $validator
	 * @return self
	 * @throws Engine_Exception Code 1 If a validator is already set.
	 */
	public function set_validator( Schema_Validator $validator ): self;

	/**
	 * Sets a translator to the builder
	 *
	 * @param \PinkCrab\Table_Builder\Engines\Schema_Translator $translator
	 * @return self
	 * @throws Engine_Exception Code 2 If a translator is already set.
	 */
	public function set_translator( Schema_Translator $translator ): self;


	/**
	 * Returns the current engines validator.
	 *
	 * @return \PinkCrab\Table_Builder\Engines\Schema_Validator
	 */
	public function get_validator(): Schema_Validator;

	/**
	 * Creates a table based on the schema passed.
	 *
	 * @param Schema $schema
	 * @return bool
	 */
	public function create_table( Schema $schema ): bool;

	/**
	 * Drops a table based on the schema passed.
	 *
	 * @param Schema $schema
	 * @return bool
	 */
	public function drop_table( Schema $schema): bool;
}
