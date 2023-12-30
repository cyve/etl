<?php

namespace Cyve\ETL\Extract;

interface ExtractorInterface
{
    public function extract(): \Iterator;
}
