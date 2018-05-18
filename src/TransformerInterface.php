<?php

namespace Cyve\ETL;

interface TransformerInterface
{
    /**
     * @param mixed $data
     * @param ContextInterface $context
     * @return mixed
     */
    function transform($data, ContextInterface $context);
}
