<?php

namespace Cyve\ETL\Extract;

class PdoExtractor implements ExtractorInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        private string $dsn,
        private string $table,
        private ?string $username = null,
        private ?string $password = null,
        private array $options = [],
        private int $fetchMode = \PDO::FETCH_OBJ,
    ) {
    }

    public function extract(): \Iterator
    {
        $pdo = new \PDO($this->dsn, $this->username, $this->password, $this->options);

        $results = $pdo->query('SELECT * FROM '.$pdo->quote($this->table), $this->fetchMode);

        if (is_iterable($results)) {
            yield from $results;
        }
    }
}
