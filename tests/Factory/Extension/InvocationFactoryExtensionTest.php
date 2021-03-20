<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Tests\Factory\Extension;

use PHPUnit\Framework\TestCase;
use GrizzIt\Storage\Component\ObjectStorage;
use GrizzIt\ObjectFactory\Component\Reflector\MethodReflector;
use GrizzIt\Services\Factory\Extension\InvocationFactoryExtension;

/**
 * @coversDefaultClass GrizzIt\Services\Factory\Extension\InvocationFactoryExtension
 */
class InvocationFactoryExtensionTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::create
     */
    public function testCreate(): void
    {
        $methodReflector = new MethodReflector(new ObjectStorage());
        $subject = new InvocationFactoryExtension($methodReflector);
        $create = (
            function (string $service): object {
                return new class {
                    public function get(string $bar = 'default'): string
                    {
                        return 'foo' . $bar;
                    }
                };
            }
        );

        $this->assertEquals(
            'foobaz',
            $subject->create(
                'invocations.foo.get',
                [
                    'service' => 'services.foo',
                    'method' => 'get',
                    'parameters' => [
                        'bar' => 'baz'
                    ]
                ],
                $create
            )
        );

        // Test cached result.
        $this->assertEquals(
            'foobaz',
            $subject->create(
                'invocations.foo.get',
                [
                    'service' => 'services.foo',
                    'method' => 'get',
                    'parameters' => [
                        'bar' => 'bar'
                    ]
                ],
                $create
            )
        );
    }
}
