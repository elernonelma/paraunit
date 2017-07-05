<?php
declare(strict_types=1);

namespace Paraunit\Bin;

use PackageVersions\Versions;
use Paraunit\Command\CoverageCommand;
use Paraunit\Command\ParallelCommand;
use Paraunit\Configuration\CoverageConfiguration;
use Paraunit\Configuration\ParallelConfiguration;
use Symfony\Component\Console\Application;

/**
 * Class Paraunit
 * @package Paraunit\Bin
 */
class Paraunit
{
    public static function createApplication(): Application
    {
        $application = new Application('Paraunit', self::getVersion());

        $parallelCommand = new ParallelCommand(new ParallelConfiguration());
        $application->add($parallelCommand);

        $CoverageCommand = new CoverageCommand(new CoverageConfiguration());
        $application->add($CoverageCommand);

        return $application;
    }

    public static function getVersion(): string
    {
        return Versions::getVersion('facile-it/paraunit');
    }
}