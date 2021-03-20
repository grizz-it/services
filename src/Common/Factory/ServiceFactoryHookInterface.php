<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Common\Factory;

interface ServiceFactoryHookInterface
{
    /**
     * Hooks in before the creation of a service.
     *
     * @param string $key
     * @param array $definition
     * @param callable $create
     *
     * @return array
     */
    public function preCreate(
        string $key,
        array $definition,
        callable $create
    ): array;

    /**
     * Hooks in after the creation of a service.
     *
     * @param string $key
     * @param array $definition
     * @param mixed $return
     * @param callable $create
     *
     * @return mixed
     */
    public function postCreate(
        string $key,
        array $definition,
        mixed $return,
        callable $create
    ): mixed;
}
