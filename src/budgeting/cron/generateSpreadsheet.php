<?php
declare(strict_types=1);

$home = getenv('HOME');

require_once($home . '/config.php');
require($home . "/public_html/budgeting/index.php");
require($home . '/vendor/autoload.php');

class GenerateSpreadsheet extends Budgeting
{
  private $link;
  private $depositAmount;
  private $depositDescription;

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

  /**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
  function getClient()
  {
      $home = getenv('HOME');
      $client = new Google\Client();

      $client->setAuthConfig($home . '/credentials.json');
      $client->setApplicationName("Ephraim Becker");
      $client->setScopes(['https://www.googleapis.com/auth/spreadsheets']);

      return $client;
  }

  function getSpreadsheetInfo(): array
  {
    $client = $this->getClient();

    $service = new Google_Service_Sheets($client);

    // The ID of the spreadsheet to update.
    $spreadsheetId = '1aQUD3MkEMHnwN069EZW9dwsW6OVicOQ89P40nKVQwhI';

    // The A1 notation of the values to update.
    $range = 'A2';

    $values = [
      [
        // Cell values ...
        "Successful"
      ],
      // Additional rows ...
    ];

    $requestBody = new Google_Service_Sheets_ValueRange([
      'values' => $values
    ]);

    $params = [
      'valueInputOption' => 2
    ];

    $response = $service->spreadsheets_values->update($spreadsheetId, $range, $requestBody, $params);
  }

  function headers()
  {
    $values = [
      [
        "Budget", ''
      ],
      [

      ],
      [
        'Date', 'Event', 'Amount', 'Balance'
      ]
    ];
    $data[] = new Google_Service_Sheets_ValueRange([
        'range' => 'A1',
        'values' => $values
    ]);

    return $data;
  }

  function currentBalanceInfo()
  {
    $valuesTwo = array();
    array_push($valuesTwo, array(date("n/j/Y"), 'Now', 'N/A', '$' . $this->getCurrentBalance()));
    $data[] = new Google_Service_Sheets_ValueRange([
        'range' => 'A4',
        'values' => $valuesTwo
    ]);

    return $data;
  }

  function expenses()
  {
    $valuesFour = array();
    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'A110',
      'values' => $valuesFour
    ]);
  }

  function futureTransactions($budget)
  {
    $valuesThree = array();
    $cell = 4;

    for($j = 0; $j < count($budget); $j++) {
      if($budget[$j]['type'] == 1) {
        array_push($valuesThree, array($budget[$j]["month"] . '/' . $budget[$j]["day"] . '/' . $budget[$j]["year"], $budget[$j]["title"], '$' . $budget[$j]["amount"], ('=D' . strval($cell)) . '-C' . $cell+1));
      } else {
        array_push($valuesThree, array($budget[$j]["month"] . '/' . $budget[$j]["day"] . '/' . $budget[$j]["year"], $budget[$j]["title"], $budget[$j]["amount"], ('=D' . strval($cell)) . '+C' . $cell+1));
      }

      $cell++;
    }
    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'A5',
      'values' => $valuesThree
    ]);

    return $data;
  }

  function styleTitle()
  {
    $requests = [
    new Google_Service_Sheets_Request([
      "mergeCells" => [
          "range" => [
            "sheetId" => 0,
            "startRowIndex" => 0,
            "endRowIndex" => 1,
            "startColumnIndex" => 0,
            "endColumnIndex" => 4
          ],
          "mergeType" => "MERGE_ALL"
        ]
      ]),
      new Google_Service_Sheets_Request([
        'repeatCell' => [
            'fields' => 'userEnteredFormat',
            "range" => [
              "sheetId" => 0,
              'startRowIndex' => 0,
              'endRowIndex' => 1,
              'startColumnIndex' => 0,
              'endColumnIndex' => 2,
            ],
            'cell' => [
                'userEnteredFormat' => [
                  "horizontalAlignment" => "CENTER",
                  'textFormat' => [
                    'bold' => true,
                    'fontSize' => 24,
                  ]
                ]
            ],
          ],
        ])
    ];

    $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
      'requests' => $requests
    ]);

    return $batchUpdateRequest;
  }

  function styleHeaders()
  {
    $requests = [
    new Google_Service_Sheets_Request([
        'repeatCell' => [
            'fields' => 'userEnteredFormat',
            "range" => [
              "sheetId" => 0,
              'startRowIndex' => 2,
              'endRowIndex' => 3,
              'startColumnIndex' => 0,
              'endColumnIndex' => 4,
            ],
            'cell' => [
                'userEnteredFormat' => [
                  "horizontalAlignment" => "CENTER",
                  'textFormat' => [
                    'bold' => true
                  ]
                ]
            ],
          ],
        ])
      ];

    // add request to batchUpdate
    $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
      'requests' => $requests
    ]);

    return $batchUpdateRequest;
  }

  function generateSpreadsheet(): void
  {
    $client = $this->getClient();

    $service = new Google_Service_Sheets($client);

    // The ID of the spreadsheet to update.
    $spreadsheetId = '1aQUD3MkEMHnwN069EZW9dwsW6OVicOQ89P40nKVQwhI';

    $budget = $this->calculateBudget($this->getCurrentBalance());

    $data = [];
    $data = $this->headers();
    $data = $this->currentBalanceInfo();
    $data = $this->futureTransactions($budget);
    // $data = $this->expenses();

    $body = new Google_Service_Sheets_BatchUpdateValuesRequest([
        'valueInputOption' => 2,
        'data' => $data,
    ]);

    $response = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);

    $batchUpdateRequest = $this->styleTitle();
    $result = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);

    $batchUpdateRequestTwo = $this->styleHeaders();
    $result = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequestTwo);
  }
}
$config = new Config();
$link = $config->connectToServer();

$generateSpreadsheet = new GenerateSpreadsheet();
$generateSpreadsheet->setLink($link);
$generateSpreadsheet->generateSpreadsheet();
