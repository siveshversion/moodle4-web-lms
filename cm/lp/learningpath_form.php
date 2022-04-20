<?php
/**
 * Add Center functionality
 *
 * @package    Custom
 * @copyright  2019 Siveshversion
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once '../../config.php';
require_once $CFG->dirroot . '/cm/lib/cmlib.php';

global $CFG, $USER, $DB;
$site = get_site();
$id = '';
$PAGE->set_pagelayout('standard');
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/cm/lp/learningpath_form.php');
$PAGE->set_title(get_string('c_lp_creation'));
$PAGE->set_heading(get_string('c_lp_creation'));
echo $OUTPUT->header();

?>
<style>
#page-header {
	display : none;
}
#region-main {
	margin-top: 20px;
}
</style>

<?php

require_login();
if (!empty($_REQUEST['submit'])) {
    $alreadycen = $_REQUEST['lpname'];
    $sql = "select * from {cm_admin_learning_path} where lpname = '$alreadycen'";
    $result = $DB->record_exists_sql($sql);
    if ($result == 1) {
        $cename = "<p style='color:red'>" . get_string('c_lp_namealreadyexist') . "</p>";
    } else {
        if (empty($_REQUEST['lpname'])) {
            echo "<p style='color:red'>" . get_string('c_lp_namemust') . "</p>";
        } else {
            $errors = '';
            $file_name = $_FILES['lpimg']['name'];
            $file_size = $_FILES['lpimg']['size'];
            $file_tmp = $_FILES['lpimg']['tmp_name'];
            $file_type = $_FILES['lpimg']['type'];
            $file_ext = strtolower(end(explode('.', $_FILES['lpimg']['name'])));

            $extensions = array("png", "jpg", "jpeg");

            if (in_array($file_ext, $extensions) === false) {
                $errors .= "<p style='color:red'>" . get_string('c_lp_filetypemsg') . "</p>";
            }

            if ($file_size > 1048576) {
                $errors .= "<p style='color:red'>" . get_string('c_lp_filesizemsg') . "</p>";
            }

            if (empty($errors) == true) {
                if (!empty($file_name)) {
                    $lpimge_name = rand(0, 9999) . $file_name;
                    move_uploaded_file($file_tmp, "lpimages/" . $lpimge_name);
                } else {
                    $lpimge_name = '';
                }

            } else {
                //$errors;
            }

            $inserted = '';
            $newcategory = new stdClass();
            $newcategory->lpname = $_REQUEST['lpname'];
            $newcategory->lpdesc = $_REQUEST['lpdesc'];
            $newcategory->points = $_REQUEST['lppoint'];
            $newcategory->lpdays = $_REQUEST['lpdays'];
            $newcategory->threshold = $_REQUEST['threshold'];
            $newcategory->startdate = $_REQUEST['stdate'];
            $newcategory->course = implode(",", $_REQUEST['course']);
            $lpcourses = implode(",", $_REQUEST['course']);
            $cuntlpcourse = explode(',', $lpcourses);
            $newcategory->coursecnt = count($cuntlpcourse);
            $newcategory->lpimage = $lpimge_name;
            $newcategory->enddate = $_REQUEST['stdate'];
            $newcategory->lpstatus = 'active';
            $newcategory->lastmodified = time();
            $newcategory->creator = $USER->id;

            if (!$DB->record_exists('cm_admin_learning_path', array('lpname' => $_REQUEST['lpname'], 'lpstatus' => 'active'))) {
                $inserted = insert_lpdetails($newcategory);
            } else {
                echo "<p style='color:red'>" . get_string('c_lp_alreadyexist') . "</p>";
            }

            if (isset($inserted)) {

                redirect("learningpath_list.php", 'Learning Plan is Added Successfully..', 3);
            }

        }
    }
}
$id = $_GET['id'];
if (!empty($_REQUEST['update'])) {
    $alreadycen = $_REQUEST['lpname'];
    $sql = "select * from {cm_admin_learning_path} where lpname = '$alreadycen' and id != $id ";
    $result = $DB->record_exists_sql($sql);
    if ($result == 1) {
        $cename = "<p style='color:red'>" . get_string('c_lp_namealreadyexist') . "</p>";
    } else {
        if (empty($_REQUEST['lpname'])) {
            echo "<p style='color:red'>" . get_string('c_lp_namemust') . "</p>";
        } else {

            $newcategory = new stdClass();
            $newcategory->id = $id;
            $newcategory->lpname = $_REQUEST['lpname'];
            $newcategory->lpdesc = $_REQUEST['lpdesc'];
            $newcategory->points = $_REQUEST['lppoint'];
            $newcategory->lpdays = $_REQUEST['lpdays'];
            $newcategory->threshold = $_REQUEST['threshold'];
            $newcategory->course = implode(",", $_REQUEST['course']);
            $lpcourses = implode(",", $_REQUEST['course']);
            $cuntlpcourse = explode(',', $lpcourses);
            $newcategory->coursecnt = count($cuntlpcourse);
            $newcategory->startdate = $_REQUEST['stdate'];
            $newcategory->enddate = $_REQUEST['stdate'];
            $newcategory->lastmodified = time();
            $newcategory->lpstatus = 'active';
            $newcategory->creator = $USER->id;
            if (!empty($_FILES['lpimg']['name'])) {
                $errors = '';
                $file_name = $_FILES['lpimg']['name'];
                $file_size = $_FILES['lpimg']['size'];
                $file_tmp = $_FILES['lpimg']['tmp_name'];
                $file_type = $_FILES['lpimg']['type'];
                $file_ext = strtolower(end(explode('.', $_FILES['lpimg']['name'])));

                $extensions = array("png", "jpg", "jpeg");

                if (in_array($file_ext, $extensions) === false) {
                    $errors .= "<p style='color:red'>" . get_string('c_lp_filetypemsg') . "</p>";
                }

                if ($file_size > 1048576) {
                    $errors .= "<p style='color:red'>" . get_string('c_lp_filesizemsg') . "</p>";
                }

                if (empty($errors) == true) {
                    $lpimge_name = rand(0, 9999) . $file_name;
                    //echo $file_tmp, "lpimages/" . $lpimge_name ;

                    move_uploaded_file($file_tmp, "lpimages/" . $lpimge_name);
//echo "ggg";
                } else {
                    $errors;
                }
                $newcategory->lpimage = $lpimge_name;

            }

            $updated = update_lpdetails($newcategory);
            if (isset($updated)) {

                /* senthamizh */

                redirect("lp_courselist.php?lpid=$id", 'Learning Plan is Updated Successfully..', 3);
            }
        }
    }
}
?>
<?php if ($id == '') {?>
    <?php
} else {
    $data = get_lp_details($id);
    ?>
<?php }?>
<style type="text/css">

