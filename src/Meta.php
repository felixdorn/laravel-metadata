<?php


namespace Felix\Metadata;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Meta
{
    protected string $prefix = '';
    private Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return Arr::get($this->all(), $this->prefix . $key, $default);
    }

    public function all(): array
    {
        return $this->model->metadata !== null ?
            json_decode($this->model->metadata, true, 512, JSON_THROW_ON_ERROR) :
            [];
    }

    /**
     * @param array|string $keys
     * @return bool
     */
    public function has($keys): bool
    {
        return Arr::has($this->all(), array_map(fn($key) => $this->prefix . $key, Arr::wrap($keys)));
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set(string $key, $value): self
    {
        $_ = $this->all();
        Arr::set($_, $this->prefix . $key, $value);
        $this->model->update([
            'metadata' => json_encode(
                $_,
                JSON_THROW_ON_ERROR
            )
        ]);

        return $this;
    }

    public function prefixWith(object $model): self
    {
        /**
         * This might seems really opinionated and that's right, it's opinionated.
         * Here's the why: I extracted this package from a big application where the meta was
         * always prefixed by an object that has a getIdentifier method.
         */
        if (method_exists($model, 'getIdentifier')) {
            return $this->prefix($model->getIdentifier());
        }

        return $this->prefix($model->id);
    }

    public function prefix(string $prefix): self
    {
        $this->prefix = str_ends_with($prefix, '.') ? $prefix : "{$prefix}.";

        return $this;
    }

    /**
     * @param array|string $keys
     * @return $this
     */
    public function delete($keys): self
    {
        $_ = $this->all();
        Arr::forget($_, array_map(fn($key) => $this->prefix . $key, Arr::wrap($keys)));

        return $this->reset($_);
    }

    public function reset(array $with = []): self
    {
        $this->model->update([
            'metadata' => json_encode($with, JSON_THROW_ON_ERROR)
        ]);

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
}
