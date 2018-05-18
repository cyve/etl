<?php

namespace Cyve\ETL;

class ETL
{
    /**
     * @var ExtractorInterface
     */
    protected $extractor;

    /**
     * @var TransformerInterface
     */
    protected $transformer;

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var ContextInterface
     */
    protected $context;

    /**
     * @param ExtractorInterface $extractor
     * @return ETL
     */
    public function setExtractor(ExtractorInterface $extractor): ETL
    {
        $this->extractor = $extractor;

        return $this;
    }

    /**
     * @param TransformerInterface $transformer
     * @return ETL
     */
    public function setTransformer(TransformerInterface $transformer): ETL
    {
        $this->transformer = $transformer;

        return $this;
    }

    /**
     * @param LoaderInterface $loader
     * @return ETL
     */
    public function setLoader(LoaderInterface $loader): ETL
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * @param ContextInterface $context
     * @return ETL
     */
    public function setContext(ContextInterface $context): ETL
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Process ETL
     */
    public function process()
    {
        foreach($this->extractor->extract($this->context) as $data) {
            try {
                $this->loader->load(
                    $this->transformer->transform($data, $this->context),
                    $this->context
                );
            }
            catch(\Exception $e){
                $this->context->addError($e);
            }
        }

        $this->loader->flush($this->context);
    }
}
