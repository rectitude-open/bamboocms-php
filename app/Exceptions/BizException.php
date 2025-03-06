<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BizException extends Exception
{
    protected string $logMessage = '';
    protected array $logContext = [];
    protected array $transParams = [];

    public static function make(string $message): self
    {
        return new static($message);
    }

    public function code(int $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function with(array|string $key, $value = null): self
    {
        if (is_array($key)) {
            $this->transParams = [...$this->transParams, ...$key];
        } else {
            $this->transParams[$key] = $value;
        }
        return $this;
    }

    public function logMessage(string $message)
    {
        $this->logMessage = $message;
        return $this;
    }

    public function logContext(array $context): self
    {
        $this->logContext = $context;
        return $this;
    }

    public function report(): void
    {
        $logData = [
            'client_params' => $this->transParams,
            'log_context' => $this->logContext,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'filtered_trace' => $this->getFilteredTrace(),
        ];

        Log::channel('biz')->error($this->getLogMessage(), $logData);
    }

    private function getFilteredTrace(): array
    {
        return collect($this->getTrace())
            ->filter(fn ($trace) => $this->isAppTrace($trace))
            ->take(5)
            ->map(fn ($trace) => [
                'file' => Str::after($trace['file'], base_path()),
                'line' => $trace['line'] ?? 0,
                'caller' => $this->formatCaller($trace)
            ])
            ->all();
    }

    private function isAppTrace(array $trace): bool
    {
        $file = $trace['file'] ?? '';

        return Str::startsWith($file, base_path().'/contexts');
    }

    private function formatCaller(array $trace): string
    {
        $class = $trace['class'] ?? '';
        $type = $trace['type'] ?? '';
        $function = $trace['function'] ?? '';

        return $class ? "$class$type$function()" : $function.'()';
    }

    private function getLogMessage(): string
    {
        return $this->logMessage ?: "[BizError] {$this->message}";
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => trans($this->message, $this->transParams)
        ], $this->code ?: 400);
    }
}
