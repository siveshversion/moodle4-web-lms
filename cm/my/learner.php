<?php

require_once '../../config.php';
require_once $CFG->dirroot . '/my/lib.php';
require_once $CFG->dirroot . '/cm/admin/mylib.php';
//require_once $CFG->dirroot . '/local/forum/lib.php';

redirect_if_major_upgrade_required();

// TODO Add sesskey check to edit
$edit  = optional_param('edit', null, PARAM_BOOL); // Turn editing on and off
$reset = optional_param('reset', null, PARAM_BOOL);

require_login();

$hassiteconfig = has_capability('moodle/site:config', context_system::instance());
if ($hassiteconfig && moodle_needs_upgrading()) {
    redirect(new moodle_url('/admin/index.php'));
}

$strmymoodle = get_string('myhome');

$user           = $DB->get_record('user', array("id" => $USER->id));
$BU             = getBuByUid($USER->id);
$user->cm_bu_id = $BU->id;

$assigned_userids_arr = getBUAssignedUsers($BU->id);

$userids = implode(',', $assigned_userids_arr);

if (isguestuser()) { // Force them to see system default, no editing allowed
    // If guests are not allowed my moodle, send them to front page.
    if (empty($CFG->allowguestmymoodle)) {
        redirect(new moodle_url('/', array('redirect' => 0)));
    }

    $userid        = null;
    $USER->editing = $edit = 0; // Just in case
    $context       = context_system::instance();
    $PAGE->set_blocks_editing_capability('moodle/my:configsyspages'); // unlikely :)
    $header    = "$SITE->shortname: $strmymoodle (GUEST)";
    $pagetitle = $header;

} else { // We are trying to view or edit our own My Moodle page
    $userid  = $USER->id; // Owner of the page
    $context = context_user::instance($USER->id);
    $PAGE->set_blocks_editing_capability('moodle/my:manageblocks');
    $header    = fullname($USER);
    $pagetitle = $strmymoodle;
}

// Get the My Moodle page info.  Should always return something unless the database is broken.
if (!$currentpage = my_get_page($userid, MY_PAGE_PRIVATE)) {
    print_error('mymoodlesetup');
}

// Start setting up the page
$params = array();
$PAGE->set_context($context);
$PAGE->set_url('/my/index.php', $params);
$PAGE->set_pagelayout('mydashboard');
$PAGE->set_pagetype('my-index');
$PAGE->blocks->add_region('content');
$PAGE->set_subpage($currentpage->id);
$PAGE->set_title($pagetitle);
$PAGE->set_heading($header);

if (!isguestuser()) { // Skip default home page for guests
    if (get_home_page() != HOMEPAGE_MY) {
        if (optional_param('setdefaulthome', false, PARAM_BOOL)) {
            set_user_preference('user_home_page_preference', HOMEPAGE_MY);
        } else if (!empty($CFG->defaulthomepage) && $CFG->defaulthomepage == HOMEPAGE_USER) {
            $frontpagenode = $PAGE->settingsnav->add(get_string('frontpagesettings'), null, navigation_node::TYPE_SETTING, null);
            $frontpagenode->force_open();
            $frontpagenode->add(get_string('makethismyhome'), new moodle_url('/my/', array('setdefaulthome' => true)),
                navigation_node::TYPE_SETTING);
        }
    }
}

// Toggle the editing state and switches
if (empty($CFG->forcedefaultmymoodle) && $PAGE->user_allowed_editing()) {
    if ($reset !== null) {
        if (!is_null($userid)) {
            require_sesskey();
            if (!$currentpage = my_reset_page($userid, MY_PAGE_PRIVATE)) {
                print_error('reseterror', 'my');
            }
            redirect(new moodle_url('/my'));
        }
    } else if ($edit !== null) { // Editing state was specified
        $USER->editing = $edit; // Change editing state
    } else { // Editing state is in session
        if ($currentpage->userid) { // It's a page we can edit, so load from session
            if (!empty($USER->editing)) {
                $edit = 1;
            } else {
                $edit = 0;
            }
        } else {
            // For the page to display properly with the user context header the page blocks need to
            // be copied over to the user context.

            $context = context_user::instance($USER->id);
            $PAGE->set_context($context);
            $PAGE->set_subpage($currentpage->id);
            // It's a system page and they are not allowed to edit system pages
            $USER->editing = $edit = 0; // Disable editing completely, just to be safe
        }
    }

    // Add button for editing page
    $params = array('edit' => !$edit);

    $resetbutton = '';
    $resetstring = get_string('resetpage', 'my');
    $reseturl    = new moodle_url("$CFG->wwwroot/my/index.php", array('edit' => 1, 'reset' => 1));

    if (!$currentpage->userid) {
        // viewing a system page -- let the user customise it
        $editstring     = get_string('updatemymoodleon');
        $params['edit'] = 1;
    } else if (empty($edit)) {

        $editstring = get_string('updatemymoodleon');
    } else {
        $editstring  = get_string('updatemymoodleoff');
        $resetbutton = $OUTPUT->single_button($reseturl, $resetstring);
    }

    if (is_siteadmin($USER)) {
        $url    = new moodle_url("$CFG->wwwroot/my/index.php", $params);
        $button = $OUTPUT->single_button($url, $editstring);
        $PAGE->set_button($resetbutton . $button);
    }
} else {
    $USER->editing = $edit = 0;
}

