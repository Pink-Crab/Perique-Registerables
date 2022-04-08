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
use PinkCrab\Registerables\Shared_Meta_Box_Controller;
use PinkCrab\Registerables\Registrar\Meta_Box_Registrar;
use PinkCrab\Registerables\Registrar\Meta_Data_Registrar;
use PinkCrab\Registerables\Registration_Middleware\Registerable;

class Shared_Meta_Box_Registrar implements Registrar {

	/**
	 * The Meta Box Registrar
	 *
	 * @var Meta_Box_Registrar
	 */
	protected $meta_box_registrar;

	/**
	 * The Meta Data Registrar
	 *
	 * @var Meta_Data_Registrar
	 */
	protected $meta_data_registrar;

	public function __construct(
		Meta_Box_Registrar $meta_box_registrar,
		Meta_Data_Registrar $meta_data_registrar
	) {
		$this->meta_box_registrar  = $meta_box_registrar;
		$this->meta_data_registrar = $meta_data_registrar;
	}

	/**
	 * Used to register a registerable
	 *
	 * @param \PinkCrab\Registerables\Registration_Middleware\Registerable $registerable
	 * @return void
	 */
	public function register( Registerable $registerable ): void {
		if ( ! is_a( $registerable, Shared_Meta_Box_Controller::class ) ) {
			return;
		}

		/** @var Shared_Meta_Box_Controller $registerable, Validation call below catches no Shared_Meta_Box_Controller Registerables */

		// Get the meta box and meta data.
		$meta_box  = $registerable->meta_box();
		$meta_data = $registerable->meta_data( array() );

		// Register the meta box.
		$this->meta_box_registrar->register( $meta_box );

		// Register all meta data.
		foreach ( $this->filter_meta_data( $meta_data ) as $meta_field ) {
			// Register meta data for each post type.
			foreach ( $meta_box->screen as $post_type ) {
				$this->meta_data_registrar->register_for_post_type( $meta_field, $post_type );
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
