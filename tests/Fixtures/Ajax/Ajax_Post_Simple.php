<?php

declare(strict_types=1);
/**
 * Basic Ajax Call using Get with none json headers
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Modules\Registerables
 */

namespace PinkCrab\Core\Tests\Fixtures\Mock_Objects;

use PinkCrab\Modules\Registerables\Ajax;
use PC_Vendor\GuzzleHttp\Psr7\LazyOpenStream;
use PC_Vendor\Psr\Http\Message\ServerRequestInterface;

class Ajax_Post_Simple extends Ajax {

	protected $nonce_handle = 'ajax_post_simple';
	protected $action       = 'ajax_post_simple';

	/**
	 * Handles the callback.
	 *
	 * @param PC_Vendor\Psr\Http\Message\ServerRequestInterface $request
	 * @return void
	 */
	public function callback( ServerRequestInterface $request ): void {
		wp_send_json_success( $request->getParsedBody() );
	}
}
