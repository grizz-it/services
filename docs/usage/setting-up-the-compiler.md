# GrizzIT Services - Setting up the compiler

The compiler is used to combine all service configuration so it can be
interpreted by the factories. It assumes that the configuration is done through
[grizz-it/configuration](https://github.com/grizz-it/configuration).

The result calling compile on the configuration compiler is required for the
services compiler. The second parameter for the compiler is a storage for the
compiled configuration. It is recommended to connect this to a caching storage
so compilation is not performed on every request.

```php
<?php

use GrizzIt\Storage\Component\ObjectStorage;
use GrizzIt\Configuration\Component\Registry\Registry;
use GrizzIt\Services\Component\Compiler\ServiceCompiler;
use GrizzIt\Configuration\Component\Compiler\ConfigurationCompiler;

/** @var ConfigurationCompiler $compiler */
$compiler;

/** @var Registry $registry */
$registry = $compiler->compile();

$compiler = new ServiceCompiler(
    $registry,
    new ObjectStorage()
);
```

When calling compile on the service compiler a registry is returned.

## Adding extensions

Extensions are used to refine service configuration even further. By default
this package supplies the service and trigger compiler extensions. These are
required in order to use the service and trigger factories.

```php
<?php
use GrizzIt\Services\Component\Compiler\Extension\ServiceCompilerExtension;
use GrizzIt\Services\Component\Compiler\Extension\TriggerCompilerExtension;


$compiler->addExtension(new ServiceCompilerExtension(), 0);
$compiler->addExtension(new TriggerCompilerExtension(), 0);
```

The second parameter is a sort order for the extensions. This can be useful when
an extension requires another to be finished first.

## Further reading

[Back to usage index](index.md)

[Setting up the factory](setting-up-the-factory.md)
