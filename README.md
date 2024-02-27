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

### Use an event dispatcher
Use the 4th argument of the constructor to inject an instance of `Psr\EventDispatcher\EventDispatcherInterface`.
At each step of each iteration, the ETL will dispatch an event containing the result if the operation succeeded, or an exception if the operation failed
```php
$eventDispatcher = new Symfony\Component\EventDispatcher\EventDispatcher();

$etl = new ETL(
    $extractor,
    $transformer,
    $loader,
    $eventDispatcher,
);
$etl->start();
```

#### Example: progress bar
```
$eventDispatcher = new Symfony\Component\EventDispatcher\EventDispatcher();
$eventDispatcher->addListener(LoadSuccessEvent::class, function (LoadSuccessEvent $event): void {
    echo '#';
});
$eventDispatcher->addListener(LoadFailureEvent::class, function (LoadFailureEvent $event): void {
    echo 'E';
});
```
