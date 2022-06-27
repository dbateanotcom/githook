<?php
namespace App\Tests\Controller;

use AppBundle\Util\Calculator;

class GitHookTest extends \PHPUnit\Framework\TestCase
{
    public function testAdd()
    {
        $calc = new Calculator();
        $result = $calc->add(30, 12);

        // nos aseguramos que la calculadora ha sumado los números correctamente
        $this->assertEquals(42, $result);
    }
}