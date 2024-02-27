<?php

namespace Cyve\ETL\Extract\Event;

class ExtractFailureEvent
{
    public function __construct(
        public int $index,
        public \Exception $exception,
    ){
    }
}
