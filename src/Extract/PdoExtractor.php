<?php

namespace Cyve\ETL\Extract;

class PdoExtractor implements ExtractorInterface
{
    public function __construct(
        private string $dsn,
        private string $table,
        private ?string $username = null,
        private ?string $password = null,
        private ?array $options = null,
        private string $fetchMode = \PDO::FETCH_OBJ,
    ) {
    }

    public function extract(): \Iterator
    {
        $pdo = new \PDO($this->dsn, $this->username, $this->password, $this->options);

        yield from $pdo->query('SELECT * FROM '.$pdo->quote($this->table), $this->fetchMode);
    }
}
