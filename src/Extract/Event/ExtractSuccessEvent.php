<?php

namespace Cyve\ETL\Extract\Event;

class ExtractSuccessEvent
{
    public function __construct(
        public int $index,
        public mixed $result = null,
    ){
    }
}
