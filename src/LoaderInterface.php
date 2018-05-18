<?php

namespace Cyve\ETL;

interface LoaderInterface
{
    /**
     * @param mixed $data
     * @param ContextInterface $context
     * @return mixed
     */
    function load($data, ContextInterface $context);

    /**
     * @param ContextInterface $context
     */
    function flush(ContextInterface $context);
}
