<?php

namespace Cyve\ETL\Test;

use Cyve\ETL\ETL;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class EltTest extends TestCase
{
    public function testEltWithCallables()
    {
        $extractor = function ($context) {
            return [['foo' => 'bar']];
        };
        $transformer = function ($data, $context) {
            $data['fromContext'] = $context['fromContext'];
            return (object) $data;
        };
        $loader = function ($data, $context) {
            $data->loaded = true;
            return $data;
        };
        $etl = new ETL($extractor, $transformer, $loader);
        $result = $etl->process(['fromContext' => 42]);

        $this->assertInstanceOf(\Generator::class, $result);
        $results = iterator_to_array($result);
        $this->assertCount(1, $results);
        $this->assertContainsOnly('object', $results);
        $this->assertEquals('bar', $results[0]->foo);
        $this->assertEquals(42, $results[0]->fromContext);
        $this->assertTrue($results[0]->loaded);
    }

    public function testEltWithInvokables()
    {
        $extractor = new class {
            public function __invoke($context) {
                return [['foo' => 'bar']];
            }
        };
        $transformer = new class {
            public function __invoke($data, $context) {
                $data['fromContext'] = $context['fromContext'];
                return (object) $data;
            }
        };
        $loader = new class {
            public function __invoke($data, $context) {
                $data->loaded = true;
                return $data;
            }
        };

        $etl = new ETL();
        $etl->setExtractor($extractor);
        $etl->setTransformer($transformer);
        $etl->setLoader($loader);

        $result = $etl->process(['fromContext' => 42]);

        $this->assertInstanceOf(\Generator::class, $result);
        $results = iterator_to_array($result);
        $this->assertCount(1, $results);
        $this->assertContainsOnly('object', $results);
        $this->assertEquals('bar', $results[0]->foo);
        $this->assertTrue($results[0]->loaded);
    }

    public function testEventDispatcher()
    {
        $extractor = function () {
            return [1, 2, 3, 4];
        };
        $transformer = function ($data) {
            if ($data === 2) throw new \RuntimeException('transformation error');
            return $data;
        };
        $loader = function ($data) {
            if ($data === 3) throw new \LogicException('loading error');
            return $data;
        };
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->method('addListener')->withConsecutive(
            ['progress', function() {}],
            ['error', function() {}]
        );
        $dispatcher->method('dispatch')->withConsecutive(
            [new GenericEvent(1, ['fromContext' => 42]), 'progress'],
            [$this->callback(function ($event) {
                return $event->getSubject() instanceof \RuntimeException;
            }), 'error'],
            [$this->callback(function ($event) {
                return $event->getSubject() instanceof \LogicException;
            }), 'error'],
            [new GenericEvent(4, ['fromContext' => 42]), 'progress'],
        );

        $etl = new ETL($extractor, $transformer, $loader);
        $etl->setDispatcher($dispatcher);
        $etl->addProgressListener(function() {});
        $etl->addErrorListener(function() {});

        $result = $etl->process(['fromContext' => 42]);

        $this->assertInstanceOf(\Generator::class, $result);
        $results = iterator_to_array($result);
        $this->assertEquals([1, 4], $results);
    }
}
