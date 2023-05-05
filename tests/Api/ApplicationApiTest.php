<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use PhpParser\JsonDecoder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApplicationApiTest extends ApiTestCase
{
    private Client $client;
    private JsonDecoder $jsonDecoder;

    protected function setup(): void
    {
        $this->client = static::createClient();
        $this->jsonDecoder = new JsonDecoder();
    }

    public function testAddNewApplicationSuccessful(): string
    {
        $response = $this->sendApplicationChangeRequest(
            'POST',
            '/api/applications',
            $this->getCorrectToken1(),
            [
                'title' => 'test from api-test',
            ],
        );

        self::assertResponseIsSuccessful();

        $json = $this->jsonDecoder->decode($response->getContent());
        return $json['@id'];
    }

    public function testAddNewApplicationUnauthorized(): void
    {
        $this->sendApplicationChangeRequest(
            'POST',
            '/api/applications',
            'wrong token',
            [
                'title' => 'test from api-test',
            ],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testAddNewApplicationUnprocessableEntity(): void
    {
        $this->sendApplicationChangeRequest(
            'POST',
            '/api/applications',
            $this->getCorrectToken1(),
            [
                'unknown_attribute' => 'wrong structure',
            ],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @depends testAddNewApplicationSuccessful
     */
    public function testGetApplicationById(string $iri): void
    {
        $this->client->request(
            'GET',
            $iri,
        );

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/api/contexts/Application',
            '@id'      => $iri,
            '@type'    => 'Application',
        ]);
    }

    /**
     * @depends testAddNewApplicationSuccessful
     */
    public function testDeleteApplicationForbidden(string $iri): void
    {
        $this->sendDeleteRequest(
            $iri,
            $this->getCorrectToken2(),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * @depends testAddNewApplicationSuccessful
     */
    public function testDeleteApplicationSuccessful(string $iri): void
    {
        $this->sendDeleteRequest(
            $iri,
            $this->getCorrectToken1(),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    /**
     * @param array<string, string> $json
     * @return string
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function getTokenByCredentials(array $json): string
    {
        $response = $this->client->request(
            'POST',
            '/api/login_check',
            ['json' => $json]
        );
        return $this->jsonDecoder->decode($response->getContent(false))['token'];
    }


    /**
     * @param string $method
     * @param string $url
     * @param string $token
     * @param array<string, string> $json
     * @return ResponseInterface
     * @throws TransportExceptionInterface
     */
    private function sendApplicationChangeRequest(
        string $method,
        string $url,
        string $token,
        array $json
    ): ResponseInterface {
        return $this->client->request(
            $method,
            $url,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
                'json'    => $json,
            ]
        );
    }

    private function sendDeleteRequest(
        string $iri,
        string $token
    ): void {
        $this->client->request(
            'DELETE',
            $iri,
            [
                'auth_bearer' => $token,
            ],
        );
    }

    private function getCorrectToken1(): string
    {
        return $this->getTokenByCredentials(
            [
                'email' => 'user@user.ru',
                'password' => 'user',
            ]
        );
    }

    private function getCorrectToken2(): string
    {
        return $this->getTokenByCredentials(
            [
                'email' => 'admin@admin.ru',
                'password' => 'admin',
            ]
        );
    }
}
