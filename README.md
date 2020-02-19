# RuleEngineBundle

## Installation:

With [Composer](http://packagist.org):
```sh
composer require cyve/etl
```

## Usage (CSV import)

```php
// src/Import/Extractor.php
class Extractor
{
    public function __invoke(array $context)
    {
        if($handle = fopen($context['filepath'], 'r')){
            $keys = fgetcsv($handle);
            while ($values = fgetcsv($handle)){
                yield array_combine($keys, $values);
            }
            fclose($handle);
        }
    }
}
```

```php
// src/Import/Transformer.php
class Transformer
{
    public function __invoke($data, array $context)
    {
        $entity = new Entity();
        $entity->setName($data['name']);
        return $entity;
    }
}
```

```php
// src/Import/Loader.php
class Loader
{
    public function __invoke($data, array $context)
    {
        // save $entity in the database
    }
}
```

```php
// src/Import/EventListener.php
class EventListener
{
    public function __invoke($event)
    {
        // handle event
    }
}
```

```php
$etl = new Cyve\ETL\ETL(new Extractor(), new Transformer(), new Loader());
$etl->addErrorListener(new EventListener());
$etl->addProgressListener(new EventListener());
$etl->process(['filepath' => '/path/to/csv']);
```

#### Usage with closures

```php
$etl = new Cyve\ETL\ETL();
$etl->setExtractor(function ($context) {
    // extract
});
$etl->setTransformer(function ($data, $context) {
   // transform
});
$etl->setLoader(function ($data, $context) {
  // load
});
$etl->addErrorListener(function ($event) {
   // handle error event
});
$etl->process();
```
