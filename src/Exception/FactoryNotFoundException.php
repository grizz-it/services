<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Exception;

use Exception;

class FactoryNotFoundException extends Exception
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
                'No factory found for service key "%s".',
                $serviceKey
            )
        );
    }
}
