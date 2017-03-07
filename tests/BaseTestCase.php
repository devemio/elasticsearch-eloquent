<?php

namespace Isswp101\Persimmon\Test;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Orchestra\Testbench\TestCase;

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
        $user = env('ELASTICSEARCH_AUTH_USER', '');
        $password = env('ELASTICSEARCH_AUTH_PASS', '');
        $host = env('ELASTICSEARCH_HOSTS', '');

        if ($user && $password) {
            $host = $user . ':' . $password . '@' . $host;
        }

        $this->es = ClientBuilder::create()->setHosts([$host])->build();

        app()->singleton(Client::class, function () {
            return $this->es;
        });
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
