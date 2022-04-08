<?php

declare(strict_types=1);

/**
 * Registration Registrar for all post types.
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

namespace PinkCrab\Registerables\Registrar;

use Exception;
use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Registerables\Meta_Box;
use PinkCrab\Perique\Services\View\View;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Registerables\Validator\Meta_Box_Validator;

class Meta_Box_Registrar {

	/**
	 * @var Meta_Box_Validator
	 */
	protected $validator;

	/**
	 * @var DI_Container
	 */
	protected $container;

	/**
	 * @var Hook_Loader
	 */
	protected $loader;

	public function __construct(
		Meta_Box_Validator $validator,
		DI_Container $container,
		Hook_Loader $loader
	) {
		$this->validator = $validator;
		$this->container = $container;
		$this->loader    = $loader;
	}

	/**
	 * Register a meta box.
	 *
	 * @param Meta_Box $meta_box
	 * @return void
	 */
	public function register( Meta_Box $meta_box ): void {

		if ( ! $this->validator->verify_meta_box( $meta_box ) ) {
			throw new Exception(
				sprintf(
					'Failed validating meta box model(%s) with errors: %s',
					get_class( $meta_box ),
					join( ', ', $this->validator->get_errors() )
				)
			);
		}

		// Set the view using View, if not traditional callback supplied and a defined template.
		if ( ! \is_callable( $meta_box->view ) && is_string( $meta_box->view_template ) ) {
			$meta_box = $this->set_view_callback_from_renderable( $meta_box );
		}
		if ( \is_callable( $meta_box->view ) ) {
			$meta_box = $this->set_view_callback_from_callable( $meta_box );
		}

		// Add meta_box to loader.
		$this->loader->action(
			'add_meta_boxes',
			function() use ( $meta_box ) : void {
				\add_meta_box(
					$meta_box->key,
					$meta_box->label,
					$meta_box->view, /** @phpstan-ignore-line, is validated above*/
					$meta_box->screen,
					$meta_box->context,
					$meta_box->priority,
					$meta_box->view_vars
				);
			}
		);

		// Deffer adding meta box hooks until we can asses the current screen.
		$this->loader->action(
			'current_screen',
			function() use ( $meta_box ) {
				// If we have any hook calls, add them to the loader.
				if ( $this->is_active( $meta_box ) && ! empty( $meta_box->actions ) ) {
					foreach ( $meta_box->actions as $handle => $hook ) {
						add_action( (string) $handle, $hook['callback'], $hook['priority'], $hook['params'] );
					}
				}
			}
		);

	}

	/**
	 * Sets the view callback for a view which is defined as a callback.
	 *
	 * @param \PinkCrab\Registerables\Meta_Box $meta_box
	 * @return \PinkCrab\Registerables\Meta_Box
	 */
	protected function set_view_callback_from_callable( Meta_Box $meta_box ): Meta_Box {

		// Get the current view callback.
		$current_callback = $meta_box->view;

		$meta_box->view(
			function ( \WP_Post $post, array $args ) use ( $meta_box, $current_callback ) {

				// Set the view args
				$args['args']['post'] = $post;
				$args['args']         = $this->filter_view_args( $meta_box, $post, $args['args'] );

				// Render the callback.
				if ( \is_callable( $current_callback ) ) {
					call_user_func( $current_callback, $post, $args );
				}
			}
		);

		return $meta_box;
	}

	/**
	 * Apply rendering the view using View to a meta_box
	 *
	 * @param \PinkCrab\Registerables\Meta_Box $meta_box
	 * @return \PinkCrab\Registerables\Meta_Box
	 */
	protected function set_view_callback_from_renderable( Meta_Box $meta_box ): Meta_Box {

		// Create View(View)
		$view = $this->container->create( View::class );
		if ( is_null( $view ) || ! is_a( $view, View::class ) ) {
			throw new Exception( 'View not defined' );
		}

		$meta_box->view(
			function ( \WP_Post $post, array $args ) use ( $meta_box, $view ) {

				$args['args']['post'] = $post;
				$args['args']         = $this->filter_view_args( $meta_box, $post, $args['args'] );

				// @phpstan-ignore-next-line, template should already be checked for valid template path in register() method (which calls this)
				$view->render( $meta_box->view_template, $args['args'] );
			}
		);

		return $meta_box;
	}

	/**
	 * Checks if the checkbox should be active.
	 *
	 * @return boolean
	 */
	protected function is_active( Meta_Box $meta_box ): bool {
		global $current_screen;

		return ! is_null( $current_screen )
		&& ! empty( $current_screen->post_type )
		&& in_array( $current_screen->post_type, $meta_box->screen, true );
	}

	/**
	 * Filters the render time args through the optional
	 * callback definition in model class.
	 *
	 * @param \PinkCrab\Registerables\Meta_Box $meta_box
	 * @param array<string, mixed> $view_args
	 * @return array<string, mixed>
	 */
	public function filter_view_args( Meta_Box $meta_box, \WP_Post $post, array $view_args ): array {
		if ( is_callable( $meta_box->view_data_filter ) ) {
			$view_args = ( $meta_box->view_data_filter )( $post, $view_args );
		}
		return $view_args;
	}
}
