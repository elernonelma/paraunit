<?php

declare(strict_types=1);

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\PHPUnitConfig;
use Tests\BaseUnitTestCase;

class PHPUnitConfigTest extends BaseUnitTestCase
{
    public function testGetBaseDirectoryIsNotLazy(): void
    {
        $config = new PHPUnitConfig('');

        $directoryPath = $config->getBaseDirectory();
        $this->assertNotEquals('', $directoryPath);
        $this->assertNotEquals('/', $directoryPath);
        $this->assertNotEquals('C:\\', $directoryPath);
    }

    public function testGetFileFullPathWithDirAndUseDefaultFileName(): void
    {
        $dir = $this->getStubPath() . 'StubbedXMLConfigs';
        $configurationFile = $dir . DIRECTORY_SEPARATOR . 'phpunit.xml.dist';

        $config = new PHPUnitConfig($dir);

        $this->assertEquals($configurationFile, $config->getFileFullPath());
    }

    public function testGetFileFullPathWithMissConfigDefault(): void
    {
        $dir = $this->getStubPath() . 'StubbedXMLConfigs' . DIRECTORY_SEPARATOR . 'MissDefault';
        $config = new PHPUnitConfig($dir);

        $configurationFile = $dir . DIRECTORY_SEPARATOR . 'phpunit.xml.dist';
        $this->assertEquals($configurationFile, $config->getFileFullPath());
    }

    public function testGetFileFullPathWithMissConfigDefaultAndFallBack(): void
    {
        $dir = $this->getStubPath() . 'StubbedXMLConfigs' . DIRECTORY_SEPARATOR . 'MissDefaultAndFallback';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(PHPUnitConfig::FALLBACK_CONFIG_FILE_NAME . ' does not exist');

        new PHPUnitConfig($dir);
    }

    public function testGetFileFullPathWithFileDoesNotExistWillThrowException(): void
    {
        $dir = $this->getStubPath() . 'PHPUnitJSONLogOutput';
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(PHPUnitConfig::FALLBACK_CONFIG_FILE_NAME . ' does not exist');

        new PHPUnitConfig($dir);
    }

    public function testGetFileFullPathWithPathDoesNotExistWillThrowException(): void
    {
        $dir = $this->getStubPath() . 'foobar';
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Config path/file provided is not valid');

        new PHPUnitConfig($dir);
    }
}
