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

use PinkCrab\Registerables\Meta_Box;
use PinkCrab\Registerables\Post_Type;
use PinkCrab\Registerables\Validator\Abstract_Validator;
use PinkCrab\Registerables\Module\Middleware\Registerable;

class Meta_Box_Validator extends Abstract_Validator {

	protected const REQUIRED_FIELDS = array( 'key', 'label' );

	/**
	 * Validates the class passed.
	 *
	 * @param \PinkCrab\Registerables\Module\Middleware\Registerable $object
	 * @return bool
	 */
	public function validate( Registerable $object ): bool { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClass
		return false; //no op
	}

	/**
	 * Verifies a metabox
	 *
	 * @param mixed $meta_box
	 * @return bool
	 */
	public function verify_meta_box( $meta_box ): bool {
		// If this is not a valid post type, just bail here.
		if ( ! is_object( $meta_box ) || ! is_a( $meta_box, Meta_Box::class ) ) {
			$this->add_error( sprintf( '%s is not a valid Meta Box Model', is_object( $meta_box ) ? get_class( $meta_box ) : \gettype( $meta_box ) ) );
			return false;
		}

		/* @var Meta_Box $object, already confirmed as a post type */

		// Ensure all required fields are set.
		$this->has_required_fields( $meta_box );

		// Ensure can render view.
		$this->has_valid_view( $meta_box );

		// Check if the passed object has any errors.
		return ! $this->has_errors();
	}

	/**
	 * Checks the model has the required fields.
	 *
	 * @param Meta_Box $meta_box
	 * @return void
	 */
	protected function has_required_fields( Meta_Box $meta_box ): void {
		foreach ( self::REQUIRED_FIELDS as $field ) {
			if ( ! is_string( $meta_box->{$field} )
			|| \mb_strlen( $meta_box->{$field} ) === 0
			) {
				$this->add_error( sprintf( '%s is not set on %s Meta Box Model', $field, get_class( $meta_box ) ) );
			}
		}
	}

	/**
	 * Checks if the meta box has a valid view callable or a template
	 * which can be rendered using VIEW.
	 *
	 * @param \PinkCrab\Registerables\Meta_Box $meta_box
	 * @return void
	 */
	protected function has_valid_view( Meta_Box $meta_box ): void {
		if ( ! \is_callable( $meta_box->view )
		&& ( ! is_string( $meta_box->view_template ) || \mb_strlen( $meta_box->view_template ) === 0 )
		) {
			$this->add_error( sprintf( '%s doesn\'t have a valid view defined.', get_class( $meta_box ) ) );
		}
	}
}
