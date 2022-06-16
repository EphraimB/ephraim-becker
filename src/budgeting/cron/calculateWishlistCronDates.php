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

  function getItemsInWishlist($budget): array
  {
    $wishlist = array();
    $lastIncomeYear = date('Y');
    $lastIncomeMonth = date('n');
    $lastIncomeDay = date('j');
    $lastIncomeIndex = 0;
    $priorityStart = 0;

    for($z = 0; $z < $this->countWishList(); $z++) {
      for($k = 0; $k < count($budget); $k++) {
        if($budget[$k]["type"] == 0) {
          $wishlistInBudget = $this->calculateWishlist($k-1, $lastIncomeIndex, $budget[$k-1]["amount"], $budget[$k-1]["balance"], $lastIncomeYear, $lastIncomeMonth, $lastIncomeDay, $this->getCurrentBalance(), $budget, $wishlist, $priorityStart);
          $wishlist = $wishlistInBudget[2];

          $lastIncomeYear = $budget[$k]["year"];
          $lastIncomeMonth = $budget[$k]["month"];
          $lastIncomeDay = $budget[$k]["day"];
          $lastIncomeIndex = $k;

          $k = $wishlistInBudget[0];
        }
      }

      $priorityStart++;
    }

    return $wishlist;
  }

  function setWithdrawalCronJob()
  {
    $budget = $this->calculateBudget($this->getCurrentBalance());

    $uniqueId = 'wishlist';

    $crontab = $this->getCronTabManager();
    $crontab->remove_cronjob('/id=' . $uniqueId . '/');

    $wishlist = $this->getItemsInWishlist($budget);

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
