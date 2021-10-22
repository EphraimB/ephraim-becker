<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

require(__DIR__ . '/../src/base.php');


class BaseTest extends TestCase
{
  function testGetDocumentRoot()
  {
    $this->assertEquals('', Base::getDocumentRoot());
  }
}
