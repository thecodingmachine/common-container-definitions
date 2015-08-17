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
use Mouf\Container\Definition\InstanceDefinition;

$instanceDefinition = new InstanceDefinition("instanceName", "My\\Class");
$instanceDefinition->addConstructorArgument("foo");
$instanceDefinition->addConstructorArgument(["bar"]);

return $instanceDefinition->toPhpCode('$container', []);
```

will return an `InlineEntry` object containing:

- **expression**: `new My\Class("foo", ["bar"])`
- **statements**: *empty*
- **usedVariables**: *empty*

You can pass references to other entries in the container by passing another object implementing the `DefinitionInterface`:
 
```php
use Mouf\Container\Definition\InstanceDefinition;

$dependencyDefinition = new InstanceDefinition("dependency", "My\\Dependency");

$instanceDefinition = new InstanceDefinition("instanceName", "My\\Class");
$instanceDefinition->addConstructorArgument($dependencyDefinition);

return $instanceDefinition->toPhpCode('$container', []);
```

will return an `InlineEntry` object containing:

- **expression**: `new My\Class($container->get("dependency"))`
- **statements**: *empty*
- **usedVariables**: *empty*

### Method calls

You can add method calls on your entry using the "addMethodCall" method:

```php
use Mouf\Container\Definition\InstanceDefinition;

$instanceDefinition = new InstanceDefinition("instanceName", "My\\Class");
$methodCall = $instanceDefinition->addMethodCall("setFoo");
$methodCall->addArgument(42);

return $instanceDefinition->toPhpCode('$container', []);
```

will return an `InlineEntry` object containing:

- **statements**: 
  ```php
  $instanceName = new My\Class();
  $instanceName->setFoo(42);
  ```
- **expression**: `$instanceName`
- **usedVariables**: `[ '$instanceName' ]`

### Setting public properties

You can add method calls on your entry using the "setProperty" method:

```php
use Mouf\Container\Definition\InstanceDefinition;

$instanceDefinition = new InstanceDefinition("instanceName", "My\\Class");
$instanceDefinition->setProperty("foo", 42);

return $instanceDefinition->toPhpCode('$container', []);
```

will return an `InlineEntry` object containing:

- **statements**: 
  ```php
  $instanceName = new My\Class();
  $instanceName->foo = 42;
  ```
- **expression**: `$instanceName`
- **usedVariables**: `[ '$instanceName' ]`

### Inlining dependencies

If you perfectly know that a dependency will be used only by a given entry, you can **inline** the code of the
dependency into the code generating the main entry. To do this, you just need to put **null** as the identifier
for your instance.

```php
use Mouf\Container\Definition\InstanceDefinition;

// null is passed as the identifier
$dependencyDefinition = new InstanceDefinition(null, "My\\Dependency");

$instanceDefinition = new InstanceDefinition("instanceName", "My\\Class");
$instanceDefinition->addConstructorArgument($dependencyDefinition);

return $instanceDefinition->toPhpCode('$container', []);
```

will return an `InlineEntry` object containing:

- **statements**: 
  ```php
  $myDependency = new My\Dependency();
  $instanceName = new My\Class($myDependency);
  ```
- **expression**: `$instanceName`
- **usedVariables**: `[ '$instanceName', '$myDependency' ]`

### Creating a parameter entry

A container does not store only objects. It can also store raw values. These values typically do not need to be
stored in callbacks as resolving the callback would be an unnecessary burden. If you want to store a raw value,
you can use the `ParameterDefinition` and directly pass the value of the parameter to this class.

```php
use Mouf\Container\Definition\ParameterDefinition;

$parameterDefinition = new ParameterDefinition("parameterName", "value");

return $parameterDefinition->toPhpCode('$container', []);
```

will return an `InlineEntry` object containing:

- **statements**: *empty*
- **expression**: `"value"`
- **usedVariables**: *empty*
- **lazilyEvaluated**: *false*

This code will generate an entry "parameterName" in your container whose value is "value".
You can pass any kind of scalar or array values to `ParameterDefinition`.

### Creating a parameter entry that references a constant

If you want your parameter entry to actually point to a constant (declared with `define`) or a class constant
(declared with `const`), you can use the `ConstParameterDefinition`.

```php
use Mouf\Container\Definition\ConstParameterDefinition;

$parameterDefinition = new ConstParameterDefinition("parameterName", "My\\Class::CONSTANT");
return $parameterDefinition->toPhpCode('$container', []);
```

will return an `InlineEntry` object containing:

- **statements**: *empty*
- **expression**: `My\\Class::CONSTANT`
- **usedVariables**: *empty*
- **lazilyEvaluated**: *false*

This code will generate an entry "parameterName" in your container that directly points to `My\\Class::CONSTANT`.

### Creating an alias

A container can store aliases to other container's entries. You can create an alias to another entry using the 
`AliasDefinition` class.

```php
use Mouf\Container\Definition\ParameterDefinition;

$aliasDefinition = new AliasDefinition("alias", "aliased_entry");
```

When calling `$container->get('alias')`, you will be given the entry stored in `aliased_entry`.
Generated code is:

- **expression**: `$container->get('aliased_entry')`
- **statements**: *empty*
- **usedVariables**: *empty*

### Creating a definition from a closure

TODO: doc need update

You can define container entries using **closures**. When the entry is retrieved, the closure will be evaluated
and the entry will be the return value of the closure.

```php
use Mouf\Container\Definition\ClosureDefinition;
use Interop\Container\ContainerInterface;

$closureDefinition = new ClosureDefinition("closureDef", function(ContainerInterface $container) {
    return new My\Service();
});
```

Please note:

 - The closure should accept one parameter: the container on which dependencies will be fetched
 - The closure cannot use the `$this` keyword
 - The closure cannot use context (the `use` keyword in the closure declaration)
 - The code of the closure will actually be **copied**, not referenced

