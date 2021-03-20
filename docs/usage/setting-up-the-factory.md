# GrizzIT Services - Setting up the factory

The factories in this package are used to instantiate definitions in the service
layer. They can be called by their service key. The factory can be setup when
the service compiler is provided. By default the service registry from the
compiler is added as an `internal` service. This can be retrieved by using
`internal.service.registry`.

```php
<?php

use GrizzIt\Services\Factory\ServiceFactory;
use GrizzIt\Services\Component\Compiler\ServiceCompiler;

/** @var ServiceCompiler $compiler */
$compiler;

$factory = new ServiceFactory($compiler);

$factory->create('internal.service.registry');
```

If the service is expecting unset parameters, then they can be passed as the
second parameter. Taking the following example:
```json
{
    "services": {
        "my.service": {
            "class": "\\GrizzIt\\Validator\\Component\\Logical\\AlwaysValidator",
            "parameters": {
                "alwaysBool": "@{parameters.my.dynamic.parameter}"
            }
        }
    }
}
```

The parameter can be dynamically added as follows:

```php
<?php
$factory->create(
    'internal.service.registry',
    [
        'my.dynamic.parameter' => true
    ]
);
```

## Adding extensions

Without any extension, only passed `parameters` and `internal` services are
available in the factory. The package provides a few extension by default. These
can be added by calling the `addExtension` method on the factory, providing the
`key` used in the services for the factory and an instance of the extension.
E.g.:

```php
<?php

use GrizzIt\Services\Factory\Extension\ParameterFactoryExtension;

$factory->addExtension('parameters', new ParameterFactoryExtension());
```

### Services extension

The services extension allows the declaration of basic classes in the services
layer under the key `services`. A simple service declaration would look like the
following:
```json
{
    "services": {
        "my.service": {
            "class": "\\GrizzIt\\Validator\\Component\\Logical\\AlwaysValidator",
            "parameters": {
                "alwaysBool": "@{parameters.my.parameter}"
            }
        }
    }
}
```

Parameters are optional if they are not required by the class. Service
configuration can also inherit from each other. This is resolved during
compilation. E.g.:
```json
{
    "services": {
        "my.parent.service": {
            "abstract": true,
            "class": "\\GrizzIt\\Validator\\Component\\Logical\\AlwaysValidator"
        },
        "my.service": {
            "parent": "my.parent.service",
            "parameters": {
                "alwaysBool": "@{parameters.my.parameter}"
            }
        }
    }
}
```

Because the first service definition is not instantiable (due to a lack of
required parameters) it is marked as `abstract`. This tells the factory that
this service definition can not be instantiated. The `parent` key is used to
tell the compiler to inherit all (except `abstract`) configuration from the
other service declaration.

### Parameters extension

Parameters are a simple extension in the services layer, which allow the use of
parameter declarations in the services configuration. These are fully freeform
and support any basic type of data or references to other services. An example
would be:

```json
{
    "parameters": {
        "my.parameter": true,
        "my.service.reference": "@{services.my.service}",
        "my.object.parameter": {
            "foo": "bar"
        }
    }
}
```

### Invocations extension

Invocations are used to call methods on resulting objects of other service
declarations. This can be useful when creating factories or certain logic needs
to be executed on another class.
```json
{
    "invocations": {
        "my.invocation": {
            "service": "services.my.service",
            "method": "__invoke",
            "parameters": {
                "input": {
                    "foo": "bar"
                }
            }
        }
    }
}
```

### Triggers extension

Triggers use two keys in the services layer (`tags` and `triggers`). These are
used for a dynamic declaration. Tags are used to determine the service that
needs to be executed when a trigger is requested. The trigger can be invoked
stand-alone, this will result in all tag invocation results being returned.
However, triggers can also be setup to be automatically called when another
service is invoked. This does require the setup of the trigger hook (more
about that below). A tag and trigger can be configured as follows:
```json
{
    "triggers": {
        "my.trigger": {
            "service": "services.my.service"
        }
    },
    "tags": {
        "my.tag": {
            "trigger": "triggers.my.trigger",
            "service": "invocations.my.invocation"
        }
    }
}
```

Whenever `triggers.my.trigger` is referenced or directly invoked, then an array
is returned with only the result of calling `invocations.my.invocation`. With
this setup (and whether the hook is configured) on calling the service
`services.my.service` in the background, the invocation is executed. This can be
useful when e.g. a registry needs to be filled with invocations, but only when
it is used to reduce resource usage.

## Adding hooks

Hooks are used to add logic on already existing service keys. They can be
registered to existing key spaces like `services`, `parameters` etc., but they
can also be registered globally on `global` (meaning all service keys). A hook
can be added as follows:
```php
<?php

use GrizzIt\Services\Factory\Hook\TriggerFactoryHook;

$factory->addHook(
    // Key
    'global',
    // Hook
    new TriggerFactoryHook($factory->create('internal.service.registry')),
    // Sort order
    0
);
```

### Triggers hook

The triggers hook adds logic to all service keys when they are invoked to check
whether another service needs to be invoked. This is registered on the `global`
key.

## Further reading

[Back to usage index](index.md)

[Setting up the compiler](setting-up-the-compiler.md)
