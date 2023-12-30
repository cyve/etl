<?php

namespace Cyve\ETL\Load;

class PdoLoader implements LoaderInterface
{
    public function __construct(
        private string $dsn,
        private string $table,
        private ?string $username = null,
        private ?string $password = null,
        private ?array $options = null,
    ) {
    }

    public function load(\Iterator $iterator): \Iterator
    {
        $pdo = new \PDO($this->dsn, $this->username, $this->password, $this->options);

        foreach ($iterator as $iteration) {
            $values = get_object_vars($iteration);

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
