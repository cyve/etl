<?php

namespace Cyve\ETL\Transform\Event;

class TransformSuccessEvent
{
    public function __construct(
        public int $index,
        public mixed $result = null,
    ){
    }
}
