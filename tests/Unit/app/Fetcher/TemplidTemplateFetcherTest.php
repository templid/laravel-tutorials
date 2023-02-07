<?php

declare(strict_types=1);

namespace Tests\Unit\App\Fetcher;

use Exception;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;
use App\Exceptions\RateLimitException;
use App\Fetcher\TemplidTemplateFetcher;
use Illuminate\Http\Client\PendingRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Fetcher\Validator\TemplidFetcherResponseValidator;

class TemplidTemplateFetcherTest extends TestCase
{
    /**
     * @var MockObject|PendingRequest
     */
    protected $request;

    /**
     * @var MockObject|TemplidFetcherResponseValidator
     */
    protected $validator;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->request   = $this->createMock(PendingRequest::class);
        $this->validator = $this->createMock(TemplidFetcherResponseValidator::class);
    }

    /**
     * @dataProvider fetcherProvider
     * 
     * @param array           $validData
     * @param array           $expectedRequestParams
     * @param Collection|null $requestParams
     * 
     * @return void
     */
    public function testFetchReturnsValidResult(
        array       $validData,
        array       $expectedRequestParams,
        ?Collection $requestParams
    ): void {
        $response = $this->createMock(Response::class);

        $response->expects($this->once())
            ->method('json')
            ->willReturn($validData);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($response)
            ->willReturnArgument(1);

        $this->request
            ->expects($this->once())
            ->method('withToken')
            ->with(env('TEMPLID_TOKEN'))
            ->willReturn($this->request);

        $this->request
            ->expects($this->once())
            ->method('post')
            ->with(
                'https://api.templid.com/v1/templates/1/render',
                $expectedRequestParams
            )
            ->willReturn($response);

        $template = $this->getFetcher()
            ->fetch(1, $requestParams);

        $this->assertSame($validData['subject'], $template->getSubject());
        $this->assertSame($validData['html'], $template->getHtml());
        $this->assertSame($validData['text'], $template->getText());
    }

    /**
     * @dataProvider fetcherProvider
     * 
     * @param array           $validData
     * @param array           $expectedRequestParams
     * @param Collection|null $requestParams
     * 
     * @return void
     */
    public function testFetchRetrunsValidResultAfterRateLimitException(
        array       $validData,
        array       $expectedRequestParams,
        ?Collection $requestParams
    ): void {
        $rateLimitMessage = 'Rate limit exceeded';

        $response = $this->createMock(Response::class);

        $response->expects($this->once())
            ->method('json')
            ->willReturn($validData);

        $this->validator
            ->expects($this->exactly(2))
            ->method('validate')
            ->with($response)
            ->will(
                $this->onConsecutiveCalls(
                    $this->throwException(new RateLimitException($rateLimitMessage)),
                    null
                )
            );

        $this->request
            ->expects($this->exactly(2))
            ->method('withToken')
            ->with(env('TEMPLID_TOKEN'))
            ->willReturn($this->request);

        $this->request
            ->expects($this->exactly(2))
            ->method('post')
            ->with(
                'https://api.templid.com/v1/templates/1/render',
                $expectedRequestParams
            )
            ->willReturn($response);

        Log::shouldReceive('notice')
            ->once()
            ->with($rateLimitMessage);

        $template = $this->getFetcher()
            ->fetch(1, collect($requestParams));

        $this->assertSame($validData['subject'], $template->getSubject());
        $this->assertSame($validData['html'], $template->getHtml());
        $this->assertSame($validData['text'], $template->getText());
    }

    /**
     * @return void
     */
    public function testFetchThrowsExceptionOnRateLimit(): void
    {
        $message = 'Rate limit exceeded';

        $response = $this->createMock(Response::class);

        $this->validator
            ->expects($this->exactly(3))
            ->method('validate')
            ->with($response)
            ->willThrowException(new RateLimitException($message));

        $this->request
            ->expects($this->exactly(3))
            ->method('withToken')
            ->willReturn($this->request);
        
        $this->request
            ->expects($this->exactly(3))
            ->method('post')
            ->willReturn($response);

        Log::shouldReceive('notice')
            ->with($message);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to fetch template');

        $this->getFetcher()->fetch(1, collect([]));
    }

    /**
     * @return iterable
     */
    public function fetcherProvider(): iterable
    {
        yield 'With request parameters' => [
            'validData' => [
                'subject' => 'Foo subject',
                'html'    => '<h1>Hello world</h1>',
                'text'    => 'Hello world',
            ],
            'expectedRequestParams' => ['foo' => 'bar'],
            'requestParams'         => collect(['foo' => 'bar']),
        ];

        yield 'No request parameters' => [
            'validData' => [
                'subject' => 'Foo subject',
                'html'    => '<h1>Hello world</h1>',
                'text'    => '',
            ],
            'expectedRequestParams' => [],
            'requestParams'         => null,
        ];
    }

    /**
     * @return TemplidTemplateFetcher
     */
    protected function getFetcher(): TemplidTemplateFetcher
    {
        return new TemplidTemplateFetcher(
            $this->request,
            $this->validator
        );
    }
}
