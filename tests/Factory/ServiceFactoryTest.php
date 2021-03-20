<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Tests\Factory;

use stdClass;
use PHPUnit\Framework\TestCase;
use GrizzIt\Services\Factory\ServiceFactory;
use GrizzIt\Services\Exception\FactoryNotFoundException;
use GrizzIt\Validator\Component\Logical\AlwaysValidator;
use GrizzIt\Services\Exception\DefinitionNotFoundException;
use GrizzIt\Services\Common\Compiler\ServiceCompilerInterface;
use GrizzIt\Services\Common\Registry\ServiceRegistryInterface;
use GrizzIt\Services\Common\Factory\ServiceFactoryHookInterface;
use GrizzIt\Services\Common\Factory\ServiceFactoryExtensionInterface;

/**
 * @coversDefaultClass GrizzIt\Services\Factory\ServiceFactory
 * @covers GrizzIt\Services\Exception\DefinitionNotFoundException
 * @covers GrizzIt\Services\Exception\FactoryNotFoundException
 */
class ServiceFactoryTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::getServiceRegistry
     * @covers ::addExtension
     * @covers ::addHook
     * @covers ::create
     * @covers ::resolveReferences
     * @covers ::addInternalService
     */
    public function testComponent(): void
    {
        $serviceCompiler = $this->createMock(ServiceCompilerInterface::class);
        $extension = $this->createMock(ServiceFactoryExtensionInterface::class);
        $hook = $this->createMock(ServiceFactoryHookInterface::class);
        $hookTwo = $this->createMock(ServiceFactoryHookInterface::class);
        $registry = $this->createMock(ServiceRegistryInterface::class);
        $subject = new ServiceFactory($serviceCompiler);
        $result = new stdClass();
        $service = 'services.my.service';
        $definitionUnresolved = [
            'class' => AlwaysValidator::class,
            'parameters' => [
                'alwaysBool' => "@{parameters.foo}"
            ]
        ];

        $definition = [
            'class' => AlwaysValidator::class,
            'parameters' => [
                'alwaysBool' => true
            ]
        ];

        $hook->expects(static::once())
            ->method('preCreate')
            ->with($service, $definition, [$subject, 'create'])
            ->willReturn([$service, $definition]);

        $hookTwo->expects(static::once())
            ->method('preCreate')
            ->with($service, $definition, [$subject, 'create'])
            ->willReturn([$service, $definition]);

        $extension->expects(static::once())
            ->method('create')
            ->with($service, $definition, [$subject, 'create'])
            ->willReturn($result);

        $hook->expects(static::once())
            ->method('postCreate')
            ->with($service, $definition, $result, [$subject, 'create'])
            ->willReturn($result);

        $hookTwo->expects(static::once())
            ->method('postCreate')
            ->with($service, $definition, $result, [$subject, 'create'])
            ->willReturn($result);

        $serviceCompiler->expects(static::once())
            ->method('compile')
            ->willReturn($registry);

        $subject->addExtension('services', $extension);
        $subject->addHook('global', $hook, 0);
        $subject->addHook('services', $hookTwo, 0);

        $registry->expects(static::once())
            ->method('getDefinitionByKey')
            ->with($service)
            ->willReturn($definitionUnresolved);

        $this->assertEquals(
            $result,
            $subject->create($service, ['foo' => true])
        );
    }

    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::getServiceRegistry
     * @covers ::create
     * @covers ::resolveReferences
     * @covers ::addInternalService
     */
    public function testComponentNoFactory(): void
    {
        $serviceCompiler = $this->createMock(ServiceCompilerInterface::class);
        $registry = $this->createMock(ServiceRegistryInterface::class);
        $subject = new ServiceFactory($serviceCompiler);
        $service = 'services.my.service';

        $definition = [
            'class' => AlwaysValidator::class,
            'parameters' => [
                'alwaysBool' => true
            ]
        ];

        $serviceCompiler->expects(static::once())
            ->method('compile')
            ->willReturn($registry);

        $registry->expects(static::once())
            ->method('getDefinitionByKey')
            ->with($service)
            ->willReturn($definition);

        $this->expectException(FactoryNotFoundException::class);
        $subject->create($service);
    }

    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::getServiceRegistry
     * @covers ::create
     * @covers ::addInternalService
     */
    public function testComponentInternal(): void
    {
        $serviceCompiler = $this->createMock(ServiceCompilerInterface::class);
        $registry = $this->createMock(ServiceRegistryInterface::class);
        $subject = new ServiceFactory($serviceCompiler);

        $serviceCompiler->expects(static::once())
            ->method('compile')
            ->willReturn($registry);

        $this->assertEquals(
            $registry,
            $subject->create('internal.service.registry')
        );
    }

    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::getServiceRegistry
     * @covers ::create
     * @covers ::resolveReferences
     * @covers ::addInternalService
     */
    public function testComponentLocalParameter(): void
    {
        $serviceCompiler = $this->createMock(ServiceCompilerInterface::class);
        $registry = $this->createMock(ServiceRegistryInterface::class);
        $subject = new ServiceFactory($serviceCompiler);

        $serviceCompiler->expects(static::once())
            ->method('compile')
            ->willReturn($registry);

        $this->assertEquals(
            true,
            $subject->create('parameters.foo', ['foo' => true])
        );
    }

    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::getServiceRegistry
     * @covers ::create
     * @covers ::addInternalService
     */
    public function testComponentNoInternal(): void
    {
        $serviceCompiler = $this->createMock(ServiceCompilerInterface::class);
        $registry = $this->createMock(ServiceRegistryInterface::class);
        $subject = new ServiceFactory($serviceCompiler);

        $serviceCompiler->expects(static::once())
            ->method('compile')
            ->willReturn($registry);

        $this->expectException(DefinitionNotFoundException::class);
        $subject->create('internal');
    }
}
