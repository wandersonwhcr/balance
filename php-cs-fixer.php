<?php
$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in('module/Balance/src');
return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::CONTRIB_LEVEL)
    ->fixers(array(
        '-long_array_syntax',
        '-align_double_arrow',
        '-logical_not_operators_with_spaces',
        '-no_blank_lines_before_namespace',
    ))
    ->finder($finder);
