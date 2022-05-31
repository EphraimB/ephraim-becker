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

  /**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
  function getClient()
  {
      $home = getenv('HOME');
      $client = new Google_Client();
      $client->setApplicationName('Ephraim Becker');
      $client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
      $client->setAuthConfig($home . '/credentials.json');
      $client->setAccessType('offline');
      $client->setPrompt('select_account consent');

      // Load previously authorized token from a file, if it exists.
      // The file token.json stores the user's access and refresh tokens, and is
      // created automatically when the authorization flow completes for the first
      // time.
      $tokenPath = 'token.json';
      if (file_exists($tokenPath)) {
          $accessToken = json_decode(file_get_contents($tokenPath), true);
          $client->setAccessToken($accessToken);
      }

      // If there is no previous token or it's expired.
      if ($client->isAccessTokenExpired()) {
          // Refresh the token if possible, else fetch a new one.
          if ($client->getRefreshToken()) {
              $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
          } else {
              // Request authorization from the user.
              $authUrl = $client->createAuthUrl();
              printf("Open the following link in your browser:\n%s\n", $authUrl);
              print 'Enter verification code: ';
              $authCode = trim(fgets(STDIN));

              // Exchange authorization code for an access token.
              $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
              $client->setAccessToken($accessToken);

              // Check to see if there was an error.
              if (array_key_exists('error', $accessToken)) {
                  throw new Exception(join(', ', $accessToken));
              }
          }
          // Save the token to a file.
          if (!file_exists(dirname($tokenPath))) {
              mkdir(dirname($tokenPath), 0700, true);
          }
          file_put_contents($tokenPath, json_encode($client->getAccessToken()));
      }
      return $client;
  }

  function connection()
  {
    $client = $this->getClient();
    $service = new Google_Service_Sheets($client);

    // Prints the names and majors of students in a sample spreadsheet:
    // https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit
    $spreadsheetId = '1aQUD3MkEMHnwN069EZW9dwsW6OVicOQ89P40nKVQwhI';
    $range = 'Class Data!A2:E';
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $values = $response->getValues();

    if (empty($values)) {
        print "No data found.\n";
    } else {
        print "Name, Major:\n";
        foreach ($values as $row) {
            // Print columns A and E, which correspond to indices 0 and 4.
            printf("%s, %s\n", $row[0], $row[4]);
        }
    }
  }

  function testSpreadsheet(): void
  {
    $client = $this->getClient();

    // Your redirect URI can be any registered URI, but in this example
    // we redirect back to this same page
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    $client->setRedirectUri($redirect_uri);

    if(isset($_GET['code'])) {
      $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    }

    $service = new Google_Service_Sheets($client);
    $values = [
      [
          // Cell values ...
          "Test sucessfull"
      ],
    // Additional rows ...
    ];

    $body = new Google_Service_Sheets_ValueRange([
      'values' => $values
    ]);

    $params = [
        'valueInputOption' => $valueInputOption
    ];

    $result = $service->spreadsheets_values->update("1aQUD3MkEMHnwN069EZW9dwsW6OVicOQ89P40nKVQwhI", "A1", $body, $params);
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
$generateSpreadsheet->connection();
