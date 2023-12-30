<?php

namespace Cyve\ETL\Test\Load;

use Cyve\ETL\Load\CsvFileLoader;
use PHPUnit\Framework\TestCase;

class CsvFileLoaderTest extends TestCase
{
    public function testLoad()
    {
        $filename = sys_get_temp_dir().'/'.uniqid();
        $loader = new CsvFileLoader($filename);
        $results = $loader->load(new \ArrayIterator([
            (object) ['name' => 'John Doe', 'email' => 'john.doe@mail.com'],
            (object) ['name' => 'Jane Doe', 'email' => 'jane.doe@mail.com'],
        ]));
        $this->assertEquals([
            (object) ['name' => 'John Doe', 'email' => 'john.doe@mail.com'],
            (object) ['name' => 'Jane Doe', 'email' => 'jane.doe@mail.com'],
        ], iterator_to_array($results));

        $this->assertFileEquals('fixtures/users.csv', $filename);
    }

    public function testLoadWithOptions()
    {
        $filename = sys_get_temp_dir().'/'.uniqid();

        $loader = new CsvFileLoader($filename, ['separator' => ';', 'enclosure' => '\'']);
        $results = $loader->load(new \ArrayIterator([
            (object) ['name' => 'The lord of the rings', 'author' => 'J.R.R. Tolkien'],
            (object) ['name' => 'A song of ice and fire', 'author' => 'George R.R. Martin'],
        ]));
        $this->assertEquals([
            (object) ['name' => 'The lord of the rings', 'author' => 'J.R.R. Tolkien'],
            (object) ['name' => 'A song of ice and fire', 'author' => 'George R.R. Martin'],
        ], iterator_to_array($results));

        $this->assertFileEquals('fixtures/books.csv', $filename);
    }
}
