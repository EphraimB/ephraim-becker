<?php
declare(strict_types=1);

session_start();

$home = getenv('HOME');

require_once($home . '/config.php');
require($home . '/vendor/autoload.php');

class GenerateSpreadsheet
{
  private $link;
  private $depositAmount;
  private $depositDescription;
  private $googleClientApiKey;

  function __construct()
  {
    $isCLI = (php_sapi_name() == 'cli');

    if(!$isCLI) {
      die("cannot run!");
    } else {
      parse_str(implode('&', array_slice($_SERVER['argv'], 1)), $_GET);
    }
  }

  function setLink($link)
  {
    $this->link = $link;
  }

  function getLink()
  {
    return $this->link;
  }

  function setGoogleClientApiKey($googleClientApiKey): void
  {
    $this->googleClientApiKey = $googleClientApiKey;
  }

  function getGoogleClientApiKey(): string
  {
    return $this->googleClientApiKey;
  }

  function testSpreadsheet(): string
  {
    $client = new Google\Client();
    $client->setApplicationName("Ephraim Becker");
    $client->setDeveloperKey($this->getGoogleClientApiKey());
    $client->setAuthConfig('credentials.json');

    $service = new Google_Service_Sheets($client);
    $result = $service->spreadsheets_values->get("1aQUD3MkEMHnwN069EZW9dwsW6OVicOQ89P40nKVQwhI", "A1");
    $numRows = $result->getValues() != null ? count($result->getValues()) : 0;

    return $numRows;
  }

  function generateSpreadsheet()
  {

  }
}
$config = new Config();
$link = $config->connectToServer();
$googleClientApiKey = $config->getGoogleClientApiKey();

$generateSpreadsheet = new GenerateSpreadsheet();
$generateSpreadsheet->setLink($link);
$generateSpreadsheet->setGoogleClientApiKey($googleClientApiKey);
$generateSpreadsheet->testSpreadsheet();
