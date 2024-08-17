<?php

namespace Cyve\ETL\Tests\Load;

use Cyve\ETL\Load\PdoLoader;
use PHPUnit\Framework\TestCase;

class PdoLoaderTest extends TestCase
{
    protected string $databaseFilename;

    protected function setUp(): void
    {
        $this->databaseFilename = sprintf('%s/%s.db', sys_get_temp_dir(), uniqid());
        copy('fixtures/test.db', $this->databaseFilename);
    }

    public function testLoad()
    {
        $Loader = new PdoLoader('sqlite:'.$this->databaseFilename, 'users');
        $results = $Loader->load(new \ArrayIterator([
            (object) ['name' => 'John X. Doe', 'email' => 'john.doe@mail.com'],
            (object) ['name' => 'Administrator', 'email' => 'admin@mail.com'],
        ]));
        $this->assertEquals([
            (object) ['name' => 'John X. Doe', 'email' => 'john.doe@mail.com'],
            (object) ['name' => 'Administrator', 'email' => 'admin@mail.com'],
        ], iterator_to_array($results));

        $pdo = new \PDO('sqlite:'.$this->databaseFilename);
        $users = $pdo->query('SELECT * FROM users')->fetchAll(\PDO::FETCH_ASSOC);
        $this->assertCount(3, $users);
        $this->assertContains(['name' => 'John X. Doe', 'email' => 'john.doe@mail.com'], $users);
        $this->assertContains(['name' => 'Administrator', 'email' => 'admin@mail.com'], $users);
    }
}
