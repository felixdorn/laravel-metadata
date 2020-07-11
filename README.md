<p align="center">
        <h1 align="center">
            Laravel Metadata
        </h1>
    <p align="center">
        <img src="https://github.com/felixdorn/laravel-metadata/workflows/CI/badge.svg?branch=master" alt="CI" />
       <img src="https://github.styleci.io/repos/278902830/shield?branch=master&style=flat" alt="StyleCI">
       <a href="https://codecov.io/gh/felixdorn/laravel-metadata">
         <img src="https://codecov.io/gh/felixdorn/laravel-metadata/branch/master/graph/badge.svg" />
       </a>
        <img src="https://img.shields.io/packagist/l/felixdorn/laravel-metadata" alt="License" />
        <img src="https://img.shields.io/packagist/v/felixdorn/laravel-metadata" alt="Last Version" />
    </p>
</p>

Attach metadata to a model with one trait.

## Getting started
You can install the package via composer, if you don't have composer, you can download it [here](https://getcomposer.org):

```bash
composer require felixdorn/laravel-metadata
```
Or by adding a requirement in your `composer.json` :
```json
{
    "require": {
        "felixdorn/laravel-metadata": "dev-master"
    }
}
```

## Usage

```php
use Felix\Metadata\HasMetadata;
use Illuminate\Database\Eloquent\Model;

class User extends Model {
    use HasMetadata;
}

$model = User::create();

$model->meta->all();
$model->meta
    ->prefix('current.')
    ->set('hello', 'world');

$model->meta->get('hello.world');
$model->meta->set('new', true);
$model->meta->get('new'); // Prefixes are not persistent

$model->meta
    ->prefix('past.')
    ->unprefix()
    ->set('hello', 'world')
    ->get('hello'); // returns world

$model->meta->has('something'); // returns false
$model->meta->delete('this');
$model->meta->reset(); 
// removes all the metadata
// if you pass an array, it will be used to initialize the new metadata

$model->meta->prefixWith($model);

// Prefix with will use a getIdentifier method to find a prefix
// If there is not such a method, the prefix will be the id property of the given object

$model->meta->getModel(); // returns the parent model
// useful as your metadata will often be shared across multiple objects// 
// that might want a convenient want to retrieve the model
```

The `Meta` class implements Countable, IteratorAggregate, ArrayAccess. And all __set, __get, __isset, __unset methods.



## Testing
``` bash
make testing
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email hi@felixdorn.fr instead of using the issue tracker.

## Credits

- [Félix Dorn](https://github.com/felixdorn)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
