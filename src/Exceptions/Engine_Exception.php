<?php

declare(strict_types=1);

/**
 * Engine exceptions.
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

namespace PinkCrab\Table_Builder\Exceptions;

use Exception;

class Engine_Exception extends Exception {

	/**
	 * Returns an exception for attmepting to set a validator to engine when already set.
	 * @code 1
	 * @return Engine_Exception
	 */
	public static function valdidator_already_defined(): Engine_Exception {
		$message = 'An Engines Schema Validator has already been defined for this builder';
		return new Engine_Exception( $message, 1 );
	}

	/**
	 * Returns an exception for attempting to set a translator to engine when already set.
	 * @code 2
	 * @return Engine_Exception
	 */
	public static function translator_already_defined(): Engine_Exception {
		$message = 'An Engines Schema Translator has already been defined for this builder';
		return new Engine_Exception( $message, 2 );
	}
}
