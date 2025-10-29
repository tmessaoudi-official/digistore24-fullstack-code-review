<?php 

use PhpCsFixer\Fixer\Import\OrderedImportsFixer;

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('vendor')
    ->exclude('public/bundles')
    ->notPath('src/Kernel.php')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@DoctrineAnnotation' => true,
        '@PER-CS' => true,
        '@PER-CS:risky' => true,
        '@PHP8x2Migration' => true,
        '@PHP8x2Migration:risky' => true,
        '@PHP8x4Migration' => true,
        '@PHPUnit10x0Migration:risky' => true,
        '@PSR1' => true,
        '@PSR12' => true,
        '@PSR12:risky' => true,
        '@PSR2' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'combine_nested_dirname' => true,
        'declare_strict_types' => true,
        'fopen_flags' => false,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'heredoc_to_nowdoc' => true,
        'list_syntax' => [
            'syntax' => 'short',
        ],
        'native_constant_invocation' => true,
        'ordered_imports' => [
            'imports_order' => [
                OrderedImportsFixer::IMPORT_TYPE_CONST,
                OrderedImportsFixer::IMPORT_TYPE_FUNCTION,
                OrderedImportsFixer::IMPORT_TYPE_CLASS,
            ],
        ],
        'php_unit_dedicate_assert' => [
            'target' => 'newest',
        ],
        'protected_to_private' => false,
        'strict_param' => true,
        'ternary_to_null_coalescing' => true,
        'modifier_keywords' => true,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setCacheFile(__DIR__ . '/var/.php-cs-fixer.cache')
;
