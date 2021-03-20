<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Component\Compiler\Extension;

use GrizzIt\Services\Common\Compiler\ServiceCompilerExtensionInterface;

class ServiceCompilerExtension implements ServiceCompilerExtensionInterface
{
    /**
     * Compile the services.
     *
     * @param array $services
     *
     * @return array
     */
    public function compile(array $services): array
    {
        $newServices = [];
        $resolve = $services['services'] ?? [];
        while (count($resolve) > 0) {
            foreach ($resolve as $serviceKey => $service) {
                if (isset($service['class'])) {
                    $newServices[$serviceKey] = $service;
                } elseif (array_key_exists($service['parent'], $newServices)) {
                    $newService = $newServices[$service['parent']];
                    unset($service['parent']);

                    if (isset($newService['abstract'])) {
                        unset($newService['abstract']);
                    }

                    $newServices[$serviceKey] = array_replace_recursive(
                        $newService,
                        $service
                    );
                } elseif (array_key_exists($service['parent'], $resolve)) {
                    // Step over the unset, because something else needs to be resolved first.
                    continue;
                }

                unset($resolve[$serviceKey]);
            }
        }

        $services['services'] = $newServices;

        return $services;
    }
}
