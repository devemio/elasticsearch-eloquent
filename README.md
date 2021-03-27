# Elasticsearch Eloquent 2.x

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]]()
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This package allows you to interact with Elasticsearch as you interact with Eloquent models in Laravel.

## Requirements

- PHP >= 8.0
- Elasticsearch >= 7.0

## Install

Via Composer

```bash
$ composer require isswp101/elasticsearch-eloquent
```

## Usage

### Create a new model

You should override `index` and `type` properties to determine the document path.

```php
use Isswp101\Persimmon\Models\BaseElasticsearchModel;
use Isswp101\Persimmon\Persistence\Persistence;
use Isswp101\Persimmon\Contracts\PersistenceContract;

class Product extends BaseElasticsearchModel
{
    protected string $index = 'index';
    protected string|null $type = 'type'; // optional

    // If you have a pre-configured Elasticsearch client you can pass it here (optional)
    public function createPersistence(): PersistenceContract
    {
        return new Persistence($client);
    }
}
```

Use the static `create()` method to create the document in Elasticsearch:

```php
$product = Product::create([
    'id' => 1, 
    'name' => 'Product',
    'price' => 10
]);
```

### Save the model

```php
$product = new Product();
$product->id = 1;
$product->name = 'Product';
$product->price = 10;
$product->save();
```

Use `save()` method to store model data in Elasticsearch. Let's see how this looks in Elasticsearch:

```json
{
   "_index": "index",
   "_type": "type",
   "_id": "1",
   "_version": 1,
   "found": true,
   "_source": {
      "id": 1,
      "name": "Product",
      "price": 10,
      "created_at": "2021-03-27T11:24:15+00:00",
      "updated_at": "2021-03-27T11:24:15+00:00"
   }
}
```

Fields `created_at` and `updated_at` were created automatically.

### Find existing model

```php
$product = Product::find(1);
```

If you have big data in Elasticsearch you can specify certain fields to retrieve:

```php
$product = Product::find(1, ['name']);
```

There are the following methods:
* `findOrFail()` returns `ModelNotFoundException` exception if no result found.

### Cache

There is a smart model cache when you use methods like `find()`, `findOrFail()` and so on.

```php
$product = Product::find(1, ['name']);  // from elasticsearch
$product = Product::find(1, ['name']);  // from cache
$product = Product::find(1, ['price']); // from elasticsearch
$product = Product::find(1, ['price']); // from cache
$product = Product::find(1, ['name']);  // from cache
```

```php
$product = Product::find(1);            // from elasticsearch
$product = Product::find(1);            // from cache
$product = Product::find(1, ['name']);  // from cache
$product = Product::find(1, ['price']); // from cache
```

### Partial update

You can use the partial update to update specific fields quickly.

```php
$product = Product::find(1, ['name']);
$product->name = 'Name';
$product->save(['name']);
```

### Delete models

```php
$product = Product::find(1);
$product->delete();
```

You can use the static method:

```php
Product::destroy(1);
```

### Model events

Out of the box you are provided with a simple implementation of events.  
You can override the following methods to define events:

* `saving()` is called before saving, updating, creating the model
* `saved()` is called after saving, updating, creating the model
* `deleting()` is called before deleting the model
* `deleted()` is called after deleting the model
* `searching()` is called after searching models
* `searched()` is called after searching models

For example:

```php
use Isswp101\Persimmon\Models\BaseElasticsearchModel;

class Product extends BaseElasticsearchModel
{
    protected function saving(): bool
    {
        // Disable update if it's free
        return $this->price <= 0;
    }

    protected function deleting(): bool
    {
        if ($this->user_id != 1) {
            throw new DomainException('No permissions to delete this model');
        }

        return true;
    }
}
```

### Basic search

There are helpers to search documents:

The `first($query)` method returns the first document according to the query or `null`.  

```php
$product = Product::first($query);
```

The `firstOrFail($query)` method returns `ModelNotFoundException` exception if `first($query)` returns `null`.

```php
$product = Product::firstOrFail($query);
```

The `search($query)` method returns documents according to the query.

```php
$products = Product::search($query);
```

The `all($query)` method returns all documents (default 50 items per request) according to the query.

```php
$products = Product::all($query);
```

If `$query` is not passed the query will be as `match_all` query.

### Query Builder

Consider using these packages:

- [ElasticsearchDSL](https://github.com/ongr-io/ElasticsearchDSL)


## Testing

``` bash
$ composer test
```


## License

The MIT License (MIT).

[ico-version]: https://img.shields.io/packagist/v/isswp101/elasticsearch-eloquent.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/isswp101/elasticsearch-eloquent/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/isswp101/elasticsearch-eloquent.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/isswp101/elasticsearch-eloquent.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/isswp101/elasticsearch-eloquent.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/isswp101/elasticsearch-eloquent
[link-travis]: https://travis-ci.org/isswp101/elasticsearch-eloquent
[link-scrutinizer]: https://scrutinizer-ci.com/g/isswp101/elasticsearch-eloquent/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/isswp101/elasticsearch-eloquent
[link-downloads]: https://packagist.org/packages/isswp101/elasticsearch-eloquent
[link-author]: https://github.com/devemio
