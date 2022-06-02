<?php
declare(strict_types=1);

$home = getenv('HOME');

require_once($home . '/config.php');
require($home . "/public_html/budgeting/index.php");

class CalculateWishlistCronDates extends Budgeting
{
  private $link;
  private $cronTabManager;
  private $cronJobUniqueId;

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

  function setCronTabManager($cronTabManager)
  {
    $this->cronTabManager = $cronTabManager;
  }

  function getCronTabManager()
  {
    return $this->cronTabManager;
  }

  function calculateWishlist($index, $lastIncomeIndex, $amount, $balance, $lastIncomeYear, $lastIncomeMonth, $lastIncomeDay, $currentBalance, $budget): array
  {
    $wishlist = array();
    $query = $this->getWishlist();
    $queryResult = mysqli_query($this->getLink(), $query);

    while($row = mysqli_fetch_array($queryResult)) {
      $id = $row['id'];
      $title = $row['title'];
      $wishlistAmount = floatval($row['price']);
      $frequency = $row['frequency'];
      $type = intval($row['type']);

      if($wishlistAmount < $balance) {
        $balance = $this->calculateAmount($wishlistAmount, $type, $lastIncomeIndex+1, $currentBalance, $budget);

        array_splice($budget, $lastIncomeIndex+1, 0, array(array(
          "year" => $lastIncomeYear,
          "month" => $lastIncomeMonth,
          "day" => $lastIncomeDay,
          "title" => $title,
          "amount" => number_format(round($wishlistAmount, 2), 2),
          "balance" => $balance,
          "type" => $type
        )));

        array_push($wishlist, array(
          "year" => $lastIncomeYear,
          "month" => $lastIncomeMonth,
          "day" => $lastIncomeDay,
          "title" => $title,
          "amount" => number_format(round($wishlistAmount, 2), 2),
          "balance" => $balance,
          "type" => $type
        ));

        $sql = $this->getLink()->prepare("UPDATE WantToBuy SET Finished=? WHERE WantToBuyId=?");
        $sql->bind_param('ii', $flag, $id);

        $flag = 1;

        $sql->execute();

        $index++;
      }
    }

    $index++;

    return array($index, $wishlist, $budget);
  }

  function fetchBudget(): array
  {
    $weeklyIndex = 0;
    $budget = array();
    $wishlist = array();
    for($l = 0; $l < 3; $l++) {
      $query = $this->expensesTableQuery($l);
      $queryResult = mysqli_query($this->getLink(), $query);

      while($row = mysqli_fetch_array($queryResult)) {
        $title = $row['title'];
        $amount = floatval($row['amount']);
        $beginYear = $row['beginYear'];
        $beginMonth = $row['beginMonth'];
        $beginDay = $row['beginDay'];
        $frequency = $row['frequency'];
        $type = intval($row['type']);

        array_push($budget, array(
          "year" => $beginYear,
          "month" => $beginMonth,
          "day" => $beginDay,
          "title" => $title,
          "amount" => number_format(round($amount, 2), 2),
          "balance" => 0,
          "type" => $type
        ));
      }

      for($j = 0; $j < 4; $j++) {
        $queryTwo = $this->loopWeeksUntilMonths($weeklyIndex);
        $queryTwoResult = mysqli_query($this->getLink(), $queryTwo);

        while($rowTwo = mysqli_fetch_array($queryTwoResult)) {
          $title = $rowTwo['title'];
          $amount = floatval($rowTwo['amount']);
          $beginYear = $rowTwo['beginYear'];
          $beginMonth = $rowTwo['beginMonth'];
          $beginDay = $rowTwo['beginDay'];
          $frequency = $rowTwo['frequency'];
          $type = intval($rowTwo['type']);

          array_push($budget, array(
            "year" => $beginYear,
            "month" => $beginMonth,
            "day" => $beginDay,
            "title" => $title,
            "amount" => number_format(round($amount, 2), 2),
            "balance" => 0,
            "type" => $type
          ));
        }

        $weeklyIndex++;
      }
    }

    $budget = $this->getSortArrayByDate($budget);

    for($m = 0; $m < count($budget); $m++) {
      $balance = $this->calculateAmount($budget[$m]["amount"], $budget[$m]["type"], $m, $this->getCurrentBalance(), $budget);

      $budget[$m]["balance"] = $balance;
    }

    $lastIncomeYear = date('Y');
    $lastIncomeMonth = date('n');
    $lastIncomeDay = date('j');
    $lastIncomeIndex = 0;

    for($k = 0; $k < count($budget); $k++) {
      if($budget[$k]["type"] == 0 && $k > 0) {
        $wishlistInBudget = $this->calculateWishlist($k-1, $lastIncomeIndex, $budget[$k-1]["amount"], $budget[$k-1]["balance"], $lastIncomeYear, $lastIncomeMonth, $lastIncomeDay, $this->getCurrentBalance(), $budget);
        $wishlist = $wishlistInBudget[1];
        $budget = $wishlistInBudget[2];

        $lastIncomeYear = $budget[$k]["year"];
        $lastIncomeMonth = $budget[$k]["month"];
        $lastIncomeDay = $budget[$k]["day"];
        $lastIncomeIndex = $k;

        $k = $wishlistInBudget[0];
      }
    }

    $budget = $this->getSortArrayByDate($budget);

    for($m = 0; $m < count($budget); $m++) {
      $balance = $this->calculateAmount($budget[$m]["amount"], $budget[$m]["type"], $m, $this->getCurrentBalance(), $budget);

      $budget[$m]["balance"] = $balance;
    }

    $sql = $this->getLink()->prepare("UPDATE WantToBuy SET Finished=?");
    $sql->bind_param('i', $flag);

    $flag = 0;

    $sql->execute();

    return $wishlist;
  }

  function setWithdrawalCronJob()
  {
    $wishlist = $this->fetchBudget();
    $uniqueId = 'wishlist';

    $crontab = $this->getCronTabManager();
    $crontab->remove_cronjob('/id=' . $uniqueId . '/');

    for($i = 0; $i < count($wishlist); $i++) {
      $title = addslashes($wishlist[$i]["title"]);
      $title = addcslashes($title, ' ');

      $command = '0 8 ' . $wishlist[$i]["day"] . ' ' . $wishlist[$i]["month"] . ' * /usr/local/bin/php /home/s8gphl6pjes9/public_html/budgeting/cron/withdrawalCronJob.php withdrawalAmount=' . $wishlist[$i]["amount"] . ' withdrawalDescription=' . $title . ' id=' . $uniqueId;

      $crontab->append_cronjob($command);
    }
  }
}
$config = new Config();
$link = $config->connectToServer();
$cronTabManager = $config->connectToCron();

$calculateWishlistCronDates = new CalculateWishlistCronDates();
$calculateWishlistCronDates->setLink($link);
$calculateWishlistCronDates->setCronTabManager($cronTabManager);
$calculateWishlistCronDates->setWithdrawalCronJob();
