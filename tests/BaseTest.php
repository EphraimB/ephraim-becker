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

  function testGetUrl(): void
  {
    $base = new Base();
    $base->setURL('/tests');

    $this->assertEquals('/tests', $base->getUrl());
  }

  function testGetTitle(): void
  {
    $base = new Base();
    $base->setTitle('Testing');

    $this->assertEquals('Testing', $base->getTitle());
  }

  function testGetHeader(): void
  {
    $base = new Base();
    $base->setHeader('Testing');

    $this->assertEquals('Testing', $base->getHeader());
  }

  function testGetBody(): void
  {
    $base = new Base();
    $base->setBody('<p>Testing</p>');

    $this->assertEquals('<p>Testing</p>', $base->getBody());
  }

  function testGetLocalScript(): void
  {
    $base = new Base();
    $base->setLocalScript('js/script.js');

    $this->assertEquals('js/script.js', $base->getLocalScript());
  }

  function testEnsureValidDocumentRoot(): void
  {
    $base = new Base();

    $this->assertEquals(true, $base->ensureValidDocumentRoot());
  }
}
