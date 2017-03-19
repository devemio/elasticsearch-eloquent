<?php

class CacheDecorator
{
    private $repository;
    private $cacheRepository;
    private $allColumns;

    public function __construct(IRepository $repository, IRepository $cacheRepository)
    {
        $this->repository = $repository;
        $this->cacheRepository = $cacheRepository;
        $this->allColumns = new Collection();
    }

    private function getHash(string $id, string $class): string
    {
        return $class . '@' . $id;
    }

    public function find(string $id, string $class, array $columns = []): ?Storable
    {
        $hash = $this->getHash($class, $id);
        $model = $this->cacheRepository->find($id, $class, $columns);
        if ($model) {
            if ($columns) {
                // If you wanna specified columns...
                if (!array_diff($columns, array_keys($model->toArray()))) {
                    return $model;
                }
            } else {
                // If you wanna all columns...
                if ($this->allColumns->get($hash, false)) {
                    return $model;
                }
            }
        }
        $model = $this->repository->find($id, $class, $columns);
        $this->cacheRepository->update($model);
        if (!$columns) {
            $this->allColumns->put($hash, true);
        }
        return $model;
    }
}