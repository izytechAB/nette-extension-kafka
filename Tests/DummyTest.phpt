<?php
use Tester\Assert;

# Load Tester library
require __DIR__ . '/../vendor/autoload.php';       # installation by Composer


# Adjust PHP behavior and enable some Tester features (described later)
Tester\Environment::setup();


$o = new dummyTest;

Assert::same('Test OK', $o->doTest('OK'));  # we expect the same

Assert::exception(function () use ($o) {
    # we expect an exception
	$o->doTest('');
}, InvalidArgumentException::class, 'Invalid status');


class dummyTest{

    public function doTest($status){
        if (!$status) {
			throw new InvalidArgumentException('Invalid status');
		}
        return "Test {$status}";
    }
}