echo $OUTPUT->header();

//checking whether is the logged in user Division admin or not - Start
$vDivisionAdmin = $DB->count_records('cm_bu_admins', array("userid" => $USER->id));

?>
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
<link href="../css/material-dashboard2.css" rel="stylesheet" />

<!-- CSS Just for demo purpose, don't include it in your project -->








<div style="clear:both"></div>


<style>
#page-content {
	display : block !important ;
}

#page-header {
display :none;

}

.ulink {
	color : #FFF ;
}
.ulink:hover {
	color : #FFF ;
}

.cusTable th{
border-bottom:1px solid #cccccc;
border-top:1px solid #cccccc;
padding:10px 5px 10px 5px;
color:#2394f2;
}
.cusLink{
color:#2394f2;
}
.cusTable td{
border-top:1px solid #cccccc;
padding:10px 5px 10px 5px;
}
</style>



<style>
.cus_card {
  box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
  transition: 0.3s;
  width: 130px;
  height: 160px;
  text-align:center;
  display:inline-block !important;
  margin:5px 5px 5px 5px;;
  padding:5px 5px 5px 5px;
}

.cus_card:hover {
  box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
}

.container {
  padding: 2px 16px;
}

.nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link{
	color:#ffffff !important;
	background-color: #2393f1 !important;
	font-size:12px !important;
}

.nav-tabs{
	/*border:none !important;*/
}

.tab-pane{
	border-left:1px solid #cccccc !important;
	border-right:1px solid #cccccc !important;
	border-bottom:1px solid #cccccc !important;
	padding:20px 5px 5px 5px;
	min-height:180px;
}

.nav-tabs .nav-link.active{
	border-color:#2393f1 !important
}

</style>


<!-- start of recent discussions -->


<br>
<h5 id="instance-950-header" class="card-title" style="padding-bottom:20px; text-transform: uppercase; letter-spacing:1px;">Dashboard</h5>

	<!-- end of recent discussions -->
<section id="inst950" class=" block block_dashboard_list  card mb-4" role="complementary" data-block="dashboard_list" aria-labelledby="instance-950-header">

<div class="card-body" id="yui_3_17_2_1_1569413180492_105">


<!-- Its Admin side -->

<!-- Its User side -->

<div class="card-text content mt-3">
    <div class="container-fluid"><div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
              <a href="employee.php#enrolleddiv">
			   <div class="card-header card-header-warning card-header-icon" style="margin-bottom:40px" >
					<div class="card-icon">
                    <i class=" fa fa-users fa-fw " aria-hidden="true" aria-label=""></i>
					</div>
					<p class="card-category">Enrolled  Courses</p>
					<h3 class="card-title" id="enrolled_count_box">
					</h3>
                </div>
				</a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
               <a href="employee.php#myoverview_timeline_view">
			   <div class="card-header card-header-success card-header-icon" style="margin-bottom:40px">
					<div class="card-icon">
                    <i class=" fa fa-history fa-fw " aria-hidden="true" aria-label=""></i>
					</div>
					<p class="card-category">In Progress Courses</p>
					<h3 class="card-title" id="in_progress_count"></h3>
                </div> </a>

            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
               <a href="employee.php#myoverview_notstarted_courses">
			   <div class="card-header card-header-danger card-header-icon" style="margin-bottom:40px">
					<div class="card-icon">
                    <i class=" fa fa-undo fa-fw " aria-hidden="true" aria-label=""></i>
					</div>
					<p class="card-category">Not Started Courses</p>
					<h3 class="card-title" id="not_started_count"></h3>
                </div>
                </a>
            </div>
        </div>

		<div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card card-stats">
               <a href="employee.php#myoverview_courses_view">
			   <div class="card-header card-header-success card-header-icon" style="margin-bottom:40px">
					<div class="card-icon">
                    <i class=" fa fa-graduation-cap  fa-fw " aria-hidden="true" aria-label=""></i>
					</div>
					<p class="card-category">Completed Courses</p>
					<h3 class="card-title" id="completed_count"></h3>
                </div> </a>

            </div>
        </div>

	</div>
	</div>
    <div class="footer"></div>

</div>



</div>

</section>


<section id="inst950" class=" block block_dashboard_list  card mb-4" role="complementary" data-block="dashboard_list" aria-labelledby="instance-950-header">

