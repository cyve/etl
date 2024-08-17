<?php

namespace Cyve\ETL\Tests\Transform;

use Cyve\ETL\Transform\NullTransformer;
use PHPUnit\Framework\TestCase;

class NullTransformerTest extends TestCase
{
    public function testTransform()
    {
        $transformer = new NullTransformer();

        $results = $transformer->transform(new \ArrayIterator([
            ['name' => 'John Doe', 'email' => 'john.doe@mail.com'],
            ['name' => 'Jane Doe', 'email' => 'jane.doe@mail.com'],
        ]));

        $this->assertEquals([
            ['name' => 'John Doe', 'email' => 'john.doe@mail.com'],
            ['name' => 'Jane Doe', 'email' => 'jane.doe@mail.com'],
        ], iterator_to_array($results));
    }
}