h1 {
	font-size : 24px;
}
h3 {
	font-size : 20px;
}
    .error {
        color : red;

    }
    </style>
<h3> Add Learning Plan</h3>

    <form method="post" name="myform" id="myform" enctype="multipart/form-data">
        <table width="60%" cellpadding="5" cellspacing="5" id="myform" >
            <tr>
                <td style="width: 40%;"><?php echo get_string('c_lp_name'); ?> <span style="color:#db1812;font-weight: bold;">*</span></td>
                <td> <?php if (isset($id)) {?>
                        <input type="text" name="lpname"  id="lpname" class="form-control" value="<?php echo $data->lpname; ?>" >
                        <?php } else {?>
                        <input type="text" name="lpname"  id="lpname" value="<?php echo $_REQUEST['lpname']; ?>" class="form-control"> <?php }?>
                    <span></span>
                </td>
            </tr>

            <tr>
  <td><?php echo get_string('c_description'); ?> <!--<span style="color:#db1812;font-weight: bold;">*</span> --></td>
                <td> <?php
if (isset($id)) {
    ?>  <textarea rows="4" cols="50" name="lpdesc"  id="lpdesc" class="form-control" value=""><?php echo $data->lpdesc; ?></textarea>
                    <?php } else {?>

                        <textarea rows="4" cols="50" name="lpdesc" id="lpdesc" value="<?php echo $_REQUEST['lpdesc']; ?>" class="form-control"> <?php }?></textarea>
                    <span></span>
                </td>
            </tr>

            <tr>
                <td><?php echo get_string('c_lp_creditpoints'); ?> <span style="color:#db1812;font-weight: bold;">*</span></td>
                <td> <?php if (isset($id)) {?>
                        <input maxlength="3" type="text" name="lppoint"  id="lppoint" class="form-control" value="<?php echo $data->points; ?>" >
                        <?php } else {?>
                        <input maxlength="3" type="text" name="lppoint"  id="lppoint" value="<?php echo $_REQUEST['points']; ?>" class="form-control"> <?php }
?>
                    <span></span>
                </td>
            </tr>

            <tr>
         <td><?php echo get_string('c_lp_noofdayscomple1'); ?> <span style="color:#db1812;font-weight: bold;">*</span> </td>
                <td> <?php
