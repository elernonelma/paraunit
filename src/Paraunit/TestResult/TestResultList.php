<?php

namespace Paraunit\TestResult;

/**
 * Class TestResultList
 * @package Paraunit\TestResult
 */
class TestResultList
{
    /** @var  TestResultContainer[] */
    private $testResultContainers;

    public function __construct()
    {
        $this->testResultContainers = array();
    }

    /**
     * @param TestResultContainer $container
     */
    public function addParser(TestResultContainer $container)
    {
        $this->testResultContainers[] = $container;
    }

    /**
     * @return TestResultContainer[]
     */
    public function getTestResultContainers()
    {
        return $this->testResultContainers;
    }
}