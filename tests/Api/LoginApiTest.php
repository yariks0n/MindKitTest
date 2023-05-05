<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class LoginApiTest extends ApiTestCase
{
    private Client $client;

    protected function setup(): void
    {
        $this->client = static::createClient();
    }

    public function testInvalidCredentials(): void
    {
        $this->sendLoginCheckRequest([
            'email'    => 'user',
            'password' => 'user',
        ]);
        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertResponseHeaderSame(
            'content-type',
            'application/json'
        );
        self::assertJsonContains([
            'code'    => 401,
            'message' => 'Invalid credentials.',
        ]);
    }

    public function testValidCredentials(): void
    {
        $this->sendLoginCheckRequest([
            'email'    => 'user@user.ru',
            'password' => 'user',
        ]);
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertResponseHeaderSame(
            'content-type',
            'application/json'
        );
        self::assertMatchesJsonSchema(['token']);
    }

    /**
     * @param array<string, string> $json
     * @return void
     * @throws TransportExceptionInterface
     */
    private function sendLoginCheckRequest(array $json): void
    {
        $this->client->request('POST', '/api/login_check', ['json' => $json]);
    }
}
