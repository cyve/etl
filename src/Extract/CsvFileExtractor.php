<?php

namespace Cyve\ETL\Extract;

class CsvFileExtractor implements ExtractorInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        private string $filename,
        private array $options = [],
    ) {
        if (!file_exists($filename)) {
            throw new \UnexpectedValueException(sprintf('File "%s" does not exist', $filename));
        }

        $this->options['separator'] ??= ',';
        $this->options['enclosure'] ??= '"';
        $this->options['ignore_headers'] ??= false;
    }

    public function extract(): \Iterator
    {
        $stream = fopen($this->filename, 'r') ?: throw new \RuntimeException(sprintf('Impossible to open file "%s".', $this->filename));
        $headers = ($this->options['ignore_headers']) ? null : fgetcsv($stream, 1024, $this->options['separator'], $this->options['enclosure']);

        while (($line = fgetcsv($stream, 1024, $this->options['separator'], $this->options['enclosure'], '\\')) !== false) {
            if ($headers) {
                $line = array_combine($headers, $line);
            }

            yield $line;
        }

        fclose($stream);
    }
}
