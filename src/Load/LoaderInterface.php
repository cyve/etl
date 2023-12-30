<?php

namespace Cyve\ETL\Load;

interface LoaderInterface
{
    public function load(\Iterator $iterator): \Iterator;
}
