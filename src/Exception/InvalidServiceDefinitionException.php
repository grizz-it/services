<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Exception;

use Exception;

class InvalidServiceDefinitionException extends Exception
{
    /**
     * Constructor.
     *
     * @param string $serviceKey
     */
    public function __construct(string $serviceKey, mixed $definition)
    {
        parent::__construct(
            sprintf(
                'Invalid service definition found with key "%s" and definition:' .
                "\n" . '%s',
                $serviceKey,
                print_r($definition, true)
            )
        );
    }
}
