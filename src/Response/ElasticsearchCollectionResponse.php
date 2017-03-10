<?php

namespace Isswp101\Persimmon\Response;

use Isswp101\Persimmon\Aggregations\Bucket;
use Isswp101\Persimmon\Aggregations\BucketCollection;
use Isswp101\Persimmon\Collection\Collection;

class ElasticsearchCollectionResponse
{
    private $response;
    private $aggregations;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function getTook(): int
    {
        return $this->response['took'] ?? 0;
    }

    public function isTimedOut(): bool
    {
        return $this->response['timed_out'] ?? false;
    }

    public function getTotalShards(): int
    {
        return $this->response['_shards']['total'] ?? 0;
    }

    public function getSuccessfulShardsCount(): int
    {
        return $this->response['_shards']['successful'] ?? 0;
    }

    public function getFailedShardsCount(): int
    {
        return $this->response['_shards']['failed'] ?? 0;
    }

    public function getTotal(): int
    {
        return $this->response['hits']['total'] ?? 0;
    }

    public function getMaxScore(): int
    {
        return $this->response['hits']['max_score'] ?? 0;
    }

    public function hits(): array
    {
        return $this->response['hits']['hits'] ?? [];
    }

    public function getAggregationBuckets(): BucketCollection
    {
        if ($this->aggregations != null) {
            return $this->aggregations;
        }
        $this->aggregations = new BucketCollection();
        foreach ($this->response['aggregations'] ?? [] as $name => $aggregation) {
            $buckets = new Collection();
            foreach ($aggregation['buckets'] ?? [] as $bucket) {
                $buckets->put($bucket['key'], new Bucket($bucket['key'], $bucket['doc_count']));
            }
            $this->aggregations->put($name, $buckets);
        }
        return $this->aggregations;
    }
}