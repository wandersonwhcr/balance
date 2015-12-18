<?php
$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in('module/Balance');
return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::CONTRIB_LEVEL)
    ->fixers(array(
        '-align_double_arrow',
        '-logical_not_operators_with_spaces',
        '-long_array_syntax',
        '-no_blank_lines_before_namespace',
        '-php_unit_strict',
        'multiline_array_trailing_comma',
        'single_array_no_trailing_comma',
        'unused_use',
    ))
    ->finder($finder);