<div class="card-body" id="yui_3_17_2_1_1569413180492_105">



    <div id="block-myoverview-5dd4d328210125dd4d3280c7ea2" class="block-my" data-region="myoverview">
       <ul id="block-myoverview-view-choices-5dd4d328210125dd4d3280c7ea2" class="nav nav-tabs" role="tablist">
	  <li class="nav-item" id="yui_3_17_2_1_1574228776267_98" style="text-transform: uppercase; letter-spacing:1px; font-weight:bold; color:#000;">
            <a class="nav-link  active show" href="#tab_1" role="tab" data-toggle="tab" data-tabname="courses" id="yui_3_17_2_1_1574228776267_97" aria-selected="true">

                Assigned Learning Plans
            </a>
        </li>
		 <li class="nav-item" id="yui_3_17_2_1_1574228776267_98" style="text-transform: uppercase; letter-spacing:1px;font-weight:bold;">
            <a class="nav-link" href="#tab_5" role="tab" data-toggle="tab" data-tabname="courses" id="yui_3_17_2_1_1574228776267_97" aria-selected="false">
               My Learning Plan
            </a>
        </li>


       </ul>

	   </div>

	   <div class="tab-content content-centred">

	   <div role="tabpanel" class="tab-pane fade in active show" id="tab_1">
		<div style="height: 200px;overflow-y: scroll;" >

				<?php
$userid = $USER->id;
$lpsqls = $DB->get_records_sql("select lp_id from {cm_lp_assignment} where userid=$userid group by lp_id");
if (count($lpsqls)) {
    foreach ($lpsqls as $lpsql) {
        $lpname = $DB->get_record_sql("select id,lpname from {cm_admin_learning_path} where id = $lpsql->lp_id and lpstatus ='active'");

        ?>

						<div class="">
						<div style="padding:2px 2px 5px 2px;">

		<div class="w-100 pull-left course_container" style="box-shadow: 0 0 5px 0px #ddd;margin-bottom: 10px;">

		<div class="col-md-10 pull-left" style="min-height:50px !important;padding: 0px;">
		<div class="w-100 pull-left  p-15" >
					<h5 style="padding: 10px;">

		&nbsp;<a  style="color: #008acf !important;font-size: 15px;" href="../lp/lp_courselist.php?lpid=<?php echo $lpsql->lp_id; ?>"><?php echo $lpname->lpname; ?></a>
				<a  class='card-link btn btn-primary' style="float:right;margin-right: -110px;padding: 1px 12px;" href="../lp/lp_courselist.php?lpid=<?php echo $lpsql->lp_id; ?>">
				Access	</a>

					</h5>
					<div  style="float:left;margin-bottom:5px;"><a title="Remainder" href="javascript:void(o);" class="" onclick="fnGoal('.$course->courseid.')"><img style="width:22px;margin-left: 12px;" src="../../cm/lp/img/watch3.png"></a></div>

             <span style="font-size:12px;padding-top: 3px;padding-left: 5px;"> Due Date :</span>
<?php
$courseids1 = $DB->get_record_sql("select * from {cm_lp_assignment} where lp_id = $lpsql->lp_id and userid = $USER->id");

        $completeddate = $DB->get_record_sql("select timecreated from {cm_lp_completion_stauts} where lp_id = $lpsql->lp_id  and userid = $USER->id ");
        $edate         = $completeddate->timecreated;
        $cc            = strtotime($courseids1->goal_end_date);
        $todaydate     = date("d-m-Y");
        $tdate         = strtotime($todaydate);
        if (!empty($courseids1->goal_end_date)) {
            if (!empty($edate)) {
                if ($cc > $edate) {

                    echo '<span style="font-size: 12px;
padding: 5px;">  <b style="color:green">' . $datel = date("d-m-Y", strtotime($courseids1->goal_end_date));
                    echo '</b></span>';
                } else if ($cc < $edate) {

                    echo '<span style="font-size: 12px;
padding: 5px;">  <b style="color:red">' . $datel = date("d-m-Y", strtotime($courseids1->goal_end_date));
                    echo '</b></span>';
                } else if ($tdate > $cc) {

                    echo '<span style="font-size: 12px;
padding: 5px;">  <b style="color:red">' . $datel = date("d-m-Y", strtotime($courseids1->goal_end_date));
                    echo '</b></span>';
                } else {
                    echo '<span style="font-size: 12px;
padding: 5px;">  <b>' . $datel = date("d-m-Y", strtotime($courseids1->goal_end_date));
                    echo '</b></span>';
                }
            } else {
                if ($tdate > $cc) {

                    echo '<span style="font-size: 12px;
padding: 5px;">  <b style="color:red">' . $datel = date("d-m-Y", strtotime($courseids1->goal_end_date));
                    echo '</b></span>';
                } else {
                    echo '<span style="font-size: 12px;
padding: 5px;">  <b>' . $datel = date("d-m-Y", strtotime($courseids1->goal_end_date));
                    echo '</b></span>';
                }

            }

        }
        ?>
				</div>


			</div>
                    </br></br>

		</div>
						</div>

						</div>
						<?php

    }
} else {?>

				<h5 style="padding: 10px;font-size: 15px">No Assigned Learning Plans  </h5>
		<?php	}
?>

		</div>

	</div>

    <div role="tabpanel" class="tab-pane fade" id="tab_5">
		<div style="height: 200px;overflow-y: scroll;" >

				<?php
