<?php

namespace Pushword\AdminBlockEditor\Block;

use Exception;

class DefaultBlock extends AbstractBlock
{
    const AVAILABLE_BLOCKS = [
        'paragraph',
        'list',
        'header',
        'raw',
        'quote',
        'code',
        'list',
        'delimiter',
        'table',
    ];

    public function __construct(string $name)
    {
        if (! \in_array($name, self::AVAILABLE_BLOCKS)) {
            throw new Exception('Not a default block `'.$name.'`');
        }

        $this->name = $name;
    }
}
