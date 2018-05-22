# RuleEngineBundle

## Installation:

With [Composer](http://packagist.org):
```sh
composer require cyve/etl
```

## Usage (CSV import)

```php
// src/Import/Extractor.php
class Extractor implements Cyve\ETL\ExtractorInterface
{
    public function extract(ContextInterface $context)
    {
        if($handle = fopen($context->get('filepath'), 'r')){
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
class Transformer implements Cyve\ETL\TransformerInterface
{
    public function transform($data, ContextInterface $context)
    {
        $entity = new Entity();
        $entity->setName($data['name']);
        return $entity;
    }
}
```

```php
// src/Import/Loader.php
class Loader implements Cyve\ETL\LoaderInterface
{
    public function load($data, ContextInterface $context)
    {
        // save $entity in the database
    }
}
```

```php
$etl = new (Cyve\ETL\ELT())
    ->setExtractor(new Extractor())
    ->setTransformer(new Transformer())
    ->setLoader(new Loader())
    ->setContext(new Context(['filepath' => '/path/to/csv']))
    ->process();
```
