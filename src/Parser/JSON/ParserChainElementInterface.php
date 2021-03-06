<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultInterface;

interface ParserChainElementInterface
{
    /**
     * @return null|TestResultInterface Returned when the chain needs to stop
     */
    public function handleLogItem(AbstractParaunitProcess $process, Log $logItem): ?TestResultInterface;
}
