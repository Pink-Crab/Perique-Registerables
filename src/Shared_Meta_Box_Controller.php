<?php

declare(strict_types=1);

/**
 * Shared Meta Box Controller for registering meta boxes and meta data
 * against multiple post types.
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

namespace PinkCrab\Registerables;

use PinkCrab\Registerables\Meta_Box;
use PinkCrab\Registerables\Module\Middleware\Registerable;

abstract class Shared_Meta_Box_Controller implements Registerable {

	/**
	 * The primary function which the meta boxes model is defined.
	 *
	 * @return Meta_Box
	 */
	abstract public function meta_box(): Meta_Box;

	/**
	 * Sets any meta data against the meta box.
	 *
	 * @param Meta_Data[] $meta_data
	 * @return Meta_Data[]
	 * @codeCoverageIgnore
	 */
	public function meta_data( array $meta_data ): array {
		return $meta_data;
	}
}
