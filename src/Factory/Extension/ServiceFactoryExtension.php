<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Factory\Extension;

use GrizzIt\ObjectFactory\Common\ObjectFactoryInterface;
use GrizzIt\Services\Exception\NonInstantiableServiceException;
use GrizzIt\Services\Common\Factory\ServiceFactoryExtensionInterface;

class ServiceFactoryExtension implements ServiceFactoryExtensionInterface
{
    /**
     * Contains the object factory.
     *
     * @var ObjectFactoryInterface $objectFactory
     */
    private ObjectFactoryInterface $objectFactory;

    /**
     * Contains the already resolved services.
     *
     * @var mixed[]
     */
    private array $resolveCache = [];

    /**
     * Constructor.
     *
     * @param ObjectFactoryInterface $objectFactory
     */
    public function __construct(ObjectFactoryInterface $objectFactory)
    {
        $this->objectFactory = $objectFactory;
    }

    /**
     * Converts a service key and definition to an instance.
     *
     * @param string $key
     * @param mixed $definition
     * @param callable $create
     *
     * @return mixed
     */
    public function create(
        string $key,
        mixed $definition,
        callable $create
    ): mixed {
        if (isset($definition['abstract']) && $definition['abstract'] === true) {
            throw new NonInstantiableServiceException($key);
        }

        if (isset($this->resolveCache[$key])) {
            return $this->resolveCache[$key];
        }

        $return = $this->objectFactory->create(
            $definition['class'],
            $definition['parameters'] ?? []
        );

        if (!isset($definition['cache']) || $definition['cache']) {
            $this->resolveCache[$key] = $return;
        }

        return $return;
    }
}
