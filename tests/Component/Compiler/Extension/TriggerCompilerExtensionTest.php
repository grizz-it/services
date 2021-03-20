<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Tests\Component\Compiler\Extension;

use PHPUnit\Framework\TestCase;
use GrizzIt\Services\Component\Compiler\Extension\TriggerCompilerExtension;

/**
 * @coversDefaultClass GrizzIt\Services\Component\Compiler\Extension\TriggerCompilerExtension
 */
class TriggerCompilerExtensionTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::compile
     */
    public function testCompile(): void
    {
        $subject = new TriggerCompilerExtension();
        $this->assertEquals(
            [
                'triggers' => [
                    'my.trigger' => [
                        'invocations.my.invocation'
                    ],
                    '_trigger.services.my.service' => [
                        'my.trigger'
                    ]
                ]
            ],
            $subject->compile(
                [
                    'triggers' => [
                        'my.trigger' => [
                            'service' => 'services.my.service'
                        ]
                    ],
                    'tags' => [
                        'my.tag' => [
                            'trigger' => 'triggers.my.trigger',
                            'service' => 'invocations.my.invocation'
                        ]
                    ]
                ]
            )
        );
    }
}
