<?php

namespace Cyve\ETL;

use ArrayAccess;

class Context implements ArrayAccess, ContextInterface
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
     * @param array|object $data
     * @throws \InvalidArgumentException if data is not an array or an object
     */
    public function __construct($data = [])
    {
        if (is_array($data)) {
            $this->data = $data;
        } elseif (is_object($data)) {
            $this->data = get_object_vars($data);
        } else {
            throw new \InvalidArgumentException(sprintf('%s expects parameter 1 to be array or object, %s given', __METHOD__, gettype($data)));
        }
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

    /**
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if(!$offset) return;

        $this->data[$offset] = $value;
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }
}
