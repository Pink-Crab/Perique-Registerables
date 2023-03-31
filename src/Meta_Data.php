<?php

declare(strict_types=1);

/**
 * Meta Data model
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

class Meta_Data {
	/**
	 * Object type meta applies to
	 *
	 * @var string
	 */
	protected string $meta_type = 'post';

	/**
	 * Holds a secondary object type, used for post type and taxonomy.
	 *
	 * @var string|null
	 */
	protected ?string $object_subtype = null;

	/**
	 * Value type.
	 * accepts 'string', 'boolean', 'integer', 'number', 'array', and 'object'
	 *
	 * @var string
	 */
	protected string $type = 'string';

	/**
	 * Meta description
	 *
	 * @var string
	 */
	protected string $description = '';

	/**
	 * Meta value is single value or array
	 *
	 * @var bool
	 */
	protected bool $single = false;

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
	* @var array{
	*  sanitize: null|callable,
	*  permissions: null|callable,
	*  rest_view: null|callable(mixed[]): void,
	*  rest_update: null|callable(mixed,\WP_Post|\WP_Term|\WP_User|\WP_Comment): void
	* }
	*/
	protected array $callbacks = array(
		'sanitize'    => null,
		'permissions' => null,
		'rest_view'   => null,
		'rest_update' => null,
	);

	/**
	 * Rest schema definitions
	 *
	 * @var bool|array<mixed>|\PinkCrab\WP_Rest_Schema\Argument\Argument
	 */
	protected $rest_schema = false;

	/**
	 * Meta key
	 *
	 * @var string
	 */
	protected string $meta_key;

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
	 * Set meta description
	 *
	 * @param string $description  Meta description
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
	 * Set the sanitization callback for setting values.
	 *
	 * @param callable(mixed):mixed $callback
	 * @return self
	 */
	public function sanitize( callable $callback ): self {
		$this->callbacks['sanitize'] = $callback;
		return $this;
	}

	/**
	 * Set the permission callback for setting/getting values
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
	 * @param bool|array<mixed> $rest_schema|PinkCrab\WP_Rest_Schema\Argument\Argument  Rest schema definitions
	 * @return self
	 */
	public function rest_schema( $rest_schema ): self {
		$this->rest_schema = $rest_schema;
		return $this;
	}

	/**
	* Sets the GET callback for REST requests.
	*
	* @param callable|null $callback
	* @return self
	*/
	public function rest_view( ?callable $callback ): self {
		$this->callbacks['rest_view'] = $callback;
		return $this;
	}

	/**
	 * Sets the UPDATE callback for REST requests.
	 *
	 * @param null|callable(mixed,\WP_Post|\WP_Term|\WP_User|\WP_Comment):void $callback
	 * @return self
	 */
	public function rest_update( ?callable $callback ): self {
		$this->callbacks['rest_update'] = $callback;
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
	 * Get meta key
	 *
	 * @return string
	 */
	public function get_meta_key(): string {
		return $this->meta_key;
	}

	/**
	 * Get object type meta applies to
	 *
	 * @return string
	 */
	public function get_meta_type(): string {
		return $this->meta_type;
	}

	/**
	 * Gets the rest schema definition.
	 *
	 * @return bool|array<mixed>|\PinkCrab\WP_Rest_Schema\Argument\Argument
	 */
	public function get_rest_schema() {
		return $this->rest_schema;
	}

	/**
	 * Get holds a secondary object type, used for post type and taxonomy.
	 *
	 * @return string|null
	 */
	public function get_subtype(): ?string {
		return $this->object_subtype;
	}

	/**
	* Gets the GET callback for REST requests.
	*
	* @return null|callable(mixed[]): void
	*/
	public function get_rest_view(): ?callable {
		return $this->callbacks['rest_view'];
	}

	/**
	 * Sets the GET callback for REST requests.
	 *
	 * @return null|callable(mixed,\WP_Post|\WP_Term|\WP_User|\WP_Comment): void
	 */
	public function get_rest_update(): ?callable {
		return $this->callbacks['rest_update'];
	}

	/**
	 * Returns the value type.
	 *
	 * @return string
	 */
	public function get_value_type(): string {
		return $this->type;
	}

}
