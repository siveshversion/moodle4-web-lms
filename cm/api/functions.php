<?php
date_default_timezone_set("Asia/Kolkata");
error_reporting(E_ALL);

function deliver_response($array)
{
 //header("HTTP/1.1 ".$array['status']);
 $response['Status']          = $array['status'];
 $response['ResponseMessage'] = $array['response_msg'];
 $response['Data']            = $array['data'];
 $response['ResponseCode']    = $array['response_code'];
 $response['methodName']      = $array['methodName'];
 $response['AdditionalData']  = $array['AdditionalData'];
 echo $json_response          = json_encode($response);
}

function TestConnection($arrInput)
{
 global $fields_head, $wwwroot;
 $count                           = 0;
 $arrResult[$count]["connection"] = 1;
 $count++;
 $arrReturn["Data"] = $arrResult;
 echo json_encode($arrReturn);
 exit;
}

function validateLogin($arrInput)
{

 global $CFG, $DB;

 $vUsername = $arrInput['username'];
 $vPassword = $arrInput['password'];

 $msg = 'Username / Password is wrong.Please enter the correct credentials';

 // $vUsername = 'admin';
 // $vPassword = 'Learn@123';

 $query          = "select id from {$CFG->prefix}user where username like BINARY '$vUsername'";
 $vUsernameExits = $DB->get_record_sql($query);

 if (!empty($vUsernameExits->id)) {
  $query   = "select suspended,deleted from {$CFG->prefix}user where id = $vUsernameExits->id";
  $userRec = $DB->get_record_sql($query);
  if ($userRec->suspended == 1) {
   $msg = 'Your account has been suspended.Please contact your administrator';
  } else if ($userRec->deleted == 1) {
   $msg = 'Your account has been deleted.Please contact your administrator';
  }

  $restformat = 'json';

  $params = array('username' => $vUsername, 'password' => $vPassword);

  $server_url = $CFG->wwwroot . '/login/token.php?service=moodle_mobile_app';

  $curl       = new curl;
  $restformat = ($restformat == 'json') ? '&moodlewsrestformat=' . $restformat : '';

  $resp = $curl->post($server_url . $restformat, $params);

  $arrToken = json_decode($resp, true);

  if ($arrToken[token] != '') {

   //get userid from username
   $objUser = $DB->get_record('user', array("username" => $vUsername));

   //update token in user key auth plugin tables
   $vMoodleToken = $arrToken[token];
   $vUserId      = $objUser->id;

   $objUserKey = $DB->get_record('user_private_key', array('userid' => $vUserId, 'script' => 'auth/userkey'));

   if ($objUserKey->id != '') {

    $DB->set_field('user_private_key', 'value', $vMoodleToken, array('userid' => $vUserId, 'script' => 'auth/userkey'));
   } else {
    $objUserAuth              = new stdClass();
    $objUserAuth->script      = 'auth/userkey';
    $objUserAuth->value       = $vMoodleToken;
    $objUserAuth->userid      = $vUserId;
    $objUserAuth->timecreated = time();
    $DB->insert_record('user_private_key', $objUserAuth);

   }
   $siteAdmin = checkisSiteAdmin($vUsernameExits->id);

   $BuAdmin = checkisBUAdmin($vUsernameExits->id);

   if ($siteAdmin) {
    $vRole = 'admin';
   } else if ($BuAdmin) {
    $vRole                        = 'manager';
    $BU                           = getBuByUid($objUser->id);
    $arrResults['Data']['buId']   = $BU->id;
    $arrResults['Data']['buName'] = $BU->bu_name;
   } else {
    //check whether the user has the student role
    $vLearners = 0;
    $query     = "SELECT id FROM {$CFG->prefix}role_assignments WHERE roleid = 5
                            and userid = $objUser->id group by roleid";
    $objInstructorsRoles = $DB->get_records_sql($query);

    $vRole = 'student';
   }

   // fix lastaccess for pwa login
   $loggedinUser             = new stdClass();
   $loggedinUser->id         = $objUser->id;
   $loggedinUser->lastaccess = time();
   $DB->update_record('user', $loggedinUser);

   $msg = 'Success';

   $arrResults['Data']['result']    = 1;
   $arrResults['Data']['message']   = $msg;
   $arrResults['Data']['token']     = $arrToken[token];
   $arrResults['Data']['userid']    = $objUser->id;
   $arrResults['Data']['email']     = $objUser->email;
   $arrResults['Data']['firstname'] = $objUser->firstname;
   $arrResults['Data']['lastname']  = $objUser->lastname;
   $arrResults['Data']['surname']   = $objUser->lastname;
   $arrResults['Data']['country']   = $objUser->country;
   $arrResults['Data']['city']      = $objUser->city;
   $arrResults['Data']['phone1']    = $objUser->phone1;
   $arrResults['Data']['role']      = $vRole;
  } else {
   $arrResults['Data']['result']  = 0;
   $arrResults['Data']['token']   = '';
   $arrResults['Data']['message'] = $msg;
  }
 } else {
  $arrResults['Data']['result']  = 0;
  $arrResults['Data']['token']   = '';
  $arrResults['Data']['message'] = $msg;
 }

 return $arrResults;
}

function getUserList()
{
 global $DB, $CFG;
 $append_query = '';
 $bu_id        = $_POST['buId'];

 if (!empty($bu_id) && ($bu_id != 'null')) {
  $assigned_userids_arr = getBUAssignedUsers($bu_id);
  $userids              = implode(',', $assigned_userids_arr);
  if (!empty($userids)) {
   $append_query = "AND u.id in($userids)";
  }
 }

 if (isset($_POST["wstoken"])) {

  $q1 = "select value from {$CFG->prefix}config where name='siteadmins'";

  $siteadmins_rec = $DB->get_record_sql($q1);

  $siteadmins = $siteadmins_rec->value;

  $q = "select u.id,u.firstname,u.lastname,u.email, u.timecreated,u.suspended,u.username,u.lastaccess
             from {$CFG->prefix}user u where u.deleted = 0 and u.id > 1 and
             u.id NOT IN($siteadmins) $append_query order by u.id DESC";

  $res = $DB->get_records_sql($q);

  $arrReturn["ResponseMessage"] = 'Success';

  foreach ($res as $rec) {
   $data                = new stdClass;
   $data->userid        = $rec->id;
   $data->username      = $rec->username;
   $data->firstname     = $rec->firstname;
   $data->lastname      = $rec->lastname;
   $BU                  = getBuByUid($rec->id);
   $data->buName        = $BU->bu_name;
   $data->createdon     = (!empty($rec->timecreated)) ? date("d-M-Y", $rec->timecreated) : null;
   $data->lastaccess    = (!empty($rec->lastaccess)) ? date("d-M-Y", $rec->lastaccess) : null;
   $data->suspended     = $rec->suspended;
   $arrReturn['Data'][] = $data;
  }
 }
 return $arrReturn;
}

function already_taken_validator()
{
 global $DB, $CFG;
 $response           = new stdClass();
 $not_in_query_param = null;
 $second_query_param = null;

 if (!empty($_POST['edit_id'])) {
  $edit_id            = $_POST['edit_id'];
  $not_in_query_param = "and id not in($edit_id)";
 }

 if (!empty($_POST['field_name2'])) {
  $field_name2        = $_POST['field_name2'];
  $field_value2       = $_POST['field_value2'];
  $second_query_param = "and $field_name2 in($field_value2)";
 }

 $table_name  = $_POST['table_name'];
 $field_name  = $_POST['field_name'];
 $field_value = $_POST['field_value'];

 $sql = "SELECT * FROM $table_name where $field_name = '$field_value' $second_query_param $not_in_query_param";
 $res = $DB->get_record_sql($sql);

 $response->exists = (!empty($res)) ? 1 : 0;

 $arrReturn["ResponseMessage"] = (!empty($res)) ? " $field_name already Taken" : "New data";

 if ((!empty($_POST['field_name2'])) && ($response->exists == 0)) {
  $field_value                  = $_POST['field_value'];
  $field_value                  = strtolower($field_value);
  $response->exists             = ($field_value == 'new') ? 1 : 0;
  $arrReturn["ResponseMessage"] = (!empty($res)) ? "Ready to edit this current record based on id" : "$field_name2 Already Taken";
 }
 $arrReturn["Data"] = $response;

 return $arrReturn;
}

function addNewUser()
{
 global $DB, $CFG;
 $response = new stdClass;

 if (isset($_POST["username"])) {
  $mainusername = $_POST['username'];
  $password     = trim($_POST['new_pwd'], ' ');
  $email        = $_POST['email'];
  $fname        = $_POST['first_name'];
  $surname      = $_POST['surname'];
  $city_town    = $_POST['location'];
  $country      = $_POST['country'];
  $bu_id        = $_POST['bu_id'];
 }
 $wsfunction = $_POST['wsfunction'];
 $wstoken    = $_POST['wstoken'];

 $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

 $new_user              = new stdClass();
 $new_user->username    = $mainusername;
 $new_user->password    = $password;
 $new_user->firstname   = $fname;
 $new_user->lastname    = $surname;
 $new_user->email       = $email;
 $new_user->auth        = 'manual';
 $new_user->idnumber    = '';
 $new_user->lang        = 'en';
 $new_user->timezone    = '';
 $new_user->mailformat  = 0;
 $new_user->description = '';
 $new_user->city        = $city_town;
 $new_user->country     = $country;
 $preference            = 'auth_forcepasswordchange';
 $new_user->preferences = array(
  array('type' => $preference, 'value' => 'true'),
 );
 $users  = array($new_user);
 $params = array('users' => $users);

 $curl          = new curl();
 $curl_response = $curl->post($server_url, $params);

 $arrReturn["Data"] = $curl_response;

 if ((!empty($curl_response)) && (!empty($bu_id))) {
  $json_arr = json_decode($curl_response, true);
  foreach ($json_arr as $user) {
   BUuserEntry($bu_id, $user['id']);
  }
 }
 return $arrReturn;
}

function getUser()
{
 global $DB, $CFG;
 $response = array();

 // $_POST["user_id"] = 5;

 if (isset($_POST["user_id"])) {

  $user_id = $_POST["user_id"];
  $sql     = "select * from {$CFG->prefix}user where id = $user_id";
  $res1    = $DB->get_records_sql($sql);

  foreach ($res1 as $rec) {
   $data            = new stdClass;
   $data->username  = $rec->username;
   $data->firstname = $rec->firstname;
   $data->lastname  = $rec->lastname;
   $data->email     = $rec->email;
   $data->city      = $rec->city;
   $data->country   = $rec->country;
   $BU              = getBuByUid($user_id);
   $data->bu_id     = $BU->id;
   $response[]      = $data;
  }
  $arrResults['Data'] = $response;
 }
 return $arrResults;
}

function updateUser()
{
 global $CFG;
 $response   = new stdClass();
 $user_id    = $_POST["user_id"];
 $bu_id      = $_POST['bu_id'];
 $wsfunction = $_POST['wsfunction'];
 $wstoken    = $_POST['wstoken'];

 if ($bu_id) {
  handleBuEntry($bu_id, $user_id);
 }

 $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

 $firstname = $_POST['first_name'];
 $lastname  = $_POST['surname'];
 $city      = $_POST['location'];
 $country   = $_POST['country'];

 $username = array('id' => $user_id, 'firstname' => $firstname, 'lastname' => $lastname, 'city' => $city, 'country' => $country);

 $users[] = $user_id;

 $params = array('users' => array($username));

 $curl             = new curl();
 $response->status = $curl->post($server_url, $params);

 $arrResults['Data'] = $response->status;

 return json_encode($response);
}

function deleteUser()
{

 global $DB, $CFG;
 $response = new stdClass();

 if (isset($_POST["user_id"])) {

  $user_id    = $_POST["user_id"];
  $wsfunction = $_POST['wsfunction'];
  $wstoken    = $_POST['wstoken'];

  $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

  $users[] = $user_id;

  $params = array('userids' => $users);

  $curl             = new curl();
  $response->status = $curl->post($server_url, $params);

  $arrResults['Data'] = $response;

 }
 return $arrResults;

}

function suspendUser()
{
 global $DB, $CFG;
 $response = new stdClass();

 if ((isset($_POST["user_id"])) && (isset($_POST["mode"]))) {

  $user_id    = $_POST["user_id"];
  $mode       = $_POST["mode"];
  $wsfunction = $_POST['wsfunction'];
  $wstoken    = $_POST['wstoken'];

  $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

  $username = array('id' => $user_id, 'suspended' => $mode);

  $users[] = $user_id;

  $params = array('users' => array($username));

  $curl             = new curl();
  $response->status = $curl->post($server_url, $params);

  $arrResults['Data'] = $response;

 }
 return $arrResults;
}

function listCategories()
{
 global $DB, $CFG;
 $response = array();
 if (isset($_POST['userId'])) {
  $q          = "SELECT * FROM {course_categories} where visible=1 and id > 1";
  $categories = $DB->get_records_sql($q);
  foreach ($categories as $rec) {
   $new_data                       = new stdClass();
   $new_data->category_name        = $rec->name;
   $new_data->category_id          = $rec->id;
   $new_data->category_courses_cnt = get_course_cnt_by_cat($rec->id);
   $response[]                     = $new_data;
  }
  $arrResults['Data'] = $response;
 }
 return $arrResults;
}

function get_course_cnt_by_cat($cat_id)
{
 global $DB, $CFG;
 $cnt_cid = null;
 $sql     = "SELECT count(id) as cnt_cid FROM {course} WHERE category='$cat_id'";
 $rec     = $DB->get_record_sql($sql);
 $cnt_cid = $rec->cnt_cid;
 return $cnt_cid;
}

function createCategory()
{
 global $DB, $CFG;
 $response = new stdClass();
 if (isset($_POST['wsfunction'])) {
  $wsfunction    = $_POST['wsfunction'];
  $wstoken       = $_POST['wstoken'];
  $category_name = $_POST['category_name'];
  $description   = $_POST['category_description'];
  $category_code = '';

  $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

  $params = array(
   'categories' => array(
    array(
     'name'              => $category_name,
     'parent'            => '0',
     'idnumber'          => $category_code,
     'description'       => $description,
     'descriptionformat' => '1',
    ),
   ),
  );
  $curl          = new curl();
  $curl_response = $curl->post($server_url, $params);
  if (!empty($curl_response)) {

   $json_arr = json_decode($curl_response, true);
   foreach ($json_arr as $rec) {
    $response->name = $rec['shortname'];
    $response->id   = $rec['id'];
   }
   $response->result   = (!empty($response->id)) ? 1 : null;
   $arrResults['Data'] = $response;
  }

 }
 return $arrResults;
}

