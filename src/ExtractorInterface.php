<?php

namespace Cyve\ETL;

interface ExtractorInterface
{
    /**
     * @param ContextInterface $context
     * @return mixed
     */
    public function extract(ContextInterface $context);
}
