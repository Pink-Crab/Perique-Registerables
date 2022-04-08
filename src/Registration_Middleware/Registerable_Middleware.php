<?php


declare(strict_types=1);

/**
 * Registerable Middleware
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
 */

namespace PinkCrab\Registerables\Registration_Middleware;

use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Registerables\Taxonomy;
use PinkCrab\Registerables\Post_Type;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Registerables\Shared_Meta_Box_Controller;
use PinkCrab\Registerables\Registrar\Registrar_Factory;
use PinkCrab\Perique\Interfaces\Registration_Middleware;
use PinkCrab\Registerables\Registrar\Meta_Box_Registrar;
use PinkCrab\Registerables\Registrar\Shared_Meta_Box_Registrar;
use PinkCrab\Registerables\Registration_Middleware\Registerable;

class Registerable_Middleware implements Registration_Middleware {

	/** @var Hook_Loader */
	protected $loader;

	/** @var DI_Container */
	protected $container;

	/**
	 * Sets the global hook loader
	 *
	 * @param \PinkCrab\Loader\Hook_Loader $loader
	 * @return void
	 */
	public function set_hook_loader( Hook_Loader $loader ) {
		$this->loader = $loader;
	}

	/**
	 * Sets the global DI containers
	 *
	 * @param \PinkCrab\Perique\Interfaces\DI_Container $container
	 * @return void
	 */
	public function set_di_container( DI_Container $container ): void {
		$this->container = $container;
	}

	/**
	 * Register all valid registerables.
	 *
	 * @param object|Registerable $class
	 * @return object
	 */
	public function process( $class ) {
		if ( ! is_a( $class, Registerable::class ) ) {
			return $class;
		}

		// Based on the registerable type.
		switch ( true ) {
			case is_a( $class, Post_Type::class ):
				$this->process_post_type( $class );
				break;

			case is_a( $class, Taxonomy::class ):
				$this->process_taxonomy( $class );
				break;

			case is_a( $class, Shared_Meta_Box_Controller::class ):
				$this->process_shared_meta_box( $class );
				break;

			default:
				// Do nothing, but should not get to here.
				break;
		}

		return $class;
	}

	/**
	 * Processes and registers a taxonomy
	 *
	 * @param \PinkCrab\Registerables\Taxonomy $taxonomy
	 * @return void
	 * @since 0.7.0
	 */
	protected function process_taxonomy( Taxonomy $taxonomy ): void {
		$this->loader->action(
			'init',
			static function() use ( $taxonomy ) {
				Registrar_Factory::new()
					->create_from_registerable( $taxonomy )
					->register( $taxonomy );
			}
		);
	}

	/**
	 * Processes and registers a post type.
	 *
	 * @param \PinkCrab\Registerables\Post_Type $post_type_registerable
	 * @return void
	 * @since 0.7.0
	 */
	protected function process_post_type( Post_Type $post_type_registerable ) {
		// Register registerable.
		$this->loader->action(
			'init',
			static function() use ( $post_type_registerable ) {
				Registrar_Factory::new()
					->create_from_registerable( $post_type_registerable )
					->register( $post_type_registerable );
			}
		);

		// Define use of gutenberg
		$this->loader->filter(
			'use_block_editor_for_post_type',
			static function( bool $state, string $post_type ) use ( $post_type_registerable ): bool {
					return $post_type === $post_type_registerable->key
						? (bool) $post_type_registerable->gutenberg
						: $state;
			},
			10,
			2
		);

		// Register meta boxes.
		$meta_boxes = $post_type_registerable->meta_boxes( array() );

		if ( ! empty( $meta_boxes ) ) {

			// Create the registrar
			$meta_box_registrar = $this->get_meta_box_registrar();

			// Register each meta box.
			foreach ( $meta_boxes as $meta_box ) {
				$meta_box->screen( $post_type_registerable->key );
				$meta_box_registrar->register( $meta_box );
			}
		}
	}

	/**
	 * Processes a shared meta box controller.
	 * Registers both meta box and meta data.
	 *
	 * @param \PinkCrab\Registerables\Shared_Meta_Box_Controller $controller
	 * @return void
	 * @since 0.7.0
	 */
	public function process_shared_meta_box( Shared_Meta_Box_Controller $controller ): void {
		$registrar = new Shared_Meta_Box_Registrar(
			$this->get_meta_box_registrar(),
			Registrar_Factory::new()->meta_data_registrar()
		);
		$registrar->register( $controller );
	}

	/**
	 * Constructs and returns and instance of the Meta Box Registrar
	 *
	 * @return \PinkCrab\Registerables\Registrar\Meta_Box_Registrar
	 * @since 0.7.0
	 */
	public function get_meta_box_registrar(): Meta_Box_Registrar {
		return Registrar_Factory::new()->meta_box_registrar( $this->container, $this->loader );
	}

	public function setup(): void {
		/*noOp*/
	}

	/**
	 * Register all routes with WordPress calls.
	 *
	 * @return void
	 */
	public function tear_down(): void {
		/*noOp*/
	}
}
