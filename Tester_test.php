<?php

require 'Tester.php';

# One tester is for testing the tester,
# the other is for running tests to test.
$f = new Tester;
$t = new Tester;

$t->test(
    'Five equals five passes',
    true === $f->test(
        'Five equals five (this should pass)',
        5 === 5
    )
);

$t->test(
    'True is not a null value passes',
    $f->test(
        'True is not a null value (this should pass)',
        false === is_null(true)
    )
);

$t->test(
    'Asserting that 2 equals 3 fails',
    false === $f->test(
        'Asserting that 2 equals 3 (this should fail)',
        2 == 3
    )
);

$t->test(
    'Some function returns true passes',
    $f->test(
        'Some function returns true (this should pass)',
        function () {
            return 1 + 1 === 2;
        }
    )
);

$t->test(
    'Some function returns false fails',
    false === $f->test(
        'Some function returns false (this should fail)',
        function () {
            return 1 + 0 === 2;
        }
    )
);

$t->test(
    'The array contains "bugs" passes',
    true === $f->test(
        'The array contains "bugs" (this should pass)',
        in_array('bugs', ['insects', 'spiders', 'bugs'])
    )
);

$t->test(
    'The array contains "bunny" fails',
    false === $f->test(
        'The array contains "bunny" (this should fail)',
        in_array('bunny', ['insects', 'spiders', 'bugs'])
    )
);

$t->test(
    'Bad PDO call throws a PDOException passes',
    true === $f->throws(
        'Bad PDO call throws a PDOException (this should pass)',
        function () {
            $db = new \PDO('sqlite::memory:');
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $st = $db->prepare('DROP TABLE nonexistent');
            $st->execute();
        },
        'PDOException'
    )
);

$t->test(
    'Bad PDO call throws a BadMethodCallException fails',
    false === $f->throws(
        'Bad PDO call throws a BadMethodCallException (this should fail)',
        function () {
            $f = new \PDO('sqlite::memory:');
            $f->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $s = $f->prepare('DROP TABLE nonexistent');
            $s->execute();
        },
        'BadMethodCallException'
    )
);

$t->test(
    '9 tests were run',
    9 === $f->getCountTotal()
);

$t->test(
    '5 tests passed',
    5 === $f->getCountPassed()
);

$t->test(
    '4 tests failed',
    4 === $f->getCountFailed()
);

$t->test(
    'formatDescription is working properly',
    function () use ($f) {
        $m = new ReflectionMethod($f, 'formatDescription');
        $m->setAccessible(true);
        return " => %1\$s :: foo" === $m->invoke($f, "\nfoo\n \t \r\n");
    }
);

$t->test(
    '[Meta] All tests passed',
    $t->getCountPassed() === $t->getCountTotal()
);

print $t->summary();
