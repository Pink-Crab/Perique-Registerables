<?php

declare(strict_types=1);

/**
 * Unit tests for the taxonomy registrar
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables\Tests\Unit\Registrar;

use Exception;
use PHPUnit\Framework\TestCase;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Registerables\Taxonomy;
use PinkCrab\Registerables\Meta_Data;
use PinkCrab\Registerables\Registrar\Taxonomy_Registrar;
use PinkCrab\Registerables\Validator\Taxonomy_Validator;
use PinkCrab\Registerables\Registrar\Meta_Data_Registrar;
use PinkCrab\Registerables\Module\Middleware\Registerable;
use PinkCrab\Registerables\Tests\Fixtures\Taxonomies\Basic_Hierarchical_Taxonomy;

class Test_Taxonomy_Registrar extends TestCase {

	/** @testdox If a taxonomy fails validation and exception should be thrown */
	public function test_fails_validation_if_none_post_type_registerable(): void {

		$validator = $this->createMock( Taxonomy_Validator::class );
		$validator->method( 'validate' )->willReturn( false );
		$validator->method( 'get_errors' )->willReturn( array( 'error1', 'error2' ) );
		$md_registrar = $this->createMock( Meta_Data_Registrar::class );

		$registrar = new Taxonomy_Registrar( $validator, $md_registrar );

		$taxonomy = $this->createMock( Registerable::class );

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Failed validating taxonomy model(' . get_class( $taxonomy ) . ') with errors: error1, error2' );
		$registrar->register( $taxonomy );
	}

	/** @testdox If a WP_Error class is returned when registering the taxonomy, this should be translated into an exception */
	public function test_throws_exception_if_register_post_type_returns_wp_error(): void {

		$validator          = $this->createMock( Taxonomy_Validator::class );
		$md_registrar = $this->createMock( Meta_Data_Registrar::class );
		$validator->method( 'validate' )->willReturn( true );

		$registrar = new Taxonomy_Registrar( $validator, $md_registrar );

		$post_type = new class() extends Taxonomy {
			// Name is capped between 1 and 20
			public string $slug     = '0123456789012345678901234567890123456789';
			public ?string $singular = '0123456789012345678901234567890123456789';
			public string $plural   = '0123456789012345678901234567890123456789';
		};

		$this->expectException( \Exception::class );
		// Based on the phpunit version.
		if ( \method_exists( $this, 'expectExceptionMessageMatches' ) ) {
			$this->expectExceptionMessageMatches( '#Failed to register 0123456789012345678901234567890123456789 taxonomy (.*)$#' );
		} else {
			$this->expectExceptionMessageRegExp( '#Failed to register 0123456789012345678901234567890123456789 taxonomy (.*)$#' );
		}
		$registrar->register( $post_type );
	}

	/** @testdox When compiled, all args should be set based on the taxonomy defined. */
	public function test_can_compile_args(): void {
		$taxonomy  = new Basic_Hierarchical_Taxonomy();
		$validator = $this->createMock( Taxonomy_Validator::class );
		$validator->method( 'validate' )->willReturn( true );
		$md_registrar = $this->createMock( Meta_Data_Registrar::class );

		$registrar = new Taxonomy_Registrar( $validator, $md_registrar );

		// Get the args array for registering taxonomy.
		$args = Objects::invoke_method( $registrar, 'compile_args', array( $taxonomy ) );

		// Check args.
		$expected = array(
			'label'                 => 'Basic Hier Taxonomies',
			'description'           => 'The Basic Hier Taxonomy.',
			'publicly_queryable'    => true,
			'show_ui'               => false,
			'show_in_menu'          => false,
			'show_in_nav_menus'     => false,
			'show_tagcloud'         => false,
			'show_in_quick_edit'    => false,
			'show_admin_column'     => false,
			'query_var'             => false,
			'update_count_callback' => '_update_post_term_count',
			'show_in_rest'          => false,
			'rest_base'             => 'basic_hier_tax',
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'default_term'          => null,
		);

		foreach ( $expected as $key => $value ) {
			$this->assertEquals( $value, $args[ $key ] );
		}

		// Check labels
		$expected = array(
			'name'              => 'Basic Hier Taxonomies',
			'singular_name'     => 'Basic Hier Taxonomy',
			'search_items'      => 'Search Basic Hier Taxonomies',
			'all_items'         => 'All Basic Hier Taxonomies',
			'parent_item'       => 'Parent Basic Hier Taxonomy',
			'parent_item_colon' => 'Parent Basic Hier Taxonomy:',
			'edit_item'         => 'Edit Basic Hier Taxonomy',
			'update_item'       => 'Update Basic Hier Taxonomy',
			'add_new_item'      => 'Add New Basic Hier Taxonomy',
			'new_item_name'     => 'New Basic Hier Taxonomy',
			'view_item'         => 'View Basic Hier Taxonomy',
			'menu_name'         => 'Basic Hier Taxonomies',
			'popular_items'     => 'Popular Basic Hier Taxonomies',
			'back_to_items'     => '← Back to Basic Hier Taxonomies',
		);

		foreach ( $expected as $key => $value ) {
			$this->assertEquals( $value, $args['labels'][ $key ] );
		}
	}

	/** @testdox When registering the taxonomy the internal filter_args and filter_labels methods should allow to overwrite values */
	public function test_uses_post_type_label_filters() {
		$taxonomy  = new class() extends Taxonomy{
			public function filter_labels( array $e ): array {
				  return array( 'foo' => 'bar' );
			}
			public function filter_args( array $e ): array {
				  return array(
					  'labels' => $e['labels'],
					  'bar'    => 'foo',
				  );
			}
		};
		$validator = $this->createMock( Taxonomy_Validator::class );
		$validator->method( 'validate' )->willReturn( true );
		$md_registrar = $this->createMock( Meta_Data_Registrar::class );

		$registrar = new Taxonomy_Registrar( $validator, $md_registrar );

		// Get the args array for registering taxonomy.
		$args = Objects::invoke_method( $registrar, 'compile_args', array( $taxonomy ) );

		$this->assertArrayHasKey( 'bar', $args );
		$this->assertEquals( 'foo', $args['bar'] );

		$this->assertArrayHasKey( 'foo', $args['labels'] );
		$this->assertEquals( 'bar', $args['labels']['foo'] );
	}


	/** @testdox When registering meta data for a term, any exceptions throws should be caught and rethrown */
	public function test_throws_upper_exception_if_exception_thrown_during_meta_data_registration_term(): void {
		// Mock Tax which uses the failing meta data.
		$tax = new class() extends Basic_Hierarchical_Taxonomy  {

			// This is a method that is written to populate an array with objects
			public function meta_data( array $collection ): array {
				// This mock object, is designed to throw exception if called.
				$collection[] = new class('test') extends Meta_Data{
					public function get_meta_type(): string {
						throw new Exception( 'MOCK EXCEPTION' );
						return $this->meta_type;
					}
				};

				return $collection;
			}
		};

		$validator = $this->createMock( Taxonomy_Validator::class );
		$validator->method( 'validate' )->willReturn( true );
		$md_registrar = $this->createMock( Meta_Data_Registrar::class );
		$md_registrar->method( 'register_for_term' )->willThrowException( new Exception( 'MOCK EXCEPTION' ) );

		$registrar = new Taxonomy_Registrar( $validator, $md_registrar );

		$this->expectExceptionMessage( 'MOCK EXCEPTION' );
		$this->expectException( \Exception::class );

		Objects::invoke_method( $registrar, 'register_meta_data', array( $tax ) );
	}

}
