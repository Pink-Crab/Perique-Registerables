<?php

declare(strict_types=1);

/**
 * Registration Registrar for Shared Meta Boxes.
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
 * @since 0.7.0
 */

namespace PinkCrab\Registerables\Registrar;

use Exception;
use PinkCrab\Registerables\Meta_Data;
use PinkCrab\Registerables\Registrar\Registrar;
use PinkCrab\Registerables\Registrar\Meta_Data_Registrar;
use PinkCrab\Registerables\Module\Middleware\Registerable;
use PinkCrab\Registerables\Additional_Meta_Data_Controller;
use PinkCrab\Registerables\Tests\Fixtures\Additional_Meta_Data;

class Additional_Meta_Data_Registrar implements Registrar {

	protected Meta_Data_Registrar $meta_data_registrar;

	public function __construct( Meta_Data_Registrar $meta_data_registrar ) {
		$this->meta_data_registrar = $meta_data_registrar;
	}

	/**
	 * Used to register a registerable
	 *
	 * @param \PinkCrab\Registerables\Module\Middleware\Registerable $registerable
	 * @return void
	 * @throws Exception If either post or term meta and the post type or taxonomy are not registered.
	 * @throws Exception If a none Additional_Meta_Data_Controller registerable is attempted to be registered.
	 * @throws Exception If a meta type which is not POST, USER, TERM or COMMENT is attempted to be registered.
	 */
	public function register( Registerable $registerable ): void {
		if ( ! is_a( $registerable, Additional_Meta_Data_Controller::class ) ) {
			throw new Exception( 'Registerable must be an instance of Additional_Meta_Data_Controller' );
		}

		/** @var Additional_Meta_Data $registerable, Validation call below catches no Additional_Meta_Data Registerables */
		$meta_data = $this->filter_meta_data( $registerable->meta_data( array() ) );

		// Iterate through all meta data and register them.
		foreach ( $meta_data as $meta_data_item ) {
			switch ( $meta_data_item->get_meta_type() ) {
				case 'post':
					// Throw if post type not defined.
					if ( null === $meta_data_item->get_subtype() ) {
						throw new Exception(
							sprintf(
								'A post type must be defined when attempting to register post meta with meta key : %s',
								$meta_data_item->get_meta_key()
							)
						);
					}

					$this->meta_data_registrar->register_for_post_type(
						$meta_data_item,
						$meta_data_item->get_subtype()
					);
					break;

				case 'term':
					// Throw if Taxonomy not defined.
					if ( null === $meta_data_item->get_subtype() ) {
						throw new Exception(
							sprintf(
								'A taxonomy must be defined when attempting to register tern meta with meta key : %s',
								$meta_data_item->get_meta_key()
							)
						);
					}

					$this->meta_data_registrar->register_for_term(
						$meta_data_item,
						$meta_data_item->get_subtype()
					);
					break;

				case 'user':
					$this->meta_data_registrar->register_for_user(
						$meta_data_item
					);
					break;

				case 'comment':
					$this->meta_data_registrar->register_for_comment(
						$meta_data_item
					);
					break;

				default:
					throw new Exception( 'Unexpected meta type' );
			}
		}
	}

	/**
	 * Filters all non meta data from array.
	 *
	 * @param mixed[] $meta_data
	 * @return Meta_Data[]
	 */
	protected function filter_meta_data( array $meta_data ): array {
		return array_filter(
			$meta_data,
			function( $e ) {
				return is_a( $e, Meta_Data::class );
			}
		);
	}
}
