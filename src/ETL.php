<?php

namespace Cyve\ETL;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ETL
{
    /**
     * @var callable
     */
    protected $extractor;

    /**
     * @var callable
     */
    protected $transformer;

    /**
     * @var callable
     */
    protected $loader;

    /**
     * @var array
     */
    protected $context = [];

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param callable|null $extractor
     * @param callable|null $transformer
     * @param callable|null $loader
     */
    public function __construct(callable $extractor = null, callable $transformer = null, callable $loader = null)
    {
        $this->extractor = $extractor;
        $this->transformer = $transformer;
        $this->loader = $loader;
        $this->dispatcher = new EventDispatcher();
    }

    /**
     * @param callable $extractor
     * @return $this
     */
    public function setExtractor(callable $extractor): self
    {
        $this->extractor = $extractor;

        return $this;
    }

    /**
     * @param callable $transformer
     * @return $this
     */
    public function setTransformer(callable $transformer): self
    {
        $this->transformer = $transformer;

        return $this;
    }

    /**
     * @param callable $loader
     * @return $this
     */
    public function setLoader(callable $loader): self
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     * @return $this
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher): self
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    /**
     * @param callable $callback
     */
    public function addProgressListener(callable $callback): void
    {
        $this->dispatcher->addListener('progress', $callback);
    }

    /**
     * @param callable $callback
     */
    public function addErrorListener(callable $callback): void
    {
        $this->dispatcher->addListener('error', $callback);
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @param array $context
     */
    public function process(array $context = []): void
    {
        $this->context = $context;

        foreach($this->extract($this->context) as $data) {
            try {
                $result = $this->load(
                    $this->transform($data, $this->context),
                    $this->context
                );

                $this->dispatcher->dispatch(new GenericEvent($result, $context), 'progress');
            }
            catch(\Exception $e){
                $this->dispatcher->dispatch(new GenericEvent($e, $context), 'error');
            }
        }
    }

    /**
     * @param array $context
     * @return iterable
     */
    private function extract(array $context = []): iterable
    {
        if (is_callable($this->extractor)) {
            return call_user_func($this->extractor, $context);
        }

        return [];
    }

    /**
     * @param mixed $data
     * @param array $context
     * @return mixed
     */
    private function transform($data, array $context = [])
    {
        if (is_callable($this->transformer)) {
            return call_user_func($this->transformer, $data, $context);
        }

        return $data;
    }

    /**
     * @param mixed $data
     * @param array $context
     * @return mixed
     */
    private function load($data, array $context = [])
    {
        if (is_callable($this->loader)) {
            return call_user_func($this->loader, $data, $context);
        }

        return $data;
    }
}
