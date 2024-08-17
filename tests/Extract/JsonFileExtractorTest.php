<?php

namespace Cyve\ETL\Tests\Extract;

use Cyve\ETL\Extract\JsonFileExtractor;
use PHPUnit\Framework\TestCase;

class JsonFileExtractorTest extends TestCase
{
    public function testExtract()
    {
        $extractor = new JsonFileExtractor('fixtures/users.json');
        $results = $extractor->extract();

        $this->assertEquals([
            (object) ['name' => 'John Doe', 'email' => 'john.doe@mail.com'],
            (object) ['name' => 'Jane Doe', 'email' => 'jane.doe@mail.com'],
        ], iterator_to_array($results));
    }

    public function testExtractUnexistingFile()
    {
        $this->expectException(\UnexpectedValueException::class);

        new JsonFileExtractor('undefined.json');
    }
}
