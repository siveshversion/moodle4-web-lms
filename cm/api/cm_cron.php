<?php

date_default_timezone_set("Asia/Kolkata");
error_reporting(E_ALL);

include_once '../../config.php';
include_once './functions.php';
require_once "./curl.php";

$_POST["wstoken"]         = '6c55a6c123a01a545efc8c20fd3a0d8a';
$_POST['wsfunction']      = 'core_enrol_get_users_courses';
$_POST['selected_filter'] = 'completed';

echo '<pre>';

$response_1 = getUserList($_POST);
$users_arr  = $response_1['Data'];

//  print_r($users_arr);
foreach ($users_arr as $user) {
 $_POST['userid'] = $user->userid;
 $response_2      = mod_get_filtered_courses($_POST);
 $courses_arr     = $response_2['Data'];
//  print_r($courses_arr);

 foreach ($courses_arr as $course) {
  // 2 indicates site admin
  $arrData['user_to_post'] = $user->userid;
  $arrData['points']       = $course['credits'];
  $arrData['point_refid']  = $course['id'];
  // 1 indicates point type as courses;
  $arrData['point_type'] = 1;
  addUserPoints($arrData);
 }
}

echo 'Custom cron executed sucessfully';
