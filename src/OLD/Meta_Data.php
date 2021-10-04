<?php

declare(strict_types=1);

/**
 * An abstract class for registering meta data.
 * Can be used to create meta for  'post', 'comment', 'term', 'user' etc.
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

use PinkCrab\Perique\Application\App;
use PinkCrab\Loader\Hook_Loader;

use InvalidArgumentException;
use PinkCrab\Perique\Interfaces\Registerable;

class Meta_Data implements Registerable {

	/**
	 * Object type meta applies to
	 *
	 * @var string
	 */
	protected $meta_type = 'post';

	/**
	 * Holds a secondary object type, used for post type and taxonomy.
	 *
	 * @var string|null
	 */
	protected $object_subtype = null;

	/**
	 * Value type.
	 * accepts 'string', 'boolean', 'integer', 'number', 'array', and 'object'
	 *
	 * @var string
	 */
	protected $type = 'string';

	/**
	 * Meta desctiption
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * Meta value is single value or array
	 *
	 * @var bool
	 */
	protected $single = false;

	/**
	 * Default value
	 * Should match same type as $type defined above.
	 *
	 * @var mixed
	 */
	protected $default = '';

	/**
	 * The meta fields callbacks
	 *
	 * @var array<string, callable|null>
	 */
	protected $callbacks = array(
		'sanitize'    => null,
		'permissions' => null,
	);

	/**
	 * Rest schema definitions
	 *
	 * @var bool|array<mixed>
	 */
	protected $rest_schema = false;

	/**
	 * Meta key
	 *
	 * @var string
	 */
	protected $meta_key;

	public function __construct( string $meta_key ) {
		$this->meta_key = $meta_key;
	}


	/**
	 * Set object type meta applies to
	 *
	 * @param string $meta_type  Object type meta applies to
	 * @return self
	 */
	public function meta_type( string $meta_type ): self {
		$this->meta_type = $meta_type;
		return $this;
	}

	/**
	 * Set holds a secondary object type, used for post type and taxonomy.
	 *
	 * @param string|null $object_subtype  Holds a secondary object type, used for post type and taxonomy.
	 * @return self
	 */
	public function object_subtype( ?string $object_subtype ): self {
		$this->object_subtype = $object_subtype ?? '';
		return $this;
	}

	/**
	 * Sets the values type.
	 * accepts 'string', 'boolean', 'integer', 'number', 'array', and 'object'
	 *
	 * @param string $type
	 * @return self
	 */
	public function type( string $type ): self {
		$this->type = $type;
		return $this;
	}

	/**
	 * Sets the meta type as POST and defined the post type (object_subtype)
	 *
	 * @param string $post_type
	 * @return self
	 */
	public function post_type( string $post_type ): self {
		$this->meta_type( 'post' );
		$this->object_subtype( $post_type );
		return $this;
	}

	/**
	 * Sets the meta type as TERM and defined the taxonomy (object_subtype)
	 *
	 * @param string $taxonomy
	 * @return self
	 */
	public function taxonomy( string $taxonomy ): self {
		$this->meta_type( 'term' );
		$this->object_subtype( $taxonomy );
		return $this;
	}

	/**
	 * Set meta desctiption
	 *
	 * @param string $description  Meta desctiption
	 *
	 * @return self
	 */
	public function description( string $description ): self {
		$this->description = $description;
		return $this;
	}

	/**
	 * Set meta value is single value or array
	 *
	 * @param bool $single  Meta value is single value or array
	 * @return self
	 */
	public function single( bool $single = true ): self {
		$this->single = $single;
		return $this;
	}

	/**
	 * Set should match same type as $type defined above.
	 *
	 * @param mixed $default
	 * @return self
	 */
	public function default( $default ): self {
		$this->default = $default;
		return $this;
	}

	/**
	 * Set the santization callback for setitng values.
	 *
	 * @param callable(mixed):mixed $callback
	 * @return self
	 */
	public function sanitize( callable $callback ): self {
		$this->callbacks['sanitize'] = $callback;
		return $this;
	}

	/**
	 * Set the persmission callback for setitng/getting values
	 *
	 * @param callable $callback
	 * @return self
	 */
	public function permissions( callable $callback ): self {
		$this->callbacks['permissions'] = $callback;
		return $this;
	}

	/**
	 * Set rest schema definitions
	 *
	 * @param bool|array<mixed> $rest_schema  Rest schema definitions
	 * @return self
	 */
	public function rest_schema( $rest_schema ): self {
		$this->rest_schema = $rest_schema;
		return $this;
	}

	/**
	 * Builds the args array for registering metadata
	 *
	 * @return array<string, mixed>
	 */
	public function parse_args(): array {
		$args = array(
			'type'              => $this->type,
			'description'       => $this->description,
			'default'           => $this->default,
			'single'            => $this->single,
			'sanitize_callback' => $this->callbacks['sanitize'],
			'auth_callback'     => $this->callbacks['permissions'],
			'show_in_rest'      => $this->rest_schema,
		);

		// Set subtype.
		if ( $this->object_subtype !== null ) {
			$args['object_subtype'] = $this->object_subtype;
		}
		return $args;
	}

	/**
	 * Register the meta field with the regular registation cylce.
	 * Even though loader isnt used, we can still add to the registration.php as normal.
	 *
	 * @param Hook_Loader $loader
	 * @return void
	 */
	public function register( Hook_Loader $loader ): void { // phpcs:ignore
		register_meta( $this->meta_type, $this->meta_key, $this->parse_args() );
	}

	/**
	 * Get meta key
	 *
	 * @return string
	 */
	public function get_meta_key(): string {
		return $this->meta_key;
	}
}
