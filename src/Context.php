<?php

namespace Cyve\ETL;

class Context implements ContextInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @param mixed $data
     */
    public function __construct($data = [])
    {
        $this->data = (array) $data;
    }

    /**
     * @param string $key
     * @param mixed $val
     * @return ContextInterface
     */
    public function set(string $key, $val): ContextInterface
    {
        $this->data[$key] = $val;

        return $this;
    }

    /**
     * @param string $key
     * @return boolean
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * @param \Exception $e
     * @return ContextInterface
     */
    public function addError(\Exception $e): ContextInterface
    {
        $this->errors[] = $e;

        return $this;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
