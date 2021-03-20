<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Common\Registry;

interface ServiceRegistryInterface
{
    /**
     * Retrieves the definition by a key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getDefinitionByKey(string $key): mixed;

    /**
     * Checks whether a definition exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists(string $key): bool;
}
