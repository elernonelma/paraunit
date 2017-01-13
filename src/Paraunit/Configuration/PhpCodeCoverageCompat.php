<?php

namespace Paraunit\Configuration;

/**
 * Class PhpCodeCoverageCompat
 * @package Paraunit\Configuration
 */
class PhpCodeCoverageCompat
{
    private static $map = array(
        '\PHP_CodeCoverage' => 'SebastianBergmann\CodeCoverage\CodeCoverage',
        '\PHP_CodeCoverage_Filter' => 'SebastianBergmann\CodeCoverage\Filter',
        '\PHP_CodeCoverage_Report_Text' => 'SebastianBergmann\CodeCoverage\Report\Text',
        '\PHP_CodeCoverage_Report_Clover' => 'SebastianBergmann\CodeCoverage\Report\Clover',
        '\PHP_CodeCoverage_Report_Crap4j' => 'SebastianBergmann\CodeCoverage\Report\Crap4j',
        '\PHP_CodeCoverage_Report_HTML' => 'SebastianBergmann\CodeCoverage\Report\Html\Facade',
        '\PHP_CodeCoverage_Report_PHP' => 'SebastianBergmann\CodeCoverage\Report\PHP',
        '\PHP_CodeCoverage_Report_XML' => 'SebastianBergmann\CodeCoverage\Report\Xml\Facade',
        '\PHP_CodeCoverage_Driver_PHPDBG' => 'SebastianBergmann\CodeCoverage\Driver\PHPDBG',
    );

    /**
     * This function does a polyfill for the PHP_CodeCoverage package, because before 4.0 it doesn't use namespaces;
     * it also does a reverse-polyfill, since the coverage results may be generated by an older version
     */
    public static function load()
    {
        foreach (self::$map as $oldName => $newName) {
            self::loadIfNotPresent($oldName, $newName);
            self::loadIfNotPresent($newName, $oldName);
        }
    }

    /**
     * @param string $class
     * @param string $alias
     */
    private static function loadIfNotPresent($class, $alias)
    {
        if (! class_exists($alias) && class_exists($class)) {
            class_alias($class, $alias);
        }
    }
}
