<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use Paraunit\Parser\JSON\Log;
use PHPUnit\Runner\AfterTestFailureHook;

class Failure extends AbstractTestHook implements AfterTestFailureHook
{
    public function executeAfterTestFailure(string $test, string $message, float $time): void
    {
        $this->write(Log::STATUS_FAILURE, $test, $message);
    }
}
