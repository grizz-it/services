<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Factory;

use GrizzIt\Services\Exception\FactoryNotFoundException;
use GrizzIt\Services\Exception\DefinitionNotFoundException;
use GrizzIt\Services\Common\Factory\ServiceFactoryInterface;
use GrizzIt\Services\Common\Compiler\ServiceCompilerInterface;
use GrizzIt\Services\Common\Registry\ServiceRegistryInterface;
use GrizzIt\Services\Common\Factory\ServiceFactoryHookInterface;
use GrizzIt\Services\Common\Factory\ServiceFactoryExtensionInterface;

class ServiceFactory implements ServiceFactoryInterface
{
    /**
     * Contains the individual service factories.
     *
     * @var ServiceFactoryExtensionInterface[]
     */
    private array $factories = [];

    /**
     * Contains the hooks for the service factories.
     *
     * @var ServiceFactoryHookInterface[][][]
     */
    private array $hooks = [];

    /**
     * Contains the service compiler.
     *
     * @var ServiceCompilerInterface
     */
    private ServiceCompilerInterface $serviceCompiler;

    /**
     * Contains the service registry.
     *
     * @var ServiceRegistryInterface|null
     */
    private ?ServiceRegistryInterface $serviceRegistry = null;

    /**
     * Contains the local parameters.
     *
     * @var mixed[]
     */
    private array $localParameters = [];

    /**
     * Contains the internal services.
     *
     * @var mixed[]
     */
    private array $internalServices = [];

    /**
     * Constructor.
     *
     * @param ServiceCompilerInterface $serviceCompiler
     */
    public function __construct(ServiceCompilerInterface $serviceCompiler)
    {
        $this->serviceCompiler = $serviceCompiler;
    }

    /**
     * Retrieves the service registry.
     *
     * @return ServiceRegistryInterface
     */
    private function getServiceRegistry(): ServiceRegistryInterface
    {
        if ($this->serviceRegistry === null) {
            $this->serviceRegistry = $this->serviceCompiler->compile();
            $this->addInternalService(
                'service.registry',
                $this->serviceRegistry
            );
        }

        return $this->serviceRegistry;
    }

    /**
     * Adds a service factory to a scope.
     *
     * @param string $scope
     * @param ServiceFactoryExtensionInterface $extension
     *
     * @return void
     */
    public function addExtension(
        string $scope,
        ServiceFactoryExtensionInterface $extension
    ): void {
        $this->factories[$scope] = $extension;
    }

    /**
     * Adds a hook to the key connected to an extension.
     *
     * @param string $scope
     * @param ServiceFactoryHookInterface $hook
     * @param int $sortOrder
     *
     * @return void
     */
    public function addHook(
        string $scope,
        ServiceFactoryHookInterface $hook,
        int $sortOrder
    ): void {
        $this->hooks[$scope][$sortOrder][] = $hook;
    }

    /**
     * Converts a service key to an instance.
     *
     * @param string $service
     * @param array $parameters
     *
     * @return mixed
     *
     * @throws FactoryNotFoundException When a factory can not be found for a
     *  service.
     * @throws DefinitionNotFoundException When an internal service can not be
     *  resolved.
     */
    public function create(string $service, array $parameters = []): mixed
    {
        if (count($parameters) > 0) {
            $this->localParameters = $parameters;
        }

        $firstDot = strpos($service, '.');
        $serviceKey = $firstDot !== false
            ? substr($service, 0, $firstDot)
            : $service;
        $localKey = substr($service, $firstDot + 1);

        $serviceRegistry = $this->getServiceRegistry();
        if ($serviceKey === 'internal') {
            if (isset($this->internalServices[$localKey])) {
                return $this->internalServices[$localKey];
            }

            throw new DefinitionNotFoundException($service);
        }

        if ($serviceKey === 'parameters') {
            if (isset($this->localParameters[$localKey])) {
                return $this->localParameters[$localKey];
            }
        }

        $definition = $this->resolveReferences(
            $serviceRegistry->getDefinitionByKey($service)
        );

        if (isset($this->factories[$serviceKey])) {
            $hooks = [];
            if (isset($this->hooks['global'])) {
                ksort($this->hooks['global']);
                $hooks = array_merge_recursive(
                    $hooks,
                    $this->hooks['global']
                );
            }

            if (isset($this->hooks[$serviceKey])) {
                ksort($this->hooks[$serviceKey]);
                $hooks = array_merge_recursive(
                    $hooks,
                    $this->hooks[$serviceKey]
                );
            }

            if (count($hooks) > 0) {
                ksort($hooks);
                $hooks = array_merge(
                    ...array_values($hooks)
                );
            }

            foreach ($hooks as $hook) {
                [$service, $definition] = $hook->preCreate(
                    $service,
                    $definition,
                    [$this, 'create']
                );
            }

            $return = $this->factories[$serviceKey]->create(
                $service,
                $definition,
                [$this, 'create']
            );

            foreach ($hooks as $hook) {
                $return = $hook->postCreate(
                    $service,
                    $definition,
                    $return,
                    [$this, 'create']
                );
            }

            return $return;
        }

        throw new FactoryNotFoundException($serviceKey);
    }

    /**
     * Resolves all references found in the definition.
     *
     * @param mixed $definition
     *
     * @return mixed
     */
    private function resolveReferences(mixed $definition): mixed
    {
        if (is_array($definition)) {
            foreach ($definition as $key => $value) {
                $definition[$key] = $this->resolveReferences($value);
            }
        }

        if (is_string($definition) && preg_match('/^@{.+}$/', $definition) === 1) {
            $definition = $this->create(trim($definition, '@{}'));
        }

        return $definition;
    }

    /**
     * Adds an internal service.
     *
     * @param string $key
     * @param mixed $service
     *
     * @return void
     */
    public function addInternalService(string $key, mixed $service): void
    {
        $this->internalServices[$key] = $service;
    }
}
