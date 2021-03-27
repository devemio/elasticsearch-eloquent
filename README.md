# Persimmon / Elasticsearch Eloquent

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This package allows you to interact with Elasticsearch as you interact with Eloquent models in Laravel.  
Feel free to improve the project.

## Install

Via Composer

```bash
$ composer require isswp101/elasticsearch-eloquent
```

## Usage

### Configure dependencies

> **Warning!** First of all you should create a base model and inherit from it their models.

```php
use Elasticsearch\Client;
use Isswp101\Persimmon\DAL\ElasticsearchDAL;
use Isswp101\Persimmon\ElasticsearchModel as Model;
use Isswp101\Persimmon\Event\EventEmitter;

class ElasticsearchModel extends Model
{
    public function __construct(array $attributes = [])
    {
        $dal = new ElasticsearchDAL($this, app(Client::class), app(EventEmitter::class));

        parent::__construct($dal, $attributes);
    }

    public static function createInstance()
    {
        return new static();
    }
}
```

In this example we use Laravel IoC Container to resolve `Elasticsearch\Client` dependency as `app(Client::class)`.

### Create a new model

You must override static variables `index` and `type` to determine the document path.

```php
class Product extends ElasticsearchModel
{
    protected static $_index = 'test';
    protected static $_type = 'test';

    public $name;
    public $price = 0;
}
```

Here `name` and `price` are fields which will be stored in Elasticsearch.  
> **Warning!** Don't use field names starting with underscore `$_*`, for example `$_name`.

Use the static `create()` method to create document in Elasticsearch:

```php
$product = Product::create(['id' => 3, 'name' => 'Product 3', 'price' => 30]);
```

### Save the model

```php
$product = new Product();
$product->id = 1;
$product->name = 'Product 1';
$product->price = 20;
$product->save();
```

Use `save()` method to store model data in Elasticsearch. Let's see how this looks in Elasticsearch:

```json
{
   "_index": "test",
   "_type": "test",
   "_id": "1",
   "_version": 1,
   "found": true,
   "_source": {
      "name": "Product 1",
      "price": 10,
      "id": 1,
      "user_id": null,
      "created_at": "2016-06-03 08:11:08",
      "updated_at": "2016-06-03 08:11:08"
   }
}
```

Fields `created_at` and `updated_at` were created automatically. The `user_id` field is persistent field to store user id.

### Find existing model

```php
$product = Product::find(1);
```

If you have big data in Elasticsearch you can specify certain fields to retrieve:

```php
$product = Product::find(1, ['name']);
```

In this case the `price` field equals `0` because it's populated as the default value that you specified in the model.

There are the following methods:
* `findOrFail()` returns `ModelNotFoundException` exception if no result found.
* `findOrNew()` returns a new model if no result found.

### Model cache

There is a smart model cache when you use methods like `find()`, `findOrFail()` and so on.

```php
$product = Product::find(1, ['name']);  // will be retrieved from the elasticsearch
$product = Product::find(1, ['name']);  // will be retrieved from the cache
$product = Product::find(1, ['price']); // elasticsearch
$product = Product::find(1, ['price']); // cache
$product = Product::find(1, ['name']);  // cache
```

```php
$product = Product::findOrFail(1);      // elasticsearch
$product = Product::find(1);            // cache
$product = Product::find(1, ['name']);  // cache
$product = Product::find(1, ['price']); // cache
```

### Partial update

You can use partial update to update specific fields quickly.

```php
$product = Product::find(1, ['name']);
$product->name = 'Product 3';
$product->save('name');
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
class Product extends ElasticsearchModel
{
    public static $_index = 'test';
    public static $_type = 'test';

    public $name;
    public $price = 0;

    protected function saving()
    {
        if ($this->price <= 0) {
            return false;
        }

        return true;
    }

    protected function deleting()
    {
        if (!$this->canDelete()) {
            throw new LogicException('No permissions to delete the model');
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

The `search($query)` method returns documents (default 50 items) according to the query.

```php
$products = Product::search($query);
```

The `map($query, callable $callback)` method returns all documents (default 50 items per request) according to the query.

```php
$total = Product::map([], function (Product $product) {
    // ...
});
```

The `all($query)` method returns all documents according to the query.

```php
$products = Product::all($query);
```

If `$query` is not passed the query will be as `match_all` query.

### Query Builder

```php
use Isswp101\Persimmon\QueryBuilder\QueryBuilder;

