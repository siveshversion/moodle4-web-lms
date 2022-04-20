<?php

require_once '../../config.php';
require_once '../../lib/moodlelib.php';


global $DB, $CFG, $DB, $USER;

echo 'TimeZone: ' . $CFG->timezone . '<br>';

$learningPlans = $DB->get_records_sql("select id,lpname,coursecnt,usercnt,lpstatus,lpdays,threshold,lpdesc from {cm_admin_learning_path} where lpstatus ='active'");

$i = 0;

foreach ($learningPlans as $lp) {

    $i++;
    $currentdate = date('M d Y');

    echo '<br>';
    echo '<b><span style="color:blue">' . $i . ') LP Name: ' . $lp->lpname . '</span></b><br>';
    echo 'id: ' . $lp->id . '<br>';
    echo 'daylimit: ' . $lp->lpdays . '<br>';
    if (!empty($lp->threshold)) {
        echo 'threshold: ' . $lp->threshold . '<br>';
    }
    $enrolledusers = $DB->get_records_sql("select userid,timecreated from {cm_lp_assignment} where lp_id=$lp->id group by userid,lp_id");
    if (!empty($enrolledusers)) {
        echo 'usercnt: ' . sizeof($enrolledusers) . '<br>';
        echo '<br><b>Enrolled Stats</b><br>';
    }

    foreach ($enrolledusers as $user) {

        $uid = $user->userid;
        $assignedtime = $user->timecreated;
        $endtime = strtotime('+' . $lp->lpdays . ' days', $assignedtime);
        $sdate = date(('M d Y'), $assignedtime);
        $edate = date(('M d Y'), $endtime);

        echo '<br>';
        echo 'uid: ' . $uid . '<br>';
        echo 'Sdate: ' . $sdate . '<br>';
        echo 'Edate: ' . $edate . '<br>';

        if (!empty($lp->threshold)) {
            $notifytime = strtotime('-' . $lp->threshold . ' days', $endtime);
            $notifydate = date(('M d Y'), $notifytime);
            echo 'Ndate: ' . $notifydate . '<br>';
            if ($notifydate == $currentdate) {
                echo '<span style="color:green">Notification Triggered Successfully</span><br>';
                trigger_notification($uid, $lp, $edate);
            }
        }

    }

}

function trigger_notification($uid, $lp, $edate)
{
    global $DB, $USER;

    //online notication
    $record = new \stdClass();
    $record->useridfrom = 2; //2 is admin id by default
    $record->useridto = $uid;
    $record->subject = 'Learning Plan expiry notification';
    $record->fullmessage = 'Enrolled LP expiry warning. You cannot access this LP if your enrollment reasches it\'s end date';
    $record->fullmessageformat = 4;
    $record->fullmessagehtml = 'Your LP "' . $lp->lpname . '" is about to expire on ' . $edate . ' <br> ';
    $record->eventtype = 'LP_enrol_expiry_notifier';
    $record->timecreated = time();

    $not_id = $DB->insert_record('notifications', $record);
    if ($not_id) {
        $popper = new \stdClass();
        $popper->notificationid = $not_id;
        $DB->insert_record('message_popup_notifications', $popper);
    }

    //email notication

    $userdata = $DB->get_record('user', array('id' => $uid));

    $supportuser = $DB->get_record('user', array('id' => $USER->id));

    $a = new \stdClass();
    $a->firstname = $userdata->firstname;
    $a->lastname = $userdata->lastname;
    $a->coursename = $lp->lpname;

    $messagehtml = text_to_html($record->fullmessagehtml, false, false, true);

    email_to_user($userdata, $supportuser, $record->subject, $record->fullmessage, $messagehtml);

}
