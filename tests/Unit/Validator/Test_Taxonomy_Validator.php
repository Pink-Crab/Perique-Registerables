<?php

declare(strict_types=1);

/**
 * UNIT tests for the taxonomy Validator
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables\Tests\Unit\Validator;

use PHPUnit\Framework\TestCase;
use PinkCrab\Registerables\Validator\Taxonomy_Validator;
use PinkCrab\Registerables\Registration_Middleware\Registerable;
use PinkCrab\Registerables\Tests\Fixtures\Taxonomies\Basic_Tag_Taxonomy;

class Test_Taxonomy_Validator extends TestCase {

	/** @testdox When validating a taxonomy, errors should be generated if attempting with none taxonomy Registerable. */
	public function test_generates_errors_if_not_post_type(): void {
		$validator = new Taxonomy_Validator();
		$mock      = $this->createMock( Registerable::class );
		$result    = $validator->validate( $mock );

		// Should return false for error.
		$this->assertFalse( $result );

		// Check created errors in log
		$this->assertTrue( $validator->has_errors() );
		$this->assertContains( get_class( $mock ) . ' is not a valid Taxonomy Model', $validator->get_errors() );
	}

	/** @testdox When validating a taxonomy, key, singular and plural must be string and not empty. If any are, then validation should fail with error messages. */
	public function test_fails_if_required_fields_are_missing(): void {
		$validator = new Taxonomy_Validator();
		$tax       = new Basic_Tag_Taxonomy;

		// Key empty string
		$tax->slug = '';
		$result   = $validator->validate( $tax );
		$this->assertFalse( $result );
		$this->assertContains( 'slug is not set on PinkCrab\Registerables\Tests\Fixtures\Taxonomies\Basic_Tag_Taxonomy Taxonomy Model', $validator->get_errors() );
		$validator->reset_errors();
		$tax = new Basic_Tag_Taxonomy;

		// Key none string.
		$tax->slug = null;
		$result   = $validator->validate( $tax );
		$this->assertFalse( $result );
		$this->assertContains( 'slug is not set on PinkCrab\Registerables\Tests\Fixtures\Taxonomies\Basic_Tag_Taxonomy Taxonomy Model', $validator->get_errors() );
		$validator->reset_errors();
		$tax = new Basic_Tag_Taxonomy;

		// singular empty string
		$tax->singular = '';
		$result        = $validator->validate( $tax );
		$this->assertFalse( $result );
		$this->assertContains( 'singular is not set on PinkCrab\Registerables\Tests\Fixtures\Taxonomies\Basic_Tag_Taxonomy Taxonomy Model', $validator->get_errors() );
		$validator->reset_errors();
		$tax = new Basic_Tag_Taxonomy;

		// singular none string.
		$tax->singular = array( null );
		$result        = $validator->validate( $tax );
		$this->assertFalse( $result );
		$this->assertContains( 'singular is not set on PinkCrab\Registerables\Tests\Fixtures\Taxonomies\Basic_Tag_Taxonomy Taxonomy Model', $validator->get_errors() );
		$validator->reset_errors();
		$tax = new Basic_Tag_Taxonomy;

		// plural empty string
		$tax->plural = '';
		$result      = $validator->validate( $tax );
		$this->assertFalse( $result );
		$this->assertContains( 'plural is not set on PinkCrab\Registerables\Tests\Fixtures\Taxonomies\Basic_Tag_Taxonomy Taxonomy Model', $validator->get_errors() );
		$validator->reset_errors();
		$tax = new Basic_Tag_Taxonomy;

		// plural none string.
		$tax->plural = (object) array( 'r' => null );
		$result      = $validator->validate( $tax );
		$this->assertFalse( $result );
		$this->assertContains( 'plural is not set on PinkCrab\Registerables\Tests\Fixtures\Taxonomies\Basic_Tag_Taxonomy Taxonomy Model', $validator->get_errors() );
		$validator->reset_errors();
		$tax = new Basic_Tag_Taxonomy;

	}
}
