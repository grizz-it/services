<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Tests\Component\Registry;

use PHPUnit\Framework\TestCase;
use GrizzIt\Services\Component\Registry\ServiceRegistry;
use GrizzIt\Services\Exception\DefinitionNotFoundException;

/**
 * @coversDefaultClass GrizzIt\Services\Component\Registry\ServiceRegistry
 * @covers GrizzIt\Services\Exception\DefinitionNotFoundException
 */
class ServiceRegistryTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::getDefinitionByKey
     * @covers ::exists
     * @covers ::getKeys
     */
    public function testComponent(): void
    {
        $subject = new ServiceRegistry(
            [
                'services' => [
                    'my.service' => [
                        'class' => 'foo',
                        'parameters' => [
                            'bar' => 'baz'
                        ]
                    ]
                ]
            ]
        );

        $this->assertEquals(true, $subject->exists('services.my.service'));
        $this->assertEquals(
            [
                'class' => 'foo',
                'parameters' => [
                    'bar' => 'baz'
                ]
            ],
            $subject->getDefinitionByKey('services.my.service')
        );
    }

    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::getDefinitionByKey
     * @covers ::exists
     * @covers ::getKeys
     */
    public function testComponentFail(): void
    {
        $subject = new ServiceRegistry([]);
        $this->assertEquals(false, $subject->exists('services.my.service'));
        $this->expectException(DefinitionNotFoundException::class);
        $subject->getDefinitionByKey('services.my.service');
    }
}
