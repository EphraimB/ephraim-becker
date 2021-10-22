<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

require(__DIR__ . '/../src/base.php');


class BaseTest extends TestCase
{
  function testGetDocumentRoot()
  {
    $base = new Base();
    $base->setDocumentRoot('');
    $base->setHeader("Testing");
    $base->setLocalStyleSheet(NULL);
    $base->setUrl('/');
    $base->setTitle("Testing");
    $base->setBody("Just testing");
    $base->setLocalScript(NULL);

    $this->assertEquals('', $base->getDocumentRoot());
  }
}
