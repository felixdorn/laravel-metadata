<?php

use Felix\Metadata\HasMetadata;
use Felix\Metadata\Meta;
use Felix\Metadata\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\MockObject\MockObject;
use function PHPUnit\Framework\once;

uses(TestCase::class);

/**
 * @method static TestModel create(array $attributes = [])
 */
class TestModel extends Model
{
    use HasMetadata;

    protected $guarded = [];
}

it('can get metadata with a prefix', function () {
    $model = TestModel::create();
    $meta = $model->meta
        ->prefix('hello.')
        ->set('a', 'b');

    assertEquals('b', $meta->get('a'));
    assertEquals('default', $meta->get('b', 'default'));
});

it('can get the meta class from a model', function () {
    $model = new TestModel();

    assertInstanceOf(Meta::class, $model->meta);
});

it('can save metadata', function () {
    /** @var TestModel|MockObject $model */
    $model = $this->createPartialMock(TestModel::class, ['update']);

    $model->expects(once())
        ->method('update')
        ->with([
            'metadata' => json_encode([
                'hello' => 'world',
            ], JSON_THROW_ON_ERROR),
        ])->willReturn(true);

    $model->meta->set('hello', 'world');
});

it('can verify if a metadata exists', function () {
    $model = TestModel::create();

    $model->meta->set('hello', 'world');

    assertTrue($model->meta->has('hello'));
    assertFalse($model->meta->has('world'));
});

it('can prefix metadata', function () {
    $model = TestModel::create();

    $model->meta->prefix('current')
        ->set('hello', 'world');

    assertEquals([
        'current' => [
            'hello' => 'world',
        ],
    ], $model->meta->all());
});

it('can remove the prefix', function () {
    $model = TestModel::create();
    $model->meta->prefix('current')
        ->set('hello', 'world')
        ->unprefix()
        ->set('hello', 'world');

    assertEquals([
        'current' => [
            'hello' => 'world',
        ],
        'hello' => 'world',
    ], $model->meta->all());
});

it('can use an object as a prefix', function () {
    $model = TestModel::create();
    $model->meta->prefixWith(TestModel::create())
        ->set('hello', 'world');

    assertEquals([
        // The model id
        2 => [
            'hello' => 'world',
        ],
    ], $model->meta->all());
});

class Prefixer
{
    public function getIdentifier(): string
    {
        return 'hello';
    }
}

it('can use an object with getIdentifier() as prefix', function () {
    $model = TestModel::create();

    $model->meta->prefixWith(new Prefixer)
        ->set('hello', 'world');

    assertEquals([
        // 1 is the model id
        'hello' => [
            'hello' => 'world',
        ],
    ], $model->meta->all());
});

it('can get metadata', function () {
    $model = TestModel::create();
    $model->meta->set('a', 'b');

    assertEquals('b', $model->meta->get('a'));
    assertEquals('default', $model->meta->get('b', 'default'));
});
it('can delete metadata', function () {
    $model = TestModel::create();
    $model->meta->set('a', 'b');

    assertEquals('b', $model->meta->get('a'));
    $model->meta->delete('a');

    assertEquals('default', $model->meta->get('a', 'default'));
});

it('can reset the metadata', function () {
    $model = TestModel::create();
    $model->meta->set('a', 'b');

    assertEquals([
        'a' => 'b',
    ], $model->meta->all());

    $model->meta->reset([
        'c' => 'd',
    ]);
    assertEquals([
        'c' => 'd',
    ], $model->meta->all());
});

it('can get the underlying model', function () {
    $model = TestModel::create();

    assertSame($model, $model->meta->getModel());
});
