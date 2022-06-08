<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HttpClientService
{
    public $client;

    private $monologLogger;

    protected $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ];

    public function __construct(LoggerInterface $monologLogger)
    {
        $this->monologLogger = $monologLogger;
    }

    public function setLogger(LoggerInterface $monologLogger)
    {
        $this->monologLogger = $monologLogger;

        return $this;
    }

    public function getHttpClient()
    {
        $this->client = new CurlHttpClient();

        return $this;
    }

    public function sendPostRequest(string $requestUrl, array $data, array $options = [])
    {
        $options['headers'] = (empty($options['headers']) ? $this->getHeaders() : $options['headers']);
        $options['body'] = json_encode($data);

        return $this->client->request('POST', $requestUrl, $options);
    }

    protected function setHeaders(array $headers = [])
    {
        $this->headers = $headers;

        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}
