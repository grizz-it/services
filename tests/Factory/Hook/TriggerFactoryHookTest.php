<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Tests\Factory\Hook;

use PHPUnit\Framework\TestCase;
use GrizzIt\Services\Factory\Hook\TriggerFactoryHook;
use GrizzIt\Services\Common\Registry\ServiceRegistryInterface;

/**
 * @coversDefaultClass GrizzIt\Services\Factory\Hook\TriggerFactoryHook
 */
class TriggerFactoryHookTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::preCreate
     * @covers ::postCreate
     */
    public function testComponent(): void
    {
        $serviceRegistry = $this->createMock(ServiceRegistryInterface::class);
        $subject = new TriggerFactoryHook($serviceRegistry);
        $invoked = [];
        $create = (
            function (string $service) use (&$invoked): string {
                $invoked[] = $service;
                return $service;
            }
        );

        $serviceRegistry->expects(static::once())
            ->method('exists')
            ->with('triggers._trigger.services.my.service')
            ->willReturn(true);

        $serviceRegistry->expects(static::once())
            ->method('getDefinitionByKey')
            ->with('triggers._trigger.services.my.service')
            ->willReturn([
                'services.foo',
                'services.bar',
                'services.baz'
            ]);

        $this->assertEquals(
            [
                'services.my.service',
                ['definition' => []]
            ],
            $subject->preCreate(
                'services.my.service',
                ['definition' => []],
                $create
            )
        );

        $this->assertEquals(
            'return',
            $subject->postCreate(
                'services.my.service',
                ['definition' => []],
                'return',
                $create
            )
        );

        $this->assertEquals(
            [
                'triggers.services.foo',
                'triggers.services.bar',
                'triggers.services.baz'
            ],
            $invoked
        );
    }
}
