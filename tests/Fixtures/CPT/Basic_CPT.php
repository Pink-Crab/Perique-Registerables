<?php

declare(strict_types=1);
/**
 * Basic CPT Mock Object
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables\Tests\Fixtures\CPT;

use PinkCrab\Registerables\Post_Type;

class Basic_CPT extends Post_Type {

	public string $key          = 'basic_cpt';
	public string $singular     = 'Basic';
	public string $plural       = 'Basics';
	public ?bool $gutenberg    = true;
	public ?bool $map_meta_cap = true;
	public array $capabilities = array(
		'edit_published_posts'   => 'edit_basic',
	);
}