$objMyLPCourses = $DB->get_record_sql("SELECT GROUP_CONCAT(courseid SEPARATOR ',') as courseids FROM {$CFG->prefix}cm_my_lp where userid = $USER->id");
$arrMyLPCourses = explode(',', $objMyLPCourses->courseids);

//Get courseid from My learning Plan (mdl_cm_my_lp) - End

$objMyLPCourses = $DB->get_records_sql("select b.id as courseid,b.fullname,b.summary as coursename,b.visible,points from {$CFG->prefix}cm_my_lp a,{$CFG->prefix}course b where a.userid = $USER->id and a.courseid = b.id");
if (!empty($objMyLPCourses)) {
    foreach ($objMyLPCourses as $course) {
        ?>

						<div class="">
						<div style="padding:2px 2px 5px 2px;">

		<div class="w-100 pull-left course_container" style="box-shadow: 0 0 5px 0px #ddd;margin-bottom: 10px;">

		<div class="col-md-10 pull-left" style="min-height:50px !important;padding: 0px;">
		<div class="w-100 pull-left  p-15" >
					<h5 style="padding: 10px;">

		&nbsp;<a  style="color: #008acf !important;font-size: 15px;" href="../../course/view.php?id=<?php echo $course->courseid ?>"><?php echo $course->fullname; ?></a>
				<a  class='card-link btn btn-primary' style="float:right;margin-right: -110px;padding: 1px 12px;" href="../../course/view.php?id=<?php echo $course->courseid ?>">
				Access	</a>

					</h5>
					<div  style="float:left;margin-bottom:5px;"><a title="Remainder" href="javascript:void(o);" class="" onclick="fnGoal('.$course->courseid.')"><img style="width:22px;margin-left: 12px;" src="../../cm/lp/img/watch3.png"></a></div>
<?php
$courseids = $DB->get_record_sql("select * from {cm_my_lp} where courseid = $course->courseid ");

        $completeddate = $DB->get_record_sql("select timecompleted from {course_completions} where course = $course->courseid  and userid = $USER->id ");
        $edate         = $completeddate->timecompleted;
        $cc            = strtotime($courseids->goal_end_date);
        $todaydate     = date("d-m-Y");
        $tdate         = strtotime($todaydate);
        if (!empty($courseids->goal_end_date)) {
            if (!empty($edate)) {
                if ($cc > $edate) {
                    echo '<span style="font-size: 12px;
padding: 5px;"> Due Date : &nbsp; <b style="color:green">' . $datel = date("d-m-Y", strtotime($courseids->goal_end_date));
                    echo '</b></span>';
                } else if ($cc < $edate) {

                    echo '<span style="font-size: 12px;
padding: 5px;"> Due Date :&nbsp; <b style="color:red">' . $datel = date("d-m-Y", strtotime($courseids->goal_end_date));
                    echo '</b></span>';
                } else if ($tdate > $cc) {

                    echo '<span style="font-size: 12px;
padding: 5px;"> Due Date :&nbsp; <b style="color:red">' . $datel = date("d-m-Y", strtotime($courseids->goal_end_date));
                    echo '</b></span>';
                } else {
                    echo '<span style="font-size: 12px;
padding: 5px;"> Due Date :&nbsp; <b>' . $datel = date("d-m-Y", strtotime($courseids->goal_end_date));
                    echo '</b></span>';
                }
            } else {
                if ($tdate > $cc) {

                    echo '<span style="font-size: 12px;
padding: 5px;"> Due Date :&nbsp; <b style="color:red">' . $datel = date("d-m-Y", strtotime($courseids->goal_end_date));
                    echo '</b></span>';
                } else {
                    echo '<span style="font-size: 12px;
padding: 5px;"> Due Date :&nbsp; <b>' . $datel = date("d-m-Y", strtotime($courseids->goal_end_date));
                    echo '</b></span>';
                }

            }
        }
        ?>
				</div>


			</div>
                    </br></br>

		</div>
						</div>

						</div>
						<?php

    }
} else {?>

				<h5 style="padding: 10px;font-size: 15px">No My Learning Plan</h5>
		<?php	}
?>

		</div>

	</div>

</div>


</div>

</section>


<section id="inst950" class=" block block_dashboard_list  card mb-4" role="complementary" data-block="dashboard_list" aria-labelledby="instance-950-header">

