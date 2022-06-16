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
  private $wishlist;
  private $moneyOwed;
  private $commuteExpenses;
  private $payrollInfo;
  private $payrollTaxes;
  private $spreadsheetId;
  private $service;

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
    $columnStart = $this->futureTransactions()[1]+6;
    $columnEnd = $this->futureTransactions()[1]+6;

    for($j = 0; $j < count($this->expenses); $j++) {
      array_push($values, array($this->expenses[$j]["title"], '$' . $this->expenses[$j]["amount"]));

      $columnEnd++;
    }

    array_push($values, array('Commute expenses', "=\$I$" . $this->commuteExpenses()[2] . "*4"));
    array_push($values, array('Food expenses', "=\$H$" . $this->foodExpenses()[2] . "*4"));

    array_push($values, array('Total', '=sum(C' . $columnStart . ':C' . $columnEnd + 2 - 1 . ')'));

    return array($values, $j+3, $columnEnd+2);
  }

  function foodExpensesHeader()
  {
    $values = [
      [
        "Day", 'Meal', 'Food', 'Amount'
      ]
    ];

    return $values;
  }

  function foodExpenses()
  {
    $values = array();
    $columnStart = $this->futureTransactions()[1]+6;
    $columnEnd = $this->futureTransactions()[1]+6;
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Shabbat'];
    $day = '';
    $meals = ["Breakfast", "Lunch", "Supper"];
    $meal = '';

    for($j = 0; $j < count($this->foodExpenses); $j++) {
      for($k = 0; $k < count($days); $k++) {
        $day = $days[$this->foodExpenses[$j]["day"]];
      }

      for($l = 0; $l < count($meals); $l++) {
        $meal = $meals[$this->foodExpenses[$j]["meal"]];
      }

      array_push($values, array($day, $meal, $this->foodExpenses[$j]["title"], '$' . $this->foodExpenses[$j]["amount"]));

      $columnEnd++;
    }

    array_push($values, array('Total', '', '', '=sum(H' . $columnStart . ':H' . $columnEnd - 1 . ')'));

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

  function wishlist()
  {
    $values = array();
    $columnStart = $this->expenses()[2]+4;
    $columnEnd = $this->expenses()[2]+4;

    for($j = 0; $j < count($this->wishlist); $j++) {
      array_push($values, array($this->wishlist[$j]["title"], '$' . $this->wishlist[$j]["amount"]));

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

  function moneyOwed()
  {
    $values = array();
    $columnStart = $this->foodExpenses()[2]+4;
    $columnEnd = $this->foodExpenses()[2]+4;

    for($j = 0; $j < count($this->moneyOwed); $j++) {
      array_push($values, array($this->moneyOwed[$j]["title"], '$' . $this->moneyOwed[$j]["planAmount"], '$' . $this->moneyOwed[$j]["moneyOwedAmount"]));

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

  function commuteExpenses()
  {
    $values = array();
    $transportationMethod = '';
    $zone = '';
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Shabbat'];
    $day = 0;
    $timeOfDays = ["Morning", "Afternoon", "Evening"];
    $timeOfDay = '';
    $columnStart = $this->moneyOwed($this->moneyOwed)[2]+4;
    $columnEnd = $this->moneyOwed($this->moneyOwed)[2]+4;

    for($j = 0; $j < count($this->commuteExpenses); $j++) {
      if($this->commuteExpenses[$j]["zoneOfTransportation"] == 0) {
        $transportationMethod = "NYC Subway";
        $zone = "N/A";
      } else if($this->commuteExpenses[$j]["zoneOfTransportation"] == 1) {
        $transportationMethod = "Long Island Rail Road";
        $zone = "1";
      } else if($this->commuteExpenses[$j]["zoneOfTransportation"] == 2) {
        $transportationMethod = "Metro North";
        $zone = "Riverdale Area";
      } else if($this->commuteExpenses[$j]["zoneOfTransportation"] == 3) {
        $transportationMethod = "Long Island Rail Road";
        $zone = "3";
      } else if($this->commuteExpenses[$j]["zoneOfTransportation"] == 4) {
        $transportationMethod = "Long Island Rail Road";
        $zone = "4";
      } else if($this->commuteExpenses[$j]["zoneOfTransportation"] == 5) {
        $transportationMethod = "Metro North";
        $zone = "White Plains Area";
      } else if($this->commuteExpenses[$j]["zoneOfTransportation"] == 7) {
        $transportationMethod = "Long Island Rail Road";
        $zone = "7";
      }

      for($k = 0; $k < count($days); $k++) {
        $day = $days[$this->commuteExpenses[$j]["day"]];
      }

      for($l = 0; $l < count($days); $l++) {
        $timeOfDay = $timeOfDays[$this->commuteExpenses[$j]["timeOfDay"]];
      }

      array_push($values, array($transportationMethod, $zone, $day, $timeOfDay, '$' . $this->commuteExpenses[$j]["amount"]));

      $columnEnd++;
    }

    array_push($values, array('Total', '', '', '', '=sum(I' . $columnStart . ':I' . $columnEnd - 1 . ')'));

    return array($values, $j+1, $columnEnd);
  }

  function payrollInfoHeader()
  {
    $values = [
      [
        "Income", ""
      ]
    ];

    return $values;
  }

  function payrollInfo($lastTax)
  {
    $values = array();
    $calculateNetPay = '';
    $columnStart = $this->futureTransactions()[1]+5;

    array_push($values, array("Hours worked per day", $this->payrollInfo[0]["hoursWorked"]));
    array_push($values, array("Days worked per week", $this->payrollInfo[0]["daysPerWeek"]));
    array_push($values, array("Pay per hour", "$" . $this->payrollInfo[0]["payPerHour"]));
    
    array_push($values, array("Pay per day", "=\$L$" . $columnStart+1 . "*\$L$" . $columnStart+3));
    array_push($values, array("Pay per week", "=\$L$" . $columnStart+2 . "*\$L$" . $columnStart+4));
    array_push($values, array("Paycheck gross (bi-monthly)", "=\$L$" . $columnStart+5 . "*2.167"));

    for($j = $columnStart+1; $j < $lastTax+1; $j++) {
      $calculateNetPay .= "-\$O$" . $j;
    }

    array_push($values, array("Paycheck net (bi-monthly)", "=\$L$" . $columnStart+6 . $calculateNetPay));
    array_push($values, array("Net Net income per month", "=\$L$" . $columnStart+7 . "*2-\$C$" . $this->expenses()[2]));

    return array($values, $columnStart+8, 8);
  }

  function payrollTaxesHeader()
  {
    $values = [
      [
        "Payroll taxes", ""
      ]
    ];

    return $values;
  }

  function payrollTaxes()
  {
    $values = array();
    
    $columnStart = $this->futureTransactions()[1]+5;
    $columnEnd = $this->futureTransactions()[1]+5;

    for($j = 0; $j < count($this->payrollTaxes); $j++) {
      if($this->payrollTaxes[$j]["fixed"] == 0) {
        array_push($values, array($this->payrollTaxes[$j]["title"], '=$L$' . $this->payrollInfo(0)[1]-2 . '*' . $this->payrollTaxes[$j]["amount"]));
      } else if($this->payrollTaxes[$j]["fixed"] == 1) {
        array_push($values, array($this->payrollTaxes[$j]["title"], '$' . $this->payrollTaxes[$j]["amount"]));
      }

      $columnEnd++;
    }

    return array($values, $j, $columnEnd);
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
        'repeatCell' => [
            'fields' => 'userEnteredFormat',
            "range" => [
              "sheetId" => 0,
              'startRowIndex' => $this->futureTransactions()[1]+4,
              'endRowIndex' => $this->futureTransactions()[1]+5,
              'startColumnIndex' => 4,
              'endColumnIndex' => 8,
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
        ]),
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
              "endColumnIndex" => 8
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
              "endColumnIndex" => 8
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

  function styleCommuteExpensesHeader()
  {
    $requests = [
    new Google_Service_Sheets_Request([
        'repeatCell' => [
            'fields' => 'userEnteredFormat',
            "range" => [
              "sheetId" => 0,
              'startRowIndex' => $this->moneyOwed()[2]+2,
              'endRowIndex' => $this->moneyOwed()[2]+3,
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

  function styleCommuteExpenses($numRows)
  {
    $requests = [
      new Google_Service_Sheets_Request([
        'repeatCell' => [
            'fields' => 'userEnteredFormat',
            "range" => [
              "sheetId" => 0,
              'startRowIndex' => $this->moneyOwed()[2]+3,
              'endRowIndex' => $this->moneyOwed()[2]+3 + $numRows,
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
              "startRowIndex" => $this->moneyOwed()[2]+2,
              "endRowIndex" => $this->moneyOwed()[2]+3 + $numRows,
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
              "startRowIndex" => $this->moneyOwed()[2]+3 + $numRows - 2,
              "endRowIndex" => $this->moneyOwed()[2]+3 + $numRows - 1,
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

  function stylePayrollInfoHeader()
  {
    $requests = [
    new Google_Service_Sheets_Request([
      "mergeCells" => [
          "range" => [
            "sheetId" => 0,
            "startRowIndex" => $this->futureTransactions()[1]+4,
            "endRowIndex" => $this->futureTransactions()[1]+5,
            "startColumnIndex" => 10,
            "endColumnIndex" => 12
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
              'startColumnIndex' => 10,
              'endColumnIndex' => 12,
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

  function stylePayrollInfo($numRows)
  {
    $requests = [
      new Google_Service_Sheets_Request([
        'repeatCell' => [
            'fields' => 'userEnteredFormat',
            "range" => [
              "sheetId" => 0,
              'startRowIndex' => $this->futureTransactions()[1]+5,
              'endRowIndex' => $this->futureTransactions()[1]+5 + $numRows,
              'startColumnIndex' => 10,
              'endColumnIndex' => 11,
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
            "startColumnIndex" => 10,
            "endColumnIndex" => 12
          ],
          "top" => [
            "style" => "SOLID",
            "width" => 3,
            "color" => [
              "green" => 1.0
            ],
          ],
          "bottom" => [
            "style" => "SOLID",
            "width" => 3,
            "color" => [
              "green" => 1.0
            ],
          ],
          "right" => [
            "style" => "SOLID",
            "width" => 3,
            "color" => [
              "green" => 1.0
            ],
          ],
          "left" => [
            "style" => "SOLID",
            "width" => 3,
            "color" => [
              "green" => 1.0
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

  function stylePayrollTaxesHeader()
  {
    $requests = [
    new Google_Service_Sheets_Request([
      "mergeCells" => [
          "range" => [
            "sheetId" => 0,
            "startRowIndex" => $this->futureTransactions()[1]+4,
            "endRowIndex" => $this->futureTransactions()[1]+5,
            "startColumnIndex" => 13,
            "endColumnIndex" => 15
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
              'startColumnIndex' => 13,
              'endColumnIndex' => 15,
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

  function stylePayrollTaxes($numRows)
  {
    $requests = [
      new Google_Service_Sheets_Request([
        'repeatCell' => [
            'fields' => 'userEnteredFormat',
            "range" => [
              "sheetId" => 0,
              'startRowIndex' => $this->futureTransactions()[1]+5,
              'endRowIndex' => $this->futureTransactions()[1]+5 + $numRows,
              'startColumnIndex' => 13,
              'endColumnIndex' => 14,
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
            "startColumnIndex" => 13,
            "endColumnIndex" => 15
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

  function overwriteExpensesAmountWithVariable($cell, $expensesCell)
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

    $result = $this->service->spreadsheets_values->update($this->spreadsheetId, 'C' . $cell, $body, $params);
  }

  function variabalizeExpensesAmount($endCell)
  {
    $cellValuePair = array();
    $cell = 5;
    $expensesStartCell = $this->futureTransactions()[1]+5;
    $expensesEndCell = $expensesStartCell + $this->expenses()[1] - 1;

    $result = $this->service->spreadsheets_values->get($this->spreadsheetId, 'B' . $cell . ':B' . $endCell);
    $numRows = $result->getValues() != null ? count($result->getValues()) : 0;

    $resultTwo = $this->service->spreadsheets_values->get($this->spreadsheetId, 'B' . $expensesStartCell . ':B' . $expensesEndCell);
    $numRowsTwo = $resultTwo->getValues() != null ? count($resultTwo->getValues()) : 0;

    for($i = 0; $i < $numRows; $i++) {
      for($j = 0; $j < $numRowsTwo; $j++) {
        if($result->getValues()[$i][0] == $resultTwo->getValues()[$j][0] && $result->getValues()[$i][0] != "Commute expenses" && $result->getValues()[$i][0] != "Food expenses") {
          $this->overwriteExpensesAmountWithVariable($cell, $expensesStartCell + $j);
        }
      }

      $cell++;
    }
  }

  function overwriteWishlistAmountWithVariable($cell, $expensesCell)
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

    $result = $this->service->spreadsheets_values->update($this->spreadsheetId, 'C' . $cell, $body, $params);
  }

  function variabalizeWishlistAmount($endCell)
  {
    $cellValuePair = array();
    $cell = 5;
    $expensesStartCell = $this->expenses()[2]+4;
    $expensesEndCell = $expensesStartCell + $this->wishlist()[1] - 1;

    $result = $this->service->spreadsheets_values->get($this->spreadsheetId, 'B' . $cell . ':B' . $endCell);
    $numRows = $result->getValues() != null ? count($result->getValues()) : 0;

    $resultTwo = $this->service->spreadsheets_values->get($this->spreadsheetId, 'B' . $expensesStartCell . ':B' . $expensesEndCell);
    $numRowsTwo = $resultTwo->getValues() != null ? count($resultTwo->getValues()) : 0;

    for($i = 0; $i < $numRows; $i++) {
      for($j = 0; $j < $numRowsTwo; $j++) {
        if($result->getValues()[$i][0] == $resultTwo->getValues()[$j][0]) {
          $this->overwriteWishlistAmountWithVariable($cell, $expensesStartCell + $j);
        }
      }

      $cell++;
    }
  }

  function overwriteMoneyOwedAmountWithVariable($cell, $expensesCell)
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

    $result = $this->service->spreadsheets_values->update($this->spreadsheetId, 'C' . $cell, $body, $params);
  }

  function variabalizeMoneyOwedAmount($endCell)
  {
    $cellValuePair = array();
    $cell = 5;
    $expensesStartCell = $this->foodExpenses()[2]+4;
    $expensesEndCell = $expensesStartCell + $this->moneyOwed()[1] - 1;

    $result = $this->service->spreadsheets_values->get($this->spreadsheetId, 'B' . $cell . ':B' . $endCell);
    $numRows = $result->getValues() != null ? count($result->getValues()) : 0;

    $resultTwo = $this->service->spreadsheets_values->get($this->spreadsheetId, 'E' . $expensesStartCell . ':E' . $expensesEndCell);
    $numRowsTwo = $resultTwo->getValues() != null ? count($resultTwo->getValues()) : 0;

    for($i = 0; $i < $numRows; $i++) {
      for($j = 0; $j < $numRowsTwo; $j++) {
        if($result->getValues()[$i][0] == $resultTwo->getValues()[$j][0]) {
          $this->overwriteMoneyOwedAmountWithVariable($cell, $expensesStartCell + $j);
        }
      }

      $cell++;
    }
  }

  function overwritePaycheckAmountWithVariable($cell, $expensesCell)
  {
    $values = [
    [
        // Cell values ...
        '=$L$' . $expensesCell
    ],
    // Additional rows ...
    ];

    $body = new Google_Service_Sheets_ValueRange([
        'values' => $values
    ]);

    $params = [
        'valueInputOption' => 2
    ];

    $result = $this->service->spreadsheets_values->update($this->spreadsheetId, 'C' . $cell, $body, $params);
  }

  function variabalizePaycheckAmount($endCell)
  {
    $cellValuePair = array();
    $cell = 5;
    $expensesStartCell = $this->payrollInfo(0)[1] - 1;
    $expensesEndCell = $this->payrollInfo(0)[1] - 1;

    $result = $this->service->spreadsheets_values->get($this->spreadsheetId, 'B' . $cell . ':B' . $endCell);
    $numRows = $result->getValues() != null ? count($result->getValues()) : 0;

    for($i = 0; $i < $numRows; $i++) {
      if($result->getValues()[$i][0] == "Paycheck") {
        $this->overwritePaycheckAmountWithVariable($cell, $expensesStartCell);
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

    $this->service = new Google_Service_Sheets($client);

    // The ID of the spreadsheet to update.
    $this->spreadsheetId = '1aQUD3MkEMHnwN069EZW9dwsW6OVicOQ89P40nKVQwhI';

    $result = $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $this->clearFormatting());

    $this->budget = $this->calculateBudget();
    $this->budget = $this->calculateWishlistPreparation($this->budget);

    $this->expenses = $this->getExpenses();
    $this->foodExpenses = $this->getFoodExpenses();
    $this->wishlist = $this->getWishlistTable($this->getWishlist());
    $this->moneyOwed = $this->getMoneyOwedTable($this->getMoneyOwed());
    $this->commuteExpenses = $this->getCommuteExpenses();
    $this->payrollInfo = $this->getPayrollInfo();
    $this->payrollTaxes = $this->getPayrollTaxes();

    $lastTax = $this->payrollTaxes()[2];

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
      'values' => $this->wishlist()[0]
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'E' . $this->foodExpenses()[2]+3,
      'values' => $this->moneyOwedHeader()
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'E' . $this->foodExpenses()[2]+4,
      'values' => $this->moneyOwed()[0]
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'E' . $this->moneyOwed()[2]+3,
      'values' => $this->commuteExpensesHeader()
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'E' . $this->moneyOwed()[2]+4,
      'values' => $this->commuteExpenses()[0]
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'K' . $this->futureTransactions()[1]+5,
      'values' => $this->payrollInfoHeader()
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'K' . $this->futureTransactions()[1]+6,
      'values' => $this->payrollInfo($lastTax)[0]
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'N' . $this->futureTransactions()[1]+5,
      'values' => $this->payrollTaxesHeader()
    ]);

    $data[] = new Google_Service_Sheets_ValueRange([
      'range' => 'N' . $this->futureTransactions()[1]+6,
      'values' => $this->payrollTaxes()[0]
    ]);

    $body = new Google_Service_Sheets_BatchUpdateValuesRequest([
        'valueInputOption' => 2,
        'data' => $data,
    ]);

    $response = $this->service->spreadsheets_values->batchUpdate($this->spreadsheetId, $body);

    $batchUpdateRequest = $this->styleTitle();
    $result = $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequest);

    $batchUpdateRequestTwo = $this->styleHeaders();
    $result = $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequestTwo);

    $batchUpdateRequestThree = $this->styleExpensesHeader();
    $result = $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequestThree);

    $batchUpdateRequestFour = $this->styleExpenses($this->expenses()[1]);
    $result = $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequestFour);

    $batchUpdateRequestFive = $this->styleFoodExpensesHeader();
    $result = $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequestFive);

    $batchUpdateRequestSix = $this->styleFoodExpenses($this->foodExpenses()[1]);
    $result = $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequestSix);

    $batchUpdateRequestSeven = $this->styleWishlistHeader();
    $result = $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequestSeven);

    $batchUpdateRequestEight = $this->styleWishlist($this->wishlist()[1]);
    $result = $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequestEight);

    $batchUpdateRequestNine = $this->styleMoneyOwedHeader();
    $result = $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequestNine);

    $batchUpdateRequestTen = $this->styleMoneyOwed($this->moneyOwed()[1]);
    $result = $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequestTen);

    $batchUpdateRequestEleven = $this->styleCommuteExpensesHeader();
    $result = $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequestEleven);

    $batchUpdateRequestTwelve = $this->styleCommuteExpenses($this->commuteExpenses()[1]);
    $result = $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequestTwelve);

    $batchUpdateRequestThirteen = $this->stylePayrollInfoHeader();
    $result = $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequestThirteen);

    $batchUpdateRequestFourteen = $this->stylePayrollInfo($this->payrollInfo(0)[2]);
    $result = $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequestFourteen);

    $batchUpdateRequestFifteen = $this->stylePayrollTaxesHeader();
    $result = $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequestFifteen);

    $batchUpdateRequestSixteen = $this->stylePayrollTaxes($this->payrollTaxes()[1]);
    $result = $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequestSixteen);

    $batchUpdateRequestSeventeen = $this->conditionalFormatting();
    $result = $this->service->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequestSeventeen);

    $this->variabalizeExpensesAmount($this->futureTransactions()[1]);
    $this->variabalizeWishlistAmount($this->futureTransactions()[1]);
    $this->variabalizeMoneyOwedAmount($this->futureTransactions()[1]);
    $this->variabalizePaycheckAmount($this->futureTransactions()[1]);
  }
}
$config = new Config();
$link = $config->connectToServer();

$generateSpreadsheet = new GenerateSpreadsheet();
$generateSpreadsheet->setLink($link);
$generateSpreadsheet->generateSpreadsheet();
