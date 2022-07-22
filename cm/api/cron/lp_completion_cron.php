<?php

date_default_timezone_set("Asia/Kolkata");
error_reporting(E_ALL);


include_once '../../../config.php';
include_once '../../lib/cmlib.php';

global $CFG, $DB, $USER;

echo '<pre>';

echo '<h3>LP Completion cron:</h3><br>';

$users = $DB->get_records_sql("select distinct * from {cm_lp_assignment} order by id desc");
foreach ($users as $user) {
    $uid       = $user->userid;
    $lpdetails = get_users_lp($uid);
    $mainlp    = '';
    foreach ($lpdetails as $lpks => $lpvs) {
        $lpids               = get_lp_details($lpvs->lp_id);
        $courseids           = get_assigned_courses($lpids->id);
        $man_courses         = get_lp_man_course($lpvs->lp_id);
        $completed_course    = get_user_coursecompletion($man_courses->courseids, $uid, $lpvs->lp_id);
        $completed_coursecnt = get_user_coursecompletion_cnt($uid, $lpvs->lp_id);
        $sedates             = get_user_learning_path_enddate($uid, $lpvs->lp_id);

        /* echo $completed_course;
        echo "!!!!!!!!";

        echo $man_courses->cnt;*/
        if (sizeof($completed_course) > 0) {
            $progress = ((sizeof($completed_course) / $man_courses->cnt) * 100) . "%";
            if ($progress == "100%") {

                $lpcompletion              = new stdClass();
                $lpcompletion->lp_id       = $lpvs->lp_id;
                $lpcompletion->lp_type     = 1;
                $lpcompletion->userid      = $uid;
                $lpcompletion->courseid    = $man_courses->courseids;
                $lpcompletion->ctype       = 1;
                $lpcompletion->status      = 1;
                $lpcompletion->timecreated = time();
                if (!$DB->record_exists('cm_lp_completion_stauts', array('lp_id' => $lpvs->lp_id, 'userid' => $uid))) { //echo "final";
                    $DB->insert_record('cm_lp_completion_stauts', $lpcompletion);
                    //echo "inserted";
                    //$userpoints = $DB->get_record_sql("select id,timecreated from {cm_user_points} where userid = $user->id");
                    if (!$DB->record_exists('cm_user_points', array('userid' => $uid, 'point_type' => 2, 'point_refid' => $lpvs->lp_id))) {
                        //echo "inside";
                        $lppoints = $DB->get_record_sql("Select points from {cm_admin_learning_path} where id = $lpvs->lp_id");
                        /*echo "</br>";echo $lppoints->points;echo "</br>";
                        echo $lpvs->lp_id;echo "</br>";
                        echo $user->id;echo "</br>";*/

                        $objRecord               = new stdClass();
                        $objRecord->userid       = $uid;
                        $objRecord->points       = $lppoints->points;
                        $objRecord->createdby    = $USER->id;
                        $objRecord->timemodified = time();
                        $objRecord->modifiedby   = $USER->id;
                        $objRecord->timecreated  = time();
                        $objRecord->point_type   = 2;
                        $objRecord->point_refid  = $lpvs->lp_id;
                        $DB->insert_record('cm_user_points', $objRecord);
                        //$sql = $DB->get_record_sql("select * from mdl_cm_user_points where point_type=2");
                        //print_object($sql);
                        /*if($inserted1){
                    echo "Points-inserted";
                    }else{
                    echo "NOt inserted";
                    }*/
                    } else {

                        /*if (!$DB->record_exists('cm_user_points', array('userid' => $user->id, 'points' => $points))) {
                    $mainlp .= $lpvs->lp_id.",";
                    $objRecord = new stdClass();
                    $objRecord->id = $userpoints->id;
                    $objRecord->userid = $user->id;
                    $objRecord->points = $points;
                    $objRecord->createdby = $USER->id;
                    $objRecord->timemodified = time();
                    $objRecord->modifiedby    = $USER->id;
                    $objRecord->timecreated =    $userpoints->timecreated;
                    //print_object($objRecord);
                    $DB->update_record('cm_user_points',$objRecord);
                    }*/
                    }
                }
            } else {
                if ($DB->record_exists('cm_lp_completion_stauts', array('lp_id' => $lpvs->lp_id, 'userid' => $uid))) {
                    if (sizeof($completed_course) < $man_courses->cnt) {
                        $ids = $DB->get_record_sql("Select id from {cm_lp_completion_stauts} where lp_id=$lpvs->lp_id and userid =  $uid");
                        $id  = $ids->id;

                        $res  = $DB->delete_records('cm_lp_completion_stauts', array('id' => $id));
                        $ids1 = $DB->get_record_sql("Select id from {cm_user_points} where point_ref=$lpvs->lp_id and userid =$uid and point_type =2 ");
                        $id1  = $ids1->id;
                        $res1 = $DB->delete_records('cm_user_points', array('id' => $id));
                        if ($res) {
                            echo " ";
                        }
                    }

                }
            }
        }
    }
}


echo 'LP completion cron executed sucessfully';