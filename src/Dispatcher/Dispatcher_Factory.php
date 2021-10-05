<?php

declare(strict_types=1);

/**
 * Factory for creating Dispatchers
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

namespace PinkCrab\Registerables\Dispatcher;

use Exception;
use PinkCrab\Registerables\Post_Type;
use PinkCrab\Registerables\Validator\Post_Type_Validator;
use PinkCrab\Registerables\Dispatcher\Post_Type_Registrar;
use PinkCrab\Registerables\Registration_Middleware\Registerable;

class Dispatcher_Factory {

	/**
	 * Returns an instance of the factory.
	 *
	 * @return self
	 */
	public static function new(): self {
		return new self();
	}

	/**
	 * Creates the dispatcher based on the registerable passed.
	 *
	 * @param \PinkCrab\Registerables\Registration_Middleware\Registerable $registerable
	 * @return \PinkCrab\Registerables\Dispatcher\Post_Type_Registrar
	 * @throws Exception If not valid registerable type passed.
	 */
	public function create_dispatcher( Registerable $registerable ): Registrar {
		switch ( true ) {
			case is_a( $registerable, Post_Type::class ):
				return $this->post_type_registrar();

			default:
				throw new Exception( 'Invalid registerable type (no dispatcher exists)' );
		}
	}

	/**
	 * Create post type dispatcher.
	 *
	 * @return \PinkCrab\Registerables\Dispatcher\Post_Type_Registrar
	 */
	public function post_type_registrar(): Post_Type_Registrar {
		return new Post_Type_Registrar( new Post_Type_Validator() );
	}
}