function get_category_by_id()
{
 global $DB, $CFG;

 $response = new stdClass();

 if (isset($_POST['cat_id'])) {
  $cat_id = $_POST['cat_id'];

  $sql = "select * from {$CFG->prefix}course_categories where id='$cat_id'";
  $rec = $DB->get_record_sql($sql);

  $response->name        = $rec->name;
  $response->description = strip_tags($rec->description);

  $arrResults['Data'] = $response;
 }
 return $arrResults;
}

function updateCategory()
{
 global $DB, $CFG;
 $response = new stdClass();
 if (isset($_POST['wsfunction'])) {
  $wsfunction    = $_POST['wsfunction'];
  $wstoken       = $_POST['wstoken'];
  $category_id   = $_POST['cat_id'];
  $category_name = $_POST['category_name'];
  $description   = $_POST['category_description'];
  $category_code = '';

  $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

  $params = array(
   'categories' => array(
    array(
     'id'                => $category_id,
     'name'              => $category_name,
     'parent'            => '0',
     'idnumber'          => $category_code,
     'description'       => $description,
     'descriptionformat' => '1',
    ),
   ),
  );

  $curl          = new curl();
  $curl_response = $curl->post($server_url, $params);
  if (!empty($curl_response)) {
   $response->status   = 'success';
   $arrResults['Data'] = $response;
  }
 }
 return $arrResults;
}

function listCourses()
{
 global $DB, $CFG;
 $response     = array();
 $append_query = '';
 $bu_id        = $_POST['buId'];
 $cat_id       = $_POST['catId'];

 if (!empty($cat_id)) {
  $catId = $_POST['catId'];
 }
 $wstoken = $_POST['wstoken'];

 $categoryFilter = isset($catId) ? "category=$catId" : "category > 0";

 $moodledata             = new stdClass();
 $moodledata->wsfunction = $_POST['wsfunction'];
 $moodledata->wstoken    = $wstoken;

 if (isset($_POST['userId'])) {
  if (!empty($bu_id) && ($bu_id != 'null')) {
   $assigned_courses = getBuCourses($bu_id);
   $q                = "SELECT * FROM {course} where visible=1 and $categoryFilter and id in($assigned_courses)";
  } else {
   $q = "SELECT * FROM {course} where visible=1 and $categoryFilter";
  }

  $courses = $DB->get_records_sql($q);
  $i       = 1;
  foreach ($courses as $rec) {
   $new_data                   = new stdClass();
   $cat                        = new stdClass();
   $cat->tablename             = 'course_categories';
   $cat->fieldname             = 'name';
   $cat->recid                 = 'id';
   $cat->id                    = $rec->category;
   $new_data->sno              = $i;
   $new_data->course_fullname  = $rec->fullname;
   $new_data->course_shortname = $rec->shortname;
   $category                   = get_field_by_id($cat);
   $new_data->category_id      = $category->id;
   $new_data->category_name    = $category->name;
   $new_data->course_id        = $rec->id;
   $participants               = getEnrolledUsers($rec->id, $moodledata);
   $new_data->enrolled_cnt     = count($participants);
   $response[]                 = $new_data;
   ++$i;
  }
  $arrResults['Data'] = $response;
 }
 return $arrResults;
}

function get_field_by_id($cat)
{
 global $DB, $CFG;
 $q        = "Select $cat->fieldname,$cat->recid from $CFG->prefix" . "$cat->tablename where id =$cat->id";
 $response = $DB->get_record_sql($q);
 return $response;
}

function create_course()
{
 global $DB, $CFG;
 $response = new stdClass();
 if (isset($_POST['wsfunction'])) {
  $wsfunction = $_POST['wsfunction'];
  $wstoken    = $_POST['wstoken'];
  $bu_id      = $_POST['bu_id'];

  $course_category  = $_POST['course_category'];
  $course_name      = $_POST['course_full_name'];
  $course_shortname = $_POST['course_short_name'];
  $course_summary   = $_POST['course_description'];
  $enrollment_type  = $_POST['enroll_type'];
  $enrollBuUsersChk = $_POST['enrollBuUsersChk'];
  $enrollBuUsersChk = ($enrollBuUsersChk === 'false') ? false : true;

  $addonData               = new stdClass();
  $addonData->coursetype   = $_POST['courseType'];
  $addonData->points       = $_POST['points'];
  $addonData->durationhrs  = $_POST['durationHrs'];
  $addonData->durationmins = $_POST['durationMins'];

  $num_sections = $_POST['topicCnt'];
  $course_code  = '';

  $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

  $params = array(
   'courses' => array(
    array(
     'fullname'    => $course_name,
     'shortname'   => $course_shortname,
     'categoryid'  => $course_category,
     'idnumber'    => $course_code,
     'summary'     => $course_summary,
     'format'      => 'topcoll',
     'numsections' => $num_sections,
    ),
   ),
  );

  $curl          = new curl();
  $curl_response = $curl->post($server_url, $params);

  if (!empty($curl_response)) {
   $json_arr = json_decode($curl_response, true);
   foreach ($json_arr as $rec) {
    $response->name = $rec['shortname'];
    $response->id   = $rec['id'];
    $addonData->id  = $response->id;
   }
   $res = update_course_cm_changes($addonData);
   update_enrollment_type($addonData->id, $enrollment_type);
   if (!empty($bu_id) && ($bu_id != 'null')) {
    $params            = new stdClass();
    $params->bu_id     = $_POST['bu_id'];
    $params->userId    = $_POST['userId'];
    $params->course_id = $addonData->id;
    $params->wstoken   = $wstoken;
    AddCoursetoBU($params);
    if ($enrollBuUsersChk) {
     EnrollBUCoursetoUsers($params);
    }
   }
   $arrResults['Data'] = $response;
  }
 }
 return $arrResults;
}

function update_course_cm_changes($params)
{
 global $DB, $CFG;

 $arrResults = array();

 if (isset($params)) {
  $Course              = new stdClass();
  $Course->id          = $params->id;
  $Course->course_type = $params->coursetype;
  $Course->points      = (int) $params->points;
  $Course->hours       = $params->durationhrs;
  $Course->mints       = $params->durationmins;
  $inserted            = $DB->update_record('course', $Course);
  $arrResults['Data']  = $inserted;
 }
 return $arrResults;

}

function get_course_by_id()
{
 global $DB, $CFG;

 $response = new stdClass();

 if (isset($_POST['course_id'])) {

  $wsfunction = $_POST['wsfunction'];
  $wstoken    = $_POST['wstoken'];

  $course_id = $_POST['course_id'];

  $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

  $ids = array($course_id);

  $params = array('options' => array('ids' => $ids));

  $curl          = new curl();
  $curl_response = $curl->post($server_url, $params);
  if (!empty($curl_response)) {
   $json_arr = json_decode($curl_response, true);
   foreach ($json_arr as $rec) {
    $response->fullname      = $rec['fullname'];
    $response->shortname     = $rec['shortname'];
    $response->description   = strip_tags($rec['summary']);
    $response->category      = $rec['categoryid'];
    $response->topics_cnt    = $rec['numsections'];
    $response->enrol_method  = get_enrollment_type($course_id);
    $custom_fields_data      = get_course_add_on_datas($course_id);
    $response->points        = $custom_fields_data->points;
    $response->course_type   = (int) $custom_fields_data->course_type;
    $response->duration_hrs  = $custom_fields_data->hours;
    $response->duration_mins = $custom_fields_data->mints;
   }
   $arrResults['Data'] = $response;
  }
 }
 return $arrResults;
}

function get_course_add_on_datas($course_id)
{
 global $DB;
 $course = $DB->get_record('course', array('id' => $course_id));
 return $course;
}

function update_course()
{
 global $DB, $CFG;
 $response = new stdClass();
 if (isset($_POST['wsfunction'])) {

  $wsfunction = $_POST['wsfunction'];
  $wstoken    = $_POST['wstoken'];

  $course_id        = $_POST['course_id'];
  $course_category  = $_POST['course_category'];
  $course_name      = $_POST['course_full_name'];
  $course_shortname = $_POST['course_short_name'];
  $course_summary   = $_POST['course_description'];
  $enrollment_type  = $_POST['enroll_type'];
  $old_enroll_id    = $_POST['old_enroll_id'];

  $addonData               = new stdClass();
  $addonData->id           = $course_id;
  $addonData->coursetype   = $_POST['courseType'];
  $addonData->points       = $_POST['points'];
  $addonData->durationhrs  = $_POST['durationHrs'];
  $addonData->durationmins = $_POST['durationMins'];

  update_course_cm_changes($addonData);

  update_enrollment_type($course_id, $enrollment_type);

  $num_sections = $_POST['topicCnt'];
  $course_code  = '';

  $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

  $params = array(
   'courses' => array(
    array(
     'id'          => $course_id,
     'fullname'    => $course_name,
     'shortname'   => $course_shortname,
     'categoryid'  => $course_category,
     'idnumber'    => $course_code,
     'summary'     => $course_summary,
     'format'      => 'topcoll',
     'numsections' => $num_sections,
    ),
   ),
  );

  $curl          = new curl();
  $curl_response = $curl->post($server_url, $params);

  if (!empty($curl_response)) {
   $arrResults['Data'] = $curl_response;
  }

 }
 return $arrResults;

}

function getCourseUsers()
{
 global $DB, $CFG;
 $response = array();

 $course_id     = $_POST['course_id'];
 $enroll_status = $_POST['enroll_status'];
 $bu_id         = $_POST['buId'];

 if (!empty($bu_id) && ($bu_id != 'null')) {
  $assigned_userids_arr = getBUAssignedUsers($bu_id);
  $userids              = implode(',', $assigned_userids_arr);
  if (!empty($userids)) {
   $append_query = "AND id in($userids)";
  }
 }
 $wsfunction = $_POST['wsfunction'];
 $wstoken    = $_POST['wstoken'];

 $moodledata             = new stdClass();
 $moodledata->wsfunction = $wsfunction;
 $moodledata->wstoken    = $wstoken;

 $enrolled_userids_arr = getEnrolledUsers($course_id, $moodledata);

 $sql = "SELECT id,concat(firstname,' ',lastname) as fullname,username FROM {$CFG->prefix}user where deleted = 0 and username not in('guest','admin') $append_query";
 $res = $DB->get_records_sql($sql);
 $i   = 1;
 foreach ($res as $rec) {
  $new_data                = new stdClass();
  $new_data->sl_no         = $i;
  $new_data->user_name     = $rec->username;
  $new_data->user_fullname = $rec->fullname;
  $new_data->user_id       = $rec->id;
  $new_data->enrolled      = in_array($rec->id, $enrolled_userids_arr) ? true : false;

  if ($enroll_status == 'all') {
   $i++;
   $response[] = $new_data;
  } else if (($new_data->enrolled == false) && ($enroll_status == 'not_enrolled')) {
   $i++;
   $response[] = $new_data;
  } else if (($new_data->enrolled == true) && ($enroll_status == 'enrolled')) {
   $i++;
   $response[] = $new_data;
  }
 }
 $arrResults['Data'] = $response;
 return $arrResults;
}

function getEnrolledUsers($courseid, $moodledata)
{
 global $CFG;
 $userids_arr = array();
 $wsfunction  = $moodledata->wsfunction;
 $wstoken     = $moodledata->wstoken;
 $server_url  = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

 $params = array('courseid' => $courseid);

 $curl          = new curl();
 $curl_response = $curl->post($server_url, $params);
 $json_arr      = json_decode($curl_response, true);

 foreach ($json_arr as $rec) {
  $userids_arr[] = $rec['id'];
 }

 return $userids_arr;
}

function enrollUserToCourse()
{
 global $DB, $CFG;
 $response = new stdClass();
 if (isset($_POST['wsfunction'])) {

  $wsfunction = $_POST['wsfunction'];
  $wstoken    = $_POST['wstoken'];

  $course_id = $_POST['course_id'];
  $user_id   = $_POST['user_id'];
  $role_id   = $_POST['role_id'];

  $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

  $sql              = "Select id from {$CFG->prefix}course_categories where id in(select category from {$CFG->prefix}course where id = '$course_id')";
  $rec              = $DB->get_record_sql($sql);
  $response->cat_id = $rec->id;

  $params = array(
   'enrolments' => array(
    array(
     'roleid'   => $role_id,
     'userid'   => $user_id,
     'courseid' => $course_id,
    ),
   ),
  );

  $curl          = new curl();
  $curl_response = $curl->post($server_url, $params);
  if (!empty($curl_response)) {
   $response->status = 1;
  }
  $arrResults['Data'] = $response;
 }
 return $arrResults;

}

function unenrollUserToCourse()
{
 global $CFG;
 $response = new stdClass();
 if (isset($_POST['wsfunction'])) {
  $wsfunction = $_POST['wsfunction'];
  $wstoken    = $_POST['wstoken'];

  $course_id = $_POST['course_id'];
  $user_id   = $_POST['user_id'];

  // fix for self enrolled courses by student mot unenrolled
  $canSelfEnroll  = canSelfEnroll($course_id);
  $response->self = ($canSelfEnroll === true) ? 1 : 0;

  $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

  $params = array(
   'enrolments' => array(
    array(
     'userid'   => $user_id,
     'courseid' => $course_id,
    ),
   ),
  );

  $curl          = new curl();
  $curl_response = $curl->post($server_url, $params);

  $moodledata             = new stdClass();
  $moodledata->wsfunction = 'core_enrol_get_enrolled_users';
  $moodledata->wstoken    = $wstoken;
  $enrolled_userids_arr   = getEnrolledUsers($course_id, $moodledata);
   $response->status   = 1;
   $response->curl_res = $curl_response;
   $response->enrolled = in_array($user_id, $enrolled_userids_arr) ? true : false;
   $arrResults['Data'] = $response;
 }
 return $arrResults;
}

