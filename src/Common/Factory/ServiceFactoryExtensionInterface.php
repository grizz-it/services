<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Common\Factory;

interface ServiceFactoryExtensionInterface
{
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
    ): mixed;
}
