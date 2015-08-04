[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thecodingmachine/common-container-definitions/badges/quality-score.png?b=1.0)](https://scrutinizer-ci.com/g/thecodingmachine/common-container-definitions/?branch=1.0)
[![Build Status](https://travis-ci.org/thecodingmachine/common-container-definitions.svg?branch=1.0)](https://travis-ci.org/thecodingmachine/common-container-definitions)
[![Coverage Status](https://coveralls.io/repos/thecodingmachine/common-container-definitions/badge.svg?branch=master&service=github)](https://coveralls.io/github/thecodingmachine/common-container-definitions?branch=1.0)

# Common container definitions for compiler-interop

This package contains common classes that implement the interfaces defined in [*compiler-interop*](https://github.com/container-interop/compiler-interop/)

## Installation

You can install this package through Composer:

```json
{
    "require": {
        "mouf/common-container-definitions": "dev-master"
    }
}
```

The packages adheres to the [SemVer](http://semver.org/) specification, and there will be full backward compatibility
between minor versions.

## Usage

Classes in this package represent **definitions** of entries that can be put in a container.
Those definitions needs to be passed to a **compiler** that will generate a **container** PHP class.

All the definitions in this package are implementing the `Interop\Container\Compiler\DefinitionInterface`.
This means they can be fed to any compiler compatible with [*compiler-interop*](https://github.com/container-interop/compiler-interop/).

### Creating a typical container entry

The typical container entry is an instance of a class that is passed some constructor arguments, with a few
method calls (typically setters).

```php
use Mouf\Container\Definition;

$instanceDefinition = new InstanceDefinition("instanceName", "My\\Class");
$instanceDefinition->addConstructorArgument("foo");
$instanceDefinition->addConstructorArgument(["bar"]);
```

will generate an instance using this PHP code:

```php
function(ContainerInterface $container) {
    return new My\Class("foo", ["bar"]);
}
```

You can pass references to other entries in the container by using the `Reference` class:
 
```php
use Mouf\Container\Definition;

$instanceDefinition = new InstanceDefinition("instanceName", "My\\Class");
$instanceDefinition->addConstructorArgument(new Reference("dependency"));
```

will generate an instance using this PHP code:

```php
function(ContainerInterface $container) {
    return new My\Class($container->get("dependency"));
}
```

You can also pass instance definitions in the arguments:
 
```php
use Mouf\Container\Definition;

$dependencyDefinition = new InstanceDefinition("dependency", "My\\Dependency");

$instanceDefinition = new InstanceDefinition("instanceName", "My\\Class");
$instanceDefinition->addConstructorArgument($dependencyDefinition);
```

will generate an instance using this PHP code:

```php
function(ContainerInterface $container) {
    return new My\Class($container->get("dependency"));
}
```

### Method calls

You can add method calls on your entry using the "addMethodCall" method:

```php
use Mouf\Container\Definition;

$instanceDefinition = new InstanceDefinition("instanceName", "My\\Class");
$methodCall = $instanceDefinition->addMethodCall("setFoo");
$methodCall->addArgument(42);
```

This code will generate an instance using this PHP code:

```php
function(ContainerInterface $container) {
    $instance = new My\Class();
    $instance->setFoo(42);
    return $instance;
}
```

### Setting public properties

You can add method calls on your entry using the "setProperty" method:

```php
use Mouf\Container\Definition;

$instanceDefinition = new InstanceDefinition("instanceName", "My\\Class");
$methodCall = $instanceDefinition->setProperty("foo", 42);
```

This code will generate an instance using this PHP code:

```php
function(ContainerInterface $container) {
    $instance = new My\Class();
    $instance->foo = 42;
    return $instance;
}
```