$query = new QueryBuilder();
```

Simple usage:

```php
$query = new QueryBuilder(['query' => ['match' => ['name' => 'Product']]]);
$products = Product::search($query);
```

The `match` query:

```php
$query = new QueryBuilder();
$query->match('name', 'Product');
$products = Product::search($query);    
```

The `range` query:

```php
$query = new QueryBuilder();
$query->betweenOrEquals('price', 20, 30)->greaterThan('price', 15);
$products = Product::search($query);
```

### Filters

Feel free to add your own filters.

The `TermFilter` filter:

```php
$query = new QueryBuilder();
$query->filter(new TermFilter('name', '2'));
$products = Product::search($query);
```

The `IdsFilter` filter:

```php
$query = new QueryBuilder();
$query->filter(new IdsFilter([1, 3]));
$products = Product::search($query);
```

The `RangeOrExistFilter` filter:

```php
$query = new QueryBuilder();
$query->filter(new RangeOrExistFilter('price', ['gte' => 20]));
$products = Product::search($query);
```

### Aggregations

Feel free to add your own aggregations.

```php
$query = new QueryBuilder();
$query->aggregation(new TermsAggregation('name'));
$products = Product::search($query);
$buckets = $products->getAggregation('name');
// Usage: $buckets[0]->getKey() and $buckets[0]->getCount()
```

### Parent-Child Relationship

The parent-child relationship is similar in nature to the nested model: both allow you to associate one entity with another. The difference is that, with nested objects, all entities live within the same document while, with parent-child, the parent and children are completely separate documents.

Let's create two models:

1. `PurchaseOrder` has many `PurchaseOrderLine` models
2. `PurchaseOrderLine` belongs to `PurchaseOrder` model

```php
class PurchaseOrder extends ElasticsearchModel
{
    protected static $_index = 'test_parent_child_rel';
    protected static $_type = 'orders';

    public $name;

    public function lines()
    {
        return $this->hasMany(PurchaseOrderLine::class);
    }
}

class PurchaseOrderLine extends ElasticsearchModel
{
    protected static $_index = 'test_parent_child_rel';
    protected static $_type = 'lines';
    
    protected static $_parentType = 'orders';

    public $name;

    public function po()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
```

To `save()` models you can use the following code:

```php
$po = new PurchaseOrder(['id' => 1, 'name' => 'PO1']);
$line = new PurchaseOrderLine(['id' => 1, 'name' => 'Line1']);

$po->save();
$po->lines()->save($line);
```

You can use the `associate()` method to save models:

```php
$po = new PurchaseOrder(['id' => 1, 'name' => 'PO1']);
$line = new PurchaseOrderLine(['id' => 1, 'name' => 'Line1']);

$po->save();
$line->po()->associate($po);
$line->save();
```

To get parent you can use the following code:

```php
$line = PurchaseOrderLine::findWithParentId(1, 1);
$po = $line->po()->get();
```

To get children you can use the following code:

```php
$po = PurchaseOrder::findOrFail(1);
$line = $po->lines()->find(1); // by id
$lines = $po->lines()->get(); // all children
```

### Inner hits

The parent/child and nested features allow the return of documents that have matches in a different scope. In the parent/child case, parent document are returned based on matches in child documents or child document are returned based on matches in parent documents. In the nested case, documents are returned based on matches in nested inner objects.

You can get parent model using only one request with `InnerHitsFilter` filter:

```php
$query = new QueryBuilder();
$query->filter(new InnerHitsFilter(PurchaseOrderLine::getParentType()));
$line = PurchaseOrderLine::search($query)->first();
$po = $line->po()->get(); // will be retrieved from inner_hits cache
```


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
