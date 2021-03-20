<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Tests\Component\Compiler\Extension;

use PHPUnit\Framework\TestCase;
use GrizzIt\Services\Component\Compiler\Extension\ServiceCompilerExtension;

/**
 * @coversDefaultClass GrizzIt\Services\Component\Compiler\Extension\ServiceCompilerExtension
 */
class ServiceCompilerExtensionTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::compile
     */
    public function testCompile(): void
    {
        $subject = new ServiceCompilerExtension();
        $this->assertEquals(
            [
                'services' => [
                    'my.service' => [
                        'class' => '\\GrizzIt\\Validator\\Component\\Logical\\AlwaysValidator',
                        'parameters' => [
                            'alwaysBool' => true
                        ]
                    ],
                    'my.abstract.service' => [
                        'abstract' => true,
                        'class' => '\\GrizzIt\\Validator\\Component\\Logical\\AlwaysValidator'
                    ]
                ]
            ],
            $subject->compile(
                [
                    'services' => [
                        'my.service' => [
                            'parent' => 'my.abstract.service',
                            'parameters' => [
                                'alwaysBool' => true
                            ]
                        ],
                        'my.abstract.service' => [
                            'abstract' => true,
                            'class' => '\\GrizzIt\\Validator\\Component\\Logical\\AlwaysValidator'
                        ]
                    ]
                ]
            )
        );
    }
}