<div class="card-body" id="yui_3_17_2_1_1569413180492_105">



    <div id="block-myoverview-5dd4d328210125dd4d3280c7ea2" class="block-my" data-region="myoverview">
       <ul id="block-myoverview-view-choices-5dd4d328210125dd4d3280c7ea2" class="nav nav-tabs" role="tablist">

		 <li class="nav-item" id="yui_3_17_2_1_1574228776267_98" style="text-transform: uppercase; letter-spacing:1px;font-weight:bold;">
            <a class="nav-link active show" href="#tab_4" role="tab" data-toggle="tab" data-tabname="courses" id="yui_3_17_2_1_1574228776267_97" aria-selected="false">
               MY BADGES
            </a>
        </li>
		 <li class="nav-item" id="yui_3_17_2_1_1574228776267_98" style="text-transform: uppercase; letter-spacing:1px;font-weight:bold;">
            <a class="nav-link" href="#tab_3" role="tab" data-toggle="tab" data-tabname="courses" id="yui_3_17_2_1_1574228776267_97" aria-selected="false">
               My CERTIFICATES
            </a>
        </li>
        <li class="nav-item" id="yui_3_17_2_1_1574228776267_105" style="text-transform: uppercase; letter-spacing:1px;font-weight:bold;">
            <a class="nav-link" href="#tab_2" role="tab" data-toggle="tab" data-tabname="timeline" id="yui_3_17_2_1_1574228776267_104" aria-selected="false" >
				LEADERBOARD
            </a>
        </li>

       </ul>

	   </div>

	   <div class="tab-content content-centred">


    <div role="tabpanel" class="tab-pane fade in active show" id="tab_4">
		<div style="margin-left:20px;">
			<table cellpadding="5px" cellspacing="5px">
				<tr>
				<?php
                $q= "select b.name,b.courseid,a.badgeid,a.uniquehash from {$CFG->prefix}badge_issued a,{$CFG->prefix}badge b where a.userid = $USER->id and a.badgeid = b.id and a.visible = 1";
$objMyBadges = $DB->get_records_sql($q);
if (!empty($objMyBadges)) {
    foreach ($objMyBadges as $badge) {
        $context = $DB->get_record('context', array("contextlevel" => 50, "instanceid" => $badge->courseid));
        ?>
					<td>
						<div class="cus_card">
						<div style="padding:2px 2px 5px 2px;">
						<?php
$imageurl = moodle_url::make_pluginfile_url($context->id, 'badges', 'badgeimage', $badge->badgeid, '/', 'f1', false);
        ?>
						<a href="<?php echo $CFG->wwwroot; ?>/badges/badge.php?hash=<?php echo $badge->uniquehash; ?>"><img src="<?php echo $imageurl; ?>"></a>
						</div>
						<div style="text-align:center;border-top:1px solid #e0e0e0;padding:2px 2px 2px 2px;">
							<?php

        $vBadgeNameLen = strlen($badge->name);

        if ($vBadgeNameLen > 25) {
            $vBadgeName = substr($badge->name, 0, 20) . '...';
        } else {
            $vBadgeName = $badge->name;
        }

        echo $vBadgeName;
        ?>
						</div>
						</div>
						<?php

    }
} else {?>
								<h5 style="padding: 10px;font-size: 15px;height:100px;">No Badges   </h5>
						<?php	}
?>
					</td>

				</tr>
			</table>
		</div>

	</div>

	<div role="tabpanel" class="tab-pane fade " id="tab_2">
	<div id="menu-outer">
		  <div class="table">
			<ul id="horizontal-list">

			<?php
if (is_siteadmin()) {
    $topupoints = $DB->get_records_sql("SELECT id,userid,sum(points) as points FROM {$CFG->prefix}cm_user_points where group by userid order by points DESC limit 0,6");
} else {
    $topupoints = $DB->get_records_sql("SELECT id,userid,sum(points) as points FROM {$CFG->prefix}cm_user_points where userid in ($userids) group by userid order by points DESC limit 0,6");
}

foreach ($topupoints as $user) {
    $objUser     = $DB->get_record('user', array("id" => $user->userid));
    $userpicture = new user_picture($objUser);
    ?>
					<li>
					<?php
if ($user->userid == $USER->id) {
        $vLoggedInUser = true;
        echo $OUTPUT->user_picture($objUser, array('size' => 70));
        echo '<div style="text-align:center;font-weight:bold;color:#2393f1;font-size:15px;">' . $objUser->firstname . '</div>';

        echo '<div style="text-align:center;font-weight:bold;color:#2393f1;font-size:15px;"><b>' . $user->points . '</b></div>';
    } else {
        echo $OUTPUT->user_picture($objUser, array('size' => 40));
        echo '<div style="text-align:center">' . $objUser->firstname . '</div>';
        echo '<div style="text-align:center"><b>' . $user->points . '</b></div>';
    }
    ?>
				</li>
				<?php }

?>

			</ul>
		  </div>
		</div>


	</div>


	<div role="tabpanel" class="tab-pane fade " id="tab_3">
		<div style="margin:0px 10px 0px 10px;">
		<table cellpadding="5px" cellspacing="5px">
				<tr>
				<?php
//$objMycert = $DB->get_records_sql("select cc.certificate,c.fullname from {$CFG->prefix}cm_coursesubmit_user cc join {$CFG->prefix}course c on cc.courseid = c.id where userid = $USER->id order by cc.id DESC limit 0,6 ");

