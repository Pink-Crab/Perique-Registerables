<?php

declare( strict_types=1 );

/**
 * A simple wrapper for getting and sanitizing all http requests.
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

namespace PinkCrab\Registerables;

use PinkCrab\Core\Services\Registration\Loader;

class MetaBox {

	/**
	 * The metabox key
	 *
	 * @var string
	 */
	public $key;

	/**
	 * The metabox label/title
	 *
	 * @var string
	 */
	public $label;

	/**
	 * The view callback
	 *
	 * @var callable
	 */
	public $view;

	/**
	 * The view args passed to view.
	 *
	 * @var array<string, mixed>
	 */
	public $view_vars = array();

	/**
	 * Screens to display metabox.
	 *
	 * @var array<int, string>
	 */
	public $screen = array();

	/**
	 * Metabox context/position
	 *
	 * @var string normal|side
	 */
	public $context = 'normal';

	/**
	 * What is the loading priroity/
	 *
	 * @var string
	 */
	public $priority = 'default';

	/**
	 * Define any hooks that should fire with the metabox.
	 *
	 * @var array<string, array>
	 */
	public $actions = array();

	/**
	 * Creates a MetaBox with a defined key.
	 *
	 * @param string $key
	 */
	final public function __construct( string $key ) {
		$this->key = $key;
	}

	/**
	 * Attempts to set its own screen.
	 *
	 * @return void
	 */
	private function set_screen(): void {
		if ( \is_admin() && \function_exists( 'get_current_screen' ) ) {
			$current_screen = \get_current_screen();
			if ( ! empty( $current_screen->post_type ) ) {
				array_push( $this->screen, $current_screen->post_type );
			}
		}
	}

	/**
	 * Creates a full width metabox with a defined key.
	 *
	 * @param string $key
	 * @return self
	 */
	public static function normal( string $key ): self {
		$meta_box          = new static( $key );
		$meta_box->context = 'normal';
		$meta_box->set_screen();
		return $meta_box;
	}

	/**
	 * Creates a full width metabox with a defined key.
	 *
	 * @param string $key
	 * @return self
	 */
	public static function side( string $key ): self {
		$meta_box          = new static( $key );
		$meta_box->context = 'side';
		return $meta_box;
	}

	/**
	 * Sets the label
	 *
	 * @param string $label
	 * @return self
	 */
	public function label( string $label ): self {
		$this->label = $label;
		return $this;
	}

	/**
	 * Sets the screens this metabox will be loaded.
	 *
	 * @param string|array<mixed>|\WP_Screen $screen
	 * @return self
	 */
	public function screen( $screen ): self {
		array_push( $this->screen, $screen );
		return $this;
	}

	/**
	 * Sets the view args.
	 *
	 * @param array<string, mixed> $view_vars
	 * @return self
	 */
	public function view_vars( array $view_vars ): self {
		$this->view_vars = $view_vars;
		return $this;
	}

	/**
	 * Sets the view metod.
	 * Can be a callable or a function or class|method array.
	 *
	 * @param callable $callable
	 * @return self
	 */
	public function view( $callable ): self {
		$this->view = $callable;
		return $this;
	}

	/**
	 * Adds a acton to be defined as a hookkey and callable|function
	 *
	 * @param string                $hook
	 * @param callable $callable
	 * @return self
	 */
	public function add_action( string $hook, callable $callable, int $priority = 10, int $params = 1 ): self {
		$this->actions[ $hook ] =
			array(
				'callback' => $callable,
				'priority' => $priority,
				'params'   => $params,
			);
		return $this;
	}

	/**
	 * Registers the meta box.
	 *
	 * @param Loader $loader
	 * @return void
	 */
	public function register( Loader $loader ): void {

		// Register the metabox.
		$loader->action(
			'add_meta_boxes',
			function() {
				\add_meta_box(
					$this->key,
					$this->label,
					$this->view,
					$this->screen,
					$this->context,
					$this->priority,
					$this->view_vars
				);
			}
		);

		// If we have any hook calls, add them to the loader.
		if ( ! empty( $this->actions ) ) {
			foreach ( $this->actions as $handle => $hook ) {
				$loader->action( (string) $handle, $hook['callback'], $hook['priority'], $hook['params'] );
			}
		}
	}


	/**
	 * Checks if the checkbox should be active.
	 *
	 * @return boolean
	 */
	protected function is_active(): bool {
		$current_screen = get_current_screen();
		return ! empty( $current_screen->post_type ) && in_array( $current_screen->post_type, $this->screen );
	}
}

