<?php

declare(strict_types=1);

/**
 * UNIT tests for the Meta Box Validator
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables\Tests\Unit\Validator;

use PHPUnit\Framework\TestCase;
use PinkCrab\Registerables\Meta_Box;
use PinkCrab\Registerables\Validator\Meta_Box_Validator;
use PinkCrab\Registerables\Registration_Middleware\Registerable;
use PinkCrab\Registerables\Tests\Fixtures\Taxonomies\Basic_Hierarchical_Taxonomy;

class Test_Meta_Box_Validator extends TestCase {

	/** @testdox Ensure the validate() method always returns false, as its not used here! */
	public function test_validate_method_always_returns_false(): void {
		$validator = new Meta_Box_Validator();
		$this->assertFalse( $validator->validate( $this->createMock( Registerable::class ) ) );
	}

	/** @testdox Attempting to pass a none object to verify should add an error to the stack */
	public function test_only_verifies_valid_type(): void {
		$validator = new Meta_Box_Validator();
		$result    = $validator->verify_meta_box( null );
		$this->assertFalse( $result );
		$this->assertTrue( $validator->has_errors() );
		$this->assertContains( 'NULL is not a valid Meta Box Model', $validator->get_errors() );
	}

	/** @testdox Attempting to pass any object type other than Meta_Data should add an error to the stack. */
	public function test_only_verifies_if_a_meta_box_model(): void {
		$validator = new Meta_Box_Validator();
		$result    = $validator->verify_meta_box( new Basic_Hierarchical_Taxonomy );
		$this->assertFalse( $result );
		$this->assertTrue( $validator->has_errors() );
		$this->assertContains( Basic_Hierarchical_Taxonomy::class . ' is not a valid Meta Box Model', $validator->get_errors() );
	}

	/** @testdox Any metabox without a label should fail validation with an error added to the stack */
	public function test_meta_box_without_label_fails_verification(): void {
		$validator = new Meta_Box_Validator();
		$result    = $validator->verify_meta_box( new Meta_Box( 'no label' ) );
		$this->assertFalse( $result );
		$this->assertTrue( $validator->has_errors() );
		$this->assertContains( 'label is not set on ' . Meta_Box::class . ' Meta Box Model', $validator->get_errors() );
	}

	/** @testdox A meta box should have a valid view method. This can either be a actual callable or have a valid template path (none empty string) */
	public function test_check_view_errors(): void {
		$validator       = new Meta_Box_Validator();
		$meta_box        = new Meta_Box( 'fails' );
		$meta_box->label = 'FAILS';

		// Fails if not a callable and no template.
		$result = $validator->verify_meta_box( $meta_box );
		$this->assertFalse( $result );
		$this->assertTrue( $validator->has_errors() );
		$this->assertContains( Meta_Box::class . ' doesn\'t have a valid view defined.', $validator->get_errors() );
		$validator->reset_errors();

		// Fails if not a callable and the template path isnt a string.
		$meta_box->view_template = array( 'not a string' );
		$result                  = $validator->verify_meta_box( $meta_box );
		$this->assertFalse( $result );
		$this->assertTrue( $validator->has_errors() );
		$this->assertContains( Meta_Box::class . ' doesn\'t have a valid view defined.', $validator->get_errors() );
		$validator->reset_errors();

		// Fails if not a callable and the template path is a string but empty.
		$meta_box->view_template = '';
		$result                  = $validator->verify_meta_box( $meta_box );
		$this->assertFalse( $result );
		$this->assertTrue( $validator->has_errors() );
		$this->assertContains( Meta_Box::class . ' doesn\'t have a valid view defined.', $validator->get_errors() );
		$validator->reset_errors();
	}

    /** @testdox A metabox with either a valid callable for the view or a template path, should pass validation. */
	public function test_allows_valid_view_processes(): void {
		$validator       = new Meta_Box_Validator();
		$meta_box        = new Meta_Box( 'passes' );
		$meta_box->label = 'PASS';

		// With a callable
		$meta_box->view = function(){};
		$result         = $validator->verify_meta_box( $meta_box );
		$this->assertTrue( $result );
		$this->assertFalse( $validator->has_errors() );
		$validator->reset_errors();

        // With a view template
		$meta_box->view = null;
        $meta_box->view_template = 'path';
		$result         = $validator->verify_meta_box( $meta_box );
		$this->assertTrue( $result );
		$this->assertFalse( $validator->has_errors() );
		$validator->reset_errors();
	}
}
