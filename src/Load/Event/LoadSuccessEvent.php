<?php

namespace Cyve\ETL\Load\Event;

class LoadSuccessEvent
{
    public function __construct(
        public int $index,
        public mixed $result = null,
    ){
    }
}
