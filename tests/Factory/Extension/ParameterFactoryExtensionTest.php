<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Tests\Factory\Extension;

use PHPUnit\Framework\TestCase;
use GrizzIt\Services\Factory\Extension\ParameterFactoryExtension;

/**
 * @coversDefaultClass GrizzIt\Services\Factory\Extension\ParameterFactoryExtension
 */
class ParameterFactoryExtensionTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::create
     */
    public function testCreate(): void
    {
        $subject = new ParameterFactoryExtension();
        $this->assertEquals(
            'foo',
            $subject->create(
                'parameters.foo',
                'foo',
                (
                    function (string $service): string {
                        return $service;
                    }
                )
            )
        );
    }
}
