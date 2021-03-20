<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Component\Registry;

use GrizzIt\Services\Exception\DefinitionNotFoundException;
use GrizzIt\Services\Common\Registry\ServiceRegistryInterface;

class ServiceRegistry implements ServiceRegistryInterface
{
    /**
     * Contains the service definitions.
     *
     * @var array
     */
    private array $definitions;

    /**
     * Constructor.
     *
     * @param array $definitions
     */
    public function __construct(array $definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * Retrieves the definition by a key.
     *
     * @param string $key
     *
     * @return mixed
     *
     * @throws DefinitionNotFoundException When a definition can not be found.
     */
    public function getDefinitionByKey(string $key): mixed
    {
        [$serviceKey, $localKey] = $this->getKeys($key);
        if (isset($this->definitions[$serviceKey][$localKey])) {
            return $this->definitions[$serviceKey][$localKey];
        }

        throw new DefinitionNotFoundException($key);
    }

    /**
     * Checks whether a definition exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists(string $key): bool
    {
        [$serviceKey, $localKey] = $this->getKeys($key);

        return isset($this->definitions[$serviceKey][$localKey]);
    }

    /**
     * Retrieves the service and local key based on the full service key.
     *
     * @param string $key
     *
     * @return array
     */
    private function getKeys(string $key): array
    {
        $firstDot = strpos($key, '.');
        $serviceKey = $firstDot !== false ? substr($key, 0, $firstDot) : $key;
        $localKey = substr($key, $firstDot + 1);

        return [$serviceKey, $localKey];
    }
}
