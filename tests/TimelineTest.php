<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

class TimelineTest extends TestCase
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
  function testGetLink(): void
  {
    $timeline = new Timeline();
    $timeline->setLink($config->connectToServer());

    $this->assertEquals(25, $timeline->getLink());
  }
}
