<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Component\Compiler;

use GrizzIt\Storage\Common\StorageInterface;
use GrizzIt\Validator\Common\ValidatorInterface;
use GrizzIt\Configuration\Common\RegistryInterface;
use GrizzIt\Services\Component\Registry\ServiceRegistry;
use GrizzIt\Services\Common\Compiler\ServiceCompilerInterface;
use GrizzIt\Services\Common\Registry\ServiceRegistryInterface;
use GrizzIt\Services\Exception\InvalidServiceDefinitionException;
use GrizzIt\Services\Common\Compiler\ServiceCompilerExtensionInterface;

class ServiceCompiler implements ServiceCompilerInterface
{
    /**
     * The key that is used in the storage to determine whether the storage
     * contains compiled service information.
     *
     * @var string
     */
    public const STORAGE_COMPILED_KEY = 'compiled';

    /**
     * The key that is used in the storage to store all compiled services.
     *
     * @var string
     */
    public const STORAGE_SERVICES_KEY = 'services';

    /**
     * Contains the compiler extensions.
     *
     * @var ServiceCompilerExtensionInterface[][]
     */
    private array $extensions = [];

    /**
     * Contains the validators for the services.
     *
     * @var ValidatorInterface[]
     */
    private array $validators = [];

    /**
     * Contains the configuration registry.
     *
     * @var RegistryInterface $registry
     */
    private RegistryInterface $registry;

    /**
     * Contains the compiled services.
     *
     * @var StorageInterface
     */
    private StorageInterface $serviceStorage;

    /**
     * Constructor.
     *
     * @param RegistryInterface $registry
     * @param StorageInterface $serviceStorage
     */
    public function __construct(
        RegistryInterface $registry,
        StorageInterface $serviceStorage
    ) {
        $this->registry = $registry;
        $this->serviceStorage = $serviceStorage;
    }

    /**
     * Compiles the services and returns the compiled services.
     *
     * @return ServiceRegistryInterface
     *
     * @throws InvalidServiceDefinitionException When an invalid service
     *  definition is found.
     */
    public function compile(): ServiceRegistryInterface
    {
        if ($this->serviceStorage->has(static::STORAGE_COMPILED_KEY)) {
            if (
                $this->serviceStorage->get(
                    static::STORAGE_COMPILED_KEY
                ) === true
            ) {
                return new ServiceRegistry(
                    $this->serviceStorage->get(static::STORAGE_SERVICES_KEY)
                );
            }

            $this->serviceStorage->unset(static::STORAGE_SERVICES_KEY);
        }

        $services = [];
        $configuration = $this->registry->get('services');
        foreach ($configuration as $config) {
            foreach ($config as $key => $definitions) {
                $services[$key] = array_merge(
                    $services[$key] ?? [],
                    $definitions
                );
            }
        }

        foreach ($this->validators as $key => $validator) {
            foreach ($services[$key] ?? [] as $serviceKey => $definition) {
                if (!$validator($definition)) {
                    throw new InvalidServiceDefinitionException(
                        $serviceKey,
                        $definition
                    );
                }
            }
        }

        ksort($this->extensions);
        foreach ($this->extensions as $extensionSet) {
            foreach ($extensionSet as $extension) {
                $services = $extension->compile($services);
            }
        }

        $this->serviceStorage->set(static::STORAGE_COMPILED_KEY, true);
        $this->serviceStorage->set(static::STORAGE_SERVICES_KEY, $services);

        return new ServiceRegistry($services);
    }

    /**
     * Adds a validator for a service key.
     *
     * @param string $key
     * @param ValidatorInterface $validator
     *
     * @return void
     */
    public function addValidator(
        string $key,
        ValidatorInterface $validator
    ): void {
        $this->validators[$key] = $validator;
    }

    /**
     * Adds an extension to the service compiler.
     *
     * @param ServiceCompilerExtensionInterface $extension
     * @param int $sortOrder
     *
     * @return void
     */
    public function addExtension(
        ServiceCompilerExtensionInterface $extension,
        int $sortOrder
    ): void {
        $this->extensions[$sortOrder][] = $extension;
    }
}
