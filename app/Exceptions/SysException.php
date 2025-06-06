<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SysException extends Exception
{
    protected array $logContext = [];

    public static function make(string $message): self
    {
        return new static($message);
    }

    public function code(int $code): self
    {
        $this->code = $code;

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
            'log_context' => $this->logContext,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'filtered_trace' => $this->getFilteredTrace(),
        ];

        Log::error($this->getLogMessage(), $logData);
    }

    private function getFilteredTrace(): array
    {
        return collect($this->getTrace())
            ->filter(fn ($trace) => $this->isAppTrace($trace))
            ->take(5)
            ->map(fn ($trace) => [
                'file' => Str::after($trace['file'], base_path()),
                'line' => $trace['line'] ?? 0,
                'caller' => $this->formatCaller($trace),
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
        return "[SysError] {$this->message}";
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => trans('We apologize for the inconvenience. The system is currently experiencing an issue. Please try again later or contact support if the problem persists.'),
        ], $this->code ?: 500);
    }
}
