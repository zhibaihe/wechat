<?php

$header = <<<EOF
This file is part of the non-official WeChat SDK developed by Zhiyan.

(c) DUAN Zhiyan <zhiyan@zhibaihe.com>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader($header);

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers(array(
        'header_comment',
        'long_array_syntax',
        'ordered_use',
        'align_equals',
        'strict',
        'strict_param',
    ))
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->exclude('example')
            ->in(__DIR__)
    )
;