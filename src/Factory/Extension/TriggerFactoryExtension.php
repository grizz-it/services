<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Factory\Extension;

use GrizzIt\Services\Common\Factory\ServiceFactoryExtensionInterface;

class TriggerFactoryExtension implements ServiceFactoryExtensionInterface
{
    /**
     * Contains the already resolved services.
     *
     * @var mixed[]
     */
    private array $resolveCache = [];

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
        if (!isset($this->resolveCache[$key])) {
            $result = [];
            $this->resolveCache[$key] = $result;
            foreach ($definition as $service) {
                $result[] = $create($service);
            }

            $this->resolveCache[$key] = $result;
        }

        return $this->resolveCache[$key];
    }
}