function mod_get_filtered_courses($arrInput)
{
 global $CFG, $DB;

 $vUserId    = $arrInput["userid"];
 $token      = $arrInput["user_key"];
 $filtertype = $arrInput["selected_filter"];

 $objCategories = $DB->get_records('course_categories');

 $wsfunction = $_POST['wsfunction'];
 $wstoken    = $_POST['wstoken'];

 //get total course count
 $vTotalCoursesCount = $DB->count_records_sql("SELECT count(*) as total_courses FROM {$CFG->prefix}course WHERE id > 1 and visible = 1");

 $params             = array('userid' => $vUserId);
 $server_url         = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";
 $curl               = new curl;
 $resp               = $curl->post($server_url, $params);
 $arrEnrolledCourses = json_decode($resp, true);

 /*echo '<pre>';
 print_r($arrEnrolledCourses);
 echo '</pre>';*/

 foreach ($arrEnrolledCourses as $course) {

  $arrDummyResults[$course["id"]]['id']           = $course["id"];
  $arrDummyResults[$course["id"]]['fullname']     = $course["fullname"];
  $arrDummyResults[$course["id"]]['categoryname'] = $objCategories[$course["category"]]->name;
  $arrDummyResults[$course["id"]]['category']     = $course["category"];

  if ($course["progress"] == '') {
   $vProgress = 0;
  } else {
   $vProgress = $course["progress"];
  }
  $arrDummyResults[$course["id"]]['progress']      = $vProgress;
  $arrDummyResults[$course["id"]]['overviewfiles'] = $course["overviewfiles"];
  $arrDummyResults[$course["id"]]['credits']       = get_course_points($course["id"]);

  $count++;
 }

 rsort($arrDummyResults);

 $count = 0;
 foreach ($arrDummyResults as $course) {
  $progress = round($course["progress"]);
  if (($filtertype == 'enrolled') || ($filtertype == '')) {
   $arrResults['Data'][$count]['id']            = $course["id"];
   $arrResults['Data'][$count]['fullname']      = $course["fullname"];
   $arrResults['Data'][$count]['category']      = $course["category"];
   $arrResults['Data'][$count]['categoryname']  = $course["categoryname"];
   $arrResults['Data'][$count]['progress']      = $progress;
   $arrResults['Data'][$count]['overviewfiles'] = $course["overviewfiles"];
   $arrResults['Data'][$count]['credits']       = $course["credits"];
   $arrResults['Data'][$count]['status']        = 1;
   $count++;
  } else if (($filtertype == 'completed') && ($progress == 100)) {
   $arrResults['Data'][$count]['id']            = $course["id"];
   $arrResults['Data'][$count]['fullname']      = $course["fullname"];
   $arrResults['Data'][$count]['category']      = $course["category"];
   $arrResults['Data'][$count]['categoryname']  = $course["categoryname"];
   $arrResults['Data'][$count]['progress']      = $progress;
   $arrResults['Data'][$count]['overviewfiles'] = $course["overviewfiles"];
   $arrResults['Data'][$count]['credits']       = $course["credits"];
   $arrResults['Data'][$count]['status']        = 1;
   $count++;
  } else if (($filtertype == 'not_started') && ($progress == 0)) {
   $arrResults['Data'][$count]['id']            = $course["id"];
   $arrResults['Data'][$count]['fullname']      = $course["fullname"];
   $arrResults['Data'][$count]['category']      = $course["category"];
   $arrResults['Data'][$count]['categoryname']  = $course["categoryname"];
   $arrResults['Data'][$count]['progress']      = $progress;
   $arrResults['Data'][$count]['overviewfiles'] = $course["overviewfiles"];
   $arrResults['Data'][$count]['credits']       = $course["credits"];
   $arrResults['Data'][$count]['status']        = 1;
   $count++;
  } else if (($filtertype == 'in_progress') && ($progress > 0) && ($progress < 100)) {
   $arrResults['Data'][$count]['id']            = $course["id"];
   $arrResults['Data'][$count]['fullname']      = $course["fullname"];
   $arrResults['Data'][$count]['category']      = $course["category"];
   $arrResults['Data'][$count]['categoryname']  = $course["categoryname"];
   $arrResults['Data'][$count]['progress']      = $progress;
   $arrResults['Data'][$count]['overviewfiles'] = $course["overviewfiles"];
   $arrResults['Data'][$count]['credits']       = $course["credits"];
   $arrResults['Data'][$count]['status']        = 1;
   $count++;
  }
 }

 return $arrResults;
}

function getCourseStatusCount($arrInput)
{
 global $DB, $CFG;

 $vUserId    = $arrInput['userid'];
 $wsfunction = $_POST['wsfunction'];
 $wstoken    = $_POST['wstoken'];

 $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

 $params = array('userid' => $vUserId);

 $curl      = new curl;
 $resp      = $curl->post($server_url, $params);
 $arrOutput = json_decode($resp, true);

 $vEnrolledCount   = 0;
 $vCompletedCount  = 0;
 $vInprogressCount = 0;
 $vNotstartedCount = 0;
 foreach ($arrOutput as $course) {

  $vEnrolledCount++;
  if ($course[progress] == 100) {
   $vCompletedCount++;
  } else if ($course[progress] == 0) {
   $vNotstartedCount++;
  } else if ($course[progress] < 100 and $course[progress] > 0) {
   $vInprogressCount++;
  }

 }
 $arrResults['Data']['enrolled_courses']  = $vEnrolledCount;
 $arrResults['Data']['complete_courses']  = $vCompletedCount;
 $arrResults['Data']['plain_courses']     = $vNotstartedCount;
 $arrResults['Data']['course_inprogress'] = $vInprogressCount;

 return $arrResults;
}

function getCourseDetails($arrInput)
{

 global $CFG, $DB;

 $wsfunction = $_POST['wsfunction'];
 $wstoken    = $_POST['wstoken'];
 $vCourseId  = $_POST['courseid'];

 $curl = new curl;

 $params = array('field' => 'id', 'value' => $vCourseId);

 $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";
 $resp       = $curl->post($server_url, $params);
 $arrOutput  = json_decode($resp, true);
 $arrCourse  = $arrOutput['courses'][0];

 $count = 0;

 $arrResults['Data']['id']           = $arrCourse['id'];
 $arrResults['Data']['displayname']  = $arrCourse['displayname'];
 $arrResults['Data']['categoryname'] = $arrCourse['categoryname'];
 $arrResults['Data']['summary']      = $arrCourse['summary'];

 $count++;

 return $arrResults;

}

function getModuleDetails($arrInput)
{
 global $CFG, $DB;

 $vUserId   = $arrInput['user_id'];
 $wstoken   = $arrInput['wstoken'];
 $vCourseId = $arrInput['courseid'];

 //get module status
 $wsfunction1 = 'core_completion_get_activities_completion_status';
 $params      = array('userid' => $vUserId, 'courseid' => $vCourseId);

 $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction1&wstoken=$wstoken";

 $curl = new curl;

 $resp            = $curl->post($server_url, $params);
 $arrModuleStatus = json_decode($resp, true);

 //print_r($arrModuleStatus);

 //get module name & details

 $wsfunction2 = 'core_course_get_contents';
 $params      = array('courseid' => $vCourseId);
 $server_url  = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction2&wstoken=$wstoken";

 $curl      = new curl;
 $resp      = $curl->post($server_url, $params);
 $arrOutput = json_decode($resp, true);

 $arrModules = $arrOutput[0]['modules'];

 $count = 0;
 foreach ($arrOutput as $topic) {

  $arrModules = $topic['modules'];
  foreach ($arrModules as $module) {
   $arrResults['Data'][$count]['id']      = $count;
   $arrResults['Data'][$count]['modid']   = $module[id];
   $arrResults['Data'][$count]['name']    = $module[name];
   $arrResults['Data'][$count]['modname'] = $module[modname];
   if ($module['completiondata']['timecompleted'] > 0) {
    $vStatus = 'Completed';
   } else {
    $vStatus = 'Not Completed';
   }
   $arrResults['Data'][$count]['status'] = $vStatus;
   $count++;
  }

 }

 return $arrResults;

}

function getadminDashStats()
{
 global $DB, $CFG;

 if (isset($_POST["wstoken"])) {

  $q1      = "SELECT count(id) as coursesCount FROM {$CFG->prefix}course where id > 1 and visible=1";
  $courses = $DB->get_record_sql($q1);

  $q2    = "SELECT count(id) as usersCount FROM {$CFG->prefix}user where id > 2 and deleted=0";
  $users = $DB->get_record_sql($q2);

  $q3  = "SELECT count(id) as lpscount FROM {$CFG->prefix}cm_admin_learning_path";
  $lps = $DB->get_record_sql($q3);

  $q4  = "SELECT count(id) as buscount FROM {$CFG->prefix}cm_business_units";
  $bus = $DB->get_record_sql($q4);

  $arrResults['Data']['usersCount']   = $users->userscount;
  $arrResults['Data']['coursesCount'] = $courses->coursescount;
  $arrResults['Data']['lpsCount']     = $lps->lpscount;
  $arrResults['Data']['busCount']     = $bus->buscount;

 }

 return $arrResults;
}

function createLP()
{
 global $DB, $CFG;

 if (isset($_POST["lp_name"])) {
  $LP               = new stdClass();
  $LP->lpname       = $_POST['lp_name'];
  $LP->lpdesc       = $_POST['lp_description'];
  $LP->points       = $_POST['lp_credit'];
  $LP->lpdays       = $_POST['lp_days'];
  $LP->threshold    = $_POST['lp_threshold'];
  $LP->lpstatus     = 'active';
  $LP->startdate    = null;
  $LP->enddate      = null;
  $LP->lpimage      = null;
  $LP->lastmodified = time();
  if (isset($_POST["buId"])) {
   $LP->buid = (int) $_POST['buId'];
  }
  $LP->creator = $_POST['lp_creator_id'];
  $inserted    = insert_lpdetails($LP);

  $arrResults['Data'] = $inserted;
 }
 return $arrResults;
}

function listLP()
{
 global $DB, $CFG;
 $response     = array();
 $append_query = '';
 $bu           = null;
 if (isset($_POST['buId']) && ($_POST['buId'] != 'null')) {
  $buId         = $_POST['buId'];
  $append_query = "AND buid = $buId";
  $bu           = $DB->get_record('cm_business_units', array('id' => $buId));
 }
 $BuName = (empty($bu)) ? 'Learnospace' : $bu->bu_name;
 if (isset($_POST['userId'])) {
  $q   = "select id,lpname,coursecnt,usercnt,lpstatus,lpdays,threshold,lpdesc,course,creator from {cm_admin_learning_path} where lpstatus ='active' $append_query";
  $lps = $DB->get_records_sql($q);
  foreach ($lps as $rec) {
   $new_data                 = new stdClass();
   $new_data->lp_name        = $rec->lpname;
   $new_data->lp_bu          = $BuName;
   $new_data->lp_id          = $rec->id;
   $new_data->lp_courses_cnt = getLPCourseCnt($rec->id);
   $new_data->lp_users_cnt   = LpUserscnt($rec->id);
   $new_data->lp_threshold   = $rec->threshold;
   $new_data->lp_status      = $rec->lpstatus;
   $new_data->lp_days        = $rec->lpdays;
   $response[]               = $new_data;
  }
  $arrResults['Data'] = $response;
 }
 return $arrResults;
}

function get_lp_by_id()
{
 global $DB, $CFG;

 $response = new stdClass();

 if (isset($_POST['lp_id'])) {
  $lp_id = $_POST['lp_id'];

  $sql = "select * from {$CFG->prefix}cm_admin_learning_path where id='$lp_id'";
  $rec = $DB->get_record_sql($sql);

  $response->name         = $rec->lpname;
  $response->description  = strip_tags($rec->lpdesc);
  $response->lp_days      = $rec->lpdays;
  $response->lp_threshold = $rec->threshold;
  $response->lp_status    = $rec->lpstatus;
  $response->lp_credit    = $rec->points;

  $arrResults['Data'] = $response;
 }
 return $arrResults;
}

function update_lp()
{
 global $DB, $CFG;

 if (isset($_POST["lp_name"])) {
  $LP               = new stdClass();
  $LP->id           = $_POST['lp_id'];
  $LP->lpname       = $_POST['lp_name'];
  $LP->lpdesc       = $_POST['lp_description'];
  $LP->points       = $_POST['lp_credit'];
  $LP->lpdays       = $_POST['lp_days'];
  $LP->threshold    = $_POST['lp_threshold'];
  $LP->lpstatus     = 'active';
  $LP->startdate    = null;
  $LP->enddate      = null;
  $LP->lpimage      = null;
  $LP->lastmodified = time();
  $LP->creator      = $_POST['lp_creator_id'];
  $inserted         = update_lpdetails($LP);

  $arrResults['Data'] = $inserted;
 }
 return $arrResults;
}

function listLPCourses()
{
 global $DB, $CFG;
 $response = array();
 if (isset($_POST['catId'])) {
  $catId = $_POST['catId'];
 }
 $coursefilter = $_POST['assign_status'];

 $categoryFilter = isset($catId) ? "category=$catId" : "category > 0";

 $moodledata             = new stdClass();
 $moodledata->wsfunction = $_POST['wsfunction'];
 $moodledata->wstoken    = $wstoken;
 $i                      = 1;
 $lpid                   = $_POST['lp_id'];
 if (isset($lpid)) {
  $q       = "SELECT * FROM {course} where visible=1 and $categoryFilter";
  $courses = $DB->get_records_sql($q);
  foreach ($courses as $rec) {
   $new_data                   = new stdClass();
   $cat                        = new stdClass();
   $cat->tablename             = 'course_categories';
   $cat->fieldname             = 'name';
   $cat->recid                 = 'id';
   $cat->id                    = $rec->category;
   $new_data->course_fullname  = $rec->fullname;
   $new_data->course_shortname = $rec->shortname;
   $new_data->sl_no            = $i;
   $category                   = get_field_by_id($cat);
   $new_data->category_id      = $category->id;
   $new_data->category_name    = $category->name;
   $new_data->course_id        = $rec->id;
   $new_data->assigned         = getAssignedLPCourses($rec->id, $lpid);
   if ($coursefilter == 'all') {
    $response[] = $new_data;
    $i++;
   } else if (($coursefilter == 'assigned') && ($new_data->assigned)) {
    $response[] = $new_data;
    $i++;
   } else if (($coursefilter == 'not_assigned') && (!$new_data->assigned)) {
    $response[] = $new_data;
    $i++;
   }

  }
  $arrResults['Data'] = $response;
 }
 return $arrResults;
}

function getLPCourseCnt($lpid)
{
 global $DB, $CFG;
 $q  = "SELECT count(id) as lpcoursecnt FROM {$CFG->prefix}cm_lp_course where lp_id= $lpid";
 $lp = $DB->get_record_sql($q);
 return $lp->lpcoursecnt;
}

function getAssignedLPCourses($cid, $lpid)
{
 global $DB, $CFG;

 $q  = "SELECT id as assigned FROM {$CFG->prefix}cm_lp_course where lp_courseid= $cid and lp_id= $lpid";
 $lp = $DB->get_record_sql($q);

 if ($lp) {
  return true;
 }

 return false;
}

function AddLPCourse()
{
 global $DB, $CFG;
 if (isset($_POST['lp_id'])) {
  $coursedid              = new stdClass();
  $coursedid->lp_id       = $_POST['lp_id'];
  $coursedid->lp_type     = '1';
  $coursedid->creator     = $_POST['userId'];
  $coursedid->timecreated = time();
  $coursedid->lp_courseid = $_POST['course_id'];
  $coursedid->status      = 1;
  $instcid                = $DB->insert_record('cm_lp_course', $coursedid);
 }
 $arrResults['Data']['lpc_id'] = $instcid;
 return $arrResults;
}

