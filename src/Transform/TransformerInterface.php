<?php

namespace Cyve\ETL\Transform;

interface TransformerInterface
{
    public function transform(\Iterator $iterator): \Iterator;
}
