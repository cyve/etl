<?php

namespace Cyve\ETL\Load;

class PdoLoader implements LoaderInterface
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
    ) {
    }

    public function load(\Iterator $iterator): \Iterator
    {
        $pdo = new \PDO($this->dsn, $this->username, $this->password, $this->options);

        foreach ($iterator as $iteration) {
            $values = match (true) {
                is_array($iteration) => $iteration,
                is_object($iteration) => get_object_vars($iteration),
                default => throw new \InvalidArgumentException(sprintf('Argument $iterator should be a iterator of arrays or objects, iterator of %s given.', get_debug_type($iteration))),
            };

            if (!isset($stmt)) {
                $columns = array_keys($values);
                array_walk($columns, function ($column) {
                    filter_var($column, FILTER_VALIDATE_REGEXP, ['options' => [ 'regexp' => '/^[a-zA-Z0-9_]+$/']]) ?: throw new \PDOException(sprintf('Invalid column name "%s"', $column));
                });

                $stmt = $pdo->prepare("REPLACE INTO ".$pdo->quote($this->table)." (".implode(',', $columns).") VALUES (:".implode(',:', $columns).")");
            }

            try {
                $stmt->execute($values);

                yield $iteration;
            } catch (\Throwable $error) {
                yield $error;
            }
        }
    }
}
