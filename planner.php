<?php
/**
* @table
* @end End Date
* @weekNrNow
*/




include "kos18.php";
include "kos20.php";
/**
* Calculates the week number in schedule
*
*/
function getScheduleWeek($d, $additionalFactor, $mod) {

   $kw = $d->format("W");
   $kw += $additionalFactor;
   $kw = $kw % ($mod);
   if ($kw == 0)
      $kw = $mod;
   
   return $kw;

}

function getAdditionalFactor($calendarWeek, $scheduleWeek, $mod) {

   $calendarWeek -= $scheduleWeek;
   $additionalFactor = $calendarWeek % $mod;
   $additionalFactor = $mod - $additionalFactor;
   return $additionalFactor;
}

$table = ${$_GET["table"]};

$headString = "Subject,Start Date,End Date,Start Time,End Time";
echo $headString."<br>";


$begin = new DateTime( 'now' );

$end = new DateTime($_GET["datepicker-end"]);

$mod = count($table) / 7;
$additionalFactor = getAdditionalFactor($begin->format("W"), $_GET["scheduleWeek"], $mod);
$isRecalculateFactor = false;


$interval = DateInterval::createFromDateString('1 day');
$period = new DatePeriod($begin, $interval, $end);

foreach ($period as $dt) {

   $dayOfWeek = $dt->format("N"); //1 := lunes

   $tomorrow = clone $dt;
   $tomorrow->modify('+1 day');
   if ($tomorrow->format('W') == 1 && $tomorrow->format('N') == 1)//tomorrow monday & first week
      $isRecalculateFactor = true;


   $weekPlan = getScheduleWeek($dt, $additionalFactor, $mod);
   $reqStr = $weekPlan.",".$dayOfWeek;
   
   $startTime = substr($table[$reqStr], 0 , 4);
   if ($startTime != "NULL") {
      $startTime = strtotime($startTime);
      $startTime = date("H:i", $startTime);
      $endTime = substr($table[$reqStr], 4 , 4);
      $endTime = strtotime($endTime);
      $endTime = date("H:i", $endTime);
      $subj = "Woche ".$weekPlan;
      $startDate = $dt->format("d-m-Y");
      $endDate = ( (int)date("H", strtotime($startTime)) > (int)date("H", strtotime($endTime)) ) ? 
                                     $tomorrow->format("d-m-Y") : $startDate;

      echo $subj.",".$startDate.",".$endDate.",".$startTime.",".$endTime."<br>";
   }
   if ($isRecalculateFactor) {
      $additionalFactor = getAdditionalFactor($tomorrow->format('W'), $weekPlan+1, $mod);
      $isRecalculateFactor = false;
   }

}

?>
