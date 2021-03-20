<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Tests\Component\Compiler;

use PHPUnit\Framework\TestCase;
use GrizzIt\Storage\Common\StorageInterface;
use GrizzIt\Validator\Common\ValidatorInterface;
use GrizzIt\Configuration\Common\RegistryInterface;
use GrizzIt\Services\Component\Compiler\ServiceCompiler;
use GrizzIt\Services\Common\Registry\ServiceRegistryInterface;
use GrizzIt\Services\Exception\InvalidServiceDefinitionException;
use GrizzIt\Services\Common\Compiler\ServiceCompilerExtensionInterface;

/**
 * @coversDefaultClass GrizzIt\Services\Component\Compiler\ServiceCompiler
 * @covers GrizzIt\Services\Exception\InvalidServiceDefinitionException
 */
class ServiceCompilerTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::compile
     * @covers ::addValidator
     * @covers ::addExtension
     */
    public function testCompile(): void
    {
        $registry = $this->createMock(RegistryInterface::class);
        $storage = $this->createMock(StorageInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $extension = $this->createMock(ServiceCompilerExtensionInterface::class);
        $subject = new ServiceCompiler($registry, $storage);

        $storage->expects(static::once())
            ->method('has')
            ->with(ServiceCompiler::STORAGE_COMPILED_KEY)
            ->willReturn(true);

        $storage->expects(static::once())
            ->method('get')
            ->with(ServiceCompiler::STORAGE_COMPILED_KEY)
            ->willReturn(false);

        $registry->expects(static::once())
            ->method('get')
            ->with('services')
            ->willReturn([
                [
                    'services' => [
                        'my.service' => [
                            'class' => '\\GrizzIt\\Validator\\Component\\Logical\\AlwaysValidator',
                            'parameters' => [
                                'alwaysBool' => true
                            ]
                        ]
                    ]
                ]
            ]);

        $validator->expects(static::once())
            ->method('__invoke')
            ->with(
                [
                    'class' => '\\GrizzIt\\Validator\\Component\\Logical\\AlwaysValidator',
                    'parameters' => [
                        'alwaysBool' => true
                    ]
                ]
            )->willReturn(true);

        $extension->expects(static::once())
            ->method('compile')
            ->with(
                [
                    'services' => [
                        'my.service' => [
                            'class' => '\\GrizzIt\\Validator\\Component\\Logical\\AlwaysValidator',
                            'parameters' => [
                                'alwaysBool' => true
                            ]
                        ]
                    ]
                ]
            )->willReturn(
                [
                    'services' => [
                        'my.service' => [
                            'class' => '\\GrizzIt\\Validator\\Component\\Logical\\AlwaysValidator',
                            'parameters' => [
                                'alwaysBool' => true
                            ]
                        ]
                    ]
                ]
            );

        $storage->expects(static::exactly(2))
            ->method('set')
            ->withConsecutive(
                [
                    ServiceCompiler::STORAGE_COMPILED_KEY,
                    true
                ],
                [
                    ServiceCompiler::STORAGE_SERVICES_KEY,
                    [
                        'services' => [
                            'my.service' => [
                                'class' => '\\GrizzIt\\Validator\\Component\\Logical\\AlwaysValidator',
                                'parameters' => [
                                    'alwaysBool' => true
                                ]
                            ]
                        ]
                    ]
                ]
            );

        $subject->addValidator('services', $validator);
        $subject->addExtension($extension, 0);
        $this->assertInstanceOf(
            ServiceRegistryInterface::class,
            $subject->compile()
        );
    }

    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::compile
     * @covers ::addValidator
     */
    public function testCompileInvalidContent(): void
    {
        $registry = $this->createMock(RegistryInterface::class);
        $storage = $this->createMock(StorageInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);
        $subject = new ServiceCompiler($registry, $storage);

        $registry->expects(static::once())
            ->method('get')
            ->with('services')
            ->willReturn([
                [
                    'services' => [
                        'my.service' => [
                            'class' => '\\GrizzIt\\Validator\\Component\\Logical\\AlwaysValidator',
                            'parameters' => [
                                'alwaysBool' => true
                            ]
                        ]
                    ]
                ]
            ]);

        $validator->expects(static::once())
            ->method('__invoke')
            ->with(
                [
                    'class' => '\\GrizzIt\\Validator\\Component\\Logical\\AlwaysValidator',
                    'parameters' => [
                        'alwaysBool' => true
                    ]
                ]
            )->willReturn(false);

        $subject->addValidator('services', $validator);
        $this->expectException(InvalidServiceDefinitionException::class);
        $subject->compile();
    }

    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::compile
     */
    public function testCompileCached(): void
    {
        $registry = $this->createMock(RegistryInterface::class);
        $storage = $this->createMock(StorageInterface::class);
        $subject = new ServiceCompiler($registry, $storage);

        $storage->expects(static::once())
            ->method('has')
            ->with(ServiceCompiler::STORAGE_COMPILED_KEY)
            ->willReturn(true);

        $storage->expects(static::exactly(2))
            ->method('get')
            ->withConsecutive(
                [ServiceCompiler::STORAGE_COMPILED_KEY],
                [ServiceCompiler::STORAGE_SERVICES_KEY]
            )->willReturnOnConsecutiveCalls(
                true,
                ['services' => []]
            );

        $this->assertInstanceOf(
            ServiceRegistryInterface::class,
            $subject->compile()
        );
    }
}
