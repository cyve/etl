<?php

namespace Cyve\ETL\Load\Event;

class LoadFailureEvent
{
    public function __construct(
        public int $index,
        public \Exception $exception,
    ){
    }
}