$objMycert = $DB->get_records_sql(" (select cc.certificate,c.fullname,c.id as cid from {cm_coursesubmit_user} cc
													join {course} c on cc.courseid = c.id where cc.userid = $USER->id and c.visible = 1 order by cc.id DESC)
													union (SELECT templateid  AS certificate, (SELECT fullname FROM mdl_course c WHERE c.id = course)
													AS fullname,course as cid   FROM {customcert} WHERE id IN (SELECT customcertid from {customcert_issues} WHERE userid=$USER->id)
													 order by timecreated DESC) LIMIT 0,4 ");

if (!empty($objMycert)) {
    foreach ($objMycert as $cert) {

        $cedate = $DB->get_record('course_modules', array('course' => $cert->cid, 'module' => 32));
        //print_object($cedate->enddate);
        $tdate = time();

        ?>
					<td>
						<div class="cus_card">
						<div style="padding:2px 2px 5px 2px;width:156px;">
						<?php
if (!is_numeric($cert->certificate)) {
            if (!empty($cert->certificate)) {
                $imgname = $cert->certificate;
            } else {
                $imgname = "courseera.png";
            }
            //echo $imgname ;
            ?>

						<a href="<?php echo $CFG->wwwroot; ?>/cm/course/images/certimages/<?php echo $imgname; ?> ">
						<img style="width:50%;     margin-left: -40px;" src="<?php echo $CFG->wwwroot; ?>/cm/course/images/certimages/<?php echo $imgname; ?> "></a>
							<?php } else {?>
								<a target="_blank" href="<?php echo $CFG->wwwroot; ?>/mod/customcert/view.php?id=<?php echo $cedate->id; ?> ">
						<img style="width:40%;     margin-left: -40px;" src="<?php echo $CFG->wwwroot; ?>/cm/image/certimages/temp1.jpg "></a>

							 <?php }

        ?>
						</div>
						<div style="text-align:center;padding:2px 2px 2px 2px;">
							<?php

        $vBadgeNameLen = strlen($cert->fullname);

        if ($vBadgeNameLen > 15) {
            $vBadgeName = substr($cert->fullname, 0, 15) . '...';
        } else {
            $vBadgeName = $cert->fullname;
        }

        echo $vBadgeName;
        ?>
						</div>
						</div>
						</td>
						<?php

    }
} else {?>
				<h5 style="padding: 10px;font-size: 15px;height:100px;">No Certificates    </h5>
						<?php	}

?>


				</tr>
			</table>
		</div>
	</div>


	</div>


</div>

</section>

<style>
#courses{
	position: absolute;
    top: 143px;
    left: 57%;
	font-size:16px;


}



#donut_chart{
	width:100%;
	float:left;


}
body {

	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;

}

.cus-card{
background: #fff;
padding: 15px;
float:left;
min-width:100%;
box-shadow: 0 1px 4px 0 rgba(0, 0, 0, 0.14);
font-size: 13px;
}


</style>

<style>
#menu-outer {
	height: 100px;
	background: url(image/bar-bg.jpg) repeat-x;
}

.table {
	display: table;   /* Allow the centering to work */
	margin: 0 auto;
}

ul#horizontal-list {
	min-width: 696px;
	list-style: none;
	padding-top: 0px;
	}
	ul#horizontal-list li {
		width:100px;
		display: inline-block;
		color:#666666;
		text-align:center;
		float:center;

	}


	.has-search .form-control {
  padding-left: 2.375rem;
}

/* Styles for wrapping the search box */

.main {
    width: 50%;
    margin: 50px auto;
}

/* Bootstrap 4 text input with search icon */

.has-search .form-control {
    padding-left: 2.375rem;
}

.has-search .form-control-feedback {
    position: absolute;
    z-index: 2;
    display: block;
    width: 2.375rem;
    height: 2.375rem;
    line-height: 2.375rem;
    text-align: center;
    pointer-events: none;
    color: #aaa;
}

.btn-secondary, .btn-default{
	color:#ffffff;
	background-color:#2393f1 !important;
}


</style>


	<?php

$cmuserfilter = $user->cm_bu_id;

//$courses = $DB->get_records_sql("select c.id,c.points,c.totalhours,c.fullname,c.summary,e.timecreated from {enrol}  e join {user_enrolments} ue on e.id = ue.enrolid join {course} c on e.courseid = c.id join {cm_courses} cc on cc.course_id = c.id where ue.userid = $USER->id and c.visible !=0 and cc.bu_id in($cmuserfilter) ");
//$courses = $DB->get_records_sql("select c.id,c.points,c.totalhours,c.fullname,c.summary,e.timecreated from {enrol}  e join {user_enrolments} ue on e.id = ue.enrolid join {course} c on e.courseid = c.id  where ue.userid = $USER->id and c.visible !=0 and c.cm_bu_id IN($user->cm_bu_id) OR c.id IN
//(SELECT course_id from {cm_courses} WHERE bu_id = $user->cm_bu_id)");