function removeLPCourse()
{
 global $DB, $CFG;

 if (isset($_POST['lp_id'])) {
  $vLPId     = $_POST['lp_id'];
  $vCourseId = $_POST['course_id'];
  $done      = $DB->delete_records('cm_lp_course', array('lp_id' => $vLPId, "lp_courseid" => $vCourseId));
 }

 $arrResults['Data']['done'] = $done;
 return $arrResults;
}

function getLPDetails()
{
 global $CFG, $DB;
 $lpId = $_POST['lp_id'];
 if ($lpId) {
  $q  = "SELECT * FROM  {$CFG->prefix}cm_admin_learning_path where id= $lpId ";
  $lp = $DB->get_record_sql($q);

  $arrResults['Data']['name']       = $lp->lpname;
  $arrResults['Data']['desc']       = $lp->lpdesc;
  $arrResults['Data']['points']     = $lp->points;
  $arrResults['Data']['days']       = $lp->lpdays;
  $arrResults['Data']['id']         = $lp->id;
  $arrResults['Data']['coursecnt']  = getLPCourseCnt($lpId);
  $arrResults['Data']['coursesarr'] = getLPCoursesArr($lpId);
  $arrResults['Data']['image']      = $CFG->wwwroot . '/cm/lp/lpimages/istockphoto.jpg';
 }
 return $arrResults;
}

function getLPCoursesArr($lpId)
{
 global $DB, $CFG;

 $q    = "SELECT lp_courseid as cid FROM {$CFG->prefix}cm_lp_course where lp_id= $lpId order by sorder ASC";
 $cids = $DB->get_records_sql($q);

 $courses = array();

 foreach ($cids as $course) {
  $q1               = "SELECT id,fullname FROM {$CFG->prefix}course where id = $course->cid and visible=1";
  $rec              = $DB->get_record_sql($q1);
  $result           = new stdClass();
  $result->id       = $rec->id;
  $result->fullname = $rec->fullname;
  $courses[]        = $result;
 }
 return $courses;
}

function getLPUsers()
{
 global $DB, $CFG;
 $response = array();

 $lp_id         = $_POST['lp_id'];
 $assign_status = $_POST['assign_status'];

 $assigned_userids_arr = getLPAssignedUsers($lp_id);

 $sql = "SELECT id,concat(firstname,' ',lastname) as fullname,username FROM {$CFG->prefix}user where deleted = 0 and username not in('guest','admin')";
 $res = $DB->get_records_sql($sql);
 $i   = 1;
 foreach ($res as $rec) {
  $new_data                = new stdClass();
  $new_data->sl_no         = $i;
  $new_data->user_name     = $rec->username;
  $new_data->user_fullname = $rec->fullname;
  $new_data->user_id       = $rec->id;
  $new_data->assigned      = in_array($rec->id, $assigned_userids_arr) ? true : false;

  if ($assign_status == 'all') {
   $i++;
   $response[] = $new_data;
  } else if (($new_data->assigned == false) && ($assign_status == 'not_assigned')) {
   $i++;
   $response[] = $new_data;
  } else if (($new_data->assigned == true) && ($assign_status == 'assigned')) {
   $i++;
   $response[] = $new_data;
  }
 }
 $arrResults['Data'] = $response;
 return $arrResults;
}

function getLPAssignedUsers($lpId)
{
 global $DB, $CFG;
 $useridsarr = array();
 $q          = "SELECT userid FROM {$CFG->prefix}cm_lp_assignment where lp_id= $lpId";
 $res        = $DB->get_records_sql($q);
 foreach ($res as $rec) {
  $useridsarr[] = $rec->userid;
 }

 return $useridsarr;
}

function assignLpUser()
{
 global $DB, $CFG;

 $lp_id      = $_POST['lp_id'];
 $user_id    = $_POST['user_id'];
 $creator_id = $_POST['creatorId'];

 $arrResults = array();

 $status = 0;

 $courseids = $DB->get_records_sql("select lp_courseid as courseid, ctype from {cm_lp_course} where lp_id = $lp_id");
 if (!empty($courseids)) {
  $lpuser              = new stdClass();
  $lpuser->lp_id       = $lp_id;
  $lpuser->lp_type     = '1';
  $lpuser->userid      = $user_id;
  $lpuser->timecreated = time();
  $lpuser->status      = 1;

  foreach ($courseids as $ck => $course) {
   $lpuser->courseid        = $course->courseid;
   $lpuser->ctype           = $course->ctype;
   $lpuser->goal_start_date = date("d-m-Y");
   $lpuser->goal_end_date   = $endday;
   $insertedid              = $DB->insert_record('cm_lp_assignment', $lpuser);

   if (!empty($course->courseid)) {

    $enrolid = $DB->get_record_sql("select id from {enrol} where courseid =$course->courseid and enrol='manual' and status =0");
    if (!empty($enrolid->id)) {
     if (!$DB->record_exists('user_enrolments', array('enrolid' => $enrolid->id, 'userid' => $user_id))) {
      $usrenrol               = new stdClass();
      $usrenrol->status       = "0";
      $usrenrol->enrolid      = $enrolid->id;
      $usrenrol->userid       = $user_id;
      $usrenrol->timestart    = time();
      $usrenrol->timeend      = "0";
      $usrenrol->modifierid   = $creator_id;
      $usrenrol->timecreated  = time();
      $usrenrol->timemodified = time();
      $DB->insert_record('user_enrolments', $usrenrol);

      $arrResults['Data']['status'] = 1;
      //echo "enrol user"; exit;
     }

     $sql_context = $DB->get_record_sql("SELECT id from {context} where instanceid = $course->courseid and contextlevel = 50");
     if (!empty($sql_context->id)) {
      if (!$DB->record_exists('role_assignments', array('contextid' => $sql_context->id, 'userid' => $user_id, 'roleid' => 5))) {
       $contextid                 = $sql_context->id;
       $groupmodify               = new stdClass();
       $groupmodify->roleid       = 5;
       $groupmodify->contextid    = $contextid;
       $groupmodify->userid       = $user_id;
       $groupmodify->timemodified = time();
       $groupmodify->modifierid   = $creator_id;
       $groupmodify->component    = "0";
       $groupmodify->itemid       = "0";
       $groupmodify->sortorder    = "0";
       $inserted                  = $DB->insert_record('role_assignments', $groupmodify);
       //echo "role user"; exit;
      }
     }
    }
   }
  }

 }

 return $arrResults;
}

function UnassignLpUser()
{
 global $DB, $CFG;

 $lp_id      = $_POST['lp_id'];
 $user_id    = $_POST['user_id'];
 $creator_id = $_POST['creatorId'];

 if ($lp_id) {
  $res      = $DB->delete_records('cm_lp_assignment', array('lp_id' => $lp_id, 'userid' => $user_id));
  $courseid = $DB->get_records_sql("select lp_courseid as courseid from {cm_lp_course} where lp_id = $lp_id");
  foreach ($courseid as $ck => $course) {

   $q1          = "select enrolid from {user_enrolments} where userid = $user_id and status = 0";
   $enrollments = $DB->get_records_sql($q1);

   foreach ($enrollments as $rec) {
    $enroll[] = $rec->enrolid;
   }
   $enrol_ids = implode(',', $enroll);

   $sql     = $DB->get_record_sql("select id from {enrol} where id in ($enrol_ids) and courseid = $course->courseid and  enrol = 'manual'");
   $enrolid = $sql->id;

   if (!empty($enrolid)) {
    $DB->delete_records('user_enrolments', array('userid' => $user_id, 'enrolid' => $enrolid));
   }
   $sql_context = $DB->get_record_sql("SELECT id from {$CFG->prefix}context where instanceid = $course->courseid and contextlevel = 50");
   $contextid   = $sql_context->id;
   if (!empty($contextid)) {
    $DB->delete_records('role_assignments', array('contextid' => $contextid, 'userid' => $user_id, 'roleid' => 5));
   }
  }

  $ccount                     = $DB->get_record_sql("select count(*) as cnt from {cm_admin_learning_path} where id=$lp_id");
  $masterupdate               = new stdClass();
  $masterupdate->id           = $lp_id;
  $masterupdate->usercnt      = $ccount->cnt;
  $masterupdate->lastmodified = time();
  $masterupdate->modifier     = $creator_id;
  $updateid                   = $DB->update_record('cm_admin_learning_path', $masterupdate);

  $arrResults['Data'] = 1;
 }

 return $arrResults;
}

function LpUserscnt($lpId)
{
 global $DB, $CFG;
 $usercnt = 0;

 $q   = "SELECT count(Distinct(userid)) as userscnt from {$CFG->prefix}cm_lp_assignment where lp_id=$lpId";
 $rec = $DB->get_record_sql($q);

 if (!empty($rec->userscnt)) {
  $usercnt = $rec->userscnt;
 }
 return $usercnt;
}

function getMyEnrolledLPs()
{
 global $DB, $CFG;
 $user_id = $_POST['userid'];

 if (!empty($user_id)) {
  $q   = "SELECT Distinct(lp_id) as lpid FROM {$CFG->prefix}cm_lp_assignment where userid= $user_id";
  $res = $DB->get_records_sql($q);
  foreach ($res as $rec) {
   $lpidsarr[] = $rec->lpid;
  }
  $lpids = implode(',', $lpidsarr);
 }

 if (!empty($lpids)) {
  $q1     = "SELECT * FROM {$CFG->prefix}cm_admin_learning_path where id in($lpids)";
  $lp_res = $DB->get_records_sql($q1);
  foreach ($lp_res as $rec) {
   $new_data           = new stdClass();
   $new_data->id       = $rec->id;
   $new_data->lpname   = $rec->lpname;
   $new_data->progress = 0;
   $new_data->imgUrl   = $CFG->wwwroot . '/cm/lp/lpimages/istockphoto.jpg';
   $LP[]               = $new_data;
  }
  $arrResults['Data'] = $LP;
 }
 return $arrResults;
}

function createBU()
{
 global $DB, $CFG;
 $bu_name      = $_POST['bu_name'];
 $logo_file    = $_POST['logoFile'];
 $logoFileName = $_POST['logoFileName'];
 $arrResults   = array();
 if ($bu_name) {
  $BU                 = new stdClass;
  $BU->bu_name        = $bu_name;
  $BU->logo_img_name  = $logoFileName;
  $BU->parent         = 1;
  $BU->sortorder      = 0;
  $buId               = $DB->insert_record('cm_business_units', $BU);
  $arrResults['Data'] = $buId;
  if (!empty($logoFileName)) {
   logo_upload($logo_file, $logoFileName, $buId);
  }

 }
 return $arrResults;
}

function get_bu_by_id()
{
 global $DB, $CFG;
 $bu_id      = $_POST['bu_id'];
 $arrResults = array();
 if ($bu_id) {
  $q                   = "Select * from {$CFG->prefix}cm_business_units where id= $bu_id";
  $rec                 = $DB->get_record_sql($q);
  $response            = new stdClass();
  $response->buname    = $rec->bu_name;
  $response->logo_name = $rec->logo_img_name;
  if (!empty($response->logo_name)) {
   $response->logo_path = "/cm/api/uploads/bu_" . $rec->id . '/' . $rec->logo_img_name;
  }
  $arrResults['Data'] = $response;
 }
 return $arrResults;
}

function updateBU()
{
 global $DB, $CFG;
 $bu_name      = $_POST["bu_name"];
 $bu_id        = $_POST["bu_id"];
 $logo_file    = $_POST['logoFile'];
 $logoFileName = $_POST['logoFileName'];

 if ((isset($logo_file)) && (!empty($logoFileName))) {
  $BU->logo_img_name = $logoFileName;
  logo_upload($logo_file, $logoFileName, $bu_id);
 }

 if (isset($bu_id)) {
  $BU->id             = $bu_id;
  $BU->bu_name        = $bu_name;
  $BU->sortorder      = 0;
  $BU->parent         = 1;
  $inserted           = $DB->update_record('cm_business_units', $BU);
  $arrResults['Data'] = $inserted;
 }

 return $arrResults;
}

function listBU()
{
 global $DB, $CFG;
 $response     = array();
 $append_query = '';
 $buId         = $_POST['buId'];
 if (!empty($buId) && ($buId != 'null')) {
  $append_query = "WHERE id = $buId";
 }

 $q   = "select * from {cm_business_units} $append_query";
 $lps = $DB->get_records_sql($q);
 foreach ($lps as $rec) {
  $new_data          = new stdClass();
  $new_data->bu_name = $rec->bu_name;
  $new_data->bu_id   = $rec->id;
  if (!empty($rec->logo_img_name)) {
   $new_data->bu_logo_path = "/cm/api/uploads/bu_" . $rec->id . '/' . $rec->logo_img_name;
  } else {
   $new_data->bu_logo_path = '/cm/lp/lpimages/istockphoto.jpg';
  }
  $q1                       = "SELECT count(Distinct(bu_courseid)) as coursecnt FROM {$CFG->prefix}cm_bu_course where bu_id=$rec->id";
  $res                      = $DB->get_record_sql($q1);
  $q2                       = "SELECT count(id) as usercnt FROM {$CFG->prefix}user where id in (select Distinct(userid) from {$CFG->prefix}cm_bu_assignment where bu_id=$rec->id) and deleted=0 and suspended=0";
  $rec                      = $DB->get_record_sql($q2);
  $new_data->bu_courses_cnt = $res->coursecnt;
  $new_data->bu_users_cnt   = $rec->usercnt;
  $response[]               = $new_data;
 }
 $arrResults['Data'] = $response;

 return $arrResults;
}

function getMyEnrolledBUs()
{
 global $DB, $CFG;
 $user_id = $_POST['userid'];

 if (!empty($user_id)) {

  $bu_arr = listBU();
  $bu_res = $bu_arr['Data'];

  foreach ($bu_res as $rec) {
   $new_data            = new stdClass();
   $new_data->id        = $rec->bu_id;
   $new_data->buName    = $rec->bu_name;
   $new_data->imgUrl    = $CFG->wwwroot . $rec->bu_logo_path;
   $new_data->buCourses = $rec->bu_courses_cnt;
   $new_data->buUsers   = $rec->bu_users_cnt;
   $BU[]                = $new_data;
  }
  $arrResults['Data'] = $BU;
 }
 return $arrResults;
}