if (isset($id)) {
    ?>
                        <input maxlength="3" type="text" name="lpdays"  id="lpdays" class="form-control" value="<?php echo $data->lpdays; ?>" >
                    <?php } else {?>
                        <input maxlength="3" type="text" name="lpdays"  id="lpdays" value="<?php echo $_REQUEST['lpdays']; ?>" class="form-control"> <?php }?>
                    <span></span>
                </td>
            </tr>

            <tr>
         <td>Threshold <span style="color:#db1812;font-weight: bold;">*</span> </td>
        <td> <?php
if (isset($id)) {
    ?>
                        <input maxlength="2" type="number" name="threshold"  id="threshold" class="form-control" value="<?php echo $data->threshold; ?>" >
                    <?php } else {?>
                        <input maxlength="2" type="number" name="threshold"  id="threshold" value="<?php echo $_REQUEST['threshold']; ?>" class="form-control"> <?php }?>
                    <span></span>
                </td>
            </tr>
			 <!--  <tr>
         <td> Courses<span style="color:#db1812;font-weight: bold;">*</span> </td>
                <td style="
    border: 1px solid #ccc;
    overflow: scroll;
    height: 400px;
    margin-left: 5px;
    width: 98%;
	display: -webkit-inline-box;">

		<?php
if (is_siteadmin()) {
    $courses = $DB->get_records_sql("SELECT id,fullname FROM {course} where visible=1");
} else {
    $courses = $DB->get_records_sql("select id,fullname from {course} c where c.visible =1 and c.cm_bu_id = $USER->cm_bu_id  OR id in (
SELECT course_id FROM mdl_cm_courses WHERE bu_id  = $USER->cm_bu_id) order by c.id desc");
}
foreach ($courses as $course) {
    if (isset($id)) {
        $jobcourse = explode(',', $data->course);
        ?>
         <input type="checkbox" name="course[]" <?php if (in_array($course->id, $jobcourse)) {?> checked = checked <?php }?>  id="course<?php echo $course->id; ?>" value="<?php echo $course->id; ?>" >&nbsp;&nbsp;<?php echo $course->fullname; ?><br>
                    <?php } else {?>
         <input type="checkbox" name="course[]"  id="course<?php echo $course->id; ?>" value="<?php echo $course->id; ?>" >&nbsp;&nbsp;<?php echo $course->fullname; ?><br>
        <?php }

}?>
                </td>
            </tr>	-->

			<tr>
            <td><label for="lpimg" class="required"><?php echo get_string('c_lp_img'); ?> </label><td>

              <?php //if (isset($id)) { ?>


									<?php if (isset($id)) {?>
									<?php if (!empty($data->lpimage)) {?>
									<a target="_blank" href="lpimages/<?php echo $data->lpimage; ?>"><img style="width:100px;height: 65px;margin-bottom: 7px;" src="lpimages/<?php echo $data->lpimage; ?>"></a>
									<?php } else {?>
									<img style="width:100px;height: 65px;margin-bottom: 7px;" src="img/image3.jpg">
									<?php }
}?>


									<input type="file" name="lpimg" id="lpimg"  /> </br>
									<span style="color: #5a5959;font-size: 12px;text-transform: lowercase;font-weight: 500;"><?php echo get_string('c_lp_uploadfileformat'); ?> <b> <?php echo get_string('c_lp_dotpng'); ?> </b><?php echo get_string('c_lp_or'); ?> <b><?php echo get_string('c_lp_dotjpg'); ?></b></span><br>
									<span style='color:red'><?php echo $lpimageErr; ?></span>
									<span style='color:red'><?php echo $errors; ?></span>
                     </td>
              <?php //} ?>
            </tr>
            <tr>

          <?php if (isset($id)) {?>
                    <td align=center><input type="submit" id="update" name="update" class="btn btn-primary form-control" value="<?php echo get_string('c_lp_update'); ?>" style="border: none;width: 50%;margin-left: 104%;"></td>
                    <?php } else {?>
                    <td align=center><input type="submit" id="submit" name="submit" class="btn btn-primary form-control" value="<?php echo get_string('c_lp_submit'); ?>" style="border: none;width: 50%;margin-left: 104%;"></td>
            <?php }?>
            </tr>
        </table>
    </form>

    <script type="text/javascript"  src="../js/jquery-3.3.1.js"></script>
    <script src="../js/jquery.validate.js" type="text/javascript"></script>
    <script type="text/javascript"  src="../js/jquery.validate.js"></script>
    <script>
        $(document).ready(function () {

            $('#myform').validate({
                rules: {
                    lpname: {
                        required: true,
                    },
                    lppoint: {
                        required: true,
                    },
					lpdays: {
                        required: true,
                    },


                },

            });
        });

    </script>

<?php
echo $OUTPUT->footer();
