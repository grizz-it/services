<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Tests\Factory\Extension;

use PHPUnit\Framework\TestCase;
use GrizzIt\Services\Factory\Extension\TriggerFactoryExtension;

/**
 * @coversDefaultClass GrizzIt\Services\Factory\Extension\TriggerFactoryExtension
 */
class TriggerFactoryExtensionTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::create
     */
    public function testCreate(): void
    {
        $subject = new TriggerFactoryExtension();
        $this->assertEquals(
            [
                'processed.services.my.service',
                'processed.invocations.my.invocations'
            ],
            $subject->create(
                'triggers.foo',
                [
                    'services.my.service',
                    'invocations.my.invocations'
                ],
                (
                    function (string $service): string {
                        return 'processed.' . $service;
                    }
                )
            )
        );
    }
}
