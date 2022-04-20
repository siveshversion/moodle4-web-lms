<?php
/**
 * Competency - Learning Path
 *
 * @package    Learning Path
 * @copyright  2021 Balamurugan M <bala.mr01@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once '../../config.php';
require_once $CFG->dirroot . '/cm/lib/cmlib.php';

$PAGE->set_context(context_system::instance());
global $CFG, $DB, $USER;
$site = get_site();

require_login();

$PAGE->set_url('/cm/lp/lp_uploaduser.php');
$PAGE->set_title('Upload User - Learning Plans');
$PAGE->set_heading('Upload User - Learning Plans');
$PAGE->set_pagelayout('standard');
echo $OUTPUT->header();

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
  
#response{
	color:red !important;
}

</style>



<img id='loading' src='../../cm/lp/img/spinner.gif' style='visibility: hidden;'>
    <div id="response"
        class="<?php if(!empty($type)) { echo $type . " display-block"; } ?>">
        <?php if(!empty($message)) { echo $message; } ?>
        </div>
    <div class="outer-scontainer">
        <div class="row">

            <form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post"
                name="frmCSVImport" id="frmCSVImport"
                enctype="multipart/form-data">
                <div class="input-row">
                    <label class="col-md-4 control-label">Choose CSV
                        File</label> <input type="file" name="file"
                        id="file" accept=".csv">
                    <button type="submit" id="submit" name="import"
                        class="btn-submit">Upload</button>
                    <br />

                </div>

            </form>

        </div>
	
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.0/jquery.js"></script>
<link href="../css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="../js/jquery.dataTables.min.js"></script>


<link href="css/lity.css" rel="stylesheet"/>
<script src="js/lity.js"></script>
	<script>
	
$(document).ready(function() {
    $("#frmCSVImport").on("submit", function () {

	    $("#response").attr("class", "");
        $("#response").html("");
        var fileType = ".csv";
        var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(" + fileType + ")$");
        if (!regex.test($("#file").val().toLowerCase())) {
        	    $("#response").addClass("error");
        	    $("#response").addClass("display-block");
            $("#response").html("Invalid File. Upload : <b>" + fileType + "</b> Files.");
            return false;
        }
        return true;
    });
});



function showLoading(){
document.getElementById("loading").style = "visibility: visible";
}
function hideLoading(){
document.getElementById("loading").style = "visibility: hidden";
}
</script>


<?php


if (isset($_POST["import"])) { 
?><script>
    showLoading();
	</script>
<?php
//echo "indei";
    unset($res);
    $fileName = $_FILES["file"]["tmp_name"];
    
    if ($_FILES["file"]["size"] > 0) {
		//echo "-----";
        
        $file = fopen($fileName, "r");
        $res= array();
        while (($column = fgetcsv($file, 500000, ",")) !== FALSE) {
			// if ($column == 1) { continue; }
			 
			 $email = strtolower(trim($column[0],""));
			 $lpid = trim($column[1],"");
			 $uids = $DB->get_records_sql("Select * from {user} where email='$email' and deleted =0 and suspended =0 and id > 2 ");
			// echo "Select * from {user} where email='$email' and deleted =0 and suspended =0 and id > 2 ";echo"</br>";
			//$uids = explode(',', $uid);
    //$uresult = sizeof($uids);
	$flag =0;
   if(!empty($uids)){
	   $lpn = $DB->get_record('cm_admin_learning_path', array('id' => $lpid));
	   $noofenddate  = $lpn->lpdays;
	    $edate = date("d-m-Y");
		$endday = date('d-m-Y', strtotime($edate. ' + '.$noofenddate.' days'));
		

    $courseid = $DB->get_records_sql("select lp_courseid as courseid, ctype from {cm_lp_course} where lp_id = $lpid");
				if(!empty($courseid)){
				foreach ($uids as $k => $v) {
					$userdid = new stdClass();
					$userdid->lp_id = $lpid;
					$userdid->lp_type = '1';
					$userdid->userid = $v->id;
					$userdid->timecreated = time();
					$userdid->status = 1;
					
					foreach ($courseid as $ck => $course) {
						$userdid->courseid = $course->courseid;
						$userdid->ctype = $course->ctype;
						$userdid->goal_start_date = date("d-m-Y");
					   $userdid->goal_end_date = $endday;
					   $lp_assign = $DB->get_record_sql("select id from {cm_lp_assignment}
							where courseid =$course->courseid and lp_id=$lpid and userid= $v->id and status =1 limit 1");
						 if(empty($lp_assign->id)){
						 $insertedid = $DB->insert_record('cm_lp_assignment', $userdid);
						 }
							
						if(!empty($course->courseid)){	
												
							$enrolid = $DB->get_record_sql("select id from {enrol} where courseid =$course->courseid and enrol='manual' and status =0");
							if(!empty($enrolid->id)){
								if (!$DB->record_exists('user_enrolments', array('enrolid' => $enrolid->id, 'userid' => $v->id))) {
									$usrenrol = new stdClass();
									$usrenrol->status = "0";
									$usrenrol->enrolid = $enrolid->id;
									$usrenrol->userid = $v->id;
									$usrenrol->timestart = time();
									$usrenrol->timeend = "0";
									$usrenrol->modifierid = $USER->id;
									$usrenrol->timecreated = time();
									$usrenrol->timemodified = time();
									$DB->insert_record('user_enrolments', $usrenrol);
									
									//echo "enrol user"; exit;
								}

							$sql_context = $DB->get_record_sql("SELECT id from {context} where instanceid = $course->courseid and contextlevel = 50");
							   if(!empty($sql_context->id)){
								   if (!$DB->record_exists('role_assignments', array('contextid' => $sql_context->id, 'userid' => $v->id, 'roleid' => 5))) {
										$contextid = $sql_context->id;
										$groupmodify = new stdClass();
										$groupmodify->roleid = 5;
										$groupmodify->contextid = $contextid;
										$groupmodify->userid = $v->id;
										$groupmodify->timemodified = time();
										$groupmodify->modifierid = $USER->id;
										$groupmodify->component = "0";
										$groupmodify->itemid = "0";
										$groupmodify->sortorder = "0";
										$inserted = $DB->insert_record('role_assignments', $groupmodify);
										//echo "role user"; exit;
									}
								}
							}
						}
					}
					
					if(isset($insertedid)){
						
						$userdata = $DB->get_record('user', array('id' => $v->id));
						$supportuser = $DB->get_record('user', array('id' => 2));
						
						 $lpn = $DB->get_record('cm_admin_learning_path', array('id' => $lpid));

						
						$a = new stdClass();
						$a->firstname   = ucfirst($userdata->firstname);
						$a->lastname    = $userdata->lastname;
						$a->lpn        	= ucfirst($lpn->lpname);
						$a->enddate     = $lpn->lpdays;
						$a->link        = 'https://biz.learnospace.com/cm/lp/user_lplist.php?lpid='.$lpid;


						$message = get_string('lpenrollmentmail', '', $a);
						$messagehtml = text_to_html(get_string('lpenrollmentmail', '', $a), false, false, true);
						
						
						$subject = "BIZ LEARNOPACE LMS: ".ucfirst($lpn->lpname)." - You have been enrolled !";
						email_to_user($userdata, $supportuser, $subject, $message,  $messagehtml);
							
			
					}
			 
					}
				}
		}else{
			$flag=1;
			if(strcmp($column[0],'email') !=0){
			array_push($res,$column[0]);
			}
			
		}
		}
		if(!empty($res)){
		echo "</br></br><B>Invalid email id's</b>";
			foreach($res as $k=>$v){
				echo "</br>".$v;
			}
		} else {
			echo "</br></br><B>Learning Plans - Users assigned successfully </b>";
		}

		 
			
	}
	
	?>
	<script> hideLoading(); </script>
	<?php
} 

echo $OUTPUT->footer();
