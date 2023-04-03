<?php

declare(strict_types=1);
/**
 * Basic simple, flat taxonomy.
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables\Tests\Fixtures\Taxonomies;

use PinkCrab\Registerables\Taxonomy;

class Basic_Tag_Taxonomy extends Taxonomy {
	public string $slug         = 'basic_tag_tax';
	public ?string $singular     = 'Basic Tag Taxonomy';
	public string $plural       = 'Basic Tag Taxonomies';
	public ?string $description  = 'The Basic Tag Taxonomy.';
	public bool $hierarchical = false;
	public array $object_type  = array( 'basic_cpt' );
	public ?array $capabilities = array(
		'manage_terms' => 'custom_terms',
		'delete_terms' => 'custom_delete',
	);
}
