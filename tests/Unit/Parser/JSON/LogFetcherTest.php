<?php

declare(strict_types=1);

namespace Tests\Unit\Parser\JSON;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Parser\JSON\Log;
use Paraunit\Parser\JSON\LogFetcher;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class LogFetcherTest extends BaseUnitTestCase
{
    public function testFetchAppendsLogEndingAnywayWithMissingLog(): void
    {
        $process = new StubbedParaunitProcess();

        $tempFileNameFactory = $this->prophesize(TempFilenameFactory::class);
        $tempFileNameFactory->getFilenameForLog($process->getUniqueId())
            ->willReturn('non-existent-log.json');

        $fetcher = new LogFetcher($tempFileNameFactory->reveal());

        $logs = $fetcher->fetch($process);

        $this->assertNotNull($logs, 'Fetcher returning a non-array');
        $this->assertCount(1, $logs, 'Log ending missing');
        $this->assertContainsOnlyInstancesOf(Log::class, $logs);

        $endingLog = end($logs);
        $this->assertNotFalse($endingLog);
        $this->assertEquals(LogFetcher::LOG_ENDING_STATUS, $endingLog->getStatus());
    }

    public function testFetch(): void
    {
        $process = new StubbedParaunitProcess();
        $filename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'testfile.json';
        copy(__DIR__ . '/../../../Stub/PHPUnitJSONLogOutput/AllGreen.json', $filename);
        $this->assertFileExists($filename, 'Test malformed, stub log file not found');

        $tempFileNameFactory = $this->prophesize(TempFilenameFactory::class);
        $tempFileNameFactory->getFilenameForLog($process->getUniqueId())
            ->willReturn($filename);

        $fetcher = new LogFetcher($tempFileNameFactory->reveal());

        $logs = $fetcher->fetch($process);

        $this->assertNotNull($logs, 'Fetcher returning a non-array');
        $this->assertCount(18 + 1, $logs, 'Log ending missing');
        $this->assertContainsOnlyInstancesOf(Log::class, $logs);

        $endingLog = end($logs);
        $this->assertNotFalse($endingLog);
        $this->assertEquals(LogFetcher::LOG_ENDING_STATUS, $endingLog->getStatus());

        $this->assertFileDoesNotExist($filename, 'Log file should be deleted to preserve memory');
    }
}
