<?php

namespace Cyve\ETL\Load;

class JsonFileLoader implements LoaderInterface
{
    public function __construct(
        private string $filename,
    ) {
    }

    public function load(\Iterator $iterator): \Iterator
    {
        $content = [];
        foreach ($iterator as $object) {
            $content[] = $object;

            yield $object;
        }

        file_put_contents($this->filename, json_encode($content, JSON_PRETTY_PRINT));
    }
}
