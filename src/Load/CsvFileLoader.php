<?php

namespace Cyve\ETL\Load;

class CsvFileLoader implements LoaderInterface
{
    public function __construct(
        private string $filename,
        private array $options = [],
    ) {
        $this->options['separator'] ??= ',';
        $this->options['enclosure'] ??= '"';
        $this->options['ignore_headers'] ??= false;
    }

    public function load(\Iterator $iterator): \Iterator
    {
        $stream = fopen($this->filename, 'a');

        $fileStats = fstat($stream) ?: [];
        $fileSize = $fileStats['size'] ?? 0;
        $insertHeaders = $fileSize === 0 && !$this->options['ignore_headers'];

        foreach ($iterator as $item) {
            try {
                $values = (array) $item;

                if ($insertHeaders) {
                    fputcsv($stream, array_keys($values), $this->options['separator'], $this->options['enclosure'], '\\', PHP_EOL);
                    $insertHeaders = false;
                }

                fputcsv($stream, $values, $this->options['separator'], $this->options['enclosure'], '\\', PHP_EOL);

                yield $item;
            } catch (\Throwable $error) {
                yield $error;
            }
        }

        fclose($stream);
    }
}
