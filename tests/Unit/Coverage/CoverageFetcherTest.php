<?php

declare(strict_types=1);

namespace Tests\Unit\Coverage;

use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Coverage\CoverageFetcher;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class CoverageFetcherTest extends BaseUnitTestCase
{
    public function testFetch(): void
    {
        $process = new StubbedParaunitProcess('test.php', 'uniqueId');

        $filename = $this->getTempFilename();
        copy($this->getCoverageStubFilePath(), $filename);
        $this->assertFileExists($filename, 'Test malformed, stub log file not found');

        $tempFilenameFactory = $this->prophesize(TempFilenameFactory::class);
        $tempFilenameFactory->getFilenameForCoverage('uniqueId')
            ->shouldBeCalled()
            ->willReturn($filename);
        $missingCoverageContainer = $this->prophesize(TestResultHandlerInterface::class);
        $missingCoverageContainer->addProcessToFilenames($process)
            ->shouldNotBeCalled();

        $fetcher = new CoverageFetcher($tempFilenameFactory->reveal(), $missingCoverageContainer->reveal());

        $result = $fetcher->fetch($process);

        $this->assertNotEmpty($result->getData());
        $this->assertFileDoesNotExist($filename, 'Coverage file should be deleted to preserve memory');
    }

    public function testFetchIgnoresMissingCoverageFiles(): void
    {
        $process = new StubbedParaunitProcess('test.php', 'uniqueId');

        $tempFilenameFactory = $this->prophesize(TempFilenameFactory::class);
        $tempFilenameFactory->getFilenameForCoverage('uniqueId')
            ->shouldBeCalled()
            ->willReturn('/path/to/missing/file');
        $missingCoverageContainer = $this->prophesize(TestResultHandlerInterface::class);
        $missingCoverageContainer->addProcessToFilenames($process)
            ->shouldBeCalled();

        $fetcher = new CoverageFetcher($tempFilenameFactory->reveal(), $missingCoverageContainer->reveal());

        $result = $fetcher->fetch($process);

        $this->assertEmpty($result->getData());
    }

    public function testFetchIgnoresWrongFiles(): void
    {
        $process = new StubbedParaunitProcess('test.php', 'uniqueId');

        $filename = $this->getTempFilename();
        copy($this->getWrongCoverageStubFilePath(), $filename);
        $this->assertFileExists($filename, 'Test malformed, stub log file not found');

        $tempFilenameFactory = $this->prophesize(TempFilenameFactory::class);
        $tempFilenameFactory->getFilenameForCoverage('uniqueId')
            ->shouldBeCalled()
            ->willReturn($filename);
        $missingCoverageContainer = $this->prophesize(TestResultHandlerInterface::class);
        $missingCoverageContainer->addProcessToFilenames($process)
            ->shouldBeCalled();

        $fetcher = new CoverageFetcher($tempFilenameFactory->reveal(), $missingCoverageContainer->reveal());

        $result = $fetcher->fetch($process);

        $this->assertEmpty($result->getData());
        $this->assertFileDoesNotExist($filename, 'Coverage file should be deleted to preserve memory');
    }

    private function getTempFilename(): string
    {
        return uniqid(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'testfile', true) . '.php';
    }
}
