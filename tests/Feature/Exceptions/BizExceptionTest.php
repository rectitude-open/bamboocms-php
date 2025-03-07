<?php

declare(strict_types=1);

use App\Exceptions\BizException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->exception = BizException::make('test.message');
});

it('can be created using make method', function () {
    $exception = BizException::make('test.message');

    expect($exception)->toBeInstanceOf(BizException::class)
        ->and($exception->getMessage())->toBe('test.message');
});

it('can set error code', function () {
    $this->exception->code(422);

    expect($this->exception->getCode())->toBe(422);
});

it('can add translation parameters using with method with key-value pair', function () {
    $this->exception->with('name', 'John');

    $reflectionClass = new ReflectionClass($this->exception);
    $property = $reflectionClass->getProperty('transParams');
    $property->setAccessible(true);

    expect($property->getValue($this->exception))->toBe(['name' => 'John']);
});

it('can add translation parameters using with method with array', function () {
    $this->exception->with(['name' => 'John', 'age' => 30]);

    $reflectionClass = new ReflectionClass($this->exception);
    $property = $reflectionClass->getProperty('transParams');
    $property->setAccessible(true);

    expect($property->getValue($this->exception))->toBe(['name' => 'John', 'age' => 30]);
});

it('can set log message', function () {
    $this->exception->logMessage('Custom log message');

    $reflectionClass = new ReflectionClass($this->exception);
    $property = $reflectionClass->getProperty('logMessage');
    $property->setAccessible(true);

    expect($property->getValue($this->exception))->toBe('Custom log message');
});

it('can set log context', function () {
    $context = ['user_id' => 1, 'action' => 'create'];
    $this->exception->logContext($context);

    $reflectionClass = new ReflectionClass($this->exception);
    $property = $reflectionClass->getProperty('logContext');
    $property->setAccessible(true);

    expect($property->getValue($this->exception))->toBe($context);
});

it('reports to log channel', function () {
    Log::shouldReceive('channel')
        ->once()
        ->with('biz')
        ->andReturnSelf();

    Log::shouldReceive('error')
        ->once()
        ->withArgs(function ($message, $data) {
            return $message === '[BizError] test.message' &&
                   isset($data['user_message']) &&
                   isset($data['log_context']) &&
                   isset($data['file']) &&
                   isset($data['line']) &&
                   isset($data['filtered_trace']);
        });

    $this->exception->report();
});

it('renders as JSON response', function () {
    $this->exception->code(422)->with('param', 'value');

    $response = $this->exception->render();

    expect($response)->toBeInstanceOf(JsonResponse::class)
        ->and($response->getStatusCode())->toBe(422)
        ->and(json_decode($response->getContent(), true))->toBe([
            'success' => false,
            'message' => 'test.message',
        ]);
});

it('uses default error code 400 when no code set', function () {
    // Mock the trans function
    $this->mock('alias:trans', function ($mock) {
        $mock->shouldReceive('__invoke')
            ->with('test.message', [])
            ->andReturn('Translated message');
    });

    $response = $this->exception->render();

    expect($response->getStatusCode())->toBe(400);
});

it('uses custom log message when available', function () {
    $this->exception->logMessage('Custom log message');

    $reflectionClass = new ReflectionClass($this->exception);
    $method = $reflectionClass->getMethod('getLogMessage');
    $method->setAccessible(true);

    expect($method->invoke($this->exception))->toBe('Custom log message');
});

it('formats caller correctly with class type and function', function () {
    $reflectionClass = new ReflectionClass($this->exception);
    $method = $reflectionClass->getMethod('formatCaller');
    $method->setAccessible(true);

    $trace = [
        'class' => 'TestClass',
        'type' => '::',
        'function' => 'testMethod',
    ];

    expect($method->invoke($this->exception, $trace))->toBe('TestClass::testMethod()');
});

it('formats caller correctly with only function', function () {
    $reflectionClass = new ReflectionClass($this->exception);
    $method = $reflectionClass->getMethod('formatCaller');
    $method->setAccessible(true);

    $trace = [
        'function' => 'testMethod',
    ];

    expect($method->invoke($this->exception, $trace))->toBe('testMethod()');
});
