<?php

namespace Cyve\ETL\Test\Extract;

use Cyve\ETL\Extract\CsvFileExtractor;
use PHPUnit\Framework\TestCase;

class CsvFileExtractorTest extends TestCase
{
    public function testExtract()
    {
        $extractor = new CsvFileExtractor('fixtures/users.csv');
        $results = $extractor->extract();

        $this->assertEquals([
            ['name' => 'John Doe', 'email' => 'john.doe@mail.com'],
            ['name' => 'Jane Doe', 'email' => 'jane.doe@mail.com'],
        ], iterator_to_array($results));
    }

    public function testExtractWithOptions()
    {
        $extractor = new CsvFileExtractor('fixtures/books.csv', ['separator' => ';', 'enclosure' => '\'']);
        $results = $extractor->extract();

        $this->assertEquals([
            ['name' => 'The lord of the rings', 'author' => 'J.R.R. Tolkien'],
            ['name' => 'A song of ice and fire', 'author' => 'George R.R. Martin'],
        ], iterator_to_array($results));
    }

    public function testExtractUnexistingFile()
    {
        $this->expectException(\UnexpectedValueException::class);

        new CsvFileExtractor('undefined.csv');
    }
}