function listBUCourses()
{
 global $DB, $CFG;
 $response = array();
 if (isset($_POST['catId'])) {
  $catId = $_POST['catId'];
 }
 $coursefilter = $_POST['assign_status'];

 $categoryFilter = isset($catId) ? "category=$catId" : "category > 0";

 $i    = 1;
 $buid = $_POST['bu_id'];
 if (isset($buid)) {
  $q       = "SELECT * FROM {course} where visible=1 and $categoryFilter";
  $courses = $DB->get_records_sql($q);
  foreach ($courses as $rec) {
   $new_data                   = new stdClass();
   $cat                        = new stdClass();
   $cat->tablename             = 'course_categories';
   $cat->fieldname             = 'name';
   $cat->recid                 = 'id';
   $cat->id                    = $rec->category;
   $new_data->course_fullname  = $rec->fullname;
   $new_data->course_shortname = $rec->shortname;
   $new_data->sl_no            = $i;
   $category                   = get_field_by_id($cat);
   $new_data->category_id      = $category->id;
   $new_data->category_name    = $category->name;
   $new_data->course_id        = $rec->id;
   $new_data->assigned         = getAssignedBUCourses($rec->id, $buid);
   if ($coursefilter == 'all') {
    $response[] = $new_data;
    $i++;
   } else if (($coursefilter == 'assigned') && ($new_data->assigned)) {
    $response[] = $new_data;
    $i++;
   } else if (($coursefilter == 'not_assigned') && (!$new_data->assigned)) {
    $response[] = $new_data;
    $i++;
   }

  }
  $arrResults['Data'] = $response;
 }
 return $arrResults;
}

function getAssignedBUCourses($cid, $buid)
{
 global $DB, $CFG;

 $q  = "SELECT id as assigned FROM {$CFG->prefix}cm_bu_course where bu_courseid= $cid and bu_id= $buid";
 $bu = $DB->get_record_sql($q);

 if ($bu) {
  return true;
 }

 return false;
}

function AddBUCourse()
{
 global $DB, $CFG;
 if (isset($_POST['bu_id'])) {
  $coursedid              = new stdClass();
  $coursedid->bu_id       = $_POST['bu_id'];
  $coursedid->creator     = $_POST['userId'];
  $coursedid->timecreated = time();
  $coursedid->bu_courseid = $_POST['course_id'];
  $instcid                = $DB->insert_record('cm_bu_course', $coursedid);
 }
 $arrResults['Data']['buc_id'] = $instcid;
 return $arrResults;
}

function AddCoursetoBU($params)
{
 global $DB, $CFG;
 $coursedid              = new stdClass();
 $coursedid->bu_id       = $params->bu_id;
 $coursedid->creator     = $params->userId;
 $coursedid->timecreated = time();
 $coursedid->bu_courseid = $params->course_id;
 $instcid                = $DB->insert_record('cm_bu_course', $coursedid);
 return $instcid;
}

function removeBUCourse()
{
 global $DB, $CFG;

 if (isset($_POST['bu_id'])) {
  $vLPId     = $_POST['bu_id'];
  $vCourseId = $_POST['course_id'];
  $done      = $DB->delete_records('cm_bu_course', array('bu_id' => $vLPId, "bu_courseid" => $vCourseId));
 }

 $arrResults['Data']['done'] = $done;
 return $arrResults;
}

function getBUUsers()
{
 global $DB, $CFG;
 $response = array();

 $bu_id         = $_POST['bu_id'];
 $assign_status = $_POST['assign_status'];

 if (!empty($bu_id)) {

  $assigned_userids_arr = getBUAssignedUsers($bu_id);

  $userids = implode(',', $assigned_userids_arr);

  $bu_managers_arr = getBUManagers($bu_id);

  if (!empty($userids)) {
   $sql = "SELECT id,concat(firstname,' ',lastname) as fullname,username FROM {$CFG->prefix}user where deleted = 0 and username not in('guest','admin') and id in($userids)";
   $res = $DB->get_records_sql($sql);
  }
  $i = 1;
  foreach ($res as $rec) {
   $new_data                   = new stdClass();
   $new_data->sl_no            = $i;
   $new_data->user_name        = $rec->username;
   $new_data->user_fullname    = $rec->fullname;
   $new_data->user_id          = $rec->id;
   $BU                         = getBuByUid($rec->id);
   $new_data->allotted_bu_name = $BU->bu_name;
   $new_data->assigned         = in_array($rec->id, $bu_managers_arr) ? true : false;

   if ($assign_status == 'all') {
    $i++;
    $response[] = $new_data;
   } else if (($new_data->assigned == false) && ($assign_status == 'not_assigned')) {
    $i++;
    $response[] = $new_data;
   } else if (($new_data->assigned == true) && ($assign_status == 'assigned')) {
    $i++;
    $response[] = $new_data;
   }
  }
 }
 $arrResults['Data'] = $response;
 return $arrResults;
}

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

function getBUManagers($buId)
{
 global $DB, $CFG;
 $useridsarr = array();
 $q          = "SELECT userid FROM {$CFG->prefix}cm_bu_admins where bu_id= $buId";
 $res        = $DB->get_records_sql($q);
 foreach ($res as $rec) {
  $useridsarr[] = $rec->userid;
 }
 return $useridsarr;
}

function assignBuManager()
{
 global $DB, $CFG;

 $bu_id   = $_POST['bu_id'];
 $user_id = $_POST['user_id'];

 $arrResults = array();

 $status = 0;

 if ($bu_id) {
  $arrResults['Data']['status'] = BUManagerEntry($bu_id, $user_id);
  $roleassignment               = adminRoleAssignment($user_id);
 }
 return $arrResults;
}

function BUManagerEntry($bu_id, $user_id)
{
 global $DB;
 $buManager         = new stdClass();
 $buManager->bu_id  = $bu_id;
 $buManager->userid = $user_id;
 $insertedid        = $DB->insert_record('cm_bu_admins', $buManager);
 return $insertedid;
}

function BUuserEntry($bu_id, $user_id)
{
 global $DB;
 $buuser              = new stdClass();
 $buuser->bu_id       = $bu_id;
 $buuser->userid      = $user_id;
 $buuser->timecreated = time();
 $insertedid          = $DB->insert_record('cm_bu_assignment', $buuser);
 return $insertedid;
}

function UnassignBuManager()
{
 global $DB, $CFG;

 $bu_id   = $_POST['bu_id'];
 $user_id = $_POST['user_id'];

 if (!empty($bu_id)) {
  $DB->delete_records('cm_bu_admins', array('userid' => $user_id, 'bu_id' => $bu_id));
  $DB->delete_records('role_assignments', array('userid' => $user_id, 'contextid' => 1, 'roleid' => 1));
  $arrResults['Data'] = 1;
 }
 return $arrResults;
}

function handleBuEntry($bu_id, $user_id)
{
 global $DB, $CFG;

 $sql    = "select id from {$CFG->prefix}cm_bu_assignment where userid= $user_id";
 $rec    = $DB->get_record_sql($sql);
 $result = '';

 if (empty($rec->id)) {
  $result = BUuserEntry($bu_id, $user_id);

 } else {
  $q      = "UPDATE {$CFG->prefix}cm_bu_assignment SET bu_id = ? WHERE userid =?";
  $result = $DB->execute($q, [$bu_id, $user_id]);
 }
 return $result;
}

function adminRoleAssignment($uidd)
{
 global $DB;
 $ra1 = '';
 if (!$DB->record_exists('role_assignments', array('userid' => $uidd, 'contextid' => 1, 'roleid' => 1))) {
  $ra1               = new stdClass();
  $ra1->roleid       = 1;
  $ra1->contextid    = 1;
  $ra1->userid       = $uidd;
  $ra1->component    = '';
  $ra1->itemid       = 0;
  $ra1->timemodified = strtotime("now");
  $ra1->modifierid   = empty($user->id) ? 0 : $user->id;
  $ra1->sortorder    = 0;
  $ra1               = $DB->insert_record('role_assignments', $ra1);
 }
 return $ra1;
}

function DeleteBu()
{
 global $DB, $CFG;
 $bu_id = $_POST['bu_id'];

 if (!empty($bu_id)) {
  deleteBUusers($bu_id);
  $DB->delete_records('cm_business_units', array("id" => $bu_id));
  $DB->delete_records('cm_bu_admins', array("bu_id" => $bu_id));
  $DB->delete_records('cm_bu_assignment', array("bu_id" => $bu_id));
  $DB->delete_records('cm_bu_course', array("bu_id" => $bu_id));
  $arrResults['Data'] = 1;
 }
 return $arrResults;
}

function deleteBUusers($bu_id)
{
 global $DB, $CFG;
 $q   = "SELECT userid FROM {$CFG->prefix}cm_bu_assignment where bu_id=$bu_id";
 $res = $DB->get_records_sql($q);
 foreach ($res as $rec) {
  $status = $DB->delete_records('user', array("id" => $rec->userid));
 }
 return $status;
}

function logo_upload($fileString, $fileName, $bu_id)
{
 if (!empty($fileString)) {
  list($type, $data) = explode(';', $fileString);
  list(, $data)      = explode(',', $data);
  if (!file_exists("uploads/logos/bu_$bu_id/")) {
   mkdir("uploads/bu_$bu_id/", 0777, true);
  }
  $data = base64_decode($data);
  file_put_contents("uploads/bu_$bu_id/" . $fileName, $data);
 }
}

function LpCourseSorting()
{
 global $DB, $CFG;

 $lpId               = $_POST['lp_id'];
 $json_courses       = $_POST['courses_arr'];
 $courses_arr        = json_decode($json_courses, true);
 $arrResults['Data'] = '';

 if (!empty($lpId)) {
  $i = 1;
  foreach ($courses_arr as $course) {
   $cId                = $course['id'];
   $q                  = "UPDATE {$CFG->prefix}cm_lp_course SET sorder = ? WHERE lp_id =? and lp_courseid=?";
   $result             = $DB->execute($q, [$i, $lpId, $cId]);
   $arrResults['Data'] = $result;
   ++$i;
  }

 }
 return $arrResults;
}

function courseReport()
{
 global $DB, $CFG;
 $userId = $_POST['userId'];
 $buId   = $_POST['buId'];
 if (!empty($buId) && ($buId != 'null')) {
  $courseids_in = getBuCourses($buId);
 }
 $response  = array();
 $siteAdmin = checkisSiteAdmin($userId);

 $user           = $DB->get_record('user', array("id" => $userId));
 $BU             = getBuByUid($user->id);
 $user->cm_bu_id = $BU->id;

 if ($siteAdmin) {
  $q1      = "SELECT c.id,c.fullname,c.shortname FROM {course} c where c.visible = 1 and c.category !=0 and c.id > 1";
  $courses = $DB->get_records_sql($q1);
 } else if ($buId) {
  $q1      = "SELECT c.id,c.fullname,c.shortname FROM {course} c where c.visible = 1 and c.category !=0 and c.id > 1 and c.id in($courseids_in)";
  $courses = $DB->get_records_sql($q1);
 } else {
  $q1      = "SELECT c.id,c.fullname,c.shortname FROM {course} c where c.visible = 1 and c.category !=0 and c.id > 1";
  $courses = $DB->get_records_sql($q1);
 }
 $row = 0;
 foreach ($courses as $rec) {
  $newData                  = new stdClass();
  $newData->sno             = ++$row;
  $newData->course_id       = $rec->id;
  $newData->course_fullname = $rec->fullname;
  $newData->enrolled_cnt    = get_custom_enrolled_users($rec->id, $user);
  $newData->completed_cnt   = get_progress_by_cid($rec->id, $user, 100);
  $newData->inprogress_cnt  = get_progress_by_cid($rec->id, $user, 50);
  $newData->notstarted_cnt  = get_progress_by_cid($rec->id, $user, 0);

  $response[] = $newData;
 }
 $arrResults['Data'] = $response;

 return $arrResults;
}

function get_custom_enrolled_users($cid, $user)
{
 $enrolled = count(get_enrolled_uids($cid, $user));
 return $enrolled;
}

function get_enrolled_courses($uid, $user)
{
 $enrolled = count(get_enrolled_cids($uid, $user));
 return $enrolled;
}

function get_enrolled_uids($cid, $user)
{
 global $DB, $CFG;

 $siteAdmin = checkisSiteAdmin($user->id);

 if ($siteAdmin) {
  $q1      = "select u.id from {enrol}  e join {user_enrolments} ue on e.id = ue.enrolid join {course} c on e.courseid = c.id join {user} u on u.id = ue.userid where c.id = $cid and c.visible !=0 and deleted = 0";
  $enusers = $DB->get_records_sql($q1);
 } else {
  $cmuserfilterbuids    = $user->cm_bu_id . ',' . $cmuserfilters;
  $assigned_userids_arr = getBUAssignedUsers($user->cm_bu_id);
  $cmuserfilter         = implode(',', $assigned_userids_arr);

  if (!empty($cmuserfilter)) {
   $q2      = "select u.id from {enrol}  e join {user_enrolments} ue on e.id = ue.enrolid join {course} c on e.courseid = c.id join {user} u on u.id = ue.userid where c.id = $cid and c.visible !=0 and deleted = 0 and u.id in($cmuserfilter)";
   $enusers = $DB->get_records_sql($q2);
  }
 }
 return $enusers;
}

function get_progress_by_cid($cid, $user, $progress_rate)
{

 global $CFG, $DB;

 $enusers = get_enrolled_uids($cid, $user);
 foreach ($enusers as $user) {

  $totals = $DB->get_records_sql("select * from {course_modules} where course = $cid and deletioninprogress = 0 and module != 9 ");
  $total  = count($totals);

  if (!empty($_POST['edate']) && ($progress_rate === 100)) {
   $stimestamp   = makeTimestamp($_POST['sdate']);
   $etimestamp   = makeEndTimestamp($_POST['edate']);
   $append_query = "and a.timemodified between $stimestamp and $etimestamp";
  } else {
   $append_query = '';
  }

  $attempt_q = "select a.id,a.timemodified from {course_modules_completion} as a
  join {course_modules} as b on a.coursemoduleid = b.id
  where a.userid = $user->id and b.course = $cid and b.module != 9 and completionstate >= 1 $append_query";

  $attempt = $DB->get_records_sql($attempt_q);

  $attempted = count($attempt);

  $value        = $attempted / $total * 100;
  $comptnmethod = $DB->get_record('course_completion_aggr_methd', array('criteriatype' => 4, 'course' => $cid));
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

  $progress_cnt = '';

  if ($progress_rate == 100) {
   if ($compteprogress == 100) {
    $sqll           = $DB->get_record_sql("select id  from {course} where id= $cid ");
    $comptecourse[] = $sqll->id;
   }
   $progress_cnt = count($comptecourse);
  }

  if ($progress_rate == 50) {
   $courselastaccess = $DB->get_record_sql("select count(id) as cout from {user_lastaccess}  where courseid = $cid  and userid = $user->id ");
   if (($courselastaccess->cout >= 1) && ($compteprogress != 100)) {
    $sqll             = $DB->get_record_sql("select *  from {course} where id= $cid");
    $myinprogcourse[] = $sqll;
   }
   $progress_cnt = count($myinprogcourse);
  }

  if ($progress_rate == 0) {
   $courselastaccess = $DB->get_record_sql("select count(id) as cout from {user_lastaccess}  where courseid = $cid  and userid = $user->id ");
   if (empty($courselastaccess->cout)) {
    $sqll               = $DB->get_record_sql("select *  from {course} where id= $cid ");
    $mynonstartcourse[] = $sqll;
   }
   $progress_cnt = count($mynonstartcourse);
  }
 }
 $progress_cnt = empty($progress_cnt) ? 0 : $progress_cnt;
 return $progress_cnt;
}

