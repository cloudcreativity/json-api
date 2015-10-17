<?php

namespace CloudCreativity\JsonApi\Integration;

use CloudCreativity\JsonApi\Error\ErrorException;
use CloudCreativity\JsonApi\Error\ThrowableError;
use CloudCreativity\JsonApi\Exceptions\ExceptionThrower;
use CloudCreativity\JsonApi\Repositories\CodecMatcherRepository;
use Neomerx\JsonApi\Contracts\Integration\CurrentRequestInterface;
use Neomerx\JsonApi\Contracts\Parameters\SupportedExtensionsInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface;
use Neomerx\JsonApi\Decoders\ObjectDecoder;
use Neomerx\JsonApi\Factories\Factory;
use Neomerx\JsonApi\Parameters\Headers\AcceptHeader;
use Neomerx\JsonApi\Parameters\Headers\Header;
use Neomerx\JsonApi\Parameters\SupportedExtensions;

class EnvironmentServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var CodecMatcherRepository
     */
    private $repository;

    /**
     * @var ContainerInterface
     */
    private $schemas;

    private $urlPrefix;

    private $currentRequest;

    /**
     * @var array
     */
    private $parameters = [
        'foo' => 'bar',
        'baz' => 'bat',
    ];

    /**
     * @var SupportedExtensionsInterface
     */
    private $supportedExtensions;

    /**
     * @var EnvironmentService
     */
    private $service;

    protected function setUp()
    {
        $this->factory = new Factory();
        $this->schemas = $this->factory->createContainer(['Author' => 'AuthorSchema']);
        $this->urlPrefix = 'http://www.example.tld/api';
        $this->supportedExtensions = new SupportedExtensions('ext1,ext2');
        $this->repository = new CodecMatcherRepository($this->factory);
        $this->repository->registerSchemas($this->schemas)->registerUrlPrefix($this->urlPrefix);

        $this->repository
            ->configure([
                'encoders' => [
                    'application/vnd.api+json',
                ],
                'decoders' => [
                    'application/vnd.api+json' => ObjectDecoder::class,
                ],
            ]);

        $this->currentRequest = $this->getMock(CurrentRequestInterface::class);
        $this->currentRequest
            ->method('getHeader')
            ->willReturnMap([
                ['Accept', 'application/vnd.api+json'],
                ['Content-Type', 'application/vnd.api+json'],
            ]);

        $this->currentRequest->method('getQueryParameters')
            ->willReturn($this->parameters);

        $this->service = new EnvironmentService($this->factory, $this->currentRequest, new ExceptionThrower());
    }

    public function testUrlPrefix()
    {
        $this->service->registerUrlPrefix($this->urlPrefix);

        $this->assertEquals($this->urlPrefix, $this->service->getUrlPrefix());
    }

    public function testSchemas()
    {
        $this->assertFalse($this->service->hasSchemas());
        $this->service->registerSchemas($this->schemas);
        $this->assertEquals($this->schemas, $this->service->getSchemas());
        $this->assertTrue($this->service->hasSchemas());
    }

    public function testEncoder()
    {
        $matcher = $this->repository->getCodecMatcher();
        $matcher->matchEncoder(AcceptHeader::parse('application/vnd.api+json'));

        $this->assertFalse($this->service->hasEncoder());
        $this->service->registerCodecMatcher($this->repository->getCodecMatcher());
        $this->assertEquals($matcher->getEncoder(), $this->service->getEncoder());
        $this->assertEquals($matcher->getEncoderRegisteredMatchedType(), $this->service->getEncoderMediaType());
        $this->assertTrue($this->service->hasEncoder());
    }

    public function testDecoder()
    {
        $matcher = $this->repository->getCodecMatcher();
        $matcher->findDecoder(Header::parse('application/vnd.api+json', 'Content-Type'));

        $this->assertFalse($this->service->hasDecoder());
        $this->service->registerCodecMatcher($this->repository->getCodecMatcher());
        $this->assertEquals($matcher->getDecoder(), $this->service->getDecoder());
        $this->assertEquals($matcher->getDecoderHeaderMatchedType(), $this->service->getDecoderMediaType());
        $this->assertTrue($this->service->hasDecoder());
    }

    public function testParameters()
    {
        $expected = $this
            ->factory
            ->createParametersParser()
            ->parse($this->currentRequest, new ExceptionThrower());

        $this->assertFalse($this->service->hasParameters());
        $this->service->registerCodecMatcher($this->repository->getCodecMatcher());
        $this->assertEquals($expected, $this->service->getParameters());
        $this->assertTrue($this->service->hasParameters());
    }

    public function testInvalidAccept()
    {
        $currentRequest = $this->getMock(CurrentRequestInterface::class);
        $currentRequest->method('getHeader')
            ->willReturnMap([
                ['Accept', 'text/plain'],
            ]);

        $currentRequest->method('getQueryParameters')
            ->willReturn([]);

        $service = new EnvironmentService($this->factory, $currentRequest, new ExceptionThrower());

        try {
            $service->registerCodecMatcher($this->repository->getCodecMatcher());
            $this->fail('No exception thrown.');
        } catch (ErrorException $e) {
            $this->assertEquals(406, $e->getError()->getStatus());
        }
    }

    public function testInvalidContentType()
    {
        $currentRequest = $this->getMock(CurrentRequestInterface::class);
        $currentRequest->method('getHeader')
            ->willReturnMap([
                ['Accept', 'application/vnd.api+json'],
                ['Content-Type', 'text/plain'],
            ]);

        $currentRequest->method('getQueryParameters')
            ->willReturn([]);

        $service = new EnvironmentService($this->factory, $currentRequest, new ExceptionThrower());

        try {
            $service->registerCodecMatcher($this->repository->getCodecMatcher());
            $this->fail('No exception thrown.');
        } catch (ErrorException $e) {
            $this->assertEquals(415, $e->getError()->getStatus());
        }
    }

    public function testSupportedExtensions()
    {
        $this->assertNull($this->service->getSupportedExtensions());
        $this->service->registerSupportedExtensions($this->supportedExtensions);
        $this->assertEquals($this->supportedExtensions, $this->service->getSupportedExtensions());
    }
}
