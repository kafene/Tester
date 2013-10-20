#!/usr/bin/env php
<?php

/**
 * A very small testing tool.
 */
class Tester
{
    /**
     * Description strings of passed tests
     *
     * @var array
     */
    public $passed = [];

    /**
     * Description strings of failed tests
     *
     * @var array
     */
    public $failed = [];

    /**
     * Run a test (assertion)
     *
     * @param string $description
     *     A description or name of the test.
     * @param mixed $result
     *     The result of the assertion/evaluation, or an
     *     invokable whose return value will be used.
     *
     * @return boolean
     *     Whether the test has passed or failed.
     */
    public function test($description, $result)
    {
        if (is_object($result) && method_exists($result, '__invoke')) {
            $result = $result();
        }
        if (true === filter_var($result, FILTER_VALIDATE_BOOLEAN)) {
            $this->passed[] = $description;
            return true;
        }
        $this->failed[] = $description;
        return false;
    }

    /**
     * Test that the given callback throws an exception of the given class.
     *
     * @param string $description
     *     A description or name of the test.
     * @param string $class
     *     The expected class of the thrown exception.
     * @param callable $callback
     *     A callback function that should throw an
     *     exception whose class is the given $class (default = \Exception).
     *
     * @return boolean
     *     Whether the test has passed or failed.
     */
    public function throws(
        $description,
        callable $callback,
        $class = 'Exception'
    ) {
        try {
            $callback();
        } catch (\Exception $e) {
            if (is_a($e, $class) || empty($class)) {
                $this->passed[] = $description;
                return true;
            }
        }
        $this->failed[] = $description;
        return false;
    }

    /**
     * Get the number of passing tests.
     *
     * @return  integer
     */
    public function getCountPassed()
    {
        return sizeof($this->passed);
    }

    /**
     * Get the number of failing tests.
     *
     * @return integer
     */
    public function getCountFailed()
    {
        return sizeof($this->failed);
    }

    /**
     * Get the number of tests total.
     *
     * @return integer
     */
    public function getCountTotal()
    {
        return $this->getCountFailed() + $this->getCountPassed();
    }

    /**
     * Returns a summary of passed tests
     *
     * @return string
     */
    public function getSummaryPassed()
    {
        if (0 === $this->getCountPassed()) {
            return ' => No tests passed :(';
        }
        return sprintf(join("\n", array_map(
            [$this, 'formatDescription'],
            $this->passed
        )), 'PASS');
    }

    /**
     * Returns a summary of failed tests
     *
     * @return string
     */
    public function getSummaryFailed()
    {
        if (0 === $this->getCountFailed()) {
            return ' => No tests failed!';
        }
        return sprintf(join("\n", array_map(
            [$this, 'formatDescription'],
            $this->failed
        )), 'FAIL');
    }

    /**
     * Returns a brief summary of number of tests run, number passed/failed.
     *
     * @return string
     */
    public function getSummaryTotal()
    {
        return sprintf(
            " => Number of tests: %d\n".
            " => Passing: %d\n".
            " => Failing: %d",
            $this->getCountTotal(),
            $this->getCountPassed(),
            $this->getCountFailed()
        );
    }

    /**
     * Format a description line for output
     *
     * @param string $description
     *     A single description line
     *
     * @return string
     */
    protected function formatDescription($description)
    {
        $description = preg_replace("/\s+/", " ", trim($description));
        $description = wordwrap($description, 60, "\n            ");
        $description = trim($description);
        return " => %1\$s :: $description";
    }

    /**
     * Returns a text summary of tests passed/failed.
     *
     * @return string
     */
    public function summary()
    {
        $cli = ('cli' == PHP_SAPI);
        $rule = str_repeat('-', 78);

        return ltrim(join("\n$rule\n", [
            $cli ? '' : '<PRE>',
            $this->getSummaryPassed(),
            $this->getSummaryFailed(),
            $this->getSummaryTotal(),
            $cli ? '' : '</PRE>',
        ]));
    }

}
