<?php

declare(strict_types=1);

/**
 * Used for registering Meta Data.
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
 * @package PinkCrab\Registerables
 * @since 0.7.1
 */

namespace PinkCrab\Registerables\Registrar;

use PinkCrab\Registerables\Meta_Data;

class Meta_Data_Registrar {

	/**
	 * Registers meta data for post types.
	 *
	 * @param \PinkCrab\Registerables\Meta_Data $meta
	 * @param string $post_type
	 * @return bool
	 * @throws \Exception if fails to register meta data.
	 */
	public function register_for_post_type( Meta_Data $meta, string $post_type ):bool {
		return $this->register_meta( $meta, 'post', $post_type );
	}

	/**
	 * Registers meta data for terms.
	 *
	 * @param \PinkCrab\Registerables\Meta_Data $meta
	 * @param string $taxonomy
	 * @return bool
	 * @throws \Exception if fails to register meta data.
	 */
	public function register_for_term( Meta_Data $meta, string $taxonomy ):bool {
		return $this->register_meta( $meta, 'term', $taxonomy );
	}

	/**
	 * Registers meta data for users.
	 *
	 * @param \PinkCrab\Registerables\Meta_Data $meta
	 * @return bool
	 * @throws \Exception if fails to register meta data.
	 */
	public function register_for_user( Meta_Data $meta ): bool {
		return $this->register_meta( $meta, 'user', '' );
	}

	/**
	 * Registers meta data for comments.
	 *
	 * @param \PinkCrab\Registerables\Meta_Data $meta
	 * @return bool
	 * @throws \Exception if fails to register meta data.
	 */
	public function register_for_comment( Meta_Data $meta ): bool {
		return $this->register_meta( $meta, 'comment', '' );
	}

	/**
	 * Registers meta data for a defined type.
	 *
	 * Will cast WP Rest Schema model to array
	 *
	 * @param \PinkCrab\Registerables\Meta_Data $meta
	 * @param  string $meta_type The object type ('post', 'user', 'comment', 'term')
	 * @param  string $sub_type The object sub-type ('post_type', 'taxonomy')
	 * @return bool
	 * @throws \Exception if fails to register meta data.
	 */
	protected function register_meta( Meta_Data $meta, string $meta_type, string $sub_type ): bool {
		// Clone and set the post type, while enforcing it as a post meta.
		$meta = clone $meta;
		$meta->object_subtype( $sub_type );
		$meta->meta_type( $meta_type );

		// Normalise rest schema model to array.
		$meta = $this->normalise_rest_schema( $meta );

		$result = register_meta( $meta->get_meta_type(), $meta->get_meta_key(), $meta->parse_args() );
		if ( ! $result ) {
			throw new \Exception(
				"Failed to register {$meta->get_meta_key()} (meta) for {$sub_type} of {$meta_type} type"
			);
		}

		// Maybe register rest fields.
		if ( false !== $meta->get_rest_schema() ) {
			$this->register_meta_rest_field( $meta );
		}

		return $result;
	}


	/**
	 * Potentially casts a Rest Schema to an array.
	 *
	 * Only if the module active and the schema is Argument type.
	 *
	 * @param \PinkCrab\Registerables\Meta_Data $meta
	 * @return \PinkCrab\Registerables\Meta_Data
	 */
	protected function normalise_rest_schema( Meta_Data $meta ): Meta_Data {
		if ( \class_exists( 'PinkCrab\WP_Rest_Schema\Argument\Argument' )
		&& $meta->get_rest_schema() instanceof \PinkCrab\WP_Rest_Schema\Argument\Argument
		) {
			$meta->rest_schema( \PinkCrab\WP_Rest_Schema\Parser\Argument_Parser::for_meta_data( $meta->get_rest_schema() ) );
		}
		return $meta;
	}

	/**
	* Registers a Meta Data object as defined REST field.
	*
	* @param \PinkCrab\Registerables\Meta_Data $meta
	* @return void
	*/
	public function register_meta_rest_field( Meta_Data $meta ) {
		// Skip if not sub type defined for post or term.
		if ( null === $meta->get_subtype() ) {
			return;
		}

		add_action(
			'rest_api_init',
			function () use ( $meta ) {
				register_rest_field(
					$meta->get_subtype(),
					$meta->get_meta_key(),
					array( // @phpstan-ignore-line WP Docblock doesn't give enough details of callable param types, so throws false positive
						'get_callback'    => $meta->get_rest_view() ?? $this->create_rest_get_method( $meta ),
						'schema'          => $meta->get_rest_schema(),
						'update_callback' => $meta->get_rest_update() ?? $this->create_rest_update_method( $meta ),
					)
				);
			}
		);
	}

	/**
	 * Creates a fallback rest get callback.
	 *
	 * @param \PinkCrab\Registerables\Meta_Data $meta
	 * @return callable(array<mixed>):void
	 */
	protected function create_rest_get_method( Meta_Data $meta ): callable {
		return function( $model ) use ( $meta ) {
			switch ( $meta->get_meta_type() ) {
				case 'post':
					$value = get_post_meta( $model['id'], $meta->get_meta_key(), true );
					break;

				case 'term':
					$value = get_term_meta( $model['id'], $meta->get_meta_key(), true );
					break;

				case 'user':
					$value = get_user_meta( $model['id'], $meta->get_meta_key(), true );
					break;

				case 'comment':
					$value = get_comment_meta( $model['id'], $meta->get_meta_key(), true );
					break;

				default:
					$value = null;
					break;
			}

			return $value;
		};
	}

	/**
	 * Creates a fallback rest update callback.
	 *
	 * @param Meta_Data $meta
	 * @return \Closure(mixed, \WP_Post|\WP_Term|\WP_User|\WP_Comment): mixed
	 */
	protected function create_rest_update_method( Meta_Data $meta ): \Closure {
		/**
		 * @param mixed $value
		 * @param \WP_Post|\WP_Term|\WP_User|\WP_Comment $object
		 */
		return function( $value, $object ) use ( $meta ) {
			switch ( $meta->get_meta_type() ) {
				case 'post':
					/** @var \WP_Post $object */
					update_post_meta( $object->ID, $meta->get_meta_key(), $value );
					break;

				case 'term':
					/** @var \WP_Term $object */
					update_term_meta( $object->term_id, $meta->get_meta_key(), $value );
					break;

				case 'user':
					/** @var \WP_User $object */
					update_user_meta( $object->ID, $meta->get_meta_key(), $value );
					break;

				case 'comment':
					/** @var \WP_Comment $object */
					update_comment_meta( (int) $object->comment_ID, $meta->get_meta_key(), $value );
					break;

				default:
					// @codeCoverageIgnoreStart
					break;
					// @codeCoverageIgnoreEnd
			}

			return $value;
		};
	}
}
