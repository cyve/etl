# RuleEngineBundle

## Installation:

With [Composer](http://packagist.org):
```sh
composer require cyve/etl
```

## Usage

```php
$etl = new Cyve\ETL\ELT();
$etl->setExtractor(new Extractor())
    ->setTransformer(new Transformer())
    ->setLoader(new Loader())
    ->setContext(new Context())
    ->process();
```
