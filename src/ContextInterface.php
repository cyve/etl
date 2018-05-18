<?php

namespace Cyve\ETL;

interface ContextInterface
{
    /**
     * @param string $key
     * @param mixed $val
     * @return ContextInterface
     */
    public function set(string $key, $val): ContextInterface;

    /**
     * @param string $key
     * @return boolean
     */
    public function has(string $key): bool;

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * @return array
     */
    public function all(): array;

    /**
     * @param \Exception $e
     * @return ContextInterface
     */
    public function addError(\Exception $e): ContextInterface;

    /**
     * @return array
     */
    public function getErrors(): array;
}
