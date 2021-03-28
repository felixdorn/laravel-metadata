<?php

namespace Felix\Metadata;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use IteratorAggregate;

class Meta implements Countable, IteratorAggregate, ArrayAccess
{
    protected string $prefix = '';
    private Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function prefixWith(object $model): self
    {
        return $this->prefix($model->getKeyName());
    }

    public function prefix(string $prefix): self
    {
        $this->prefix = str_ends_with($prefix, '.') ? $prefix : "{$prefix}.";

        return $this;
    }

    public function unprefix(): self
    {
        $this->prefix = '';

        return $this;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->all());
    }

    public function all(): array
    {
        return $this->model->metadata !== null ?
            json_decode($this->model->metadata, true, 512, JSON_THROW_ON_ERROR) :
            [];
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->all());
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return Arr::get($this->all(), $this->prefix.$key, $default);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set(string $key, $value): self
    {
        $_ = $this->all();
        Arr::set($_, $this->prefix.$key, $value);
        $this->model->update([
            'metadata' => json_encode(
                $_,
                JSON_THROW_ON_ERROR
            ),
        ]);

        return $this;
    }

    public function update(array $metadata): self
    {
        $this->model->update([
            'metadata' => json_encode(array_merge($this->all(), $metadata), JSON_THROW_ON_ERROR),
        ]);

        return $this;
    }

    public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * @param array|string $keys
     * @return bool
     */
    public function has($keys): bool
    {
        return Arr::has($this->all(), array_map(fn ($key) => $this->prefix.$key, Arr::wrap($keys)));
    }

    public function __unset($name)
    {
        return $this->delete($name);
    }

    /**
     * @param array|string $keys
     * @return $this
     */
    public function delete($keys): self
    {
        $_ = $this->all();
        Arr::forget($_, array_map(fn ($key) => $this->prefix.$key, Arr::wrap($keys)));

        return $this->reset($_);
    }

    public function reset(array $with = []): self
    {
        $this->model->update([
            'metadata' => json_encode($with, JSON_THROW_ON_ERROR),
        ]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        return $this->delete($offset);
    }
}
