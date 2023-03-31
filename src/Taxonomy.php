<?php

declare(strict_types=1);

/**
 * An abstract class for registering custom taxonomies.
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

use PinkCrab\Registerables\Module\Middleware\Registerable;

abstract class Taxonomy implements Registerable {

	/**
	 * The singular label
	 *
	 * @var string|null
	 * @required
	 */
	public ?string $singular = null;

	/**
	 * Plural label
	 *
	 * @var string
	 * @required
	 */
	public string $plural = '';

	/**
	 * Taxonomy slug
	 *
	 * @var string
	 * @required
	 */
	public string $slug = '';

	/**
	 * The taxonomies label.
	 * Uses plural if not set.
	 *
	 * @var string|null
	 */
	public ?string $label = null;

	/**
	 * The taxonomy description.
	 *
	 * @var string
	 */
	public ?string $description = null;

	/**
	 * Which post types should this taxonomy be applied to.
	 *
	 * @var string[]
	 */
	public array $object_type = array( 'post' );

	/**
	 * Should this taxonomy have a hierarchy
	 *
	 * @var bool
	 */
	public bool $hierarchical = false;

	/**
	 * Render WP_Admin UI
	 *
	 * @var bool
	 */
	public bool $show_ui = true;

	/**
	 * Show in WP_Admin menu list.
	 *
	 * @var bool
	 */
	public bool $show_in_menu = true;

	/**
	 * Show in post type list.
	 *
	 * @var bool
	 */
	public bool $show_admin_column = true;

	/**
	 * Include in the tag cloud.
	 *
	 * @var bool
	 */
	public bool $show_tagcloud = false;

	/**
	 * Inlcude in quick edit.
	 *
	 * @var bool
	 */
	public bool $show_in_quick_edit = true;

	/**
	 * Should terms remain in the order added
	 * if false will be alphabetical.
	 *
	 * @var bool
	 */
	public bool $sort = true;

	/**
	 * Render wp meta box.
	 *
	 * @var callable|null
	 */
	public $meta_box_cb;

	/**
	 * Include in rest
	 *
	 * @var bool
	 */
	public bool $show_in_rest = false;

	/**
	 * Base rest path.
	 * If not set, will use taxonomy slug
	 *
	 * @var string|null
	 */
	public ?string $rest_base = null;

	/**
	 * Rest base controller.
	 *
	 * @var string
	 */
	public $rest_controller_class = 'WP_REST_Terms_Controller';

	/**
	 * Is this Taxonomy to be used frontend wise
	 *
	 * @var bool
	 */
	public bool $public = true;

	/**
	 * Whether the taxonomy is publicly queryable.
	 *
	 * @var bool
	 */
	public bool $publicly_queryable = true;

	/**
	 * Define a custom query var, if false with use $this->slug
	 *
	 * @var bool|string
	 */
	public $query_var = false;

	/**
	 * Rewrite the peramlinks structure.
	 * If set to true will use the default of the slug.
	 *
	 * @var array<string, mixed>|bool
	 */
	public $rewrite = true;

	/**
	 * String of function name used for counting.
	 * If blank string will use the internal counting functions.
	 * Must be a string and not an inline callable.
	 *
	 * @var string|null
	 */
	public ?string $update_count_callback = null;

	/**
	 * Array of capabilities for the taxonomy
	 *
	 * @var array<string, mixed>|null
	 */
	public ?array $capabilities = null;

	/**
	 * Sets the default term for the taxonomy
	 *
	 * @var array<string, mixed>|null
	 */
	public ?array $default_term = null;

	/**
	 * Filters the labels through child class.
	 *
	 * @param array<string, mixed> $labels
	 * @return array<string, mixed>
	 */
	public function filter_labels( array $labels ): array {
		return $labels;
	}

	/**
	 * Filters the args used to register the taxonomy.
	 *
	 * @param array<string, mixed> $args
	 * @return array<string, mixed>
	 */
	public function filter_args( array $args ): array {
		return $args;
	}

	/**
	 * Allows for the setting of meta data specifically for this taxonomy.
	 *
	 * @param Meta_Data[] $collection
	 * @return Meta_Data[]
	 */
	public function meta_data( array $collection ): array {
		return $collection;
	}
}