function get_progress_by_uid($uid, $user, $progress_rate)
{
 global $DB, $CFG;
 $encourses = get_enrolled_cids($uid, $user);
 foreach ($encourses as $course) {

  $totals = $DB->get_records_sql("select * from {course_modules} where course = $course->id and deletioninprogress = 0 and module != 9 ");
  $total  = count($totals);

  if (!empty($_POST['edate']) && ($progress_rate === 100)) {
   $stimestamp   = makeTimestamp($_POST['sdate']);
   $etimestamp   = makeEndTimestamp($_POST['edate']);
   $append_query = "and a.timemodified between $stimestamp and $etimestamp";
  } else {
   $append_query = '';
  }

  $attempt_q = "select a.id,a.timemodified from {course_modules_completion} as a
             join {course_modules} as b on a.coursemoduleid = b.id
             where a.userid = $uid and b.course = $course->id and b.module != 9 and completionstate >= 1 $append_query";

  $attempt = $DB->get_records_sql($attempt_q);

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

  $progress_cnt = '';

  if ($progress_rate == 100) {
   if ($compteprogress == 100) {
    $sqll           = $DB->get_record_sql("select id  from {course} where id= $course->id ");
    $comptecourse[] = $sqll->id;
   }
   $progress_cnt = count($comptecourse);
  }

  if ($progress_rate == 50) {
   $courselastaccess = $DB->get_record_sql("select count(id) as cout from {user_lastaccess}  where courseid = $course->id and userid = $uid ");
   if (($courselastaccess->cout >= 1) && ($compteprogress != 100)) {
    //if($courselastaccess->cout > 0){
    $sqll             = $DB->get_record_sql("select *  from {course} where id= $course->id");
    $myinprogcourse[] = $sqll;
   }
   $progress_cnt = count($myinprogcourse);
  }

  if ($progress_rate == 0) {
   $courselastaccess = $DB->get_record_sql("select count(id) as cout from {user_lastaccess}  where courseid = $course->id  and userid = $uid ");
   if (empty($courselastaccess->cout)) {
    $sqll               = $DB->get_record_sql("select *  from {course} where id= $course->id ");
    $mynonstartcourse[] = $sqll;
   }
   $progress_cnt = count($mynonstartcourse);
  }
 }
 $progress_cnt = empty($progress_cnt) ? 0 : $progress_cnt;
 return $progress_cnt;
}

function get_enrolled_cids($uid, $user)
{
 global $DB;

 $siteAdmin = checkisSiteAdmin($user->id);

 $user           = $DB->get_record('user', array("id" => $user->id));
 $BU             = getBuByUid($user->id);
 $user->cm_bu_id = $BU->id;

 if ($siteAdmin) {
  $encourses = $DB->get_records_sql("select c.id from {enrol}  e join {user_enrolments} ue on e.id = ue.enrolid join {course} c on e.courseid = c.id where ue.userid = $uid and c.visible !=0  and c.category !=0");
 } else {
  $encourses = $DB->get_records_sql("select c.id from {enrol}  e join {user_enrolments} ue on e.id = ue.enrolid join {course} c on e.courseid = c.id where ue.userid = $uid and c.visible !=0  and c.category !=0");
 }
 return $encourses;
}

function checkisSiteAdmin($userId)
{
 global $CFG;
 $knownsiteadmins = $CFG->siteadmins;
 $siteadmins      = explode(',', $CFG->siteadmins);
 $siteAdmin       = in_array($userId, $siteadmins);
 return $siteAdmin;
}

function checkisBUAdmin($userId)
{
 global $CFG, $DB;
 $rec     = $DB->get_record('cm_bu_admins', array("userid" => $userId));
 $buAdmin = (!empty($rec)) ? true : false;
 return $buAdmin;
}

function courseDetailedReport()
{
 global $DB, $CFG;
 $userId    = $_POST['userId'];
 $CourseId  = $_POST['course_id'];
 $type      = $_POST['type'];
 $bu_id     = $_POST['bu_id'];
 $response  = array();
 $siteAdmin = checkisSiteAdmin($userId);

 $user           = $DB->get_record('user', array("id" => $userId));
 $BU             = getBuByUid($userId);
 $user->cm_bu_id = $BU->id;

 if ($siteAdmin) {
  $q1     = "SELECT c.id,c.fullname,c.shortname FROM {course} c where c.visible = 1 and c.category !=0 and c.id = $CourseId";
  $course = $DB->get_record_sql($q1);
 } else {
  $q1     = "SELECT c.id,c.fullname,c.shortname FROM {course} c where c.visible = 1 and c.category !=0 and c.id = $CourseId";
  $course = $DB->get_record_sql($q1);
 }
 $row = 0;

 $newData = new stdClass();

 if ($type == 'enrolled') {
  $userids_arr = get_enrolled_uids($course->id, $user);
 } else if ($type == 'completed') {
  $userids_arr = get_uids_progress($course->id, $user, 100);
 } else if ($type == 'inprogress') {
  $userids_arr = get_uids_progress($course->id, $user, 50);
 } else if ($type == 'notstarted') {
  $userids_arr = get_uids_progress($course->id, $user, 0);
 }

 $i = 0;
 foreach ($userids_arr as $res) {
  $sql                     = "SELECT id,concat(firstname,' ',lastname) as fullname,username FROM {$CFG->prefix}user where deleted = 0 and username not in('guest','admin') and id=$res->id";
  $user_res                = $DB->get_record_sql($sql);
  $new_data                = new stdClass();
  $new_data->sl_no         = ++$i;
  $new_data->user_name     = $user_res->username;
  $new_data->user_fullname = $user_res->fullname;
  $new_data->user_id       = $user_res->id;
  $BU                      = getBuByUid($user_res->id);
  $new_data->bu_name       = $BU->bu_name;
  if ($bu_id == $BU->id) {
   $response[] = $new_data;
  } else if ($bu_id < 0) {
   $response[] = $new_data;
  }
 }
 $arrResults['Data']['Course']       = $course;
 $arrResults['Data']['Participants'] = $response;

 return $arrResults;
}

function get_uids_progress($cid, $u, $progress_rate)
{

 global $CFG, $DB;

 $enusers = get_enrolled_uids($cid, $u);

 $userids_arr = array();

 foreach ($enusers as $user) {

  $totals = $DB->get_records_sql("select * from {course_modules} where course = $cid and deletioninprogress = 0 and module != 9 ");
  $total  = count($totals);

  if (($_POST['edate'] != 'null') && (!empty($_POST['edate'])) && ($progress_rate === 100)) {
   $stimestamp   = makeTimestamp($_POST['sdate']);
   $etimestamp   = makeEndTimestamp($_POST['edate']);
   $append_query = "and a.timemodified between $stimestamp and $etimestamp";
  } else {
   $append_query = '';
  }

  $attempt_q = "select a.id,a.timemodified from {course_modules_completion} as a
      join {course_modules} as b on a.coursemoduleid = b.id
      where a.userid = $user->id and b.course = $cid and b.module != 9 and completionstate >= 1 $append_query";

  $attempt = $DB->get_records_sql($attempt_q);

  $attempted = count($attempt);

  $value        = $attempted / $total * 100;
  $comptnmethod = $DB->get_record('course_completion_aggr_methd', array('criteriatype' => 4, 'course' => $cid));
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

  if ($progress_rate == 100) {
   if ($compteprogress == 100) {
    $sqll           = $DB->get_record_sql("select id  from {course} where id= $cid ");
    $comptecourse[] = $sqll->id;
    $userids_arr[]  = $user;
   }
  }

  if ($progress_rate == 50) {
   $courselastaccess = $DB->get_record_sql("select count(id) as cout from {user_lastaccess}  where courseid = $cid  and userid = $user->id ");
   if (($courselastaccess->cout >= 1) && ($compteprogress != 100)) {
    $sqll             = $DB->get_record_sql("select *  from {course} where id= $cid");
    $myinprogcourse[] = $sqll;
    $userids_arr[]    = $user;
   }
  }

  if ($progress_rate == 0) {
   $courselastaccess = $DB->get_record_sql("select count(id) as cout from {user_lastaccess}  where courseid = $cid  and userid = $user->id ");
   if (empty($courselastaccess->cout)) {
    $sqll               = $DB->get_record_sql("select *  from {course} where id= $cid ");
    $mynonstartcourse[] = $sqll;
    $userids_arr[]      = $user;
   }
  }
 }
 // $progress_cnt = empty($progress_cnt) ? 0 : $progress_cnt;
 return $userids_arr;
}

