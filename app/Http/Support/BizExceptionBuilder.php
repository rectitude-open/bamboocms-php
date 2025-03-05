<?php

declare(strict_types=1);

namespace App\Http\Support;

use App\Exceptions\BizException;
use Exception;

class BizExceptionBuilder
{
    protected string $message;

    protected string $logMessage = '';

    protected array $context = [];

    protected int $code = 400;

    protected ?Exception $previous = null;

    public static function make(string $message): self
    {
        $instance = new self;
        $instance->message = $message;

        return $instance;
    }

    public function logMessage(string $logMessage): self
    {
        $this->logMessage = $logMessage;

        return $this;
    }

    public function context(array $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function code(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function previous(?Exception $previous): self
    {
        $this->previous = $previous;

        return $this;
    }

    public function throw(): void
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];

        $exception = new BizException(
            $this->message,
            $this->logMessage,
            $this->context,
            $this->code,
            $this->previous
        );
        $exception->setFile($trace['file']);
        $exception->setLine($trace['line']);
        throw $exception;
    }
}
