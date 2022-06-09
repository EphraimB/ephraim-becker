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

    return $values;
  }

  function currentBalanceInfo()
  {
    $values = array();
    array_push($values, array(date("n/j/Y"), 'Now', 'N/A', '$' . $this->getCurrentBalance()));

    return $values;
  }

  function futureTransactions($budget)
  {
    $values = array();
    $cell = 4;

    for($j = 0; $j < count($budget); $j++) {
      if($budget[$j]['type'] == 1) {
        array_push($values, array($budget[$j]["month"] . '/' . $budget[$j]["day"] . '/' . $budget[$j]["year"], $budget[$j]["title"], '$' . $budget[$j]["amount"], ('=D' . strval($cell)) . '-C' . $cell+1));
      } else {
        array_push($values, array($budget[$j]["month"] . '/' . $budget[$j]["day"] . '/' . $budget[$j]["year"], $budget[$j]["title"], $budget[$j]["amount"], ('=D' . strval($cell)) . '+C' . $cell+1));
      }

      $cell++;
    }

    return $values;
  }

  function expensesHeader()
  {
    $values = [
      [
        "Expenses per month", ''
      ]
    ];

    return $values;
  }

  function expenses($expenses)
  {
    $values = array();
    $column = 114;

    for($j = 0; $j < count($expenses); $j++) {
      array_push($values, array($expenses[$j]["title"], '$' . $expenses[$j]["amount"]));

      $column++;
    }

    array_push($values, array('Total', '=sum(C114:C' . $column - 1 . ')'));

    return array($values, $j+1);
  }

  function foodExpensesHeader()
  {
    $values = [
      [
        "Food Expenses", ''
      ]
    ];

    return $values;
  }

  function foodExpenses($foodExpenses)
  {
    $values = array();

    for($j = 0; $j < count($foodExpenses); $j++) {
      array_push($values, array($foodExpenses[$j]["title"], '$' . $foodExpenses[$j]["amount"]));
    }

    return $values;
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

  function styleExpensesHeader()
  {
    $requests = [
    new Google_Service_Sheets_Request([
      "mergeCells" => [
          "range" => [
            "sheetId" => 0,
            "startRowIndex" => 112,
            "endRowIndex" => 113,
            "startColumnIndex" => 1,
            "endColumnIndex" => 3
          ],
          "mergeType" => "MERGE_ALL"
        ]
      ]),
      new Google_Service_Sheets_Request([
        'repeatCell' => [
            'fields' => 'userEnteredFormat',
            "range" => [
              "sheetId" => 0,
              'startRowIndex' => 112,
              'endRowIndex' => 113,
              'startColumnIndex' => 1,
              'endColumnIndex' => 3,
            ],
            'cell' => [
                'userEnteredFormat' => [
                  "horizontalAlignment" => "CENTER",
                  'textFormat' => [
                    'bold' => true,
                    'fontSize' => 12,
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

  function styleExpenses($numRows)
  {
    $requests = [
      new Google_Service_Sheets_Request([
        'repeatCell' => [
            'fields' => 'userEnteredFormat',
            "range" => [
              "sheetId" => 0,
              'startRowIndex' => 113,
              'endRowIndex' => 113 + $numRows,
              'startColumnIndex' => 1,
              'endColumnIndex' => 2,
            ],
            'cell' => [
                'userEnteredFormat' => [
                  'textFormat' => [
                    'bold' => true,
                    'fontSize' => 10,
                  ]
                ]
            ],
          ],
        ]),
        new Google_Service_Sheets_Request([
        "updateBorders" => [
          "range" => [
            "sheetId" => 0,
            "startRowIndex" => 112,
            "endRowIndex" => 113 + $numRows,
            "startColumnIndex" => 1,
            "endColumnIndex" => 3
          ],
          "top" => [
            "style" => "SOLID",
            "width" => 3,
            "color" => [
              "red" => 1.0
            ],
          ],
          "bottom" => [
            "style" => "SOLID",
            "width" => 3,
            "color" => [
              "red" => 1.0
            ],
          ],
          "right" => [
            "style" => "SOLID",
            "width" => 3,
            "color" => [
              "red" => 1.0
            ],
          ],
          "left" => [
            "style" => "SOLID",
            "width" => 3,
            "color" => [
              "red" => 1.0
            ],
          ],
        ]
      ])
    ];

    $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
      'requests' => $requests
    ]);

    return $batchUpdateRequest;
  }

  function styleFoodExpensesHeader()
  {
    $requests = [
    new Google_Service_Sheets_Request([
      "mergeCells" => [
          "range" => [
            "sheetId" => 0,
            "startRowIndex" => 112,
            "endRowIndex" => 113,
            "startColumnIndex" => 5,
            "endColumnIndex" => 7
          ],
          "mergeType" => "MERGE_ALL"
        ]
      ]),
      new Google_Service_Sheets_Request([
        'repeatCell' => [
            'fields' => 'userEnteredFormat',
            "range" => [
              "sheetId" => 0,
              'startRowIndex' => 112,
              'endRowIndex' => 113,
              'startColumnIndex' => 5,
              'endColumnIndex' => 7,
            ],
            'cell' => [
                'userEnteredFormat' => [
                  "horizontalAlignment" => "CENTER",
                  'textFormat' => [
                    'bold' => true,
                    'fontSize' => 12,
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

  function styleFoodExpenses()
  {
    $requests = [
    new Google_Service_Sheets_Request([
      "updateBorders" => [
        "range" => [
          "sheetId" => 0,
          "startRowIndex" => 112,
          "endRowIndex" => 118,
          "startColumnIndex" => 5,
          "endColumnIndex" => 7
        ],
        "top" => [
          "style" => "SOLID",
          "width" => 2,
          "color" => [
            "red" => 1.0
          ],
        ],
        "bottom" => [
          "style" => "SOLID",
          "width" => 2,
          "color" => [
            "red" => 1.0
          ],
        ],
        "right" => [
          "style" => "SOLID",
          "width" => 2,
          "color" => [
            "red" => 1.0
          ],
        ],
        "left" => [
          "style" => "SOLID",
          "width" => 2,
          "color" => [
            "red" => 1.0
          ],
        ],
      ]
    ]),
    ];

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
    $expenses = $this->getExpenses();
    $foodExpenses = $this->getFoodExpenses();

    $data = [];

    $data[] = new Google_Service_Sheets_ValueRange([
        'range' => 'A1',
        'values' => $this->headers()
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
        'range' => 'A4',
        'values' => $this->currentBalanceInfo()
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'A5',
      'values' => $this->futureTransactions($budget)
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'B113',
      'values' => $this->expensesHeader()
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'B114',
      'values' => $this->expenses($expenses)[0]
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'F113',
      'values' => $this->foodExpensesHeader()
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'F114',
      'values' => $this->foodExpenses($foodExpenses)
    ]);

    $body = new Google_Service_Sheets_BatchUpdateValuesRequest([
        'valueInputOption' => 2,
        'data' => $data,
    ]);

    $response = $service->spreadsheets_values->batchUpdate($spreadsheetId, $body);

    $batchUpdateRequest = $this->styleTitle();
    $result = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);

    $batchUpdateRequestTwo = $this->styleHeaders();
    $result = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequestTwo);

    $batchUpdateRequestThree = $this->styleExpensesHeader();
    $result = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequestThree);

    $batchUpdateRequestFour = $this->styleExpenses($this->expenses($expenses)[1]);
    $result = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequestFour);

    $batchUpdateRequestFive = $this->styleFoodExpensesHeader();
    $result = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequestFive);

    $batchUpdateRequestSix = $this->styleFoodExpenses();
    $result = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequestSix);
  }
}
$config = new Config();
$link = $config->connectToServer();

$generateSpreadsheet = new GenerateSpreadsheet();
$generateSpreadsheet->setLink($link);
$generateSpreadsheet->generateSpreadsheet();
