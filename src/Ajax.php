<?php

declare(strict_types=1);

/**
 * An inheritable ajax loader.
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

use PinkCrab\HTTP\HTTP;
use PinkCrab\Loader\Loader;
use InvalidArgumentException;
use PinkCrab\Enqueue\Enqueue;
use PinkCrab\Core\Application\App;
use Psr\Http\Message\ResponseInterface;
use PinkCrab\Core\Collection\Collection;
use PinkCrab\Core\Interfaces\Registerable;
use Psr\Http\Message\ServerRequestInterface;

abstract class Ajax implements Registerable {

	/**
	 * The ajax calls nonce handle.
	 *
	 * @var string|null
	 */
	protected $nonce_handle;

	/**
	 * Define the action to call.
	 *
	 * @var string|null
	 * @required
	 */
	protected $action;

	/**
	 * Should the ajax call be registered if the user is logged in.
	 *
	 * @var boolean
	 */
	protected $logged_in = true;

	/**
	 * Should the ajax call be registered if the user is not logged in
	 * non_priv
	 *
	 * @var boolean
	 */
	protected $logged_out = true;



	/**
	 * The field name/id for the nonce field.
	 *
	 * @var string
	 */
	protected $nonce_field = 'nonce';

	/**
	 * Collection of Equeue objects
	 *
	 * @var Collection
	 */
	protected $scripts;

	/**
	 * The incoming request
	 *
	 * @var ServerRequestInterface
	 */
	protected $request;

	/**
	 * Creates an instance of ajax with a blank scripts stack.
	 */
	public function __construct( ServerRequestInterface $request ) {
		$this->scripts = new Collection();
		$this->request = $request;
	}

	/**
	 * Called before the ajax call is registed.
	 *
	 * @return void
	 */
	public function set_up(): void {}

	/**
	 * Called before the ajax call is registed.
	 *
	 * @return void
	 */
	public function tare_down():void {}

	/**
	 * Define any conditionals to only load .
	 *
	 * @return boolean
	 */
	public function conditional(): bool {
		return true;
	}

	/**
	 * Handles the callback.
	 *
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	abstract public function callback( ResponseInterface $response ): ResponseInterface;

	/**
	 * Validates the nonce
	 *
	 * @param ServerRequestInterface $request
	 * @return bool
	 */
	protected function validate( ServerRequestInterface $request ): bool {
		// Extract the params from the request (POST or GET)
		$request_params = $this->extract_request_params( $request );
		// If we have a nonce value to check.
		if ( ! empty( $this->nonce_handle ) ) {
			$nonce_value = \array_key_exists( $this->nonce_field, $request_params )
				? \sanitize_text_field( $request_params[ $this->nonce_field ] )
				: null;

			// If no nonce, fail.
			if ( is_null( $nonce_value ) || empty( $nonce_value ) ) {
				return false;
			}

			return (bool) wp_verify_nonce( $nonce_value, $this->nonce_handle );
		}

		return true;
	}

	/**
	 * Based on the request the request type, either extract the body or params.
	 *
	 * @param ServerRequestInterface $request
	 * @return array<string, string>
	 */
	protected function extract_request_params( ServerRequestInterface $request ): array {
		switch ( $request->getMethod() ) {
			case 'POST':
				// Return different post types.
				if ( str_contains( $request->getHeaderLine( 'Content-Type' ), 'application/x-www-form-urlencoded;' ) ) {
					$params = (array) $request->getParsedBody();
				} else {
					$params = json_decode( (string) $request->getBody(), true ) ?? array();
				}
				break;
			case 'GET':
				$params = $request->getQueryParams();
				break;
			default:
				$params = array();
				break;
		}
		return $params;
	}

	/**
	 * Registers the ajax action and any enqueued JS.
	 *
	 * @return void
	 */
	public function register( Loader $loader ): void {

		// Run any setup before registering.
		$this->set_up();
		// Ensure we have a valid action.

		if ( empty( $this->action ) || ! is_string( $this->action ) ) {
			throw new InvalidArgumentException( 'Ajax calls must have a action defined ' . static::class );
		}

		// Register the ajax action using loader.
		$loader->ajax(
			$this->action,
			array( $this, 'entry' ),
			$this->logged_out,
			$this->logged_in
		);

		// Add scripts if conditional is passed and scripts is not empty.
		if ( $this->conditional() && ! $this->scripts->is_empty() ) {
			// Load front end scripts.
			if ( ! is_admin() ) {
				do {
					$script = $this->scripts->pop();
					if ( $script instanceof Enqueue ) {
						$loader->front_action(
							'wp_enqueue_scripts',
							function() use ( $script ) {
								$script->register();
							}
						);
					}
				} while ( ! $this->scripts->is_empty() );
			}

			// Load admin scripts.
			if ( is_admin() ) { /** @phpstan-ignore-line */
				do {
					$script = $this->scripts->pop();
					if ( $script instanceof Enqueue ) {
						$loader->admin_action(
							'admin_enqueue_scripts',
							function( string $hook ) use ( $script ) {  // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterface
								$script->register();
							}
						);
					}
				} while ( ! $this->scripts->is_empty() );
			}
		}

		$this->tare_down();
	}

	/**
	 * The entry point for the ajax call.
	 *
	 * Populates the callback with the request and emits the response.
	 *
	 * @return void
	 */
	public function entry(): void {

		// Helper HTTP.
		$http = new HTTP();

		// If we have a nonce key to validate, check
		if ( $this->validate( $this->request ) ) {
			$response = $this->callback( $http->psr7_response() );
		} else { // Returns 401 if not validated.
			$response = $http->psr7_response(
				wp_json_encode( array( 'error' => 'Request not authenticated' ) ) ?: null,
				401
			);
		}

		$http->emit_response( $response );
		wp_die();
	}

	/**
	 * Return as Json.
	 *
	 * @deprecated 0.2.0
	 * @codeCoverageIgnore
	 * @param array<string, mixed> $data
	 * @param int $status
	 * @param array<string, mixed> $headers
	 * @return void
	 */
	protected function returnAsJson( array $data = array(), ?int $status = null, array $headers = array() ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceAfterLastUsed		
		wp_send_json( $data, $status );
	}

	/**
	 * Returns the nonce field.
	 *
	 * @return void
	 */
	public static function nonce_field(): void {

		// $instance is Ajax
		$instance = App::make( static::class );

		// Check we have a valid Ajax instance.
		if ( is_object( $instance )
			&& is_a( $instance, static::class )
			&& property_exists( $instance, 'nonce_handle' )
			&& is_string( $instance->nonce_handle )
		) {
			$nonce = wp_create_nonce( $instance->nonce_handle );
			print( "<input type='hidden' name='{$instance->nonce_field}' id='{$instance->nonce_field}' value='{$nonce}'>" ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Returns the nonce value.
	 *
	 * @return string
	 */
	public static function nonce_value(): string {

		// $instance is Ajax
		$instance = App::make( static::class );

		// Check we have a valid Ajax instance.
		if ( is_object( $instance )
		&& is_a( $instance, static::class )
		&& property_exists( $instance, 'nonce_handle' )
		&& is_string( $instance->nonce_handle ) ) {
			return wp_create_nonce( $instance->nonce_handle );
		}

		return '';

	}

	/**
	 * Returns the ajax calls action, if defined.
	 *
	 * @return string
	 */
	public static function action(): string {

		// $instance is Ajax
		$instance = App::make( static::class );

		// Check we have a valid Ajax instance.
		if ( is_object( $instance )
		&& is_a( $instance, static::class )
		&& property_exists( $instance, 'action' )
		&& is_string( $instance->nonce_handle ) ) {
			return $instance->action ?? '';
		}

		return '';
	}
}
