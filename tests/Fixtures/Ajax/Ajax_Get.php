<?php

declare(strict_types=1);
/**
 * Basic Ajax Call using GET
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables\Tests\Fixtures\Ajax;

use PinkCrab\HTTP\HTTP;
use PinkCrab\Registerables\Ajax;
use Psr\Http\Message\ResponseInterface;

class Ajax_Get extends Ajax {

	// protected $nonce_handle = 'basic_ajax_get';
	protected $action       = 'basic_ajax_get';

	/**
	 * Handles the callback.
	 *
	 * @param ResponseInterface $request
	 * @return void
	 */
	public function callback( ResponseInterface $response ): ResponseInterface {
		$response_array = array( 'result' => $this->request->getQueryParams()['ajax_get_data'] );

		return $response->withBody(
			( new HTTP() )->create_stream_with_json( $response_array )
		);
	}
}
