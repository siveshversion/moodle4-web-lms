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


$userId = 2;
$user           = $DB->get_record('user', array("id" => $userId));
$BU             = getBuByUid($userId);
$user->cm_bu_id = $BU->id;

$newData->enrolled_cnt    = get_custom_enrolled_users(60, $user);
// $newData->completed_cnt   = get_progress_by_cid(60, $user, 100);
$newData->inprogress_cnt  = get_progress_by_cid(60, $user, 50);
$newData->notstarted_cnt  = get_progress_by_cid(60, $user, 0);

print_r($newData);
exit;


$_POST['userId'] = 2;
$res =listLP();

print_r($res);

exit;

$res = courseReport();

print_r($res['Data']['cimages']);

exit;



$userId = 2;
$user           = $DB->get_record('user', array("id" => $userId));
$BU             = getBuByUid($userId);
$user->cm_bu_id = $BU->id;

$participants = get_enrolled_uids(60, $user);

print_r($participants);
exit;


$user           = $DB->get_record('user', array("id" => 48));
$BU             = getBuByUid($user->id);
$user->cm_bu_id = $BU->id;

$newData->inprogress_cnt = get_progress_by_cid(60, $user, 50);
$newData->notstarted_cnt = get_progress_by_cid(60, $user, 0);

print_r($newData);

exit;

$BuAdmin = checkisBUAdmin(47);

$moodledata             = new stdClass();
$moodledata->wsfunction = 'core_enrol_get_enrolled_users';
$moodledata->wstoken    = '6c55a6c123a01a545efc8c20fd3a0d8a';

if ($BuAdmin) {
 $moodledata->buId = 132;
}

if ($moodledata->buId) {
 $bu_assigned_userids_arr = getBUAssignedUsers($moodledata->buId);
} else {
 $enrolled_users_arr = $userids_arr;
}
$participants = getEnrolledUsers(55, $moodledata);

foreach ($bu_assigned_userids_arr as $enrolledbuuserid) {
 if (in_array($enrolledbuuserid, $participants)) {
  $enrolled_users_arr[] = $enrolledbuuserid;
 }
}

print_r($enrolled_users_arr);

exit;

$res = canCourseEdit(63, 47);

echo $res;

exit;

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
