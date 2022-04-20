<?php
/**
 *
 * @package    custom learning path
 * @subpackage competency
 * @copyright  2020 Siveshversion
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once $CFG->libdir . '/datalib.php';

function get_allcourse_name($lpid)
{
    global $DB;

    if (!$DB->record_exists('cm_admin_learning_path', array('id' => $lpid))) {
        $courseids = $DB->get_records_sql("select id,fullname,shortname,category from {course} where visible =1 and id >1 ");
    } else {
        if (!$DB->record_exists('cm_lp_course', array('lp_id' => $lpid))) {
            $courseids = $DB->get_records_sql("select id,fullname,shortname,category from {course} where visible =1 and id >1 ");
        } else {
            $courseids = $DB->get_records_sql("select id,fullname,shortname,category from {course} where id not in(select lp_courseid from {cm_lp_course} where lp_id=$lpid) and visible =1 and id >1");
        }
    }
    return $courseids;
}

function get_assigned_courses($lpid)
{
    global $DB;

    $sql = $DB->get_records_sql("select id,fullname from {course} where id in (select lp_courseid from {cm_lp_course} where lp_id =$lpid) and id >1 and visible =1 ");
    return $sql;
}

function get_lp_details($lpid)
{
    global $DB;
    $sql = $DB->get_record_sql("select id,lpname,coursecnt,usercnt,lpstatus,lpdays,threshold,lpdesc,points,lpimage,course from {cm_admin_learning_path} where id =$lpid and lpstatus ='active'");

    return $sql;
}
function get_completelp_details()
{
    global $DB, $USER;

    if (is_siteadmin()) {
        $sql = $DB->get_records_sql("select id,lpname,coursecnt,usercnt,lpstatus,lpdays,threshold,lpdesc,course,creator from {cm_admin_learning_path} where lpstatus ='active'");

    } else {
        $lp_adminid = $DB->get_record_sql("select group_concat(id) as id from {user} where cm_bu_id = (select cm_bu_id from {user} where id =$USER->id) and role in(2,4)");
        $sql = $DB->get_records_sql("select id,lpname,coursecnt,usercnt,lpstatus,lpdays,threshold,lpdesc,creator from {cm_admin_learning_path} where lpstatus ='active' and creator in ($lp_adminid->id)");
    }
    return $sql;
}

function insert_lpdetails($newlp)
{
    global $DB;

    $inserted = $DB->insert_record('cm_admin_learning_path', $newlp);
    return $inserted;
}

function update_lpdetails($updatelpstatus)
{
    global $DB;

    $updated = $DB->update_record('cm_admin_learning_path', $updatelpstatus);
    return $updated;
}

function get_alluser_name($lpid)
{
    global $DB, $USER;
    if (is_siteadmin()) {
        if (!$DB->record_exists('cm_admin_learning_path', array('id' => $lpid))) {
            $userids = $DB->get_records_sql("select id,concat(firstname,' ',lastname) as fullname,email,username,role,cm_bu_id from {user} where deleted =0 and id >2 ");
        } else {
            if (!$DB->record_exists('cm_lp_assignment', array('lp_id' => $lpid))) {
                $userids = $DB->get_records_sql("select id,concat(firstname,' ',lastname) as fullname,email,username,role,cm_bu_id from {user} where deleted =0 and id >2  ");
            } else {
                $userids = $DB->get_records_sql("select id,concat(firstname,' ',lastname) as fullname,email,username,role,cm_bu_id from {user} where id not in(select userid from {cm_lp_assignment} where lp_id=$lpid group by userid) and deleted =0 and id > 2");
            }
        }
    } else {
        if (!$DB->record_exists('cm_admin_learning_path', array('id' => $lpid))) {
            $userids = $DB->get_records_sql("select id,concat(firstname,' ',lastname) as fullname,email,username,role,cm_bu_id from {user} where deleted =0 and id >2 and cm_bu_id=$USER->cm_bu_id ");
        } else {
            if (!$DB->record_exists('cm_lp_assignment', array('lp_id' => $lpid))) {
                $userids = $DB->get_records_sql("select id,concat(firstname,' ',lastname) as fullname,email,username,role,cm_bu_id from {user} where deleted =0 and id >2 and cm_bu_id=$USER->cm_bu_id  ");
            } else {
                $userids = $DB->get_records_sql("select id,concat(firstname,' ',lastname) as fullname,email,username,role,cm_bu_id from {user} where id not in(select userid from {cm_lp_assignment} where lp_id=$lpid group by userid) and deleted =0 and id > 2 and cm_bu_id=$USER->cm_bu_id");
            }
        }
    }
    return $userids;
}

function get_assigned_users($lpid)
{
    global $DB, $USER;
    if (is_siteadmin()) {
        $sql = $DB->get_records_sql("select id,concat(firstname,' ',lastname) as fullname,email,username,role,cm_bu_id from {user} where id in (select userid from {cm_lp_assignment} where lp_id =$lpid group by userid) and deleted =0 and id > 2");
    } else {
        $sql = $DB->get_records_sql("select id,concat(firstname,' ',lastname) as fullname,email,username,role,cm_bu_id from {user} where id in (select userid from {cm_lp_assignment} where lp_id =$lpid group by userid) and deleted =0 and id > 2 and cm_bu_id = $USER->cm_bu_id");
    }
    return $sql;
}

function get_users_lp($userid)
{
    global $DB;
    $sql = $DB->get_records_sql("select lp_id from {cm_lp_assignment} where userid=$userid group by lp_id");
    return $sql;
}

function get_user_coursecompletion($courseids, $userid, $lpid)
{
    global $DB;

    /*$cids = $DB->get_record_sql("select group_concat(id) as ids from {course} where id in (select lp_courseid from {cm_lp_course} where lp_id =$lpid and ctype =1) and id >1 ");
    if(!empty($cids)){
    $sql = $DB->get_records_sql("select id from {course_completions} where userid = $userid and course in($cids->ids) and timecompleted is not null");
    }*/
    /* if(!empty($courseids)){
    $sql = $DB->get_records_sql("select id from {course_completions} where userid = $userid and course in($courseids) and timecompleted is not null");
    }*/

    // print_object($sql);
    // return $sql;

    $res = array();
    $cids = $DB->get_records_sql("select id from {course} where id in (select lp_courseid from {cm_lp_course} where lp_id =$lpid) and id >1 and visible =1 ");
    foreach ($cids as $course) {
        $totals = $DB->get_records_sql("select * from {course_modules} where course = $course->id and deletioninprogress = 0 and module != 9 ");
        $total = count($totals);

        $attempt = $DB->get_records_sql("select a.id  from {course_modules_completion} as a
		join {course_modules} as b on a.coursemoduleid = b.id
		where a.userid = $userid and b.course = $course->id and b.module != 9 and completionstate >= 1");

        $attempted = count($attempt);

        $value = $attempted / $total * 100;
        $comptnmethod = $DB->get_record('course_completion_aggr_methd', array('criteriatype' => 4, 'course' => $course->id));
        if ($comptnmethod->method == 2) {
            if ($attempted >= 1) {
                $compteprogress = 100;
            } else {
                $compteprogress = 0;
            }
        } else {
            if ($attempted != 0) {
                $compteprogress = number_format($value, 0);
            } else {
                $compteprogress = 0;
            }
        }

        if ($compteprogress == 100) {
            $res[] = $course->id;
        }
    }

    return $res;
}

