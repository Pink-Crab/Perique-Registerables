<?php


declare(strict_types=1);

/**
 * Validates a Taxonomy model.
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

use PinkCrab\Registerables\Taxonomy;
use PinkCrab\Registerables\Validator\Abstract_Validator;
use PinkCrab\Registerables\Module\Middleware\Registerable;

class Taxonomy_Validator extends Abstract_Validator {

	protected const REQUIRED_FIELDS = array( 'slug', 'singular', 'plural' );

	/**
	 * Validates the class passed.
	 *
	 * @param \PinkCrab\Registerables\Module\Middleware\Registerable $object
	 * @return bool
	 */
	public function validate( Registerable $object ): bool {
		// If this is not a valid taxonomy, just bail here.
		if ( ! is_a( $object, Taxonomy::class ) ) {
			$this->add_error( sprintf( '%s is not a valid Taxonomy Model', get_class( $object ) ) );
			return false;
		}

		/* @var Taxonomy $object, already confirmed as a Taxonomy */

		// Ensure all required fields are set.
		$this->has_required_fields( $object );

		// Check if the passed object has any errors.
		return ! $this->has_errors();
	}

	/**
	 * Checks the model has the required fields.
	 *
	 * @param Taxonomy $taxonomy
	 * @return void
	 */
	protected function has_required_fields( Taxonomy $taxonomy ): void {
		foreach ( self::REQUIRED_FIELDS as $field ) {
			if ( ! is_string( $taxonomy->{$field} )
			|| \mb_strlen( $taxonomy->{$field} ) === 0
			) {
				$this->add_error( sprintf( '%s is not set on %s Taxonomy Model', $field, get_class( $taxonomy ) ) );
			}
		}
	}
}
