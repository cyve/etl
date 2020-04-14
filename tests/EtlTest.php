<?php

namespace Cyve\ETL\Test;

use Cyve\ETL\ETL;
use PHPUnit\Framework\TestCase;

class EtlTest extends TestCase
{
    public function testCreateFromCallables()
    {
        $etl = new ETL(function(){}, function(){}, function(){});

        $extractorPropery = new \ReflectionProperty(ETL::class, 'extractor');
        $extractorPropery->setAccessible(true);
        $this->assertIsCallable($extractorPropery->getValue($etl));

        $transformerPropery = new \ReflectionProperty(ETL::class, 'transformer');
        $transformerPropery->setAccessible(true);
        $this->assertIsCallable($transformerPropery->getValue($etl));

        $loaderPropery = new \ReflectionProperty(ETL::class, 'loader');
        $loaderPropery->setAccessible(true);
        $this->assertIsCallable($loaderPropery->getValue($etl));
    }

    public function testCreateFromInvokables()
    {
        $extractor = new class {
            public function __invoke() {}
        };
        $transformer = new class {
            public function __invoke() {}
        };
        $loader = new class {
            public function __invoke() {}
        };

        $etl = new ETL();
        $etl->setExtractor($extractor);
        $etl->setTransformer($transformer);
        $etl->setLoader($loader);

        $extractorPropery = new \ReflectionProperty(ETL::class, 'extractor');
        $extractorPropery->setAccessible(true);
        $this->assertIsCallable($extractorPropery->getValue($etl));

        $transformerPropery = new \ReflectionProperty(ETL::class, 'transformer');
        $transformerPropery->setAccessible(true);
        $this->assertIsCallable($transformerPropery->getValue($etl));

        $loaderPropery = new \ReflectionProperty(ETL::class, 'loader');
        $loaderPropery->setAccessible(true);
        $this->assertIsCallable($loaderPropery->getValue($etl));
    }

    public function testFoo()
    {
        $results = $errors = [];

        $etl = new ETL();
        $etl->setExtractor(function () {
            return ['ok', 'transformer_nok', 'loader_nok'];
        });
        $etl->setTransformer(function ($data) {
            if ($data === 'transformer_nok') throw new \RuntimeException('transformation error');
            return (object) ['term' => $data];
        });
        $etl->setLoader(function ($data) {
            if ($data->term === 'loader_nok') throw new \LogicException('loading error');
            return $data;
        });
        $etl->addProgressListener(function($event) use (&$results) {
            $results[] = $event->getSubject();
        });
        $etl->addErrorListener(function($event) use (&$errors) {
            $errors[] = $event->getSubject();
        });
        $etl->process();

        $this->assertCount(1, $results);
        $this->assertCount(2, $errors);
    }
}
