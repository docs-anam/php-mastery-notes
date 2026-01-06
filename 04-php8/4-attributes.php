<?php
/**
 * PHP 8.0 Attributes - Detailed Summary
 *
 * Attributes (also known as annotations) were introduced in PHP 8.0 as a way to add structured, machine-readable metadata to classes, functions, methods, properties, and parameters.
 * They provide a native alternative to PHPDoc comments for metadata.
 *
 * Key Points:
 * 1. Syntax:
 *    - Attributes are declared using #[AttributeName(arguments)] before the target element.
 *    - Multiple attributes can be applied to the same element.
 *
 * 2. Defining an Attribute:
 *    - An attribute is a PHP class marked with #[Attribute] above its definition.
 *    - The class can define a constructor to accept arguments.
 *
 * 3. Applying Attributes:
 *    - Attributes can be applied to classes, methods, properties, parameters, and functions.
 *
 * 4. Reflection:
 *    - Attributes can be read at runtime using PHP's Reflection API (ReflectionClass, ReflectionMethod, etc.).
 *
 * Example:
 */

#[Attribute]
class ExampleAttribute
{
    public function __construct(
        public string $description,
        public int $level = 1
    ) {}
}

#[ExampleAttribute('This is a test class', level: 2)]
class TestClass
{
    #[ExampleAttribute('Test property')]
    public string $property;

    #[ExampleAttribute('Test method', level: 3)]
    public function testMethod(
        #[ExampleAttribute('Test parameter')] $param
    ) {}
}

// Reading attributes via Reflection
$reflection = new ReflectionClass(TestClass::class);
$attributes = $reflection->getAttributes();

foreach ($attributes as $attribute) {
    $instance = $attribute->newInstance();
    echo $instance->description . ' (level: ' . $instance->level . ')' . PHP_EOL;
}

/**
 * Benefits:
 * - Native, structured metadata.
 * - No need for parsing docblocks.
 * - Useful for frameworks (e.g., routing, validation, ORM mapping).
 *
 * Limitations:
 * - Only available since PHP 8.0.
 * - Not all use cases from docblocks are covered.
 *
 * References:
 * - https://www.php.net/manual/en/language.attributes.overview.php
 */