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

class Test_Meta_Box_Validator extends TestCase {

	/** @testdox Ensure the validate() method always returns false, as its not used here! */
	public function test_validate_method_always_returns_false(): void {
		$validator = new Meta_Box_Validator();
		$this->assertFalse( $validator->validate( $this->createMock( Registerable::class ) ) );
	}

    /** @testdox Attempting to pass a nonce object to verify should add an error to the stack */
    public function test_only_verifies_valid_object(): void
    {
        $validator = new Meta_Box_Validator();
        $result = $validator->verify_meta_box(null);
        $this->assertFalse($result);
        $this->assertTrue($validator->has_errors());
    }
}
