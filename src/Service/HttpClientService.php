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
        $response = $this->client->request(
            'POST',
            $requestUrl, [
                'headers' => $this->getHeaders(),
                'body' => json_encode($data)
            ]
        );

        $this->monologLogger->info('HTTPCLIENT DATA: ' . json_encode($data));

        return $response;
    }

    protected function setHeader($key, $value)
    {
        $this->headers[] = [$key => $value];

        return $this;
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
