<?php

namespace Isswp101\Persimmon\Cache;

use Isswp101\Persimmon\Model\IEloquent;

class RuntimeCache
{
    /**
     * Cache.
     *
     * @var array [
     *   'instance' => IEloquent,
     *   'attributes' => []
     * ]
     */
    private $cache = [];

    /**
     * Return true if cache contains this key.
     *
     * @param mixed $key
     * @return bool
     */
    public function has($key): bool
    {
        return array_key_exists($key, $this->cache);
    }

    /**
     * Return instance from cache.
     *
     * @param mixed $key
     * @return IEloquent
     */
    public function get($key)
    {
        return $this->has($key) ? $this->cache[$key]['instance'] : null;
    }

    /**
     * Return all cache keys.
     *
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->cache);
    }

    /**
     * Put instance to cache.
     *
     * @param mixed $key
     * @param IEloquent $instance
     * @param array $attributes
     * @return IEloquent
     */
    public function put($key, IEloquent $instance, array $attributes = ['*']): IEloquent
    {
        if ($attributes != ['*'] && $this->has($key)) {
            $instance = RuntimeCache::merge($this->cache[$key]['instance'], $instance, $attributes);
            $attributes = array_merge($this->cache[$key]['attributes'], $attributes);
        }
        $this->cache[$key] = [
            'instance' => $instance,
            'attributes' => $attributes
        ];
        return $instance;
    }

    /**
     * Return true if cache has already given attributes.
     *
     * @param mixed $key
     * @param array $attributes
     * @return bool
     */
    public function containsAttributes($key, array $attributes = ['*']): bool
    {
        return empty($this->getNotCachedAttributes($key, $attributes));
    }

    /**
     * Return the difference between given attributes and attributes which are already cached.
     *
     * @param mixed $key
     * @param array $attributes
     * @return array
     */
    public function getNotCachedAttributes($key, array $attributes = ['*']): array
    {
        if (!$this->has($key)) {
            return $attributes;
        }
        $cachedAttributes = $this->cache[$key]['attributes'];
        return $cachedAttributes == ['*'] ? [] : array_diff($attributes, $cachedAttributes);
    }

    /**
     * Remove an item from the cache by key.
     *
     * @param mixed $key
     * @return $this
     */
    public function forget($key)
    {
        unset($this->cache[$key]);
        return $this;
    }

    private static function merge(IEloquent $model1, IEloquent $model2, array $attributes): IEloquent
    {
        foreach ($attributes as $attribute) {
            $model1->{$attribute} = $model2->{$attribute};
        }
        return $model1;
    }
}
