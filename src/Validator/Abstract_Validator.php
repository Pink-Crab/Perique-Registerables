<?php

declare(strict_types=1);

/**
 * Validates a Post Type model.
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
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables\Validator
 */

namespace PinkCrab\Registerables\Validator;

use PinkCrab\Registerables\Registration_Middleware\Registerable;

abstract class Abstract_Validator {

	/**
	 * All errors found during validation
	 *
	 * @var string[]
	 */
	protected $errors = array();

	/**
	 * Checks if errors set.
	 *
	 * @return bool
	 */
	public function has_errors(): bool {
		return count( $this->errors ) >= 1;
	}

	/**
	 * Returns all errors.
	 *
	 * @return string[]
	 */
	public function get_errors(): array {
		return $this->errors;
	}

	/**
	 * Adds an error to the collection.
	 *
	 * @param string $error
	 * @return self
	 */
	public function add_error( string $error ): self {
		$this->errors[] = $error;
		return $this;
	}

	/**
	 * Reset the error collection
	 *
	 * @return self
	 */
	public function reset_errors(): self {
		$this->errors = array();
		return $this;
	}

	/**
	 * Validates the class passed.
	 *
	 * @param \PinkCrab\Registerables\Registration_Middleware\Registerable $object
	 * @return bool
	 */
	abstract public function validate( Registerable $object ): bool;
}
