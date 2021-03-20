<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Exception;

use Exception;

class DefinitionNotFoundException extends Exception
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
                'Definition not found for "%s".',
                $serviceKey
            )
        );
    }
}
