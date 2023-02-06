<?php

declare(strict_types=1);

namespace Tests\Unit\App\Fetcher\Validator;

use Exception;
use PHPUnit\Framework\TestCase;
use Illuminate\Http\Client\Response;
use App\Exceptions\RateLimitException;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Fetcher\Validator\TemplidFetcherResponseValidator;

class TemplidFetcherResponseValidatorTest extends TestCase
{
    /**
     * @dataProvider validatorProvider
     * 
     * @param int    $responseStatus
     * @param string $exceptionClass
     * @param string $expectedExceptionMessage
     * 
     * @return void
     */
    public function testValidateThrowsExceptionOnInvalidResponseStatus(
        int    $responseStatus,
        string $exceptionClass,
        string $expectedExceptionMessage
    ): void {
        $response = $this->createMock(Response::class);

        $response->method('status')
            ->willReturn($responseStatus);

        $validator = new TemplidFetcherResponseValidator();

        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $validator->validate($response);
    }

    /**
     * @return iterable
     */
    public function validatorProvider(): iterable
    {
        yield [401, Exception::class, 'Unauthorized'];
        yield [404, Exception::class, 'Template not found'];
        yield [429, RateLimitException::class, 'Too many requests'];
        yield [500, Exception::class, 'API error'];
    }

    /**
     * @return void
     */
    public function testValidateResponsePassesValidation(): void
    {
        $response = $this->createMock(Response::class);

        $response->method('status')
            ->willReturn(200);

        $validator = new TemplidFetcherResponseValidator();

        $validator->validate($response);

        $this->assertTrue(true, 'Response validation passed');
    }
}
