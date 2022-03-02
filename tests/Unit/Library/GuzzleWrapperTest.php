<?php

use App\Library\GuzzleWrapper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException as ClientException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class GuzzleWrapperTest extends TestCase
{
    /**
     * @var GuzzleWrapper
     */
    private $sut;
    /**
     * @var Client | MockObject
     */
    private $clientMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->clientMock = $this->getMockBuilder(Client::class)->getMock();
        $this->sut = new GuzzleWrapper($this->clientMock);
    }

    /**
     * Tests
     */

    public function testInitialStatusShouldBeNotSuccessNorFailure(): void
    {
        $this->assertFalse($this->sut->isSuccess());
        $this->assertFalse($this->sut->isFailure());
    }

    public function testItHasSuccessStatusWhenSucceed(): void
    {
        $this->sendRequestGeneratingEmptySuccess();
        $this->assertTrue($this->sut->isSuccess());
        $this->assertFalse($this->sut->isFailure());
    }

    public function testItHasFailureStatusWhenFailed(): void
    {
        $this->sendRequestGeneratingError();
        $this->assertFalse($this->sut->isSuccess());
        $this->assertTrue($this->sut->isFailure());
    }

    public function testPreviousValuesAreCleared(): void
    {
        $this->sendRequestGeneratingEmptySuccess();
        $this->sendRequestGeneratingError();
        $this->assertTrue($this->sut->isFailure());
        $this->assertFalse($this->sut->isSuccess());
    }

    public function testWillAlwaysReturnSymfonyResponse(): void
    {
        $this->sendRequestGeneratingEmptySuccess();
        self::assertInstanceOf(HttpResponse::class, $this->sut->getResponse());

        $this->sendRequestGeneratingError();
        self::assertInstanceOf(HttpResponse::class, $this->sut->getResponse());
    }

    /**
     * Helper methods
     */

    private function sendRequestGeneratingEmptySuccess(): void
    {
        $this->clientMock->method($this->anything())->willReturn(new Response(200, [], 'test response body'));
        $this->sut->sendRequest('uri');
    }

    private function sendRequestGeneratingError(): void
    {
        /** @var ClientException | MockObject $exception */
        $exception = $this->getMockBuilder(ClientException::class)->getMock();
        $this->clientMock->method($this->anything())->willThrowException($exception);
        $this->sut->sendRequest('uri');
    }
}