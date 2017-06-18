<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->exclude('build')
    ->exclude('docs')
    ->in(__DIR__);

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
//        'method_argument_space' => ['ensure_fully_multiline' => false],
    ])
    ->setFinder($finder);
