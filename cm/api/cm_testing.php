<?php

date_default_timezone_set("Asia/Kolkata");
error_reporting(E_ALL);

include_once '../../config.php';
include_once './functions.php';
require_once "./curl.php";

$_POST["wstoken"]    = '6c55a6c123a01a545efc8c20fd3a0d8a';
$_POST['wsfunction'] = 'core_enrol_get_users_courses';

global $DB, $CFG;

echo '<pre>';

$userId         = 9;
$user           = $DB->get_record('user', array("id" => $userId));
$BU             = getBuByUid($user->id);
$user->cm_bu_id = $BU->id;
$cid            = 7;
$progress_rate  = 100;
$_POST['sdate'] = '04/05/2022';
$_POST['edate'] = '15/05/2022';
$stimestamp     = makeTimestamp($_POST['sdate']);
$etimestamp     = makeTimestamp($_POST['edate']);
$sdate = makeDate($stimestamp);
$edate = makeDate($etimestamp);

echo $stimestamp;
echo '<br>';
echo $etimestamp;

get_progress_by_cid($cid, $user, $progress_rate);

exit;

//  print_r($res);