/*function get_user_courseprogress($userid,$courseid){
global $DB;
$sql = $DB->get_records_sql("select id from mdl_course_completions where userid = $userid and course =$courseid and timecompleted is not null");
return $sql;
}*/

function get_user_courseprogress($userid, $courseid)
{
    global $DB;
    $res = 0;
    //$sql = $DB->get_record_sql("select id from {course_completions} where userid = $userid and course =$courseid and timecompleted is not null");
    //if(!empty($sql->id)){
    $totals = $DB->get_records_sql("select * from {course_modules} where course = $courseid and deletioninprogress = 0 and module != 9 ");
    $total = count($totals);

    $attempt = $DB->get_records_sql("select a.id  from {course_modules_completion} as a
		join {course_modules} as b on a.coursemoduleid = b.id
		where a.userid = $userid and b.course = $courseid and b.module != 9 and completionstate >= 1");

    $attempted = count($attempt);
    $value = $attempted / $total * 100;
    $comptnmethod = $DB->get_record('course_completion_aggr_methd', array('criteriatype' => 4, 'course' => $courseid));
    if ($comptnmethod->method == 2) {
        if ($attempted >= 1) {
            $compteprogress = 100;
        } else {
            $compteprogress = 0;
        }
    } else {
        if ($attempted != 0) {
            $compteprogress = number_format($value, 0);
        } else {
            $compteprogress = 0;
        }
    }

    //$sql = $DB->get_record_sql("select id from {course_completions} where userid = $userid and course =$courseid and timecompleted is not null");
    if ($compteprogress == 100) {
        $res = 2;
    } else {
        $courselastaccess = $DB->get_record_sql("select count(id) as cout,timeaccess from {user_lastaccess}  where courseid = $courseid  and userid = $userid");
        //echo "select count(id) as cout,timeaccess from {user_lastaccess}  where courseid = $courseid  and userid = $userid";
        // $sql1 = $DB->get_record_sql("select id from {course_completions} where userid = $userid and course =$courseid and timecompleted is null and timestarted is not null");
        if ($courselastaccess->cout > 0) {
            $res = 1;
        } else {
            $res = 0;
        }
    }
    return $res;
}

function get_lp_course_type($courseid, $lpid)
{
    global $DB;
    $sql = $DB->get_record_sql("select ctype from {cm_lp_course} where lp_id=$lpid and lp_courseid=$courseid");
    return $sql->ctype;
}

function readMoreHelper($story_desc, $chars = 250)
{
    $story_desc = substr($story_desc, 0, $chars);
    $story_desc = substr($story_desc, 0, strrpos($story_desc, ' '));
    $story_desc = $story_desc . " ...";
    return $story_desc;
}

function get_user_learning_path_enddate($userid, $lpid)
{
    global $DB;

    $sedates = $DB->get_record_sql("select DATE_FORMAT(FROM_UNIXTIME(timecreated), '%e-%b-%Y') AS 'startdate',
               DATE_FORMAT(DATE_ADD(FROM_UNIXTIME(timecreated), INTERVAL (select lpdays from  {cm_admin_learning_path}
               where id=$lpid) DAY), '%e-%b-%Y') AS 'enddate' from {cm_lp_assignment} where lp_id = $lpid and userid=$userid
               order by timecreated asc limit 1");

    return $sedates;
}

function get_lp_man_course($lpid)
{
    global $DB;
    $sql = $DB->get_record_sql("select count(*) as cnt,group_concat(id) as courseids from {course} where id in
	(select lp_courseid from {cm_lp_course} where lp_id=$lpid and ctype=1) and visible =1 and id > 1");
    return $sql;
}

function get_user_coursecompletion_cnt($userid, $lpid)
{
    global $DB;

    /*$cids = $DB->get_record_sql("select group_concat(id) as ids from {course} where id in (select lp_courseid from {cm_lp_course} where lp_id =$lpid) and id >1 ");
    if(!empty($cids)){
    $sql = $DB->get_records_sql("select id from {course_completions} where userid = $userid and course in($cids->ids) and timecompleted is not null");
    }  */
    $res = array();
    $cids = $DB->get_records_sql("select id from {course} where id in(select lp_courseid from {cm_lp_course} where lp_id=$lpid and ctype=1) and visible =1 and id > 1 ");
    foreach ($cids as $course) {
        $totals = $DB->get_records_sql("select * from {course_modules} where course = $course->id and deletioninprogress = 0 and module != 9 ");
        $total = count($totals);

        $attempt = $DB->get_records_sql("select a.id  from {course_modules_completion} as a
		join {course_modules} as b on a.coursemoduleid = b.id
		where a.userid = $userid and b.course = $course->id and b.module != 9 and completionstate >= 1");

        $attempted = count($attempt);

        $value = $attempted / $total * 100;
        $comptnmethod = $DB->get_record('course_completion_aggr_methd', array('criteriatype' => 4, 'course' => $course->id));
        if ($comptnmethod->method == 2) {
            if ($attempted >= 1) {
                $compteprogress = 100;
            } else {
                $compteprogress = 0;
            }
        } else {
            if ($attempted != 0) {
                $compteprogress = number_format($value, 0);
            } else {
                $compteprogress = 0;
            }
        }

        if ($compteprogress == 100) {
            $res[] = $course->id;
        }
    }

    return $res;
    //return $sql;
}

function get_course_module($courseid)
{
    global $DB;
    $sql = $DB->get_records_sql("SELECT cm.id as cmid,
c.shortname AS course,
m.name AS activitytype,
m.id as moduleid,

cm.instance as instanceid,
CASE
    WHEN cm.module = 1 THEN (SELECT a1.name FROM  {assign} a1            WHERE a1.id = cm.instance)
    WHEN cm.module = 2 THEN (SELECT a2.name FROM  {assignment} a2    WHERE a2.id = cm.instance)
    WHEN cm.module = 3 THEN (SELECT a3.name FROM {book} a3               WHERE a3.id = cm.instance)
    WHEN cm.module = 4 THEN (SELECT a4.name FROM  {chat} a4                WHERE a4.id = cm.instance)
   -- WHEN cm.module = 5 THEN (SELECT a5.name FROM  {choice} a5            WHERE a5.id = cm.instance)
   -- WHEN cm.module = 6 THEN (SELECT a6.name FROM  {data} a6                WHERE a6.id = cm.instance)
    WHEN cm.module = 7 THEN (SELECT a7.name FROM  {feedback} a7        WHERE a7.id = cm.instance)
    WHEN cm.module = 8 THEN (SELECT a8.name FROM  {folder} a8              WHERE a8.id = cm.instance)
    WHEN cm.module = 9 THEN (SELECT a9.name FROM  {forum} a9              WHERE a9.id = cm.instance)
   --  WHEN cm.module = 10 THEN (SELECT a10.name FROM  {glossary} a10         WHERE a10.id = cm.instance)
   -- WHEN cm.module = 11 THEN (SELECT a11.name FROM  {imscp}  a11           WHERE a11.id = cm.instance)
    WHEN cm.module = 12 THEN (SELECT a12.name FROM  {label} a12              WHERE a12.id = cm.instance)
    WHEN cm.module = 13 THEN (SELECT a13.name FROM  {lesson} a13            WHERE a13.id = cm.instance)
   -- WHEN cm.module = 14 THEN (SELECT a14.name FROM  {lti} a14                    WHERE a14.id = cm.instance)
    WHEN cm.module = 15 THEN (SELECT a15.name FROM  {page} a15               WHERE a15.id = cm.instance)
    WHEN cm.module = 16 THEN (SELECT a16.name FROM  {quiz}  a16               WHERE a16.id = cm.instance)
    WHEN cm.module = 17 THEN (SELECT a17.name FROM  {resource} a17         WHERE a17.id = cm.instance)
    WHEN cm.module = 18 THEN (SELECT a18.name FROM  {scorm} a18             WHERE a18.id = cm.instance)
   -- WHEN cm.module = 19 THEN (SELECT a19.name FROM  {survey} a19             WHERE a19.id = cm.instance)
   -- WHEN cm.module = 20 THEN (SELECT a20.name FROM  {url} a20                      WHERE a20.id = cm.instance)
    WHEN cm.module = 21 THEN (SELECT a21.name FROM  {wiki} a21                    WHERE a21.id = cm.instance)
   -- WHEN cm.module = 22 THEN (SELECT a22.name FROM  {workshop} a22           WHERE a22.id = cm.instance)
    WHEN cm.module = 23 THEN (SELECT a23.name FROM  {attendance} a23           WHERE a23.id = cm.instance)
   -- WHEN cm.module = 24 THEN (SELECT a24.name FROM  {checklist} a24           WHERE a24.id = cm.instance)
    WHEN cm.module = 25 THEN (SELECT a25.name FROM 	{hvp} a25           WHERE a25.id = cm.instance)
   -- WHEN cm.module = 27 THEN (SELECT a27.name FROM  {dialogue} a27           WHERE a27.id = cm.instance)
   -- WHEN cm.module = 28 THEN (SELECT a28.name FROM 	{zoom} a28           WHERE a28.id = cm.instance)
   -- WHEN cm.module = 29 THEN (SELECT a29.name FROM  {icontent} a29           WHERE a29.id = cm.instance)
   -- WHEN cm.module = 30 THEN (SELECT a30.name FROM  {offlinequiz} a30           WHERE a30.id = cm.instance)
    WHEN cm.module = 32 THEN (SELECT a32.name FROM  {customcert} a32           WHERE a32.id = cm.instance)
     -- WHEN cm.module = 33 THEN (SELECT a33.name FROM  {certificate} a33           WHERE a33.id = cm.instance)
    WHEN cm.module = 34 THEN (SELECT a34.name FROM  {bigbluebuttonbn} a34           WHERE a34.id = cm.instance)
END AS 'actvityname',
 cm.section AS coursesection,
CASE
    WHEN cm.completion = 0 THEN '0 None'
    WHEN cm.completion = 1 THEN '1 Self'
    WHEN cm.completion = 2 THEN '2 Auto'
END AS activtycompletiontype


FROM
 {course_modules} cm
JOIN  {course} c ON cm.course = c.id
JOIN  {modules} m ON cm.module = m.id
# skip the predefined admin AND guest USER
WHERE  c.id=$courseid and cm.deletioninprogress=0");
    return $sql;
}

function get_course_module_completion_cnt($cid, $cmid)
{
    global $DB, $USER;
    if (is_siteadmin()) {

        $sql = $DB->get_records_sql("select* from {user} where id in (select userid from {course_modules_completion} where coursemoduleid =$cmid and completionstate =1) and id > 2 and deleted=0 ");
    } else {
        $sql = $DB->get_records_sql("select* from {user} where id in (select userid from {course_modules_completion} where coursemoduleid =$cmid and completionstate =1) and id > 2 and deleted=0 and cm_bu_id=$USER->cm_bu_id");
    }
    return $sql;

}

function get_cm_completion_details($cmid)
{
    global $DB, $USER;
    if (is_siteadmin()) {
        $sql = $DB->get_records_sql("select *,(case when timemodified !=0 then (DATE_FORMAT(FROM_UNIXTIME(timemodified), '%e %b %Y')) else '' end) AS 'lastaccess' from {course_modules_completion} WHERE coursemoduleid =$cmid and completionstate =1 and userid > 2");
    } else {
        $sql = $DB->get_records_sql("select *,(case when timemodified !=0 then (DATE_FORMAT(FROM_UNIXTIME(timemodified), '%e %b %Y')) else '' end) AS 'lastaccess' from {course_modules_completion} WHERE coursemoduleid =$cmid and completionstate =1 and userid > 2 and userid in (select id from {user} where cm_bu_id = $USER->cm_bu_id) ");
    }
    return $sql;
}
function get_cm_completion_userdetails($userid)
{
    global $DB, $USER;
    if (is_siteadmin()) {
        $sql = $DB->get_record_sql("select concat(firstname,' ',lastname) as fullname from {user} where id =$userid  and deleted = 0 ");
    } else {
        $sql = $DB->get_record_sql("select concat(firstname,' ',lastname) as fullname from {user} where id =$userid  and deleted = 0 and cm_bu_id=$USER->cm_bu_id");
    }
    return $sql->fullname;
}
function get_cm_userfinal_grade($uid, $cid, $instance)
{
    global $DB;
    $sql = $DB->get_record_sql("select rawgrademax,finalgrade from {grade_grades} where userid=$uid and itemid=(select id from {grade_items} where courseid  =$cid and iteminstance=$instance)");
    return $sql;
}
/* lp reports */

function get_lpcourse_details($lpid)
{
    global $DB;
    $sql = $DB->get_records_sql("select id,fullname,shortname,category from {course} where id in(select lp_courseid from {cm_lp_course} where lp_id=$lpid) and visible =1 and id >0 ");
    return $sql;
}

function get_lpusers_details($lpid)
{
    global $DB;
    $sql = $DB->get_records_sql("select id,concat(firstname,' ', lastname) as fullname,username,email from {user} where id in(select userid from {cm_lp_assignment} where lp_id=$lpid) and deleted =0 and id >2 ");
    return $sql;
}

function get_all_lp_completion($lpid, $userid)
{
    global $DB;
    // echo "i am here";
    $man_courses = get_lp_man_course($lpid);
    $lpunames = get_lpusers_details($lpid);
    //echo "inside";
    /*foreach ($lpunames as $lpuname) {
    $completed_course = get_user_coursecompletion($man_courses->courseids, $lpuname->id,$lpid);
    $completed_coursecnt = get_user_coursecompletion_cnt($lpuname->id,$lpid);
    $progress = 0;
    if (sizeof($completed_course) > 0) {
    $progress = ((sizeof($completed_course) / $man_courses->cnt) * 100) . "%";
    } else {
    $progress = '';
    }*/
    // if($progress == "100%"){
    // $res =  $DB->get_record_sql("select * from mdl_cm_lp_completion_stauts where lp_id = $lpid and userid = $userid"); //$DB->record_exists('cm_lp_completion_stauts', array('lp_id' => $lpid, 'userid' => $userid));
    // print_object($res);
    $lpcompletion = new stdClass();
    $lpcompletion->lp_id = $lpid;
    $lpcompletion->lp_type = 1;
    $lpcompletion->userid = $userid;
    $lpcompletion->courseid = $man_courses->courseids;
    $lpcompletion->ctype = 1;
    $lpcompletion->status = 1;
    $lpcompletion->timecreated = time();
    if (!$DB->record_exists('cm_lp_completion_stauts', array('lp_id' => $lpid, 'userid' => $userid))) { //echo "final";
        $inserted = $DB->insert_record('cm_lp_completion_stauts', $lpcompletion);
    } /*else {
    $updateid = $DB->get_record_sql("select id from {cm_lp_completion_stauts} where lp_id =$lpid and userid=$lpuname->id and status=1");
    $lpcompletion->id = $updateid->id;
    $updated =  $DB->update_record('cm_lp_completion_stauts',$lpcompletion);
    }*/

    // }
    //}

    return $inserted;
}

function get_lp_completion_details($lpid)
{
    global $DB;

    $sql = $DB->get_records_sql("select id from {cm_lp_completion_stauts} where lp_id = $lpid");
    return sizeof($sql);
}

function get_user_lpcoursecheck($userid, $courseids)
{
    global $DB;
    $res = 0;
    $sql = $DB->get_records_sql("select id from {user_lastaccess} where userid = $userid and courseid in ($courseids)");
    if (sizeof($sql) > 0) {
        $res = 1;
    } else {
        $res = 0;
    }
    return $res;
}

function get_lp_usercompletion_details($lpid, $userid)
{
    global $DB;

    $sql = $DB->get_record_sql("select id,timecreated from {cm_lp_completion_stauts} where lp_id = $lpid and userid =$userid");
    //$sql = $DB->get_record_sql("select * from {cm_lp_completion_stauts} order by id desc");
    //echo "select id,timecreated from {cm_lp_completion_stauts} where lp_id = $lpid and userid =$userid";
    //print_object($sql);
    return $sql;
}
/* Lp reports*/

function delete_lp_details($lpid)
{
    global $DB;

    $userids = $DB->get_records_sql("select distinct userid from {cm_lp_assignment} where lp_id = $lpid");

    if (sizeof($userids) > 0) {
        $courseids = $DB->get_records_sql("select lp_courseid as cid from {cm_lp_course} where lp_id = $lpid");

        if (sizeof($courseids) > 0) {
            foreach ($userids as $users) {
                foreach ($courseids as $course) {

                    $enrolled = $DB->get_record_sql("select id from {cm_lp_assignment} where userid =$users->userid and courseid =$course->cid and "
                        . "timecreated <= (select timecreated from {user_enrolments} where userid=$users->userid  and enrolid = "
                        . "(select id from {enrol} where courseid =$course->cid and enrol='manual'))");

                    if (!empty($enrolled->id)) {
                        $roleassignid = $DB->get_record_sql("select id from {role_assignments} where userid=$users->userid and contextid= "
                            . "(select id from {context} where instanceid = $course->cid and contextlevel=50) and roleid=5");
                        $userroleassign = $DB->delete_records('role_assignments', array('id' => $roleassignid->id));
                        $userenroll = $DB->get_record_sql("select id from {user_enrolments} where userid=$users->userid  and enrolid = "
                            . "(select id from {enrol} where courseid =$course->cid and enrol='manual')");
                        $userenrolled = $DB->delete_records('user_enrolments', array('id' => $userenroll->id));

                    }
                }
            }
        }
    }

    $deleteuserassign = $DB->delete_records('cm_lp_assignment', array('lp_id' => $lpid));
    $deletecourseassign = $DB->delete_records('cm_lp_course', array('lp_id' => $lpid));
    $deletecoursecompletion = $DB->delete_records('cm_lp_completion_stauts', array('lp_id' => $lpid));
    $deletelp = $DB->delete_records('cm_admin_learning_path', array('id' => $lpid));

    return 1;
}

function get_course_name($cid)
{
    global $DB;

    $sql = $DB->get_record_sql("select fullname from {course} where id = $cid");

    return $sql->fullname;
}

function get_lp_bu_name($uid)
{
    global $DB;

    // $sql = $DB->get_record_sql("select bu_name from {cm_business_units} where id = (select cm_bu_id from {user} where id = $uid) ");
    // return $sql->bu_name;
    return 'Learnospace';
}
