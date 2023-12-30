# Extract Transform Load

## Installation:

With [Composer](http://packagist.org):
```sh
composer require cyve/etl
```

## Usage

### Use case: convert CSV to JSON
```php
$etl = new ETL(
    new CsvFileExtractor('users.csv'),
    new NullTransformer(),
    new JsonFileLoader('users.json')
);
$etl->start();
```

### Use a logger
Use the method `setLogger()` to inject an instance of `Psr\Log\LoggerInterface`.
At each step of each iteration, the ETL will call `LoggerInterface::info()` if the operation succeeded, or `LoggerInterface::error()` if the operation failed
```php
$logger = new Monolog\Logger();

$etl = new ETL(
    $extractor,
    $transformer,
    $loader,
);
$etl->setLogger($logger);
$etl->start();
```

#### Example: progress bar
```
$logger = new class () implements LoggerInterface {
    public function error(string $message, array $context = []): void {
        echo 'E';
    }
    public function info(string $message, array $context = []): void {
        echo '#';
    }
};
```