function get_cids_progress($userId, $u, $progress_rate)
{
 global $CFG, $DB;

 $courseids_arr = get_enrolled_cids($userId, $u);

 $result_cids_arr = array();

 foreach ($courseids_arr as $course) {

  $totals = $DB->get_records_sql("select * from {course_modules} where course = $course->id and deletioninprogress = 0 and module != 9 ");
  $total  = count($totals);

  $attempt = $DB->get_records_sql("select a.id  from {course_modules_completion} as a
         join {course_modules} as b on a.coursemoduleid = b.id
         where a.userid = $userId and b.course = $course->id and b.module != 9 and completionstate >= 1");

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

  if ($progress_rate == 100) {
   if ($compteprogress == 100) {
    $sqll              = $DB->get_record_sql("select id  from {course} where id= $course->id ");
    $comptecourse[]    = $sqll->id;
    $result_cids_arr[] = $course;
   }
  }

  if ($progress_rate == 50) {
   $courselastaccess = $DB->get_record_sql("select count(id) as cout from {user_lastaccess}  where courseid = $course->id  and userid = $userId ");
   if (($courselastaccess->cout >= 1) && ($compteprogress != 100)) {
    $sqll              = $DB->get_record_sql("select *  from {course} where id= $course->id");
    $myinprogcourse[]  = $sqll;
    $result_cids_arr[] = $course;
   }
  }

  if ($progress_rate == 0) {
   $courselastaccess = $DB->get_record_sql("select count(id) as cout from {user_lastaccess}  where courseid = $course->id  and userid = $userId ");
   if (empty($courselastaccess->cout)) {
    $sqll               = $DB->get_record_sql("select *  from {course} where id= $course->id ");
    $mynonstartcourse[] = $sqll;
    $result_cids_arr[]  = $course;
   }
  }
 }
 return $result_cids_arr;
}

function generate_get_user_token($arrInput)
{
 return validateLogin($arrInput);
}

function userCourseReport()
{
 global $DB, $CFG;
 $userId    = $_POST['userId'];
 $buId      = $_POST['bu_id'];
 $response  = array();
 $siteAdmin = checkisSiteAdmin($userId);

 $user           = $DB->get_record('user', array("id" => $userId));
 $BU             = getBuByUid($user->id);
 $user->cm_bu_id = $BU->id;

 $q1             = "select value from {$CFG->prefix}config where name='siteadmins'";
 $siteadmins_rec = $DB->get_record_sql($q1);
 $siteadmins     = $siteadmins_rec->value;
 $q              = "select u.id,u.firstname,u.lastname,u.email, u.timecreated,u.suspended,u.username,u.lastaccess
                 from {$CFG->prefix}user u where u.deleted = 0 and u.id > 1 and
                 u.id NOT IN($siteadmins)";

 $res = $DB->get_records_sql($q);

 foreach ($res as $rec) {
  $data                 = new stdClass;
  $data->userid         = $rec->id;
  $data->username       = $rec->username;
  $data->firstname      = $rec->firstname;
  $data->lastname       = $rec->lastname;
  $BU                   = getBuByUid($rec->id);
  $data->buName         = $BU->bu_name;
  $data->buId           = $BU->id;
  $filtered_users_arr[] = $data;
 }

 $row = 0;
 foreach ($filtered_users_arr as $rec) {
  $newData                 = new stdClass();
  $newData->sno            = ++$row;
  $newData->user_id        = $rec->userid;
  $newData->user_name      = $rec->username;
  $newData->user_fullname  = $rec->firstname . ' ' . $rec->lastname;
  $newData->bu_name        = $rec->buName;
  $newData->enrolled_cnt   = get_enrolled_courses($rec->userid, $user);
  $newData->completed_cnt  = get_progress_by_uid($rec->userid, $user, 100);
  $newData->inprogress_cnt = get_progress_by_uid($rec->userid, $user, 50);
  $newData->notstarted_cnt = get_progress_by_uid($rec->userid, $user, 0);
  if ($buId == $rec->buId) {
   $response[] = $newData;
  } else if ($buId < 0) {
   $response[] = $newData;
  }
 }
 $arrResults['Data'] = $response;

 return $arrResults;
}

function userDetailedReport()
{
 global $DB, $CFG;

 $type     = $_POST['type'];
 $response = array();
 if (!empty($_POST['userId']) && !empty($_POST['user_id'])) {

  //getting param user details we need
  $uId                 = $_POST['userId'];
  $user_rec            = $DB->get_record('user', array("id" => $uId));
  $paramUser           = new stdClass();
  $paramUser->id       = $uId;
  $paramUser->fullname = $user_rec->firstname . ' ' . $user_rec->lastname;
  //getting logged in user details
  $loggedInUserId = $_POST['user_id'];
  $siteAdmin      = checkisSiteAdmin($loggedInUserId);
  $user           = $DB->get_record('user', array("id" => $loggedInUserId));
  $BU             = getBuByUid($user->id);
  $user->cm_bu_id = $BU->id;

  $row = 0;

  $newData       = new stdClass();
  $courseids_arr = array();

  if ($type == 'enrolled') {
   $courseids_arr = get_enrolled_cids($uId, $user);
  } else if ($type == 'completed') {
   $courseids_arr = get_cids_progress($uId, $user, 100);
  } else if ($type == 'inprogress') {
   $courseids_arr = get_cids_progress($uId, $user, 50);
  } else if ($type == 'notstarted') {
   $courseids_arr = get_cids_progress($uId, $user, 0);
  } else if ($type == 'all') {
   $completedcrs  = get_cids_progress($uId, $user, 100);
   $inprogresscrs = get_cids_progress($uId, $user, 50);
   $notstartedcrs = get_cids_progress($uId, $user, 0);
   foreach ($completedcrs as $c) {
    $comp_arr[]      = $c->id;
    $courseids_arr[] = $c;
   }
   foreach ($inprogresscrs as $c) {
    $inp_arr[]       = $c->id;
    $courseids_arr[] = $c;
   }
   foreach ($notstartedcrs as $c) {
    $nots_arr[]      = $c->id;
    $courseids_arr[] = $c;
   }
  }

  $i = 0;
  foreach ($courseids_arr as $course) {
   $course                = $DB->get_record('course', array("id" => $course->id));
   $new_data              = new stdClass();
   $new_data->sl_no       = ++$i;
   $new_data->course_name = $course->fullname;
   $new_data->enrolled_on = get_activity_timestamp($paramUser->id, $course->id, 'timestart');
   $new_data->last_access = get_activity_timestamp($paramUser->id, $course->id, 'last_access');
   $ext1                  = in_array($course->id, $comp_arr);
   $ext2                  = in_array($course->id, $inp_arr);
   $ext3                  = in_array($course->id, $nots_arr);
   if ($ext1) {
    $new_data->status = 'Completed';
   } else if ($ext2) {
    $new_data->status = 'In Progress';
   } else if ($ext3) {
    $new_data->status = 'Not Started';
   } else {
    $new_data->status = '';
   }

   $response[] = $new_data;
  }
  $arrResults['Data']['User']    = $paramUser;
  $arrResults['Data']['Courses'] = $response;

  return $arrResults;
 }
}

function getMyEnrolledCourses($arrInput)
{
 global $CFG, $DB;

 $vUserId = $arrInput["userid"];
 $token   = $arrInput["user_key"];

 $objCategories = $DB->get_records('course_categories');

 $wsfunction = $_POST['wsfunction'];
 $wstoken    = $_POST['wstoken'];

 //get total course count
 $vTotalCoursesCount = $DB->count_records_sql("SELECT count(*) as total_courses FROM `mdl_course` WHERE id > 1 and visible = 1");

 $params             = array('userid' => $vUserId);
 $server_url         = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";
 $curl               = new curl;
 $restformat         = ($restformat == 'json') ? '&moodlewsrestformat=' . $restformat : '';
 $resp               = $curl->post($server_url . $restformat, $params);
 $arrEnrolledCourses = json_decode($resp, true);

 /*echo '<pre>';
 print_r($arrEnrolledCourses);
 echo '</pre>';*/

 foreach ($arrEnrolledCourses as $course) {
  $arrDummyResults[$course["id"]]['id']           = $course["id"];
  $arrDummyResults[$course["id"]]['fullname']     = $course["fullname"];
  $arrDummyResults[$course["id"]]['categoryname'] = $objCategories[$course["category"]]->name;
  $arrDummyResults[$course["id"]]['category']     = $course["category"];

  if ($course["progress"] == '') {
   $vProgress = 0;
  } else {
   $vProgress = $course["progress"];
  }
  $arrDummyResults[$course["id"]]['progress']      = $vProgress;
  $arrDummyResults[$course["id"]]['overviewfiles'] = $course["overviewfiles"];
  $arrDummyResults[$course["id"]]['credits']       = get_course_points($course["id"]);

  $count++;
 }

 rsort($arrDummyResults);

 $count = 0;
 foreach ($arrDummyResults as $course) {
  $arrResults['Data'][$count]['id']                 = $course["id"];
  $arrResults['Data'][$count]['fullname']           = $course["fullname"];
  $arrResults['Data'][$count]['category']           = $course["category"];
  $arrResults['Data'][$count]['categoryname']       = $course["categoryname"];
  $arrResults['Data'][$count]['progress']           = $course["progress"];
  $arrResults['Data'][$count]['overviewfiles']      = $course["overviewfiles"];
  $arrResults['Data'][$count]['credits']            = $course["credits"];
  $arrResults['Data'][$count]['status']             = 1;
  $arrResults['Data'][$count]['total_course_count'] = $vTotalCoursesCount;

  $count++;
 }

 return $arrResults;
}

function get_activity_timestamp($uid, $cid, $class)
{
 global $DB;
 if ($class == 'timestart') {
  $q1 = "select ue.timemodified from {enrol} e join {user_enrolments} ue on e.id = ue.enrolid join
        {user} u on u.id = ue.userid where u.id = $uid and e.courseid=$cid";
  $timestamp = $DB->get_record_sql($q1);
  $res       = (!empty($timestamp->timemodified)) ? date('d-m-Y', $timestamp->timemodified) : '-';
 }
 if ($class == 'last_access') {
  $q1        = "select timeaccess from {user_lastaccess} where userid=$uid and courseid=$cid";
  $timestamp = $DB->get_record_sql($q1);
  $res       = (!empty($timestamp->timeaccess)) ? date('d-m-Y', $timestamp->timeaccess) : '-';
 }
 return $res;
}

function getUserDetail()
{
 global $DB, $CFG;
 $response = new stdClass();
 if (isset($_POST['wsfunction'])) {

  $wsfunction = $_POST['wsfunction'];
  $wstoken    = $_POST['wstoken'];
  $user_id    = $_POST['user_id'];

  $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

  $params = array(
   'field'     => 'id',
   'values[0]' => $user_id,
  );

  $curl          = new curl();
  $curl_response = $curl->post($server_url, $params);
  if (!empty($curl_response)) {
   $json_arr         = json_decode($curl_response, true);
   $response->status = 1;
   $response->info   = $json_arr;
  }
  $arrResults['Data'] = $response;
 }
 return $arrResults;

}

function saveReview()
{

 global $DB, $CFG;

 $rating = $_POST['rating'];

 if (!empty($rating)) {
  $courseid                = $_POST['cid'];
  $newReview               = new stdClass();
  $newReview->courseid     = $courseid;
  $newReview->userid       = $_POST['userid'];
  $newReview->ratingnumber = $rating;
  $newReview->title        = $_POST['review_headline'];
  $newReview->comments     = $_POST['reviewDesc'];
  $newReview->created      = time();

  $res = $DB->insert_record('course_rating', $newReview);
 }
 $arrResults['Data'] = $res;

 return $arrResults;
}

function getReviewsbyCid()
{
 global $DB, $CFG;
 $response         = array();
 $submitedUsersarr = array();

 $cid           = $_POST['cid'];
 $userid        = $_POST['userid'];
 $courseratings = $DB->get_records_sql("select * from {course_rating} where courseid = $cid order by id desc ");

 if (!empty($courseratings)) {
  $arrResults['Data']['submissions'] = 1;
 } else {
  $arrResults['Data']['submissions'] = 0;
 }

 foreach ($courseratings as $courserating) {
  $Review               = new stdClass();
  $user                 = $DB->get_record('user', array('id' => $courserating->userid));
  $submitedUsersarr[]   = $courserating->userid;
  $Review->username     = $user->firstname . ' ' . $user->lastname;
  $Review->ratingnumber = $courserating->ratingnumber;
  $Review->title        = $courserating->title;
  $Review->comments     = $courserating->comments;
  $response[]           = $Review;
 }

 $arrResults['Data']['response'] = $response;

 if (in_array($userid, $submitedUsersarr)) {
  $arrResults['Data']['submitted'] = 1;
 } else {
  $arrResults['Data']['submitted'] = 0;
 }

 return $arrResults;
}

function getMyRating()
{
 global $DB, $CFG;
 $userid        = $_POST['userid'];
 $cid           = $_POST['cid'];
 $q             = "select * from {course_rating} where courseid = $cid and userid = $userid  order by id desc";
 $courseratings = $DB->get_record_sql($q);

 if (!empty($courseratings)) {
  $arrResults['Data']['ratestr'] = "Rating $courseratings->ratingnumber out of 5";
  $arrResults['Data']['rating']  = $courseratings->ratingnumber;
 }
 return $arrResults;
}

function getPointsReport()
{

 global $DB, $CFG;
 $response = array();

 $q   = "select c.id as userid,c.firstname,c.lastname,c.email from {$CFG->prefix}user c where c.deleted=0 and c.suspended=0 and c.id > 2  GROUP BY c.id ";
 $res = $DB->get_records_sql($q);

 $i = 1;
 foreach ($res as $rec) {
  $new_data            = new stdClass();
  $new_data->slno      = $i;
  $new_data->userid    = $rec->userid;
  $new_data->full_name = $rec->firstname . ' ' . $rec->lastname;
  $new_data->email_id  = $rec->email;
  $points              = "select sum(b.points) AS points from {$CFG->prefix}cm_user_points b where b.userid = $rec->userid ";
  $obtained            = $DB->get_record_sql($points);
  $new_data->points    = $obtained->points;
  $response[]          = $new_data;
  ++$i;
 }
 $arrResults['Data'] = $response;

 return $arrResults;
}

function getFullnameById($id)
{
 global $DB, $CFG;
 $fullname = '';

 $objUser = $DB->get_record('user', array("id" => $id));

 if ($objUser) {
  $fullname = $objUser->firstname . ' ' . $objUser->lastname;
 }
 return $fullname;
}

function getPointsDetailReport($arrInput)
{
 global $CFG, $DB;

 $vUserId  = $arrInput["userId"];
 $response = array();

 $objPoints = $DB->get_records_sql("SELECT * FROM {cm_user_points} WHERE userid = $vUserId");

 $netPointsObj = $DB->get_record_sql("select sum(b.points) AS points from {$CFG->prefix}cm_user_points b where b.userid = $vUserId");

 $x = 1;
 foreach ($objPoints as $val) {
  $ptype = '';
  $aname = '';

  if (($val->point_type == 1) && (!empty($val->point_refid))) {
   $ptype      = "Course";
   $coursename = $DB->get_record_sql("select fullname from {course} where id= $val->point_refid ");
   $aname      = ucfirst($coursename->fullname);
  } else if (($val->point_type == 2) && (!empty($val->point_refid))) {
   $ptype   = "Learning Plan";
   $lpnames = $DB->get_record_sql("select lpname from {cm_admin_learning_path} where id= $val->point_refid ");
   $aname   = ucfirst($lpnames->lpname);
  }

  if ($aname) {
   $new_data                     = new stdClass();
   $new_data->slno               = $x;
   $new_data->ptype              = $ptype;
   $new_data->aname              = $aname;
   $new_data->points             = $val->points;
   $response[]                   = $new_data;
   $arrResults['Data']['exists'] = 1;
   ++$x;
  }
 }
 $arrResults['Data']['courseAlike'] = $response;
 $arrResults['Data']['totalPoints'] = $netPointsObj->points;
 $arrResults['Data']['fullname']    = getFullnameById($vUserId);
 return $arrResults;
}

function getLeaderboardPoints()
{
 global $DB, $CFG;
 $userid   = $_POST['userid'];
 $response = array();

 $user           = $DB->get_record('user', array("id" => $userid));
 $BU             = getBuByUid($userid);
 $user->cm_bu_id = $BU->id;

 $assigned_userids_arr = getBUAssignedUsers($BU->id);

 $userids = implode(',', $assigned_userids_arr);

 $siteAdmin = checkisSiteAdmin($vUsernameExits->id);

 if ($siteAdmin) {
  $topupoints = $DB->get_records_sql("SELECT id,userid,sum(points) as points FROM {$CFG->prefix}cm_user_points where group by userid order by points DESC limit 0,6");
 } else {
  $topupoints = $DB->get_records_sql("SELECT id,userid,sum(points) as points FROM {$CFG->prefix}cm_user_points where userid in ($userids) group by userid order by points DESC limit 0,6");
 }

 if (empty($topupoints)) {
  $arrResults['Data']['empty'] = true;
 }

 foreach ($topupoints as $prec) {
  $objUser                     = $DB->get_record('user', array("id" => $prec->userid));
  $new_data                    = new stdClass();
  $new_data->firstname         = $objUser->firstname;
  $usercontext                 = context_user::instance($prec->userid, IGNORE_MISSING);
  $new_data->profilepic        = $CFG->wwwroot . '/pluginfile.php' . '/' . $usercontext->id . '/user/icon/f1';
  $new_data->points            = $prec->points;
  $response[]                  = $new_data;
  $arrResults['Data']['empty'] = false;
 }
 $arrResults['Data']['userpoints'] = $response;
 return $arrResults;
}

function getBadges()
{
 global $DB, $CFG;
 $userid   = $_POST['userid'];
 $response = array();
 require_once $CFG->libdir . '/badgeslib.php';

 $q = "SELECT c.id FROM {course} c JOIN {enrol} en ON en.courseid = c.id
    JOIN {user_enrolments} ue ON ue.enrolid = en.id WHERE ue.userid = $userid";

 $enrolled_c = $DB->get_records_sql($q);

 foreach ($enrolled_c as $course) {
  $badges_res = badges_get_badges(2, $course->id, '', '', 0, 0, $userid);
  foreach ($badges_res as $badges) {
   $badges_arr[] = $badges;
  }
 }

 if (empty($badges_arr)) {
  $arrResults['Data']['empty'] = true;
 }

 foreach ($badges_arr as $prec) {
  $new_data                    = new stdClass();
  $new_data->name              = $prec->name;
  $new_data->courseid          = $prec->courseid;
  $context                     = context_course::instance($prec->courseid);
  $new_data->badgeimg          = $CFG->wwwroot . '/pluginfile.php' . '/' . $context->id . '/badges/badgeimage/' . $prec->id . '/f1';
  $response[]                  = $new_data;
  $arrResults['Data']['empty'] = false;
 }
 $arrResults['Data']['badges'] = $response;
 return $arrResults;
}

function getCerts()
{
 global $DB, $CFG;
 $userid   = $_POST['userid'];
 $response = array();
 require_once $CFG->libdir . '/badgeslib.php';

 $q = "SELECT cert.id as certid,cert.name as certname,(SELECT c.fullname as coursename FROM {course} c WHERE c.id = course)
 AS fullname,course as cid FROM {customcert} as cert WHERE id IN (SELECT customcertid from {customcert_issues} WHERE userid=$userid
  order by timecreated DESC)";

 $objMycert = $DB->get_records_sql($q);

 if (empty($objMycert)) {
  $arrResults['Data']['empty'] = true;
 }

 foreach ($objMycert as $cert) {

  $q2                          = "SELECT cm.id as cmodid  FROM {course_modules} as cm join {customcert} as cc on cm.instance=cc.id WHERE cm.module = 25 and cc.course =  $cert->cid";
  $course_module               = $DB->get_record_sql($q2);
  $new_data                    = new stdClass();
  $new_data->name              = $cert->certname;
  $new_data->cid               = $cert->cid;
  $new_data->cmodid            = $course_module->cmodid;
  $new_data->certimg           = $CFG->wwwroot . '/cm/image/courseera.png';
  $response[]                  = $new_data;
  $arrResults['Data']['empty'] = false;
 }
 $arrResults['Data']['certs'] = $response;
 return $arrResults;
}

function getBUadminDashStats()
{
 global $DB, $CFG;

 $user_id    = $_POST["userid"];
 $wsfunction = $_POST['wsfunction'];
 $wstoken    = $_POST["wstoken"];

 if (isset($_POST['buId']) && ($_POST['buId'] != 'null')) {
  $buId         = $_POST['buId'];
  $append_query = "WHERE buid = $buId";
 }

 if (!empty($wstoken)) {

  $q1                       = "SELECT count(Distinct(bu_courseid)) as coursecnt FROM {$CFG->prefix}cm_bu_course where bu_id= $buId ";
  $res                      = $DB->get_record_sql($q1);
  $q2                       = "SELECT count(id) as usercnt FROM {$CFG->prefix}user where id in (select Distinct(userid) from {$CFG->prefix}cm_bu_assignment where bu_id=$buId) and deleted=0 and suspended=0";
  $rec                      = $DB->get_record_sql($q2);
  $new_data->bu_courses_cnt = $res->coursecnt;
  $new_data->bu_users_cnt   = $rec->usercnt;

  $q3  = "SELECT count(id) as lpscount FROM {$CFG->prefix}cm_admin_learning_path $append_query";
  $lps = $DB->get_record_sql($q3);

  $arrResults['Data']['usersCount']   = $new_data->bu_users_cnt;
  $arrResults['Data']['coursesCount'] = $new_data->bu_courses_cnt;
  $arrResults['Data']['lpsCount']     = $lps->lpscount;

  // start of user enrolled_courses fetching
  $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

  $params = array('userid' => $user_id);

  $curl      = new curl;
  $resp      = $curl->post($server_url, $params);
  $arrOutput = json_decode($resp, true);

  $enrolledCount = 0;

  foreach ($arrOutput as $course) {
   $enrolledCount++;
  }
  $arrResults['Data']['enCoursesCnt'] = $enrolledCount;
 }
 return $arrResults;
}

function getBuCourses($buId)
{
 global $DB, $CFG;
 $courseids_arr = array();
 $q             = "SELECT DISTINCT(bu_courseid)as courseid FROM {cm_bu_course} where bu_id = $buId";
 $courses       = $DB->get_records_sql($q);
 foreach ($courses as $rec) {
  $courseids_arr[] = $rec->courseid;
 }
 $courseids_in = implode(',', $courseids_arr);
 return $courseids_in;
}

function DeleteLP()
{
 global $DB, $CFG;
 $lp_id = $_POST['lp_id'];

 if (!empty($lp_id)) {
  $DB->delete_records('cm_admin_learning_path', array("id" => $lp_id));
  $DB->delete_records('cm_lp_assignment', array("lp_id" => $lp_id));
  $DB->delete_records('cm_lp_completion_stauts', array("lp_id" => $lp_id));
  $DB->delete_records('cm_lp_course', array("lp_id" => $lp_id));
  $arrResults['Data'] = 1;
 }
 return $arrResults;
}

function update_enrollment_type($courseid, $enrollment_type)
{
 global $DB, $CFG;
 $status = 0;

 $q                  = "SELECT * FROM {enrol} WHERE courseid = $courseid";
 $enrollment_methods = $DB->get_records_sql($q);
 $update_q           = "UPDATE {$CFG->prefix}enrol SET status = ? WHERE id =? and enrol= ?";
 foreach ($enrollment_methods as $enrol) {
  if ($enrollment_type == 'self') {
   $status = $DB->execute($update_q, [0, $enrol->id, 'self']);
  } else if ($enrollment_type == 'manual') {
   $status = $DB->execute($update_q, [1, $enrol->id, 'self']);
  }
 }
 return $status;
}

function get_enrollment_type($courseid)
{
 global $DB, $CFG;
 $enrollments_arr    = array();
 $enrollment_type    =
 $q                  = "SELECT * FROM {enrol} WHERE courseid = $courseid and status = 0 order by id DESC";
 $enrollment_methods = $DB->get_records_sql($q);

 foreach ($enrollment_methods as $enrol) {
  $enrollments_arr[] = $enrol->enrol;
 }
 $enrollment_type = in_array('self', $enrollments_arr) ? 'self' : 'manual';
 return $enrollment_type;
}

function get_course_points($course_id)
{
 global $DB;
 $q           = "Select points from {course} where id =$course_id";
 $res         = $DB->get_record_sql($q);
 $res->points = ($res->points > 0) ? ($res->points) : 0;
 return $res->points;
}

function addUserPoints($arrInput)
{
 global $DB, $CFG;

 $vUserId     = $arrInput['user_to_post'];
 $vPoints     = $arrInput['points'];
 $point_refid = $arrInput['point_refid'];
 $point_type  = $arrInput['point_type'];

 $condition['userid'] = $vUserId;
 $objCount            = $DB->get_record('cm_user_points', $condition);

 $objRecord         = new stdClass();
 $objRecord->points = $vPoints;
 if ($objCount->userid != '') {
  $objRecord->id           = $objCount->id;
  $objRecord->points       = $vPoints;
  $objRecord->timemodified = time();
  $objRecord->modifiedby   = $vUserId;
  $objRecord->point_refid  = $point_refid;
  $objRecord->point_type   = $point_type;
  $DB->update_record('cm_user_points', $objRecord);
 } else {
  $objRecord->userid       = $vUserId;
  $objRecord->points       = $vPoints;
  $objRecord->timemodified = time();
  $objRecord->modifiedby   = $vUserId;
  $objRecord->timecreated  = time();
  $objRecord->createdby    = $vUserId;
  $objRecord->point_refid  = $point_refid;
  $objRecord->point_type   = $point_type;
  $DB->insert_record('cm_user_points', $objRecord);
 }
 $arrResults['Data'] = 'Success';
 return $arrResults;
}

function EnrollBUCoursetoUsers($params)
{
 global $DB, $CFG;
 $lastenrolledid        = '';
 $assigned_userids_arr  = getBUAssignedUsers($params->bu_id);
 $bu_managers_arr       = getBUManagers($params->bu_id);
 $enrolled_learners_arr = array_diff($assigned_userids_arr, $bu_managers_arr);
 foreach ($enrolled_learners_arr as $learnerid) {
  $_POST['course_id']  = $params->course_id;
  $_POST['user_id']    = $learnerid;
  $_POST['role_id']    = 5;
  $_POST['wsfunction'] = 'enrol_manual_enrol_users';
  $_POST['wstoken']    = $params->wstoken;
  $lastenrolledid      = enrollUserToCourse($_POST);
 }
 return $lastenrolledid;
}

function addBulkUsers()
{
 global $CFG;
 $response = array();
 if (isset($_POST["file_input"])) {

  $file_name    = $_POST['file_name'];
  $encoded_file = $_POST['file_input'];
  $user_id      = $_POST['user_id'];
  $wsfunction   = $_POST['wsfunction'];
  $wstoken      = $_POST['wstoken'];

  //$user_id = 44;

  $filepath = csv_file_upload($encoded_file, $file_name, $user_id);

  //$filepath = "./uploads/u_44/csv_bulk_user/sample4.csv";

  $ins_arr = csv_to_array($filepath);

  if (empty($ins_arr)) {
   $error_arr['error'] = 'Field Rows cannot be empty';
   $response[]         = $error_arr;
  }

  $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

  foreach ($ins_arr as $key => $value) {

   $is_valid = true;
   $data     = new stdClass();

   if (isset($value['username'])) {
    $username = $value['username'];
    $res_arr  = username_validator($username);
    foreach ($res_arr as $key => $res) {
     $data->$key = $res;
    }
   }
   if (isset($value['password'])) {
    $password = $value['password'];
    $res_arr  = password_validator($password);
    foreach ($res_arr as $key => $res) {
     $data->$key = $res;
    }
   }
   if (isset($value['email'])) {
    $email   = $value['email'];
    $res_arr = usermail_validator($email);
    foreach ($res_arr as $key => $res) {
     $data->$key = $res;
    }
   }

   if (isset($data->error)) {
    $is_valid   = false;
    $response[] = $data;
   }

   if ($is_valid == true) {
    $res_arr = bulk_user_insert($value, $server_url, $user_id);
    foreach ($res_arr as $key => $res) {
     $data->$key = $res;
    }
    $response[] = $data;
   }
  }
  $arrResults['Data'] = $response;
 }
 return $arrResults;
}

function bulk_user_insert($array, $serverurl, $creatorId)
{
 global $DB;
 $mainusername = trim(strtolower($array['username']), ' ');
 $password     = trim($array['password'], ' ');
 $email        = trim(strtolower($array['email']), ' ');
 $firstname    = trim(strtolower($array['fname']), ' ');
 $lastname     = trim(strtolower($array['surname']), ' ');
 $city_town    = trim(strtolower($array['city_town']), ' ');
 $buId         = trim(strtolower($array['bu_id']), ' ');
 $country      = trim($array['country'], ' ');

 $required_arr = array();

 $compulsary_fields = array('username' => $mainusername, 'password' => $password, 'email' => $email, 'firstname' => $firstname, 'lastname' => $lastname, 'BuId' => $buId, 'city' => $city_town, 'country' => $country);

 foreach ($compulsary_fields as $key => $values) {
  if (empty($values)) {
   $required_arr['error'] = 'Parameter : ' . $key . ' -  is missing';
   return $required_arr;
  }
 }
 // to remove the BU ID from compulsary fields as it is not in user table
 array_splice($compulsary_fields, 5, 1);
 $params = array('users' => array($compulsary_fields));

 $curl = new curl;

 $resp = $curl->post($serverurl, $params);

 $result     = json_decode($resp, true);
 $vNewUserId = $result[0][id];

 if ($vNewUserId) {
  if ($DB->get_record('cm_business_units', array('id' => $buId))) {
   BUuserEntry($buId, $vNewUserId);
  }
  $arr['status'] = 'ok';
 }

 return $arr;
}

function csv_file_upload($fileString, $fileName, $user_id)
{
 if (!empty($fileString)) {
  $time = null;
  // $time = time();
  list($type, $data) = explode(';', $fileString);
  list(, $data)      = explode(',', $data);
  if (!file_exists("uploads/u_$user_id")) {
   mkdir("uploads/u_$user_id", 0777, true);
  }
  if (!file_exists("uploads/u_$user_id/csv_bulk_user")) {
   mkdir("uploads/u_$user_id/csv_bulk_user", 0777, true);
  }
  $data = base64_decode($data);
  file_put_contents("uploads/u_$user_id/csv_bulk_user/" . $fileName, $data);
 }
 $filepath = "uploads/u_$user_id/csv_bulk_user/" . $fileName;
 return $filepath;
}

function csv_to_array($filename = '', $delimiter = ',')
{
 if (!file_exists($filename) || !is_readable($filename)) {
  return false;
 }

 $header = null;
 $data   = array();
 if (($handle = fopen($filename, 'r')) !== false) {
  while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
   if (!$header) {
    $header = $row;
   } else {
    $data[] = array_combine($header, $row);
   }

  }
  fclose($handle);
 }
 return $data;
}

