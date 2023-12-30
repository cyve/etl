<?php

namespace Cyve\ETL;

use Cyve\ETL\Extract\ExtractorInterface;
use Cyve\ETL\Load\LoaderInterface;
use Cyve\ETL\Transform\TransformerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class ETL implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private ExtractorInterface $extractor,
        private TransformerInterface $transformer,
        private LoaderInterface $loader,
    ) {
    }

    public function start(): void
    {
        $extracted = (function () {
            foreach ($this->extractor->extract() as $index => $result) {
                if ($result instanceof \Throwable) {
                    $this->logger?->error($result->getMessage(), ['handler' => $this->extractor::class, 'index' => $index, 'exception' => $result]);
                    continue;
                }

                $this->logger?->info('Extraction success', ['handler' => $this->extractor::class, 'index' => $index, 'result' => $result]);

                yield $result;
            }
        });

        $transformed = (function () use ($extracted) {
            foreach ($this->transformer->transform($extracted()) as $index => $result) {
                if ($result instanceof \Throwable) {
                    $this->logger?->error($result->getMessage(), ['handler' => $this->transformer::class, 'index' => $index, 'exception' => $result]);
                    continue;
                }

                $this->logger?->info('Transformation success', ['handler' => $this->transformer::class, 'index' => $index, 'result' => $result]);

                yield $result;
            }
        });

        foreach ($this->loader->load($transformed()) as $index => $result) {
            if ($result instanceof \Throwable) {
                $this->logger?->error($result->getMessage(), ['handler' => $this->loader::class, 'index' => $index, 'exception' => $result]);
                continue;
            }

            $this->logger?->info('Loading success', ['handler' => $this->loader::class, 'index' => $index, 'result' => $result]);
        }
    }
}
