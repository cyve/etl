<?php

namespace Cyve\ETL;

use Cyve\ETL\Extract\Event\ExtractFailureEvent;
use Cyve\ETL\Extract\Event\ExtractSuccessEvent;
use Cyve\ETL\Extract\ExtractorInterface;
use Cyve\ETL\Load\Event\LoadFailureEvent;
use Cyve\ETL\Load\Event\LoadSuccessEvent;
use Cyve\ETL\Load\LoaderInterface;
use Cyve\ETL\Transform\Event\TransformFailureEvent;
use Cyve\ETL\Transform\Event\TransformSuccessEvent;
use Cyve\ETL\Transform\TransformerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class ETL
{
    public function __construct(
        private ExtractorInterface $extractor,
        private TransformerInterface $transformer,
        private LoaderInterface $loader,
        private ?EventDispatcherInterface $eventDispatcher = null,
    ) {
    }

    public function start(): void
    {
        $extracted = (function () {
            foreach ($this->extractor->extract() as $index => $result) {
                if ($result instanceof \Throwable) {
                    $this->eventDispatcher?->dispatch(new ExtractFailureEvent($index, $result));
                    continue;
                }

                $this->eventDispatcher?->dispatch(new ExtractSuccessEvent($index, $result));

                yield $result;
            }
        });

        $transformed = (function () use ($extracted) {
            foreach ($this->transformer->transform($extracted()) as $index => $result) {
                if ($result instanceof \Throwable) {
                    $this->eventDispatcher?->dispatch(new TransformFailureEvent($index, $result));
                    continue;
                }

                $this->eventDispatcher?->dispatch(new TransformSuccessEvent($index, $result));

                yield $result;
            }
        });

        foreach ($this->loader->load($transformed()) as $index => $result) {
            if ($result instanceof \Throwable) {
                $this->eventDispatcher?->dispatch(new LoadFailureEvent($index, $result));
                continue;
            }

            $this->eventDispatcher?->dispatch(new LoadSuccessEvent($index, $result));
        }
    }
}
