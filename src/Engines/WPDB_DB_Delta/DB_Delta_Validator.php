<?php

declare(strict_types=1);

/**
 * WPDB DB_Delta table validator.
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

namespace PinkCrab\Table_Builder\Engines\WPDB_DB_Delta;

use PinkCrab\Table_Builder\Schema;
use PinkCrab\Table_Builder\Engines\Schema_Validator;

class DB_Delta_Validator implements Schema_Validator {

	/**
	 * All errors found while validating
	 *
	 * @var array<string>
	 */
	protected $errors = array();

	/**
	 * Valdiates the schema passed.
	 *
	 * @param Schema $schema
	 * @return bool
	 */
	public function validate( Schema $schema ): bool {
		// Reset errors.
		$this->errors = array();

		return true;
	}

	/**
	 * Returns all errors encountered during validation.
	 *
	 * @return array<string>
	 */
	public function get_errors(): array {
		return $this->errors;
	}


}
