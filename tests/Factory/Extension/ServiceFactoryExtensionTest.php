<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Tests\Factory\Extension;

use stdClass;
use PHPUnit\Framework\TestCase;
use GrizzIt\ObjectFactory\Common\ObjectFactoryInterface;
use GrizzIt\Services\Exception\NonInstantiableServiceException;
use GrizzIt\Services\Factory\Extension\ServiceFactoryExtension;

/**
 * @coversDefaultClass GrizzIt\Services\Factory\Extension\ServiceFactoryExtension
 * @covers GrizzIt\Services\Exception\NonInstantiableServiceException
 */
class ServiceFactoryExtensionTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::create
     */
    public function testCreate(): void
    {
        $objectFactory = $this->createMock(ObjectFactoryInterface::class);
        $result = new stdClass();
        $subject = new ServiceFactoryExtension($objectFactory);
        $create = (
            function (string $service): string {
                return $service;
            }
        );

        $definition = [
            'class' => 'foo',
            'parameters' => [
                'foo' => 'bar'
            ]
        ];

        $objectFactory->expects(static::once())
            ->method('create')
            ->with(
                'foo',
                ['foo' => 'bar']
            )->willReturn($result);

        $this->assertEquals(
            $result,
            $subject->create(
                'services.my.service',
                $definition,
                $create
            )
        );

        $this->assertEquals(
            $result,
            $subject->create(
                'services.my.service',
                $definition,
                $create
            )
        );
    }

    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::create
     */
    public function testCreateNoCache(): void
    {
        $objectFactory = $this->createMock(ObjectFactoryInterface::class);
        $result = new stdClass();
        $resultTwo = new stdClass();
        $subject = new ServiceFactoryExtension($objectFactory);
        $create = (
            function (string $service): string {
                return $service;
            }
        );

        $definition = [
            'cache' => false,
            'class' => 'foo',
            'parameters' => [
                'foo' => 'bar'
            ]
        ];

        $objectFactory->expects(static::exactly(2))
            ->method('create')
            ->with(
                'foo',
                ['foo' => 'bar']
            )->willReturnOnConsecutiveCalls($result, $resultTwo);

        $this->assertEquals(
            $result,
            $subject->create(
                'services.my.service',
                $definition,
                $create
            )
        );

        $this->assertEquals(
            $resultTwo,
            $subject->create(
                'services.my.service',
                $definition,
                $create
            )
        );
    }

    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::create
     */
    public function testCreateFail(): void
    {
        $objectFactory = $this->createMock(ObjectFactoryInterface::class);
        $subject = new ServiceFactoryExtension($objectFactory);
        $this->expectException(NonInstantiableServiceException::class);
        $subject->create(
            'services.my.service',
            [
                'abstract' => true
            ],
            (
                function (string $service): string {
                    return $service;
                }
            )
        );
    }
}
