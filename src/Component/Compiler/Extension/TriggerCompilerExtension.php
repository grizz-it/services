<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace GrizzIt\Services\Component\Compiler\Extension;

use GrizzIt\Services\Common\Compiler\ServiceCompilerExtensionInterface;

class TriggerCompilerExtension implements ServiceCompilerExtensionInterface
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
        foreach ($services['tags'] ?? [] as $tag) {
            // Trim "triggers." for the lookup.
            $triggerKey = substr($tag['trigger'], 9);
            if (isset($services['triggers'][$triggerKey])) {
                $services['triggers'][$triggerKey]['tags']
                    [$tag['sortOrder'] ?? 1000][] = $tag['service'];
            }
        }

        $triggers = [];
        foreach ($services['triggers'] ?? [] as $triggerKey => $trigger) {
            $trigger['tags'] = $trigger['tags'] ?? [];
            ksort($trigger['tags']);
            $trigger['tags'] = array_merge(...$trigger['tags']);
            if (isset($trigger['service'])) {
                $triggers['_trigger.' . $trigger['service']][] = $triggerKey;
            }

            $triggers[$triggerKey] = $trigger['tags'] ?? [];
        }

        unset($services['tags']);
        $services['triggers'] = $triggers;

        return $services;
    }
}
