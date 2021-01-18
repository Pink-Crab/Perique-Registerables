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

use PinkCrab\Registerables\Ajax;
use Psr\Http\Message\ServerRequestInterface;

class Ajax_Get extends Ajax {

	protected $nonce_handle = 'basic_ajax_get';
	protected $action       = 'basic_ajax_get';

	/**
	 * Handles the callback.
	 *
	 * @param ServerRequestInterface $request
	 * @return void
	 */
	public function callback( ServerRequestInterface $request ): void {
		print( $request->getQueryParams()['ajax_get_data'] );
	}
}
