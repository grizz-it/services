<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Common\Factory;

use GrizzIt\Services\Common\Factory\ServiceFactoryHookInterface;
use GrizzIt\Services\Common\Factory\ServiceFactoryExtensionInterface;

interface ServiceFactoryInterface
{
    /**
     * Adds a service factory to a scope.
     *
     * @param string $scope
     * @param ServiceFactoryInterface $serviceFactory
     *
     * @return void
     */
    public function addExtension(
        string $scope,
        ServiceFactoryExtensionInterface $extension
    ): void;

    /**
     * Adds a hook to the key connected to an extension.
     *
     * @param string $key
     * @param string $class
     * @param int $sortOrder
     * @param array $parameters
     *
     * @return void
     */
    public function addHook(
        string $scope,
        ServiceFactoryHookInterface $hook,
        int $sortOrder
    ): void;

    /**
     * Converts a service key to an instance.
     *
     * @param string $service
     * @param array $parameters
     *
     * @return mixed
     */
    public function create(string $service, array $parameters = []): mixed;

    /**
     * Adds an internal service.
     *
     * @param string $key
     * @param mixed $service
     *
     * @return void
     */
    public function addInternalService(string $key, mixed $service): void;
}
