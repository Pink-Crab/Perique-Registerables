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
}
