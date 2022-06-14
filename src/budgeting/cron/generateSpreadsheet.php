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
  private $budget;
  private $expenses;
  private $foodExpenses;

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

  function futureTransactions()
  {
    $values = array();
    $cell = 4;

    for($j = 0; $j < count($this->budget); $j++) {
      if($this->budget[$j]['type'] == 1) {
        array_push($values, array($this->budget[$j]["month"] . '/' . $this->budget[$j]["day"] . '/' . $this->budget[$j]["year"], $this->budget[$j]["title"], '$' . $this->budget[$j]["amount"], ('=D' . strval($cell)) . '-C' . $cell+1));
      } else {
        array_push($values, array($this->budget[$j]["month"] . '/' . $this->budget[$j]["day"] . '/' . $this->budget[$j]["year"], $this->budget[$j]["title"], '$' . $this->budget[$j]["amount"], ('=D' . strval($cell)) . '+C' . $cell+1));
      }

      $cell++;
    }

    return array($values, $cell);
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

  function expenses()
  {
    $values = array();
    $columnStart = $this->futureTransactions($this->budget)[1]+5;
    $columnEnd = $this->futureTransactions($this->budget)[1]+5;

    for($j = 0; $j < count($this->expenses); $j++) {
      array_push($values, array($this->expenses[$j]["title"], '$' . $this->expenses[$j]["amount"]));

      $columnEnd++;
    }

    array_push($values, array('Total', '=sum(C' . $columnStart . ':C' . $columnEnd - 1 . ')'));

    return array($values, $j+1, $columnEnd);
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

  function foodExpenses()
  {
    $values = array();
    $columnStart = $this->futureTransactions()[1]+5;
    $columnEnd = $this->futureTransactions()[1]+5;

    for($j = 0; $j < count($this->foodExpenses); $j++) {
      array_push($values, array($this->foodExpenses[$j]["title"], '$' . $this->foodExpenses[$j]["amount"]));

      $columnEnd++;
    }

    array_push($values, array('Total', '=sum(F' . $columnStart . ':F' . $columnEnd - 1 . ')'));

    return array($values, $j+1, $columnEnd);
  }

  function wishlistHeader()
  {
    $values = [
      [
        "Wishlist", ''
      ]
    ];

    return $values;
  }

  function wishlist($wishlist)
  {
    $values = array();
    $columnStart = $this->expenses()[2]+4;
    $columnEnd = $this->expenses()[2]+4;

    for($j = 0; $j < count($wishlist); $j++) {
      array_push($values, array($wishlist[$j]["title"], '$' . $wishlist[$j]["amount"]));

      $columnEnd++;
    }

    array_push($values, array('Total', '=sum(C' . $columnStart . ':C' . $columnEnd - 1 . ')'));

    return array($values, $j+1, $columnEnd);
  }

  function moneyOwedHeader()
  {
    $values = [
      [
        "Money owed to", "Plan per month", "Money owed"
      ]
    ];

    return $values;
  }

  function moneyOwed($moneyOwed)
  {
    $values = array();
    $columnStart = $this->foodExpenses()[2]+4;
    $columnEnd = $this->foodExpenses()[2]+4;

    for($j = 0; $j < count($moneyOwed); $j++) {
      array_push($values, array($moneyOwed[$j]["title"], '$' . $moneyOwed[$j]["planAmount"], '$' . $moneyOwed[$j]["moneyOwedAmount"]));

      $columnEnd++;
    }

    array_push($values, array('Total', '=sum(F' . $columnStart . ':F' . $columnEnd - 1 . ')', '=sum(G' . $columnStart . ':G' . $columnEnd - 1 . ')'));

    return array($values, $j+1, $columnEnd);
  }

  function commuteExpensesHeader()
  {
    $values = [
      [
        "Transportation Method", "Zone", "Day", "Time of day", "Amount"
      ]
    ];

    return $values;
  }

  function commuteExpenses($commuteExpenses, $moneyOwed)
  {
    $values = array();
    $transportationMethod = '';
    $zone = '';
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Shabbat'];
    $day = 0;
    $timeOfDays = ["Morning", "Afternoon", "Evening"];
    $timeOfDay = '';
    $columnStart = $this->moneyOwed($moneyOwed)[2]+4;
    $columnEnd = $this->moneyOwed($moneyOwed)[2]+4;

    for($j = 0; $j < count($commuteExpenses); $j++) {
      if($commuteExpenses[$j]["zoneOfTransportation"] == 0) {
        $transportationMethod = "NYC Subway";
        $zone = "N/A";
      } else if($commuteExpenses[$j]["zoneOfTransportation"] == 1) {
        $transportationMethod = "Long Island Rail Road";
        $zone = "1";
      } else if($commuteExpenses[$j]["zoneOfTransportation"] == 2) {
        $transportationMethod = "Metro North";
        $zone = "Riverdale Area";
      } else if($commuteExpenses[$j]["zoneOfTransportation"] == 3) {
        $transportationMethod = "Long Island Rail Road";
        $zone = "3";
      } else if($commuteExpenses[$j]["zoneOfTransportation"] == 4) {
        $transportationMethod = "Long Island Rail Road";
        $zone = "4";
      } else if($commuteExpenses[$j]["zoneOfTransportation"] == 5) {
        $transportationMethod = "Metro North";
        $zone = "White Plains Area";
      } else if($commuteExpenses[$j]["zoneOfTransportation"] == 7) {
        $transportationMethod = "Long Island Rail Road";
        $zone = "7";
      }

      for($k = 0; $k < count($days); $k++) {
        $day = $days[$commuteExpenses[$j]["day"]];
      }

      for($l = 0; $l < count($days); $l++) {
        $timeOfDay = $timeOfDays[$commuteExpenses[$j]["timeOfDay"]];
      }

      array_push($values, array($transportationMethod, $zone, $day, $timeOfDay, '$' . $commuteExpenses[$j]["amount"]));

      $columnEnd++;
    }

    array_push($values, array('Total', '', '', '', '=sum(I' . $columnStart . ':I' . $columnEnd - 1 . ')'));

    return array($values, $j+1, $columnEnd);
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
            "startRowIndex" => $this->futureTransactions()[1]+4,
            "endRowIndex" => $this->futureTransactions()[1]+5,
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
              'startRowIndex' => $this->futureTransactions()[1]+4,
              'endRowIndex' => $this->futureTransactions()[1]+5,
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
              'startRowIndex' => $this->futureTransactions()[1]+5,
              'endRowIndex' => $this->futureTransactions()[1]+5 + $numRows,
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
            "startRowIndex" => $this->futureTransactions()[1]+4,
            "endRowIndex" => $this->futureTransactions()[1]+5 + $numRows,
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
      ]),
      new Google_Service_Sheets_Request([
        "updateBorders" => [
          "range" => [
            "sheetId" => 0,
            "startRowIndex" => $this->futureTransactions()[1]+5 + $numRows - 2,
            "endRowIndex" => $this->futureTransactions()[1]+5 + $numRows - 1,
            "startColumnIndex" => 1,
            "endColumnIndex" => 3
          ],
          "bottom" => [
            "style" => "SOLID",
            "width" => 1,
            "color" => [
              "red" => 0.0,
              "green" => 0.0,
              "blue" => 0.0
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
            "startRowIndex" => $this->futureTransactions()[1]+4,
            "endRowIndex" => $this->futureTransactions()[1]+5,
            "startColumnIndex" => 4,
            "endColumnIndex" => 6
          ],
          "mergeType" => "MERGE_ALL"
        ]
      ]),
      new Google_Service_Sheets_Request([
        'repeatCell' => [
            'fields' => 'userEnteredFormat',
            "range" => [
              "sheetId" => 0,
              'startRowIndex' => $this->futureTransactions()[1]+4,
              'endRowIndex' => $this->futureTransactions()[1]+5,
              'startColumnIndex' => 4,
              'endColumnIndex' => 6,
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

  function styleFoodExpenses($numRows)
  {
    $requests = [
      new Google_Service_Sheets_Request([
        'repeatCell' => [
            'fields' => 'userEnteredFormat',
            "range" => [
              "sheetId" => 0,
              'startRowIndex' => $this->futureTransactions()[1]+5,
              'endRowIndex' => $this->futureTransactions()[1]+5 + $numRows,
              'startColumnIndex' => 4,
              'endColumnIndex' => 5,
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
              "startRowIndex" => $this->futureTransactions()[1]+4,
              "endRowIndex" => $this->futureTransactions()[1]+5 + $numRows,
              "startColumnIndex" => 4,
              "endColumnIndex" => 6
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
        new Google_Service_Sheets_Request([
          "updateBorders" => [
            "range" => [
              "sheetId" => 0,
              "startRowIndex" => $this->futureTransactions()[1]+5 + $numRows - 2,
              "endRowIndex" => $this->futureTransactions()[1]+5 + $numRows - 1,
              "startColumnIndex" => 4,
              "endColumnIndex" => 6
            ],
            "bottom" => [
              "style" => "SOLID",
              "width" => 1,
              "color" => [
                "red" => 0.0,
                "green" => 0.0,
                "blue" => 0.0
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

  function styleWishlistHeader()
  {
    $requests = [
    new Google_Service_Sheets_Request([
      "mergeCells" => [
          "range" => [
            "sheetId" => 0,
            "startRowIndex" => $this->expenses()[2]+2,
            "endRowIndex" => $this->expenses()[2]+3,
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
              'startRowIndex' => $this->expenses()[2]+2,
              'endRowIndex' => $this->expenses()[2]+3,
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

  function styleWishlist($numRows)
  {
    $requests = [
      new Google_Service_Sheets_Request([
        'repeatCell' => [
            'fields' => 'userEnteredFormat',
            "range" => [
              "sheetId" => 0,
              'startRowIndex' => $this->expenses()[2]+3,
              'endRowIndex' => $this->expenses()[2]+3 + $numRows,
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
              "startRowIndex" => $this->expenses()[2]+2,
              "endRowIndex" => $this->expenses()[2]+3 + $numRows,
              "startColumnIndex" => 1,
              "endColumnIndex" => 3
            ],
            "top" => [
              "style" => "SOLID",
              "width" => 1,
              "color" => [
                "red" => 1.0
              ],
            ],
            "bottom" => [
              "style" => "SOLID",
              "width" => 1,
              "color" => [
                "red" => 1.0
              ],
            ],
            "right" => [
              "style" => "SOLID",
              "width" => 1,
              "color" => [
                "red" => 1.0
              ],
            ],
            "left" => [
              "style" => "SOLID",
              "width" => 1,
              "color" => [
                "red" => 1.0
              ],
            ],
          ]
        ]),
        new Google_Service_Sheets_Request([
          "updateBorders" => [
            "range" => [
              "sheetId" => 0,
              "startRowIndex" => $this->expenses()[2]+3 + $numRows - 2,
              "endRowIndex" => $this->expenses()[2]+3 + $numRows - 1,
              "startColumnIndex" => 1,
              "endColumnIndex" => 3
            ],
            "bottom" => [
              "style" => "SOLID",
              "width" => 1,
              "color" => [
                "red" => 0.0,
                "green" => 0.0,
                "blue" => 0.0
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

  function styleMoneyOwedHeader()
  {
    $requests = [
    new Google_Service_Sheets_Request([
        'repeatCell' => [
            'fields' => 'userEnteredFormat',
            "range" => [
              "sheetId" => 0,
              'startRowIndex' => $this->foodExpenses()[2]+2,
              'endRowIndex' => $this->foodExpenses()[2]+3,
              'startColumnIndex' => 4,
              'endColumnIndex' => 7,
            ],
            'cell' => [
                'userEnteredFormat' => [
                  "horizontalAlignment" => "CENTER",
                  'textFormat' => [
                    'bold' => true,
                    'fontSize' => 10,
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

  function styleMoneyOwed($numRows)
  {
    $requests = [
      new Google_Service_Sheets_Request([
        'repeatCell' => [
            'fields' => 'userEnteredFormat',
            "range" => [
              "sheetId" => 0,
              'startRowIndex' => $this->foodExpenses()[2]+3,
              'endRowIndex' => $this->foodExpenses()[2]+3 + $numRows,
              'startColumnIndex' => 4,
              'endColumnIndex' => 5,
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
              "startRowIndex" => $this->foodExpenses()[2]+2,
              "endRowIndex" => $this->foodExpenses()[2]+3 + $numRows,
              "startColumnIndex" => 4,
              "endColumnIndex" => 7
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
        ]),
        new Google_Service_Sheets_Request([
          "updateBorders" => [
            "range" => [
              "sheetId" => 0,
              "startRowIndex" => $this->foodExpenses()[2]+3 + $numRows - 2,
              "endRowIndex" => $this->foodExpenses()[2]+3 + $numRows - 1,
              "startColumnIndex" => 4,
              "endColumnIndex" => 7
            ],
            "bottom" => [
              "style" => "SOLID",
              "width" => 1,
              "color" => [
                "red" => 0.0,
                "green" => 0.0,
                "blue" => 0.0
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

  function styleCommuteExpensesHeader($moneyOwed)
  {
    $requests = [
    new Google_Service_Sheets_Request([
        'repeatCell' => [
            'fields' => 'userEnteredFormat',
            "range" => [
              "sheetId" => 0,
              'startRowIndex' => $this->moneyOwed($moneyOwed)[2]+2,
              'endRowIndex' => $this->moneyOwed($moneyOwed)[2]+3,
              'startColumnIndex' => 4,
              'endColumnIndex' => 9,
            ],
            'cell' => [
                'userEnteredFormat' => [
                  "horizontalAlignment" => "CENTER",
                  'textFormat' => [
                    'bold' => true,
                    'fontSize' => 10,
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

  function styleCommuteExpenses($numRows, $moneyOwed)
  {
    $requests = [
      new Google_Service_Sheets_Request([
        'repeatCell' => [
            'fields' => 'userEnteredFormat',
            "range" => [
              "sheetId" => 0,
              'startRowIndex' => $this->moneyOwed($moneyOwed)[2]+3,
              'endRowIndex' => $this->moneyOwed($moneyOwed)[2]+3 + $numRows,
              'startColumnIndex' => 4,
              'endColumnIndex' => 5,
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
              "startRowIndex" => $this->moneyOwed($moneyOwed)[2]+2,
              "endRowIndex" => $this->moneyOwed($moneyOwed)[2]+3 + $numRows,
              "startColumnIndex" => 4,
              "endColumnIndex" => 9
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
        new Google_Service_Sheets_Request([
          "updateBorders" => [
            "range" => [
              "sheetId" => 0,
              "startRowIndex" => $this->moneyOwed($moneyOwed)[2]+3 + $numRows - 2,
              "endRowIndex" => $this->moneyOwed($moneyOwed)[2]+3 + $numRows - 1,
              "startColumnIndex" => 4,
              "endColumnIndex" => 9
            ],
            "bottom" => [
              "style" => "SOLID",
              "width" => 1,
              "color" => [
                "red" => 0.0,
                "green" => 0.0,
                "blue" => 0.0
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

  function getWishlistTable($query)
  {
    $wishlist = array();
    $queryResult = mysqli_query($this->getLink(), $query);

    while($row = mysqli_fetch_array($queryResult)) {
      $title = $row['title'];
      $amount = floatval($row['price']);

      array_push($wishlist, array(
        "title" => $title,
        "amount" => $amount,
      ));
    }

    return $wishlist;
  }

  function getMoneyOwedTable($query)
  {
    $moneyOwed = array();
    $queryResult = mysqli_query($this->getLink(), $query);

    while($row = mysqli_fetch_array($queryResult)) {
      $title = $row['title'];
      $planAmount = floatval($row['planAmount']);
      $moneyOwedAmount = floatval($row['amountOwed']);

      array_push($moneyOwed, array(
        "title" => $title,
        "planAmount" => $planAmount,
        "moneyOwedAmount" => $moneyOwedAmount
      ));
    }

    return $moneyOwed;
  }

  function overwriteExpensesAmountWithVariable($service, $spreadsheetId, $cell, $expensesCell)
  {
    $values = [
    [
        // Cell values ...
        '=$C$' . $expensesCell
    ],
    // Additional rows ...
    ];

    $body = new Google_Service_Sheets_ValueRange([
        'values' => $values
    ]);

    $params = [
        'valueInputOption' => 2
    ];

    $result = $service->spreadsheets_values->update($spreadsheetId, 'C' . $cell, $body, $params);
  }

  function variabalizeExpensesAmount($service, $spreadsheetId, $endCell)
  {
    $cellValuePair = array();
    $cell = 5;
    $expensesStartCell = $this->futureTransactions()[1]+5;
    $expensesEndCell = $expensesStartCell + $this->expenses()[1] - 1;

    $result = $service->spreadsheets_values->get($spreadsheetId, 'B' . $cell . ':B' . $endCell);
    $numRows = $result->getValues() != null ? count($result->getValues()) : 0;

    $resultTwo = $service->spreadsheets_values->get($spreadsheetId, 'B' . $expensesStartCell . ':B' . $expensesEndCell);
    $numRowsTwo = $resultTwo->getValues() != null ? count($resultTwo->getValues()) : 0;

    for($i = 0; $i < $numRows; $i++) {
      for($j = 0; $j < $numRowsTwo; $j++) {
        if($result->getValues()[$i][0] == $resultTwo->getValues()[$j][0]) {
          $this->overwriteExpensesAmountWithVariable($service, $spreadsheetId, $cell, $expensesStartCell + $j);
        }
      }

      $cell++;
    }
  }

  function overwriteWishlistAmountWithVariable($service, $spreadsheetId, $cell, $expensesCell)
  {
    $values = [
    [
        // Cell values ...
        '=$C$' . $expensesCell
    ],
    // Additional rows ...
    ];

    $body = new Google_Service_Sheets_ValueRange([
        'values' => $values
    ]);

    $params = [
        'valueInputOption' => 2
    ];

    $result = $service->spreadsheets_values->update($spreadsheetId, 'C' . $cell, $body, $params);
  }

  function variabalizeWishlistAmount($service, $spreadsheetId, $endCell, $wishlist)
  {
    $cellValuePair = array();
    $cell = 5;
    $expensesStartCell = $this->expenses()[2]+4;
    $expensesEndCell = $expensesStartCell + $this->wishlist($wishlist)[1] - 1;

    $result = $service->spreadsheets_values->get($spreadsheetId, 'B' . $cell . ':B' . $endCell);
    $numRows = $result->getValues() != null ? count($result->getValues()) : 0;

    $resultTwo = $service->spreadsheets_values->get($spreadsheetId, 'B' . $expensesStartCell . ':B' . $expensesEndCell);
    $numRowsTwo = $resultTwo->getValues() != null ? count($resultTwo->getValues()) : 0;

    for($i = 0; $i < $numRows; $i++) {
      for($j = 0; $j < $numRowsTwo; $j++) {
        if($result->getValues()[$i][0] == $resultTwo->getValues()[$j][0]) {
          $this->overwriteWishlistAmountWithVariable($service, $spreadsheetId, $cell, $expensesStartCell + $j);
        }
      }

      $cell++;
    }
  }

  function overwriteMoneyOwedAmountWithVariable($service, $spreadsheetId, $cell, $expensesCell)
  {
    $values = [
    [
        // Cell values ...
        '=$F$' . $expensesCell
    ],
    // Additional rows ...
    ];

    $body = new Google_Service_Sheets_ValueRange([
        'values' => $values
    ]);

    $params = [
        'valueInputOption' => 2
    ];

    $result = $service->spreadsheets_values->update($spreadsheetId, 'C' . $cell, $body, $params);
  }

  function variabalizeMoneyOwedAmount($service, $spreadsheetId, $endCell, $wishlist)
  {
    $cellValuePair = array();
    $cell = 5;
    $expensesStartCell = $this->foodExpenses()[2]+4;
    $expensesEndCell = $expensesStartCell + $this->moneyOwed($wishlist)[1] - 1;

    $result = $service->spreadsheets_values->get($spreadsheetId, 'B' . $cell . ':B' . $endCell);
    $numRows = $result->getValues() != null ? count($result->getValues()) : 0;

    $resultTwo = $service->spreadsheets_values->get($spreadsheetId, 'E' . $expensesStartCell . ':E' . $expensesEndCell);
    $numRowsTwo = $resultTwo->getValues() != null ? count($resultTwo->getValues()) : 0;

    for($i = 0; $i < $numRows; $i++) {
      for($j = 0; $j < $numRowsTwo; $j++) {
        if($result->getValues()[$i][0] == $resultTwo->getValues()[$j][0]) {
          $this->overwriteMoneyOwedAmountWithVariable($service, $spreadsheetId, $cell, $expensesStartCell + $j);
        }
      }

      $cell++;
    }
  }

  function conditionalFormatting()
  {
    $myRange = [
      'sheetId' => 0,
      'startRowIndex' => 4,
      'endRowIndex' => $this->futureTransactions()[1],
      'startColumnIndex' => 3,
      'endColumnIndex' => 4,
    ];

    $requests = [
      new Google_Service_Sheets_Request([
        'addConditionalFormatRule' => [
          'rule' => [
            'ranges' => [$myRange],
              'booleanRule' => [
                'condition' => [
                  'type' => 'CUSTOM_FORMULA',
                    'values' => [['userEnteredValue' => '=GT(D5, D4)']]
                  ],
                  'format' => [
                  'backgroundColor' => ['red' => 0.0, 'green' => 1.0, 'blue' => 0.0]
                ]
              ]
          ],
          'index' => 0
        ]
      ]),
      new Google_Service_Sheets_Request([
        'addConditionalFormatRule' => [
          'rule' => [
            'ranges' => [$myRange],
              'booleanRule' => [
                'condition' => [
                  'type' => 'CUSTOM_FORMULA',
                    'values' => [['userEnteredValue' => '=LT(D5, D4)']]
                  ],
                  'format' => [
                  'backgroundColor' => ['red' => 1.0, 'green' => 0.0, 'blue' => 0.0]
                ]
              ]
          ],
          'index' => 0
        ]
      ])
    ];

    $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
      'requests' => $requests
    ]);

    return $batchUpdateRequest;
  }

  function getConditionalFormats()
  {

  }

  function clearFormatting()
  {
    $requests = [
      new Google_Service_Sheets_Request([
        "updateCells" => [
          "range" => [
            "sheetId" => 0
          ],
          "fields" => "userEnteredValue, userEnteredFormat"
        ]
      ]),
      new Google_Service_Sheets_Request([
        "deleteConditionalFormatRule" => [
          "sheetId" => 0,
          "index" => 0
        ]
      ]),
      new Google_Service_Sheets_Request([
        "deleteConditionalFormatRule" => [
          "sheetId" => 0,
          "index" => 0
        ]
      ])
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

    $result = $service->spreadsheets->batchUpdate($spreadsheetId, $this->clearFormatting());

    $this->budget = $this->calculateBudget();
    $this->budget = $this->calculateWishlistPreparation($this->budget);

    $this->expenses = $this->getExpenses();
    $this->foodExpenses = $this->getFoodExpenses();
    $wishlist = $this->getWishlistTable($this->getWishlist());
    $moneyOwed = $this->getMoneyOwedTable($this->getMoneyOwed());
    $commuteExpenses = $this->getCommuteExpenses();

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
      'values' => $this->futureTransactions()[0]
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'B' . $this->futureTransactions()[1]+5,
      'values' => $this->expensesHeader()
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'B' . $this->futureTransactions()[1]+6,
      'values' => $this->expenses()[0]
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'E' . $this->futureTransactions()[1]+5,
      'values' => $this->foodExpensesHeader()
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'E' . $this->futureTransactions()[1]+6,
      'values' => $this->foodExpenses()[0]
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'B' . $this->expenses()[2]+3,
      'values' => $this->wishlistHeader()
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'B' . $this->expenses()[2]+4,
      'values' => $this->wishlist($wishlist)[0]
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'E' . $this->foodExpenses()[2]+3,
      'values' => $this->moneyOwedHeader()
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'E' . $this->foodExpenses()[2]+4,
      'values' => $this->moneyOwed($moneyOwed)[0]
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'E' . $this->moneyOwed($moneyOwed)[2]+3,
      'values' => $this->commuteExpensesHeader()
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'E' . $this->moneyOwed($moneyOwed)[2]+4,
      'values' => $this->commuteExpenses($commuteExpenses, $moneyOwed)[0]
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

    $batchUpdateRequestFour = $this->styleExpenses($this->expenses()[1]);
    $result = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequestFour);

    $batchUpdateRequestFive = $this->styleFoodExpensesHeader();
    $result = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequestFive);

    $batchUpdateRequestSix = $this->styleFoodExpenses($this->foodExpenses()[1]);
    $result = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequestSix);

    $batchUpdateRequestSeven = $this->styleWishlistHeader();
    $result = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequestSeven);

    $batchUpdateRequestEight = $this->styleWishlist($this->wishlist($wishlist)[1]);
    $result = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequestEight);

    $batchUpdateRequestNine = $this->styleMoneyOwedHeader();
    $result = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequestNine);

    $batchUpdateRequestTen = $this->styleMoneyOwed($this->moneyOwed($moneyOwed)[1]);
    $result = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequestTen);

    $batchUpdateRequestEleven = $this->styleCommuteExpensesHeader($moneyOwed);
    $result = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequestEleven);

    $batchUpdateRequestTwelve = $this->styleCommuteExpenses($this->commuteExpenses($commuteExpenses, $moneyOwed)[1], $moneyOwed);
    $result = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequestTwelve);

    $batchUpdateRequestThirteen = $this->conditionalFormatting();
    $result = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequestThirteen);

    $this->variabalizeExpensesAmount($service, $spreadsheetId, $this->futureTransactions()[1]);
    $this->variabalizeWishlistAmount($service, $spreadsheetId, $this->futureTransactions()[1], $wishlist);
    $this->variabalizeMoneyOwedAmount($service, $spreadsheetId, $this->futureTransactions()[1], $wishlist);
  }
}
$config = new Config();
$link = $config->connectToServer();

$generateSpreadsheet = new GenerateSpreadsheet();
$generateSpreadsheet->setLink($link);
$generateSpreadsheet->generateSpreadsheet();
