<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

require(__DIR__ . '/../src/base.php');


class BaseTest extends TestCase
{
  function testGetDocumentRoot(): void
  {
    $base = new Base();

    $this->assertEquals('', $base->getDocumentRoot());
  }

  function testGetLocalStyleSheet(): void
  {
    $base = new Base();
    $base->setLocalStyleSheet('css/style.css');

    $this->assertEquals('css/style.css', $base->getLocalStyleSheet());
  }
}
