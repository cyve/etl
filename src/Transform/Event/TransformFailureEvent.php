<?php

namespace Cyve\ETL\Transform\Event;

class TransformFailureEvent
{
    public function __construct(
        public int $index,
        public \Exception $exception,
    ){
    }
}
