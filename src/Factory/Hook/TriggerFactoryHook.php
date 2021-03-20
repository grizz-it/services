<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Factory\Hook;

use GrizzIt\Services\Common\Registry\ServiceRegistryInterface;
use GrizzIt\Services\Common\Factory\ServiceFactoryHookInterface;

class TriggerFactoryHook implements ServiceFactoryHookInterface
{
    /**
     * Contains the service registry.
     *
     * @var ServiceRegistryInterface $serviceRegistry
     */
    private ServiceRegistryInterface $serviceRegistry;

    /**
     * Constructor.
     *
     * @param ServiceRegistryInterface $serviceRegistry
     */
    public function __construct(ServiceRegistryInterface $serviceRegistry)
    {
        $this->serviceRegistry = $serviceRegistry;
    }

    /**
     * Hooks in before the creation of a service.
     *
     * @param string $key
     * @param mixed $definition
     * @param callable $create
     *
     * @return array
     */
    public function preCreate(
        string $key,
        mixed $definition,
        callable $create
    ): array {
        return [$key, $definition];
    }

    /**
     * Hooks in after the creation of a service.
     *
     * @param string $key
     * @param mixed $definition
     * @param mixed $return
     * @param callable $create
     *
     * @return mixed
     */
    public function postCreate(
        string $key,
        mixed $definition,
        mixed $return,
        callable $create
    ): mixed {
        if ($this->serviceRegistry->exists('triggers._trigger.' . $key)) {
            $definition = $this->serviceRegistry->getDefinitionByKey(
                'triggers._trigger.' . $key
            );

            foreach ($definition as $trigger) {
                $create('triggers.' . $trigger);
            }
        }

        return $return;
    }
}
