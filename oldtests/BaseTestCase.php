<?php

namespace Isswp101\Persimmon\Test;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Orchestra\Testbench\TestCase;
use Shift31\LaravelElasticsearch\ElasticsearchServiceProvider;

class BaseTestCase extends TestCase
{
    /**
     * @var Client
     */
    protected $es;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->loadDotenv();

        parent::setUp();

        $this->es = app(Client::class);
    }

    /**
     * Load Dotenv.
     */
    protected function loadDotenv()
    {
        $dotenv = new Dotenv(__DIR__);
        try {
            $dotenv->load();
        } catch (InvalidPathException $e) {
            // It's workaround for Travis CI
        }
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $host = env('ELASTICSEARCH_AUTH_USER', '') . ':' .
            env('ELASTICSEARCH_AUTH_PASS', '') . '@' .
            env('ELASTICSEARCH_HOSTS', '');

        $app['config']->set('elasticsearch.hosts', [$host]);

        $elasticsearchServiceProvider = new ElasticsearchServiceProvider($app);
        $elasticsearchServiceProvider->register();
    }

    /**
     * Sleep.
     *
     * @param int $seconds
     */
    protected function sleep($seconds = 1)
    {
        sleep($seconds);
    }

    /**
     * Delete index.
     *
     * @param mixed $index
     */
    protected function deleteIndex($index)
    {
        try {
            $this->es->indices()->delete(['index' => $index]);
        } catch (Missing404Exception $e) {
        }
    }
}