$courses1 = $DB->get_record_sql("select distinct group_concat(distinct e.courseid) as id

from {enrol}  e join {user_enrolments} ue on e.id = ue.enrolid  WHERE e.courseid IN
( SELECT c.id FROM {course} c WHERE c.visible=1 AND c.cm_bu_id IN($user->cm_bu_id)
OR c.id IN (Select id from {course} where id in (SELECT cc.course_id FROM {cm_courses} cc
 ) ) and ue.userid = $USER->id) ");
$course1 = trim($courses1->id, ',');
if (!empty($course1)) {
    $courses = $DB->get_records_sql("SELECT distinct e.courseid as id,
(select points from {course} where id = e.courseid) as points,
(select totalhours from {course} where id = e.courseid) as totalhours,
(select fullname from {course} where id = e.courseid) as fullname,
(select summary from {course} where id = e.courseid) as summary,
e.timecreated  FROM {enrol} e JOIN {user_enrolments} ue ON e.id = ue.enrolid
  WHERE e.courseid IN($course1) AND ue.userid=$USER->id ");
} else {
    $courses = '';
}

//$courses = $DB->get_records_sql("select c.id,c.summary,e.timecreated from {enrol}  e join {user_enrolments} ue on e.id = ue.enrolid join {course} c on e.courseid = c.id where ue.userid = $USER->id and c.visible !=0");

//$courses = $DB->get_records_sql("select c.id,c.points,c.totalhours,c.fullname,c.summary,e.timecreated from {enrol}  e join {user_enrolments} ue on e.id = ue.enrolid join {course} c on e.courseid = c.id join {cm_courses} cc on cc.course_id = c.id where ue.userid = $USER->id and c.visible !=0 and cc.bu_id in($cmuserfilter) ");
$mycompcourse     = array();
$myinprogcourse   = array();
$mynonstartcourse = array();
//print_object($courses);
foreach ($courses as $course) {

    $totals = $DB->get_records_sql("select * from {course_modules} where course = $course->id and deletioninprogress = 0 and module != 9  ");

    $total = count($totals);

    $attempt = $DB->get_records_sql("select a.id  from {course_modules_completion} as a
									join {course_modules} as b on a.coursemoduleid = b.id
									where a.userid = $USER->id and b.course = $course->id and b.module != 9 and completionstate >= 1");

    $attempted = count($attempt);

    $value        = $attempted / $total * 100;
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
        $sqll           = $DB->get_record_sql("select *  from {course} where id= $course->id ");
        $mycompcourse[] = $sqll;
    } else {

        $courselastaccess = $DB->get_record_sql("select count(id) as cout from {user_lastaccess}  where courseid = $course->id  and userid = $USER->id ");

        if ($courselastaccess->cout > 0) {

            $sqll             = $DB->get_record_sql("select *  from {course} where id= $course->id ");
            $myinprogcourse[] = $sqll;

        } else {

            $sqll               = $DB->get_record_sql("select *  from {course} where id= $course->id ");
            $mynonstartcourse[] = $sqll;
        }

    }

}

?>

<script type="text/javascript" src="../js/jquery-3.3.1.js"></script>
<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>


<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.min.css">
<script type="text/javascript">

$(document).ready(function() {


var table_1 = $('#example').DataTable( {



       "bLengthChange": true,

       "bInfo": true,

       "bFilter": true,

       "bPaginate": true,

       "bAutoWidth": false,

	   "bSort" : false

});

var table_1 = $('#example1').DataTable( {



       "bLengthChange": true,

       "bInfo": true,

       "bFilter": true,

       "bPaginate": true,

       "bAutoWidth": false,

	   "bSort" : false

});

 var table_1 = $('#example2').DataTable( {



       "bLengthChange": true,

       "bInfo": true,

       "bFilter": true,

       "bPaginate": true,

       "bAutoWidth": false,

	   "bSort" : false

});

});

</script>


<style>
table.dataTable thead th, table.dataTable thead td {
	padding :0px !important;
}

.dataTables_wrapper .dataTables_filter input {
	margin-left: -80px !important;
}
#example_filter label {
    color: #fff;
}

#example1_filter label {
    color: #fff;
}
#example2_filter label {
    color: #fff;
}
	#pstylev p {
		margin :0px;
	}

	#coustmprogress {
    height: 7px;
    overflow: hidden;
    float: left;
    width: 60%;
	border-radius: 3px;
   }


	a:hover {
    color: #0b4f8a;
    text-decoration: none;
    }
    .event-list {
		list-style: none;
		font-family: 'Lato', sans-serif;
		margin: 0px;
		padding: 0px;
	}
	.event-list > li {
		background-color: rgb(255, 255, 255);
		box-shadow: 0px 0px 5px rgb(51, 51, 51);
		box-shadow: 0px 0px 5px rgba(51, 51, 51, 0.7);
		padding: 0px;
		margin: 0px 0px 20px;
	}
	.event-list > li > time {
		display: inline-block;
		width: 100%;
		color: rgb(255, 255, 255);
		background-color: rgb(197, 44, 102);
		padding: 5px;
		text-align: center;
		text-transform: uppercase;
		float: right;

	}

	.event-list > li > img {
		width: 100%;
	}
	.event-list > li > .info {
		padding-top: 5px;
		text-align: center;
	}
	.event-list > li > .info > .title {
		font-size: 13pt;
		margin: 0px;
	}
	.event-list > li > .info > .desc {
		font-size: 13pt;
		font-weight: 300;
		margin: 0px;
	}
	.event-list > li > .info > ul,
	.event-list > li > .social > ul {
		display: table;
		list-style: none;
		padding: 0px;
		width: 100%;

	}
	.event-list > li > .social > ul {
		margin: 0px;
	}
	.event-list > li > .info > ul > li,
	.event-list > li > .social > ul > li {

		cursor: pointer;
		color: rgb(144, 137, 137);
		font-size: 11pt;
        padding: 3px 10px;
	}
    .event-list > li > .info > ul > li > a {
		display: block;
		width: 100%;
		color: rgb(30, 30, 30);
		text-decoration: none;
	}
    .event-list > li > .social > ul > li {
        padding: 0px;
    }
    .event-list > li > .social > ul > li > a {
        padding: 3px 7px;
		margin-bottom: 3px;
	}
	.event-list > li > .info > ul > li:hover,
	.event-list > li > .social > ul > li:hover {
		color: rgb(30, 30, 30);
	}
	.facebook a
	 {
		display: block;
		color: rgb(75, 110, 168) !important;
		background-color: rgb(75, 110, 168) !important;
	}

	.facebook:hover a {
		color: rgb(255, 255, 255) !important;
		background-color: rgb(75, 110, 168) !important;
	}


	@media (min-width: 768px) {
		.event-list > li {
			position: relative;
			display: block;
			width: 100%;
			height: 130px;
			padding: 0px;
			padding-top: 10px
		}
		.event-list > li > time,
		.event-list > li > img  {
			display: inline-block;
		}
		.event-list > li > time,
		.event-list > li > img {
			width: 120px;
			float: left;
		}
		.event-list > li > .info {
			background-color: #FFFFFF;
			overflow: hidden;
		}
		.event-list > li > time,
		.event-list > li > img {
			padding: 0px;
			margin: 0px 20px 6px 6px;
		}
		.event-list > li > .info {
			position: relative;

			text-align: left;
			padding-right: 20px;
		}
		.event-list > li > .info > .title,
		.event-list > li > .info > .desc {

		}
		.event-list > li > .info > ul {

		}
		.event-list > li > .social {
			position: absolute;
			top: 3px;
			right: 0px;
			display: block;
			width: 70px;
		}
        .event-list > li > .social > ul {

        }
		.event-list > li > .social > ul > li {
			display: block;
            padding: 0px;
		}
		.event-list > li > .social > ul > li > a {
		border-radius : 3px;
		}
	}


