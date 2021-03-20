<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Common\Compiler;

use GrizzIt\Validator\Common\ValidatorInterface;
use GrizzIt\Services\Common\Registry\ServiceRegistryInterface;
use GrizzIt\Services\Common\Compiler\ServiceCompilerExtensionInterface;

interface ServiceCompilerInterface
{
    /**
     * Compiles the services and returns the compiled services.
     *
     * @return ServiceRegistryInterface
     */
    public function compile(): ServiceRegistryInterface;

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
    ): void;

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
    ): void;
}
