<?php

namespace Cyve\ETL\Transform;

class ClosureTransformer implements TransformerInterface
{
    public function __construct(
        private \Closure $closure
    ) {
    }

    public function transform(\Iterator $iterator): \Iterator
    {
        foreach ($iterator as $iteration) {
            try {
                yield call_user_func($this->closure, $iteration);
            } catch (\Throwable $error) {
                yield $error;
            }
        }
    }
}
