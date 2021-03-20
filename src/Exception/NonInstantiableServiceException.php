<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Exception;

use Exception;

class NonInstantiableServiceException extends Exception
{
    /**
     * Constructor.
     *
     * @param string $serviceKey
     */
    public function __construct(string $serviceKey)
    {
        parent::__construct(
            sprintf(
                'Tried to instantiate non-instantiable service "%s".',
                $serviceKey
            )
        );
    }
}
