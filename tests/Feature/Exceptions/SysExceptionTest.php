<?php

declare(strict_types=1);

use App\Exceptions\SysException;
use Illuminate\Support\Facades\Log;

it('can create a system exception with message', function () {
    $exception = SysException::make('System Exception Test');

    expect($exception)->toBeInstanceOf(SysException::class)
        ->and($exception->getMessage())->toBe('System Exception Test');
});

it('can set exception code', function () {
    $exception = SysException::make('System Exception Test')->code(503);

    expect($exception->getCode())->toBe(503);
});

it('can set log context', function () {
    $context = ['user_id' => 123, 'action' => 'test_action'];
    $exception = SysException::make('System Exception Test')->logContext($context);

    $reflector = new ReflectionClass($exception);
    $property = $reflector->getProperty('logContext');
    $property->setAccessible(true);

    expect($property->getValue($exception))->toBe($context);
});

it('logs error with correct format when reported', function () {
    Log::shouldReceive('error')
        ->once()
        ->withArgs(function ($message, $data) {
            return $message === '[SysError] System Exception Test' &&
                isset($data['log_context']) &&
                isset($data['file']) &&
                isset($data['line']) &&
                isset($data['filtered_trace']);
        });

    $exception = SysException::make('System Exception Test')
        ->logContext(['test' => 'data']);
    $exception->report();
});

it('renders json response with 500 status code by default', function () {
    $exception = SysException::make('System Exception Test');
    $response = $exception->render();

    expect($response->getStatusCode())->toBe(500)
        ->and(json_decode($response->getContent(), true))->toBe([
            'success' => false,
            'message' => trans('We apologize for the inconvenience. The system is currently experiencing an issue. Please try again later or contact support if the problem persists.'),
        ]);
});

it('renders json response with custom status code', function () {
    $exception = SysException::make('System Exception Test')->code(503);
    $response = $exception->render();

    expect($response->getStatusCode())->toBe(503)
        ->and(json_decode($response->getContent(), true))->toBe([
            'success' => false,
            'message' => trans('We apologize for the inconvenience. The system is currently experiencing an issue. Please try again later or contact support if the problem persists.'),
        ]);
});

it('properly formats trace data', function () {
    $exception = SysException::make('System Exception Test');

    $reflector = new ReflectionClass($exception);
    $formatCallerMethod = $reflector->getMethod('formatCaller');
    $formatCallerMethod->setAccessible(true);

    $traceWithClass = [
        'class' => 'TestClass',
        'type' => '::',
        'function' => 'testMethod',
    ];

    $traceWithoutClass = [
        'function' => 'testFunction',
    ];

    expect($formatCallerMethod->invoke($exception, $traceWithClass))->toBe('TestClass::testMethod()')
        ->and($formatCallerMethod->invoke($exception, $traceWithoutClass))->toBe('testFunction()');
});
