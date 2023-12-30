<?php

namespace Cyve\ETL\Transform;

class NullTransformer implements TransformerInterface
{
    public function transform(\Iterator $iterator): \Iterator
    {
        yield from $iterator;
    }
}
