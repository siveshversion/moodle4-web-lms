<?php
/**
 * Competency - Learning Path
 *
 * @package    Learning Path
 * @copyright  2019 Siveshversion
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once '../../config.php';
require_once $CFG->dirroot . '/cm/lib/cmlib.php';

$PAGE->set_context(context_system::instance());
global $CFG, $DB, $USER;
$site = get_site();

require_login();

$PAGE->set_url('/cm/lp/learningpath_list.php');
$PAGE->set_title(get_string('c_lplist'));
$PAGE->set_heading('');
$PAGE->set_pagelayout('standard');
echo $OUTPUT->header();
?>

<h3 style="text-align:left"> <?php echo get_string('c_learningplan'); ?> </h3>
<div><a class="cusLink btn btn-primary " style="float: right;padding: 0 20px;margin:10px" href="learningpath_form.php"><?php echo get_string('add'); ?> </a></div>
<br>
<br>
<?php
if (!empty($_REQUEST['id'])) {
    $dlpid = $_REQUEST['id'];
    $deleted = delete_lp_details($dlpid);
    if (isset($deleted)) {
        redirect('learningpath_list.php', 'Deleted Successfully ', 3);
    }
}
?>

<style>
#example_filter label {
	color: #fff;
}

.dataTables_wrapper .dataTables_filter input {
margin-left : -80px !important;
}
h2 {
	font-size : 20px;
}
h3 {
	font-size : 20px;
}

#toolbar {
  margin: 0;
}
.float-right {
	  display : block !important;
  }
  .mobilefooter {
	margin-left:-150% !important;
  }

</style>


<table id="example"  class='display' width="100%">
	<thead>
		<tr>
      <th>#</th>
      <th>Name</th>
      <th>Business Unit</th>
      <th>Courses</th>
      <th>Users</th>
      <th>Completion in Days</th>
      <th>Threshold</th>
      <th>Status</th>
      <th>Action</th>

		</tr>
	</thead>



        <?php
$lpnames = get_completelp_details();
$s = 1;
foreach ($lpnames as $lpname) {
    $lp_id = $lpname->id;
    ?>
            <tr>
                <td><?php echo $s; ?></td>
                <td>
				<?php if (is_siteadmin()) {?>
				<a href="lp_courselist2.php?lpid=<?php echo $lp_id; ?>"><?php echo ucfirst($lpname->lpname); ?></a>
				<?php } else {?>
				<a href="lp_courselist.php?lpid=<?php echo $lp_id; ?>"><?php echo ucfirst($lpname->lpname); ?></a>
				<?php }?>
				</td>
			 <td><?php $bu_name = get_lp_bu_name($lpname->creator);
    echo $bu_name;?></td>
                <td>
                    <?php

    $ccount = $DB->get_record_sql("select count(*) as cnt from {course} where id in(select lp_courseid from {cm_lp_course} where lp_id=$lp_id) and visible =1 and id >0");
//$ccount = $DB->get_record_sql("select count(*) as cnt from {cm_lp_course} where lp_id=$lp_id");

    if (($ccount->cnt) != 0) {
        echo $ccount->cnt;
        $enrolUserBtn = "<a href='userAssign.php?lpid=$lpname->id'><i class='icon fa fa-user-plus fa-fw iconsmall' title='Users' aria-label='Users'></i></a>";
    } else {
        echo '0';
        $enrolUserBtn = '';
    }?>
                </td>
                <td>
                        <?php
//$ucount = $DB->get_records_sql("select * from {cm_lp_assignment} where lp_id=$lp_id group by userid,lp_id");
    $ucount = $DB->get_records_sql("select id as cnt from {user} where id in(select userid from {cm_lp_assignment} where lp_id=$lp_id) and deleted =0 and id >2 ");

    if (sizeof($ucount) != 0) {
        echo sizeof($ucount);
    } else {
        echo '0';
    }?>
                </td>

                <td><?php echo $lpname->lpdays; ?></td>
                <td><?php echo $lpname->threshold; ?></td>
                <td><?php echo ucfirst($lpname->lpstatus); ?></td>

                <td><a href="learningpath_form.php?id=<?php echo $lpname->id; ?>"><i class="icon fa slicon-settings fa-fw " title="Edit" aria-label="Edit"></i></a>&nbsp;&nbsp;
                    <a onClick='javascript: return confirm("<?php echo get_string('c_lp_delete_confirmation'); ?>");' href="learningpath_list.php?id=<?php echo $lpname->id; ?>"><i class="icon fa slicon-trash fa-fw " title="Delete" aria-label="Delete"></i></a>

                <a class='' href='lp_courses.php?lpid=<?php echo $lpname->id; ?>' data-lity>
<i class="icon fa slicon-graduation fa-fw " title="Courses" aria-label="Courses" style=""></i>
	</a>
    <?php echo $enrolUserBtn; ?>
                </td>
            </tr>
            <?php
$s++;
}
?>
    </tbody>
</table>
<span id="const" style="display:none">Learning Plan</span>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.0/jquery.js"></script>
<link href="../css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="../js/jquery.dataTables.min.js"></script>


<link href="css/lity.css" rel="stylesheet"/>
<script src="js/lity.js"></script>
	<script>
	$(document).ready(function() {


var table_1 = $('#example').DataTable( {



       "bLengthChange": true,

       "bInfo": true,

       "bFilter": true,

       "bPaginate": true,

       "bAutoWidth": false,

	   "bSort" : false

});
});


</script>

<?php
/*  <th><?php echo get_string('c_asssymbal'); ?></th>
<th><?php echo get_string('c_rolename'); ?></th>
<?php if(is_siteadmin()){ ?> <th><?php echo get_string('c_college'); ?></th> <?php } ?>
<th><?php echo get_string('c_coursecount'); ?></th>
<th><?php echo get_string('c_usercount'); ?></th>
<th><?php echo get_string('c_completedindays'); ?></th>
<th><?php echo get_string('c_status'); ?></th>
<th><?php echo get_string('c_action'); ?></th> */
echo $OUTPUT->footer();
