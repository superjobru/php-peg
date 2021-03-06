<?php

require_once __DIR__ . '/../autoloader.php';

use hafriedlander\Peg;

class ParserTestWrapper {

	function __construct($testcase, $class) {
		$this->testcase = $testcase;
		$this->class = $class;
	}

	function function_name( $str ) {
		$str = preg_replace( '/-/', '_', $str );
		$str = preg_replace( '/\$/', 'DLR', $str );
		$str = preg_replace( '/\*/', 'STR', $str );
		$str = preg_replace( '/[^\w]+/', '', $str );
		return $str;
	}

	function match($method, $string, $allowPartial = false) {
		$class = $this->class;
		$func = $this->function_name('match_' . $method);

		$parser = new $class($string);
		$res = $parser->$func();
		return ($allowPartial || $parser->pos === strlen($string)) ? $res : false;
	}

	function matches($method, $string, $allowPartial = false) {
		return $this->match($method, $string, $allowPartial) !== false;
	}

	function assertMatches($method, $string, $message = null) {
		$this->testcase->assertTrue($this->matches($method, $string), $message ? $message : "Assert parser method $method matches string $string");
	}

	function assertDoesntMatch($method, $string, $message = null) {
		$this->testcase->assertFalse($this->matches($method, $string), $message ? $message : "Assert parser method $method doesn't match string $string");
	}
}

class ParserTestBase extends \PHPUnit\Framework\TestCase {

	function buildParser($grammar, $baseClass = 'Basic') {

		$class = 'Parser_' . md5(uniqid());
		eval(Peg\Compiler::compile("
			class $class extends hafriedlander\Peg\Parser\\$baseClass {
				$grammar
			}
		"));

		return new ParserTestWrapper($this, $class);

	}

}
