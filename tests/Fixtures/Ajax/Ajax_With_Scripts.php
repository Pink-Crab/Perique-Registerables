<?php

declare(strict_types=1);
/**
 * Tests registering ajax with scripts.
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables\Tests\Fixtures\Ajax;

use PinkCrab\Enqueue\Enqueue;
use PinkCrab\Registerables\Ajax;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Ajax_With_Scripts extends Ajax {

	protected $nonce_handle = 'ajax_with_scripts';
	protected $action       = 'ajax_with_scripts';

	public $_conditional_value = true;

	/**
	 * Fires before register, can be used to do last min changes.
	 *
	 * @return void
	 */
	public function set_up(): void {
		// Remote JS File.
		$this->scripts->push(
			Enqueue::script( 'ajax_with_scripts_one' )
				->src( 'http://www.url.tld/file.js' )
				->deps( 'jquery' )
				->ver( '1.2.3' )
				->footer()
		);
		// Local JS file.
		$this->scripts->push(
			Enqueue::script( 'ajax_with_scripts_two' )
				->src( __DIR__ . '/file.js' )
				->deps( 'angular' )
				->ver( '0.1.2' )
		);

	}

	/**
	 * Should the scripts be loaded.
	 *
	 * @return boolean
	 */
	public function conditional(): bool {
		return $this->_conditional_value;
	}

	/**
	 * Handles the callback.
	 *
	 * @param ServerRequestInterface $request
	 * @return ServerRequestInterface
	 */
	public function callback( ResponseInterface $response ): ResponseInterface {
		// Does nothing here
	}
}

class Get_Users_Favourites extends Ajax {

	// Nonce and action
	protected $nonce_handle = 'prefix_get_user_favourites';
	protected $action       = 'prefix_get_user_favourites';

	/**
	 * @var Favourites_Repository
	 */
	protected $favourites;

	public function __construct(
		ServerRequestInterface $request,
		Favourites_Repository $favourites
	) {
		parent::__construct( $request );
		$this->wpdb = $wpdb;
	}

	/**
	 * Fires before register, can be used to do last min changes.
	 *
	 * @return void
	 */
	public function set_up(): void {
		$this->scripts->push(
			Enqueue::script( 'user_favourites' )
				->src( __DIR__ . '/user_favourites.js' )
				->deps( 'jquery' )
				->ver( '2.1.8' )
		);
	}

	/**
	 * @return boolean
	 */
	public function conditional(): bool {
		return is_singular( 'some_cpt' );
	}

	/**
	 * @param ServerRequestInterface $request
	 * @return ServerRequestInterface
	 */
	public function callback( ResponseInterface $response ): ResponseInterface {
		// Get the payload from the request.
		$request = $this->request->getQueryParams();
		$user    = (int) $request['user_id'];

		// Do whatever is needed.
		$favourites = $this->favourites->get_favourites( $user ) ?? [];
		$grouped_by_type = $this->favourites->get_groups($user ) ?? [];

		// Populate our response.
		return $response->withBody(
			HTTP_Helper::stream_from_scala(
				array(
					'favourites' => $favourites,
					'count' => count($favourites),
					'groups' => $grouped_by_type
				)
			)
		);
	}
}
