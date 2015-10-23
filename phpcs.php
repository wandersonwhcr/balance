<?php
$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in('module/Balance/src');
return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::CONTRIB_LEVEL)
    ->fixers(array('-short_array_syntax', '-align_double_arrow'))
    ->finder($finder);
