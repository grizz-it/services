<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Factory\Extension;

use GrizzIt\Services\Exception\InvalidArgumentException;
use GrizzIt\ObjectFactory\Common\MethodReflectorInterface;
use GrizzIt\Services\Common\Factory\ServiceFactoryExtensionInterface;

class InvocationFactoryExtension implements ServiceFactoryExtensionInterface
{
    /**
     * Contains the object factory.
     *
     * @var MethodReflectorInterface
     */
    private MethodReflectorInterface $methodReflector;

    /**
     * Contains the already resolved services.
     *
     * @var mixed[]
     */
    private array $resolveCache = [];

    /**
     * Constructor.
     *
     * @param MethodReflectorInterface $methodReflector
     */
    public function __construct(MethodReflectorInterface $methodReflector)
    {
        $this->methodReflector = $methodReflector;
    }

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
        if (isset($this->resolveCache[$key])) {
            return $this->resolveCache[$key];
        }

        $subject = $create($definition['service']);
        $parametersAnalysis = $this->methodReflector->reflect(
            get_class($subject),
            $definition['method']
        );

        $parameters = [];
        foreach ($parametersAnalysis as $parameterName => $parameterAnalysis) {
            $parameterValue = $parameterAnalysis['default'];

            if (isset($definition['parameters'][$parameterName])) {
                $parameterValue = $definition['parameters'][$parameterName];
            }

            $parameters[] = $parameterValue;
        }

        $return = call_user_func_array(
            [$subject, $definition['method']],
            $parameters
        );

        if (!isset($definition['cache']) || $definition['cache']) {
            $this->resolveCache[$key] = $return;
        }

        return $return;
    }
}
