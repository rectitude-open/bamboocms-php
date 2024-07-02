<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BizException extends Exception
{
    public function __construct(
        string $message = '',
        protected string $logMessage = '',
        protected array $context = [],
        int $code = 400,
        ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function setFile(string $file): void
    {
        $this->file = $file;
    }

    public function setLine(int $line): void
    {
        $this->line = $line;
    }

    public function report(): void
    {
        $previousTrace = $this->getPrevious();
        $previousTrace = $this->getPrevious() ? explode("\n", $this->getPrevious()->getTraceAsString()) : [];
        $previousTraceSlice = array_slice($previousTrace, 0, 5);
        $previousTraceString = implode("\n", $previousTraceSlice);

        $logEntry = [
            'message' => $this->logMessage,
            'context' => $this->context,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'previous' => $previousTraceString,
        ];
        Log::info('BizException', $logEntry);
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->message,
        ], $this->getCode());
    }
}
