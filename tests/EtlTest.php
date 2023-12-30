<?php

namespace Cyve\ETL\Test;

use Cyve\ETL\ETL;
use Cyve\ETL\Extract\CsvFileExtractor;
use Cyve\ETL\Extract\ExtractorInterface;
use Cyve\ETL\Extract\JsonFileExtractor;
use Cyve\ETL\Extract\PdoExtractor;
use Cyve\ETL\Load\CsvFileLoader;
use Cyve\ETL\Load\JsonFileLoader;
use Cyve\ETL\Load\LoaderInterface;
use Cyve\ETL\Load\PdoLoader;
use Cyve\ETL\Transform\NullTransformer;
use Cyve\ETL\Transform\TransformerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class EtlTest extends TestCase
{
    public function testConvertCsvToJson()
    {
        $etl = new ETL(
            new CsvFileExtractor('fixtures/users.csv'),
            new NullTransformer(),
            new JsonFileLoader($filename = sys_get_temp_dir().'/'.uniqid()),
        );
        $etl->start();

        $this->assertJsonFileEqualsJsonFile('fixtures/users.json', $filename);
    }

    public function testConvertJsonToPdo()
    {
        $pdo = new \PDO($dsn = sprintf('sqlite:%s/%s.db', sys_get_temp_dir(), uniqid()));
        $pdo->exec('CREATE TABLE users (name VARCHAR(255), email VARCHAR(255))');

        $etl = new ETL(
            new JsonFileExtractor('fixtures/users.json'),
            new NullTransformer(),
            new PdoLoader($dsn, 'users'),
        );
        $etl->start();

        $users = $pdo->query('SELECT * FROM users')->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertCount(2, $users);
        $this->assertContains(['name' => 'John Doe', 'email' => 'john.doe@mail.com'], $users);
        $this->assertContains(['name' => 'Jane Doe', 'email' => 'jane.doe@mail.com'], $users);
    }

    public function testConvertPdoToCsv()
    {
        $etl = new ETL(
            new PdoExtractor('sqlite:fixtures/test.db', 'users'),
            new NullTransformer(),
            new CsvFileLoader($filename = sys_get_temp_dir().'/'.uniqid()),
        );
        $etl->start();

        $this->assertFileEquals('fixtures/users.csv', $filename);
    }

    public function testLogger()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->exactly(6))->method('info');
        $logger->expects($this->exactly(3))->method('error');

        $etl = new ETL(
            new class () implements ExtractorInterface {
                public function extract(): \Iterator
                {
                    yield 1;
                    yield 2;
                    yield 3;
                    yield new \RuntimeException('Extraction error');
                }
            },
            new class () implements TransformerInterface {
                public function transform($iterator): \Iterator
                {
                    foreach ($iterator as $iteration) {
                        yield $iteration < 3 ? $iteration : new \RuntimeException('Transformation error');
                    }
                }
            },
            new class () implements LoaderInterface {
                public function load($iterator): \Iterator
                {
                    foreach ($iterator as $iteration) {
                        yield $iteration < 2 ? $iteration : new \RuntimeException('Loading error');
                    }
                }
            }
        );
        $etl->setLogger($logger);
        $etl->start();
    }
}
