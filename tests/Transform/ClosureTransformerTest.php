<?php

namespace Cyve\ETL\Test\Transform;

use Cyve\ETL\Transform\ClosureTransformer;
use PHPUnit\Framework\TestCase;

class ClosureTransformerTest extends TestCase
{
    public function testTransform()
    {
        $transformer = new ClosureTransformer(fn ($iteration) => (object) $iteration);

        $results = $transformer->transform(new \ArrayIterator([
            ['name' => 'John Doe', 'email' => 'john.doe@mail.com'],
            ['name' => 'Jane Doe', 'email' => 'jane.doe@mail.com'],
        ]));

        $this->assertEquals([
            (object) ['name' => 'John Doe', 'email' => 'john.doe@mail.com'],
            (object) ['name' => 'Jane Doe', 'email' => 'jane.doe@mail.com'],
        ], iterator_to_array($results));
    }

    public function testTransformWithError()
    {
        $error = new \RuntimeException('An error occurs');
        $transformer = new ClosureTransformer(fn ($iteration) => throw $error);

        $results = $transformer->transform(new \ArrayIterator([
            ['name' => 'John Doe', 'email' => 'john.doe@mail.com'],
        ]));

        $this->assertEquals([$error], iterator_to_array($results));
    }
}
