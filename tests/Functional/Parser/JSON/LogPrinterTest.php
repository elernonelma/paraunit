<?php

namespace Tests\Functional\Parser\JSON;

use Paraunit\Configuration\StaticOutputPath;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Parser\JSON\LogPrinter;
use PHPUnit\Framework\TestSuite;
use Tests\BaseFunctionalTestCase;

/**
 * Class LogPrinterTest
 * @package Tests\Functional\Parser\JSON
 */
class LogPrinterTest extends BaseFunctionalTestCase
{
    public function testLogFilenameMatches()
    {
        $testName = get_class();
        $testSuite = $this->prophesize(TestSuite::class);
        $testSuite->getName()
            ->willReturn($testName);
        $testSuite->count()
            ->willReturn(1);

        $dir = sys_get_temp_dir();
        new StaticOutputPath($dir);
        $printer = new LogPrinter();

        $reflectionMethod = new \ReflectionMethod($printer, 'getLogFilename');
        $reflectionMethod->setAccessible(true);
        $this->assertEquals(
            $dir . DIRECTORY_SEPARATOR . md5(__FILE__) . '.json.log',
            $reflectionMethod->invoke($printer, $testSuite->reveal())
        );
    }

    public function testWrite()
    {
        $testName = get_class();
        $testSuite = $this->prophesize(TestSuite::class);
        $testSuite->getName()
            ->willReturn($testName);
        $testSuite->count()
            ->willReturn(1);
        $logFilename = $this->getLogFilenameForTest(__FILE__);

        new StaticOutputPath(dirname($logFilename));
        $printer = new LogPrinter();

        $printer->startTestSuite($testSuite->reveal());

        $this->assertFileExists($logFilename);

        $content = file_get_contents($logFilename);
        $this->assertJson($content);
        $decodedJson = json_decode($content, true);
        $this->assertEquals(array('event' => 'suiteStart', 'suite' => $testName, 'tests' => 1), $decodedJson);
    }

    /**
     * @param string $testFilename
     * @return string
     */
    private function getLogFilenameForTest($testFilename)
    {
        /** @var TempFilenameFactory $filenameFactory */
        $filenameFactory = $this->container->get('paraunit.configuration.temp_filename_factory');

        return $filenameFactory->getFilenameForLog(md5($testFilename));
    }
}
