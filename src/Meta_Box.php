<?php

declare(strict_types=1);

/**
 * Meta Box model
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

class Meta_Box {

	/**
	 * The meta box key
	 *
	 * @var string
	 * @required
	 */
	public string $key = '';

	/**
	 * The meta box label/title
	 *
	 * @var string
	 * @required
	 */
	public string $label = '';

	/**
	 * The view callback
	 *
	 * @var callable|null
	 */
	public $view;

	/**
	 * The view args passed to view.
	 *
	 * @var array<string, mixed>
	 */
	public array $view_vars = array();

	/**
	 * The path relative to the defined base path
	 * in config
	 *
	 * @var string|null
	 */
	public ?string $view_template = null;

	/**
	 * Screens to display meta box.
	 *
	 * @var array<int, string>
	 * @required
	 */
	public array $screen = array();

	/**
	 * Meta box context/position
	 *
	 * @var 'advanced'|'normal'|'side'
	 * @required
	 */
	public string $context = 'normal';

	/**
	 * What is the loading priority
	 *
	 * @var 'core'|'default'|'high'|'low'
	 */
	public string $priority = 'default';

	/**
	 * Define any hooks that should fire with the meta box.
	 *
	 * @var array<string, array{callback:callable,priority:int,params:int}>
	 */
	public array $actions = array();

	/**
	 * Filter for pushing post specific data into the views
	 * global variable scope
	 *
	 * @var null|callable(\WP_Post $post,array<string, mixed> $args):array<string, mixed>
	 */
	public $view_data_filter;

	/**
	 * Creates a Meta Box with a defined key.
	 *
	 * @param string $key
	 */
	final public function __construct( string $key ) {
		$this->key = $key;
	}

	/**
	 * Creates a full width meta box with a defined key.
	 *
	 * @param string $key
	 * @return self
	 */
	public static function normal( string $key ): self {
		$meta_box          = new static( $key );
		$meta_box->context = 'normal';
		return $meta_box;
	}

	/**
	 * Creates a full width meta box with a defined key.
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
	 * Sets the screens this meta box will be loaded.
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
	 * Sets the views template path.
	 * Should be relative to the base path defined in config.
	 *
	 * @param string $view_template
	 * @return self
	 */
	public function view_template( string $view_template ): self {
		$this->view_template = $view_template;
		return $this;
	}

	/**
	 * Sets the view method.
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
	 * Adds a acton to be defined as a hook key and callable|function
	 *
	 * @param string   $hook
	 * @param callable $callable
	 * @param int $priority
	 * @param int $params
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
	 * Set $args):args
	 *
	 * @param callable(\WP_Post $post,array<string, mixed> $args):array<string, mixed> $view_data_filter
	 * @return self
	 */
	public function view_data_filter( callable $view_data_filter ): self {
		$this->view_data_filter = $view_data_filter;
		return $this;
	}
}