function password_validator($password)
{
 $error_arr = array();
// Validate password strength
 $uppercase    = preg_match('@[A-Z]@', $password);
 $lowercase    = preg_match('@[a-z]@', $password);
 $number       = preg_match('@[0-9]@', $password);
 $specialChars = preg_match('@[^\w]@', $password);

 if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
  $error_arr['error'] = 'Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.';
 }
 return $error_arr;
}

function username_validator($username)
{
 global $DB;
 $error_arr = array();
 $sql       = "SELECT username FROM {user} where  username = '$username'";
 $res       = $DB->get_records_sql($sql);
 if (!empty($res)) {
  foreach ($res as $rec) {
   $error_arr['error'] = 'Username: ' . $rec->username . ' -  already taken';
   return $error_arr;
  }
 }
 return $error_arr;
}

function usermail_validator($email)
{
 global $DB;
 $error_arr = array();
 $sql       = "SELECT email FROM {user} where email = '$email'";
 $res       = $DB->get_records_sql($sql);
 if (!empty($res)) {
  foreach ($res as $rec) {
   $error_arr['error'] = 'Email Id: ' . $rec->email . '  -  already taken';
   return $error_arr;
  }
 }
 return $error_arr;
}

function getAvailCourses()
{
 global $DB, $CFG;

 require_once $CFG->libdir . '/enrollib.php';

 $user_id = $_POST['user_id'];

 $courses = enrol_get_users_courses($user_id, false, null, null);

 foreach ($courses as $course) {
  $enrolled_arr[] = $course->id;
 }
 $enrolled_cids = implode(',', $enrolled_arr);

 $append_notin_query = empty($enrolled_cids) ? '' : "AND id NOT IN($enrolled_cids)";

 $q1                = "SELECT id,fullname,summary FROM {$CFG->prefix}course where id > 1 and visible=1 $append_notin_query";
 $remaining_courses = $DB->get_records_sql($q1);

 foreach ($remaining_courses as $rec) {
  $result           = new stdClass();
  $result->id       = $rec->id;
  $result->fullname = $rec->fullname;
  $result->desc     = $rec->summary;
  $canSelfEnroll    = canSelfEnroll($rec->id);
  if ($canSelfEnroll) {
   $response[] = $result;
  }

 }
 $arrResults['Data'] = $response;
 return $arrResults;
}

function canSelfEnroll($courseid)
{
 global $CFG;
 $flag = false;
 require_once $CFG->libdir . '/enrollib.php';

 $instances = enrol_get_instances($courseid, false);

 foreach ($instances as $instance) {
  if ($instance->enrol === 'self') {
   if ($instance->status === '0') {
    $flag = true;
   }
  }
 }
 return $flag;
}

function getCatalog()
{
 global $DB, $CFG;
 $categories = $DB->get_records('course_categories');
 foreach ($categories as $category) {
  $courses        = $DB->get_records('course', array('category' => $category->id, 'visible' => 1));
  $raw_course_arr = array();
  foreach ($courses as $course) {
   $raw_course              = new stdClass();
   $raw_course->course_id   = $course->id;
   $raw_course->course_name = $course->fullname;
   $raw_course_arr[]        = $raw_course;
  }
  $raw_category                = new stdClass();
  $raw_category->category_id   = $category->id;
  $raw_category->category_name = $category->name;
  $raw_category->course_cnt    = sizeof($raw_course_arr);
  $raw_category->courses       = $raw_course_arr;
  $response[]                  = $raw_category;
 }
 $arrResults['Data'] = $response;
 return $arrResults;
}

function makeDate($timestamp)
{
 $readable_date = date("d-M-Y", $timestamp);
 return $readable_date;
}

function makeTimestamp($dateString)
{
 $mdate     = str_replace('/', '-', $dateString);
 $timestamp = strtotime($mdate);
 return $timestamp;
}

function makeEndTimestamp($dateString)
{
 $mdate     = str_replace('/', '-', $dateString);
 $timestamp = strtotime($mdate);
 // added 23 hours extra as the date time will be xx-xx-xxx 00:00:00
 $timestamp = $timestamp + 23 * 3600;
 return $timestamp;
}
