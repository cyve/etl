<?php

namespace Cyve\ETL\Tests\Load;

use Cyve\ETL\Load\JsonFileLoader;
use PHPUnit\Framework\TestCase;

class JsonFileLoaderTest extends TestCase
{
    public function testLoad()
    {
        $loader = new JsonFileLoader($filename = sys_get_temp_dir().'/'.uniqid());
        $results = $loader->load(new \ArrayIterator([
            (object) ['name' => 'John Doe', 'email' => 'john.doe@mail.com'],
            (object) ['name' => 'Jane Doe', 'email' => 'jane.doe@mail.com'],
        ]));
        $this->assertEquals([
            (object) ['name' => 'John Doe', 'email' => 'john.doe@mail.com'],
            (object) ['name' => 'Jane Doe', 'email' => 'jane.doe@mail.com'],
        ], iterator_to_array($results));

        $this->assertJsonFileEqualsJsonFile('fixtures/users.json', $filename);
    }
}
