<?php

namespace Cyve\ETL\Test;

use Cyve\ETL\Context;
use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
{
    public function testContext()
    {
        $context = new Context(['foo' => 'foo']);
        $this->assertTrue($context->has('foo'));
        $this->assertEquals('foo', $context->get('foo'));
        $this->assertFalse($context->has('bar'));
        $this->assertEmpty($context->get('bar'));
        $this->assertInstanceOf(Context::class, $context->set('bar', 'bar'));
        $this->assertEquals('bar', $context->get('bar'));
        $this->assertIsArray($context->all());
        $this->assertEquals(['foo' => 'foo', 'bar' => 'bar'], $context->all());
        $this->assertInstanceOf(Context::class, $context->addError(new \Exception('error')));
        $this->assertIsArray($context->getErrors());
        $this->assertContainsOnlyInstancesOf(\Exception::class, $context->getErrors());
    }

    public function testContextArrayAccess()
    {
        $context = new Context();
        $this->assertEquals('foo', $context['foo'] = 'foo');
        $this->assertTrue(isset($context['foo']));
        $this->assertEquals('foo', $context['foo']);
        unset($context['foo']);
        $this->assertFalse(isset($context['foo']));
    }

    public function testContextFromObjectData()
    {
        $context = new Context((object) ['foo' => 'foo']);
        $this->assertEquals('foo', $context->get('foo'));
    }

    public function testContextFromInvalidData()
    {
        $this->expectException(\InvalidArgumentException::class);

        new Context('foo');
    }
}
