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

//login token generation test cases

$arrInput['username'] = 'admin';
$arrInput['password'] = 'Learn@123';

$token = generate_get_user_token($arrInput);

echo 'logged in user token: ';
print_r($token['Data']['token']);

exit;

//complettion date range filter test cases

$userId         = 40;
$user           = $DB->get_record('user', array("id" => $userId));
$BU             = getBuByUid($user->id);
$user->cm_bu_id = $BU->id;
$cid            = 53;
$progress_rate  = 100;
$_POST['sdate'] = '11/07/2022';
$_POST['edate'] = '11/07/2022';
$stimestamp     = makeTimestamp($_POST['sdate']);
$etimestamp     = makeEndTimestamp($_POST['edate']);
$stimestamp     = $stimestamp + 24 * 3600;
$sdate          = makeDate($stimestamp);
$edate          = makeDate($etimestamp);

echo $stimestamp;
echo '<br>';
echo $etimestamp;

get_progress_by_cid($cid, $user, $progress_rate);

exit;

//  print_r($res);
