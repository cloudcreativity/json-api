<?php

namespace CloudCreativity\JsonApi\Http\Client;

use CloudCreativity\JsonApi\Encoder\Encoder;
use CloudCreativity\JsonApi\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Encoder\Parameters\EncodingParameters;
use PHPUnit_Framework_MockObject_MockObject as Mock;
use function GuzzleHttp\Psr7\parse_query;

class GuzzleClientTest extends TestCase
{

    /**
     * @var Mock
     */
    private $encoder;

    /**
     * @var object
     */
    private $record;

    /**
     * @var MockHandler
     */
    private $mock;

    /**
     * @var GuzzleClient
     */
    private $client;

    /**
     * @return void
     */
    protected function setUp()
    {
        /** @var Encoder $encoder */
        $encoder = $this->encoder = $this->createMock(Encoder::class);
        $this->record = (object) [
            'type' => 'posts',
            'id' => null,
            'attributes' => ['title' => 'Hello World'],
        ];

        $this->client = new GuzzleClient(new Client([
            'handler' => $this->mock = new MockHandler(),
            'base_uri' => 'http://localhost/api/v1/',
        ]), $encoder);
    }

    public function testCreate()
    {
        $this->willSerializeRecord()->willSeeRecord(201);
        $response = $this->client->create($this->record);
        $this->assertSame(201, $response->getPsrResponse()->getStatusCode());

        $this->assertRequested('POST', '/posts')
            ->assertRequestSentRecord()
            ->assertHeader('Accept', 'application/vnd.api+json')
            ->assertHeader('Content-Type', 'application/vnd.api+json');
    }

    public function testCreateWithParameters()
    {
        $parameters = new EncodingParameters(
            ['author', 'site'],
            ['author' => ['first-name', 'surname'], 'site' => ['uri']]
        );

        $this->willSerializeRecord($parameters)->willSeeRecord(201);
        $this->client->create($this->record, $parameters);
        $this->assertQueryParameters([
            'include' => 'author,site',
            'fields[author]' => 'first-name,surname',
            'fields[site]' => 'uri',
        ]);
    }

    private function willSerializeRecord(EncodingParametersInterface $parameters = null)
    {
        $this->encoder
            ->expects($this->once())
            ->method('serializeData')
            ->with($this->record, $parameters)
            ->willReturn(['data' => (array) $this->record]);

        return $this;
    }

    /**
     * @param int $status
     * @return $this
     */
    private function willSeeRecord($status = 200)
    {
        $this->appendResponse($status, ['Content-Type' => 'application/vnd.api+json'], [
            'data' => (array) $this->record,
        ]);

        return $this;
    }

    /**
     * @param int $status
     * @param array $headers
     * @param array|null $body
     * @return $this
     */
    private function appendResponse($status = 200, array $headers = [], array $body = null)
    {
        if (is_array($body)) {
            $body = json_encode($body);
        }

        $this->mock->append(new Response($status, $headers, $body));

        return $this;
    }

    /**
     * @return $this
     */
    private function assertRequestSentRecord()
    {
        $request = $this->mock->getLastRequest();
        $this->assertJsonStringEqualsJsonString(json_encode([
            'data' => (array) $this->record,
        ]), (string) $request->getBody());

        return $this;
    }

    /**
     * @param $method
     * @param $path
     * @return $this
     */
    private function assertRequested($method, $path)
    {
        $uri = 'http://localhost/api/v1' . $path;
        $request = $this->mock->getLastRequest();
        $this->assertEquals($method, $request->getMethod());
        $this->assertEquals($uri, (string) $request->getUri(), 'request uri');

        return $this;
    }

    /**
     * @param $key
     * @param $expected
     * @return $this
     */
    private function assertHeader($key, $expected)
    {
        $request = $this->mock->getLastRequest();
        $actual = $request->getHeaderLine($key);
        $this->assertSame($expected, $actual);

        return $this;
    }

    /**
     * @param array $expected
     * @return $this
     */
    private function assertQueryParameters(array $expected)
    {
        $query = $this->mock->getLastRequest()->getUri()->getQuery();
        $this->assertEquals($expected, parse_query($query));

        return $this;
    }
}
