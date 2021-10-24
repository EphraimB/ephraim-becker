<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
  function setUp(): void
  {
    $_SERVER['DOCUMENT_ROOT'] = __DIR__ . '/../src';
    require(__DIR__ . '/../src/index.php');
  }

  function tearDown(): void
  {
    unset($_SERVER['DOCUMENT_ROOT']);
  }

    /**
  * @test
  * @runInSeparateProcess
  */
  function testGetAge(): void
  {
    $index = new Index();
    $index->setAge();

    $this->assertEquals(25, $index->getAge());
  }
}