.block_myoverview   {
	display : none;
}

.block_recentlyaccessedcourses {

	display : none ;
}



#region-main-box {
	margin-top : 20px;
}

table.dataTable thead th, table.dataTable thead td{
	border-bottom: 1px solid #cccccc !important;
}

table.dataTable.no-footer{
		border-bottom: 1px solid #cccccc !important;
}

#example{
	font-size:12px !important;
	color:#666666 !important;
}


</style>

<script type="text/javascript">

function Search(){
vSearch = $('#search').val();
	window.location="./course_search.php?search="+vSearch;
}

$("#search").keyup(function(event) {
	vSearch = $('#search').val();
    if (event.keyCode === 13) {
        window.location="./course_search.php?search="+vSearch;
    }
});

</script>



<?php
$vTotalEnrolledCount = count($mynonstartcourse) + count($myinprogcourse) + count($mycompcourse);
echo '<script type="text/javascript">';
echo '$(window).bind("load", function() {';
echo '$("#enrolled_count_box").html(' . $vTotalEnrolledCount . ');';
echo '$("#not_started_count_box").html(' . count($mynonstartcourse) . ');';
echo '$("#in_progress_count_box").html(' . count($myinprogcourse) . ');';
echo '$("#completed_count_box").html(' . count($mycompcourse) . ');';
echo '$("#not_started_count").html(' . count($mynonstartcourse) . ');';
echo '$("#in_progress_count").html(' . count($myinprogcourse) . ');';
echo '$("#completed_count").html(' . count($mycompcourse) . ');';
echo '});';
echo '</script>';
?>
<?php
echo $OUTPUT->custom_block_region('content');

echo $OUTPUT->footer();


function getBuByUid($uId)
{
 global $DB, $CFG;
 $q   = "SELECT bu.id,bu.bu_name,logo_img_name FROM {$CFG->prefix}cm_business_units as bu join {$CFG->prefix}cm_bu_assignment as bas on bas.bu_id=bu.id where bas.userid=$uId";
 $req = $DB->get_record_sql($q);
 return $req;
}

function getBUAssignedUsers($buId)
{
 global $DB, $CFG;
 $useridsarr = array();
 $q          = "SELECT userid FROM {$CFG->prefix}cm_bu_assignment where bu_id= $buId";
 $res        = $DB->get_records_sql($q);
 foreach ($res as $rec) {
  $useridsarr[] = $rec->userid;
 }
 return $useridsarr;
}