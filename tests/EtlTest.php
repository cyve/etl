<?php

namespace Cyve\ETL\Test;

use Cyve\ETL\Context;
use Cyve\ETL\ETL;
use Cyve\ETL\ExtractorInterface;
use Cyve\ETL\LoaderInterface;
use Cyve\ETL\TransformerInterface;
use PHPUnit\Framework\TestCase;

class EltTest extends TestCase
{
    public function testElt()
    {
        $extractor = $this->createMock(ExtractorInterface::class);
        $extractor->expects($this->once())->method('extract')->willReturn([1, 2]);
        
        $transformer = $this->createMock(TransformerInterface::class);
        $transformer->expects($this->exactly(2))->method('transform');
        
        $loader = $this->createMock(LoaderInterface::class);
        $loader->expects($this->exactly(2))->method('load');
        $loader->expects($this->once())->method('flush');
        
        $context = $this->createMock(Context::class);
        
        $etl = new ETL();
        $this->assertInstanceOf(ETL::class, $etl->setExtractor($extractor));
        $this->assertInstanceOf(ETL::class, $etl->setTransformer($transformer));
        $this->assertInstanceOf(ETL::class, $etl->setLoader($loader));
        $this->assertInstanceOf(ETL::class, $etl->setContext($context));
        $etl->process();
    }
}
