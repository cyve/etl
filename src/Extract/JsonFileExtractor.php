<?php

namespace Cyve\ETL\Extract;

class JsonFileExtractor implements ExtractorInterface
{
    public function __construct(
        private string $filename,
    ) {
        if (!file_exists($this->filename)) {
            throw new \UnexpectedValueException(sprintf('File "%s" does not exist', $this->filename));
        }
    }

    public function extract(): \Iterator
    {
        $content = file_get_contents($this->filename);
        $content = json_decode($content, null, 512, \JSON_THROW_ON_ERROR);

        if (is_array($content) && array_is_list($content)) {
            foreach ($content as $object) {
                yield $object;
            }
        } else {
            yield $content;
        }
    }
}
