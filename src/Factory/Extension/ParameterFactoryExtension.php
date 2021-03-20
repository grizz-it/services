<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Factory\Extension;

use GrizzIt\Services\Common\Factory\ServiceFactoryExtensionInterface;

class ParameterFactoryExtension implements ServiceFactoryExtensionInterface
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
    ): mixed {
        return $definition;
    }
}
