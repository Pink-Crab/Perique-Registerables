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

namespace PinkCrab\Registerables\Module\Middleware;

use PinkCrab\Registerables\Taxonomy;
use PinkCrab\Registerables\Post_Type;
use PinkCrab\Perique\Interfaces\Inject_Hook_Loader;
use PinkCrab\Perique\Interfaces\Inject_DI_Container;
use PinkCrab\Registerables\Shared_Meta_Box_Controller;
use PinkCrab\Registerables\Registrar\Registrar_Factory;
use PinkCrab\Perique\Interfaces\Registration_Middleware;
use PinkCrab\Registerables\Registrar\Meta_Box_Registrar;
use PinkCrab\Registerables\Module\Middleware\Registerable;
use PinkCrab\Registerables\Additional_Meta_Data_Controller;
use PinkCrab\Registerables\Registrar\Shared_Meta_Box_Registrar;
use PinkCrab\Registerables\Registrar\Additional_Meta_Data_Registrar;
use PinkCrab\Perique\Services\Container_Aware_Traits\Inject_Hook_Loader_Aware;
use PinkCrab\Perique\Services\Container_Aware_Traits\Inject_DI_Container_Aware;

class Registerable_Middleware implements Registration_Middleware, Inject_Hook_Loader, Inject_DI_Container {

	use Inject_Hook_Loader_Aware;
	use Inject_DI_Container_Aware;

	/**
	 * Register all valid registerables.
	 *
	 * @param Registerable $class_instance
	 * @return object
	 */
	public function process( object $class_instance ): object {
		if ( ! is_a( $class_instance, Registerable::class ) ) {
			return $class_instance;
		}

		// Based on the registerable type.
		switch ( true ) {
			case is_a( $class_instance, Post_Type::class ):
				$this->process_post_type( $class_instance );
				break;

			case is_a( $class_instance, Taxonomy::class ):
				$this->process_taxonomy( $class_instance );
				break;

			case is_a( $class_instance, Shared_Meta_Box_Controller::class ):
				$this->process_shared_meta_box( $class_instance );
				break;

			case is_a( $class_instance, Additional_Meta_Data_Controller::class ):
				$this->process_additional_meta_data( $class_instance );
				break;

			default:
				// Do nothing, but should not get to here.
				break;
		}

		return $class_instance;
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
			static function () use ( $taxonomy ) {
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
			static function () use ( $post_type_registerable ) {
				Registrar_Factory::new()
					->create_from_registerable( $post_type_registerable )
					->register( $post_type_registerable );
			}
		);

		// Define use of gutenberg
		$this->loader->filter(
			'use_block_editor_for_post_type',
			static function ( bool $state, string $post_type ) use ( $post_type_registerable ): bool {
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
	 * Process the additional meta data controller.
	 *
	 * @param \PinkCrab\Registerables\Additional_Meta_Data_Controller $controller
	 * @return void
	 * @since 0.8.0
	 */
	public function process_additional_meta_data( Additional_Meta_Data_Controller $controller ): void {
		$registrar = new Additional_Meta_Data_Registrar(
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
		return Registrar_Factory::new()->meta_box_registrar( $this->di_container, $this->loader );
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
