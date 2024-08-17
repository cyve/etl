<?php

namespace Cyve\ETL\Tests\Extract;

use Cyve\ETL\Extract\PdoExtractor;
use PHPUnit\Framework\TestCase;

class PdoExtractorTest extends TestCase
{
    public function testExtract()
    {
        $extractor = new PdoExtractor('sqlite:fixtures/test.db', 'users');
        $results = $extractor->extract();

        $this->assertEquals([
            (object) ['name' => 'John Doe', 'email' => 'john.doe@mail.com'],
            (object) ['name' => 'Jane Doe', 'email' => 'jane.doe@mail.com'],
        ], iterator_to_array($results));
    }

    public function testExtractUnexistingTable()
    {
        $this->expectException(\PDOException::class);

        $extractor = new PdoExtractor('sqlite:fixtures/test.db', 'undefined');
        iterator_count($extractor->extract());
    }
}
