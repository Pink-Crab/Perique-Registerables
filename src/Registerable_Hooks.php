<?php

declare(strict_types=1);

/**
 * All hooks for Registerables
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

class Registerable_Hooks {

	/**
	 * The prefix used on all hooks.
	 */
	private const HOOK_PREFIX = 'PinkCrab/Registerable/';

	/**
	 * Filter handle for post type args
	 */
	public const POST_TYPE_ARGS = self::HOOK_PREFIX . 'post_type_args';

	/**
	 * Filter handle for post type labels
	 */
	public const POST_TYPE_LABELS = self::HOOK_PREFIX . 'post_type_labels';

	/**
	 * Filter handle for post type args
	 */
	public const TAXONOMY_ARGS = self::HOOK_PREFIX . 'taxonomy_args';

	/**
	 * Filter handle for post type labels
	 */
	public const TAXONOMY_LABELS = self::HOOK_PREFIX . 'taxonomy_labels';
}
