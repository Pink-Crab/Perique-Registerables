<?php

declare(strict_types=1);
/**
 * Basic simple, Hierarchical taxonomy.
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables\Tests\Fixtures\Taxonomies;

use PinkCrab\Registerables\Taxonomy;

class Basic_Hierarchical_Taxonomy extends Taxonomy {
	public string $slug         = 'basic_hier_tax';
	public ?string $singular    = 'Basic Hier Taxonomy';
	public string $plural       = 'Basic Hier Taxonomies';
	public ?string $description = 'The Basic Hier Taxonomy.';
	public bool $hierarchical        = true;
	public array $object_type         = array( 'basic_cpt', 'page' );
	public bool $public              = false;
	public bool $show_ui             = false;
	public bool $show_in_menu        = false;
	public bool $show_admin_column   = false;
	public bool $show_tagcloud       = false;
	public bool $show_in_quick_edit  = false;
}
