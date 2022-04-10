<?php

declare(strict_types=1);

/**
 * [Forced] Application Tests for the Meta Data Registrar
 *
 * Tests the fallback, generate callback for GET/VIEW and SET/UPDATE methods.
 *
 * @since 0.7.2
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables\Tests\Application;

use WP_UnitTestCase;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Registerables\Meta_Data;

class Test_Meta_Rest_Field extends WP_UnitTestCase {

	/** @testdox Whenever a meta field is created, a default callback should be created for getting meta, based on the meta type. */
	public function test_create_get_method(): void {
		$registrar = new \PinkCrab\Registerables\Registrar\Meta_Data_Registrar();

		// Test post meta.
		$post_meta    = ( new Meta_Data( 'some_key' ) )->post_type( 'post' );
		$post_meta_cb = Objects::invoke_method( $registrar, 'create_rest_get_method', array( $post_meta ) );
		update_post_meta( 12, 'some_key', 'some_value' );
		$this->assertEquals( 'some_value', $post_meta_cb( array( 'id' => 12 ) ) );

		// Test user meta.
		$user_meta    = ( new Meta_Data( 'some_key_user' ) )->meta_type( 'user' );
		$user_meta_cb = Objects::invoke_method( $registrar, 'create_rest_get_method', array( $user_meta ) );
		update_user_meta( 5, 'some_key_user', 'some_value' );
		$this->assertEquals( 'some_value', $user_meta_cb( array( 'id' => 5 ) ) );

		// Test term meta.
		$term_meta    = ( new Meta_Data( 'some_key_term' ) )->taxonomy( 'category' );
		$term_meta_cb = Objects::invoke_method( $registrar, 'create_rest_get_method', array( $term_meta ) );
		wp_set_object_terms( 7, 'category', 'category' );
		update_term_meta( 7, 'some_key_term', 'some_value' );
		$this->assertEquals( 'some_value', $term_meta_cb( array( 'id' => 7 ) ) );

		// Test comment meta.
		$comment_meta    = ( new Meta_Data( 'some_key_comment' ) )->meta_type( 'comment' );
		$comment_meta_cb = Objects::invoke_method( $registrar, 'create_rest_get_method', array( $comment_meta ) );
		wp_insert_comment(
			array(
				'comment_ID'      => 6,
				'comment_content' => 'some_value',
			)
		);
		update_comment_meta( 6, 'some_key_comment', 'some_value' );
		$this->assertEquals( 'some_value', $comment_meta_cb( array( 'id' => 6 ) ) );

		// Test returns null if meta type is not set.
		$meta_fail    = ( new Meta_Data( 'some_key' ) )->meta_type( 'foo' );
		$meta_fail_cb = Objects::invoke_method( $registrar, 'create_rest_get_method', array( $meta_fail ) );
		$this->assertNull( $meta_fail_cb( array( 'id' => 12 ) ) );

	}

	/** @testdox Whenever a meta field is created, a default callback should be created for setting meta, based on the meta type. */
	public function test_create_set_method(): void {
		$registrar = new \PinkCrab\Registerables\Registrar\Meta_Data_Registrar();

		// Test post meta.
		$post_meta    = ( new Meta_Data( 'some_key' ) )->post_type( 'post' );
		$post_meta_cb = Objects::invoke_method( $registrar, 'create_rest_update_method', array( $post_meta ) );
		$post_meta_cb( 'some_value', (object) array( 'ID' => 12 ) );
		$this->assertEquals( 'some_value', get_post_meta( 12, 'some_key', true ) );

		// Test user meta.
		$user_meta    = ( new Meta_Data( 'some_key_user' ) )->meta_type( 'user' );
		$user_meta_cb = Objects::invoke_method( $registrar, 'create_rest_update_method', array( $user_meta ) );
		$user_meta_cb( 'some_value', (object) array( 'ID' => 5 ) );
		$this->assertEquals( 'some_value', get_user_meta( 5, 'some_key_user', true ) );

		// Test term meta.
		$term_meta    = ( new Meta_Data( 'some_key_term' ) )->taxonomy( 'category' );
		$term_meta_cb = Objects::invoke_method( $registrar, 'create_rest_update_method', array( $term_meta ) );
		$term_meta_cb( 'some_value', (object) array( 'term_id' => 7 ) );
		$this->assertEquals( 'some_value', get_term_meta( 7, 'some_key_term', true ) );

		// Test comment meta.
		$comment_meta    = ( new Meta_Data( 'some_key_comment' ) )->meta_type( 'comment' );
		$comment_meta_cb = Objects::invoke_method( $registrar, 'create_rest_update_method', array( $comment_meta ) );
		$comment_meta_cb( 'some_value', (object) array( 'comment_ID' => 6 ) );
		$this->assertEquals( 'some_value', get_comment_meta( 6, 'some_key_comment', true ) );

		// Test returns null if meta type is not set.
		$meta_fail    = ( new Meta_Data( 'some_key' ) )->meta_type( 'foo' );
		$meta_fail_cb = Objects::invoke_method( $registrar, 'create_rest_update_method', array( $meta_fail ) );
		// $this->assertNull( $meta_fail_cb(['id' => 12]) );

	}
}
