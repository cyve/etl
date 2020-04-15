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
        $results = [];

        $etl = new ETL();
        $etl->setExtractor(function () {
            return ['foo', 'bar'];
        });
        $etl->setTransformer(function ($data) {
            return (object) ['term' => $data];
        });
        $etl->setLoader(function ($data) {
            $data->loaded = true;
            return $data;
        });
        $etl->addProgressListener(function($event) use (&$results) {
            $results[] = $event->getSubject();
        });
        $etl->process();

        $this->assertCount(2, $results);
        $this->assertObjectHasAttribute('term', $results[0]);
        $this->assertObjectHasAttribute('loaded', $results[0]);
    }

    public function testExtractorException()
    {
        $errors = [];

        $etl = new ETL();
        $etl->setExtractor(function () {
            throw new \RuntimeException('extractor error');
        });
        $etl->addErrorListener(function($event) use (&$errors) {
            $errors[] = $event->getSubject();
        });
        $etl->process();

        $this->assertCount(1, $errors);
        $this->assertEquals('extractor error', $errors[0]->getMessage());
    }

    public function testTransformerError()
    {
        $errors = [];

        $etl = new ETL();
        $etl->setExtractor(function () {
            return ['foo'];
        });
        $etl->setTransformer(function () {
            throw new \RuntimeException('transformer error');
        });
        $etl->addErrorListener(function($event) use (&$errors) {
            $errors[] = $event->getSubject();
        });
        $etl->process();

        $this->assertCount(1, $errors);
        $this->assertEquals('transformer error', $errors[0]->getMessage());
    }

    public function testLoaderError()
    {
        $errors = [];

        $etl = new ETL();
        $etl->setExtractor(function () {
            return ['foo'];
        });
        $etl->setTransformer(function () {
            return ['foo'];
        });
        $etl->setLoader(function () {
            throw new \RuntimeException('loader error');
        });
        $etl->addErrorListener(function($event) use (&$errors) {
            $errors[] = $event->getSubject();
        });
        $etl->process();

        $this->assertCount(1, $errors);
        $this->assertEquals('loader error', $errors[0]->getMessage());
    }
}
