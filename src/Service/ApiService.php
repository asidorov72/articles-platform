<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Service\HttpClientService;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ApiService
{
    public $client;

    private $monologLogger;

    private $httpClientService;

    private $encryptor;

    private $container;

    private $userRepository;

    const ACTION_NAME = 'register';
    const STATUS_CODE_SUCCESS = 200;

    public function __construct(
        LoggerInterface $monologLogger,
        Encryptor $encryptor,
        ContainerInterface $container,
        UserRepository $userRepository
    )
    {
        $this->monologLogger = $monologLogger;
        $this->httpClientService = new HttpClientService($monologLogger);
        $this->encryptor = $encryptor;
        $this->container = $container;
        $this->userRepository = $userRepository;
    }

    protected function registerApiUser(FormInterface $form)
    {
        $encryptedEmail = $this->encryptor->encrypt(trim($form->get('email')->getData()));
        $encryptedPassword = $this->encryptor->encrypt(trim($form->get('plainPassword')->getData()));

        $this->httpClientService->getHttpClient();

        return $this->httpClientService->sendPostRequest(
            $this->container->getParameter('api_register_url'),
            [
                'action' => self::ACTION_NAME,
                'data' => [
                    'email' => $encryptedEmail,
                    'password' => $encryptedPassword,
                    'roles' => $form->get('roles')->getData()
                ]
            ]
        );
    }

    protected function handleApiResponse(FormInterface $form)
    {
        $response = $this->registerApiUser($form);

        try {
            $content = $response->getContent();
            $response = json_decode($content, true);

            if (isset($response['code']) && $response['code'] === (int) self::STATUS_CODE_SUCCESS) {

                $this->monologLogger->info('CurlHttpClient RESPONSE: ' . json_encode($response));

                $decryptedEmail = $this->encryptor->decrypt($response['response']['hashedEmail']);
                $user = $this->userRepository->findOneBy(['email' => $decryptedEmail]);

                return $user;
            }
        } catch (\Exception $e) {
            $this->monologLogger->error($e->getMessage());
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function getApiRegisteredUser(FormInterface $form)
    {
        try {
            return $this->handleApiResponse($form);
        } catch (\Exception $e) {
            throw new \Exception('Code 400, Bad request. Check API log file permissions.', 400);
        }
    }
}
