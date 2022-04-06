<?php
date_default_timezone_set("Asia/Kolkata");
error_reporting(E_ALL);

function deliver_response($array)
{
    //header("HTTP/1.1 ".$array['status']);
    $response['Status'] = $array['status'];
    $response['ResponseMessage'] = $array['response_msg'];
    $response['Data'] = $array['data'];
    $response['ResponseCode'] = $array['response_code'];
    $response['methodName'] = $array['methodName'];
    $response['AdditionalData'] = $array['AdditionalData'];
    echo $json_response = json_encode($response);
}

function TestConnection($arrInput)
{
    global $fields_head, $wwwroot;
    $count = 0;
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

    // $vUsername = 'admin';
    // $vPassword = 'Learn@123';

    $query = "select id from mdl_user where username like BINARY '$vUsername'";
    $vUsernameExits = $DB->get_record_sql($query);

    if ($vUsernameExits->id == '') {

        $query = "select id from mdl_user where username like '$vUsername'";
        $vUsernameExits = $DB->get_record_sql($query);
    }

    if ($vUsernameExits->id != '') {

        $restformat = 'json';

        $params = array('username' => $vUsername, 'password' => $vPassword);

        $serverurl = $CFG->wwwroot . '/login/token.php?service=moodle_mobile_app';

        $curl = new curl;
        $restformat = ($restformat == 'json') ? '&moodlewsrestformat=' . $restformat : '';

        $resp = $curl->post($serverurl . $restformat, $params);

        $arrToken = json_decode($resp, true);

        if ($arrToken[token] != '') {

            //get userid from username
            $objUser = $DB->get_record('user', array("username" => $vUsername));

            //update token in user key auth plugin tables
            $vMoodleToken = $arrToken[token];
            $vUserId = $objUser->id;

            $objUserKey = $DB->get_record('user_private_key', array('userid' => $vUserId, 'script' => 'auth/userkey'));

            if ($objUserKey->id != '') {

                $DB->set_field('user_private_key', 'value', $vMoodleToken, array('userid' => $vUserId, 'script' => 'auth/userkey'));
            } else {

                $objUserAuth = new stdclass();
                $objUserAuth->script = 'auth/userkey';
                $objUserAuth->value = $vMoodleToken;
                $objUserAuth->userid = $vUserId;
                $objUserAuth->timecreated = time();

                $DB->insert_record('user_private_key', $objUserAuth);

            }

            //check whether the user have other than the student role
            $vInstructors = 0;
            $query = "SELECT id FROM mdl_role_assignments WHERE roleid = 3 and userid = $objUser->id group by roleid";
            $objInstructorsRoles = $DB->get_records_sql($query);
            foreach ($objInstructorsRoles as $role) {
                $vInstructors++;
            }

            //check whether the user have other than the student role
            $vHr = 0;
            $objHrRoles = $DB->get_records_sql("SELECT id FROM mdl_role_assignments WHERE roleid =11 and userid = $objUser->id group by roleid");
            //print_r($objHrRoles);
            //exit;
            foreach ($objHrRoles as $role) {
                $vHr++;
            }

            if ($vHr > 0) {
                $vRole = 'hr';
            } else if ($vInstructors > 0) {
                $vRole = 'instructor';
            } else {
                $vRole = 'learner';
            }

            $arrResults['Data']['result'] = 1;
            $arrResults['Data']['message'] = 'Success';
            $arrResults['Data']['token'] = $arrToken[token];
            $arrResults['Data']['userid'] = $objUser->id;
            $arrResults['Data']['email'] = $objUser->email;
            $arrResults['Data']['firstname'] = $objUser->firstname;
            $arrResults['Data']['lastname'] = $objUser->lastname;
            $arrResults['Data']['surname'] = $objUser->lastname;
            $arrResults['Data']['country'] = $objUser->country;
            $arrResults['Data']['city'] = $objUser->city;
            $arrResults['Data']['phone1'] = $objUser->phone1;
            $arrResults['Data']['role'] = $vRole;
        } else {
            $arrResults['Data']['result'] = 0;
            $arrResults['Data']['token'] = '';
            $arrResults['Data']['message'] = 'Username / Password is wrong, please enter the correct details';
        }

    } else {
        $arrResults['Data']['result'] = 0;
        $arrResults['Data']['token'] = '';
        $arrResults['Data']['message'] = 'Username / Password is wrong, please enter the correct details';
    }

    return $arrResults;
}

function getUserList()
{
    global $DB;

    if (isset($_POST["wstoken"])) {

        $q1 = "select value from mdl_config where name='siteadmins'";

        $siteadmins_rec = $DB->get_record_sql($q1);

        $siteadmins = $siteadmins_rec->value;

        $q = "select u.id,u.firstname,u.lastname,u.email, u.timecreated,u.suspended,u.username,u.lastaccess
             from mdl_user u where u.deleted = 0 and u.id > 1 and
             u.id NOT IN($siteadmins)";

        $res = $DB->get_records_sql($q);

        $arrReturn["ResponseMessage"] = 'Success';

        foreach ($res as $rec) {
            $data = new stdClass;
            $data->userid = $rec->id;
            $data->username = $rec->username;
            $data->firstname = $rec->firstname;
            $data->lastname = $rec->lastname;
            $data->createdon = (!empty($rec->timecreated)) ? date("d-M-Y", $rec->timecreated) : null;
            $data->lastaccess = (!empty($rec->lastaccess)) ? date("d-M-Y", $rec->lastaccess) : null;
            $data->suspended = $rec->suspended;
            $arrReturn['Data'][] = $data;
        }
    }
    return $arrReturn;
}

function already_taken_validator()
{
    global $DB;
    $response = new stdClass();
    $not_in_query_param = null;
    $second_query_param = null;

    if (!empty($_POST['edit_id'])) {
        $edit_id = $_POST['edit_id'];
        $not_in_query_param = "and id not in($edit_id)";
    }

    if (!empty($_POST['field_name2'])) {
        $field_name2 = $_POST['field_name2'];
        $field_value2 = $_POST['field_value2'];
        $second_query_param = "and $field_name2 in($field_value2)";
    }

    $table_name = $_POST['table_name'];
    $field_name = $_POST['field_name'];
    $field_value = $_POST['field_value'];

    $sql = "SELECT * FROM $table_name where $field_name = '$field_value' $second_query_param $not_in_query_param";
    $res = $DB->get_record_sql($sql);

    $response->exists = (!empty($res)) ? 1 : 0;

    $arrReturn["ResponseMessage"] = (!empty($res)) ? " $field_name already Taken" : "New data";

    if ((!empty($_POST['field_name2'])) && ($response->exists == 0)) {
        $field_value = $_POST['field_value'];
        $field_value = strtolower($field_value);
        $response->exists = ($field_value == 'new') ? 1 : 0;
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
        $password = trim($_POST['new_pwd'], ' ');
        $email = $_POST['email'];
        $fname = $_POST['first_name'];
        $surname = $_POST['surname'];
        $city_town = $_POST['location'];
        $country = $_POST['country'];
    }
    $wsfunction = $_POST['wsfunction'];
    $wstoken = $_POST['wstoken'];

    $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

    $new_user = new stdClass();
    $new_user->username = $mainusername;
    $new_user->password = $password;
    $new_user->firstname = $fname;
    $new_user->lastname = $surname;
    $new_user->email = $email;
    $new_user->auth = 'manual';
    $new_user->idnumber = '';
    $new_user->lang = 'en';
    $new_user->timezone = '';
    $new_user->mailformat = 0;
    $new_user->description = '';
    $new_user->city = $city_town;
    $new_user->country = $country;
    $preference = 'auth_forcepasswordchange';
    $new_user->preferences = array(
        array('type' => $preference, 'value' => 'true'),
    );
    $users = array($new_user);
    $params = array('users' => $users);

    $curl = new curl();
    $response = $curl->post($server_url, $params);

    $arrReturn["Data"] = $response;

    return $arrReturn;
}

function getUser()
{
    global $DB;
    $response = array();

    // $_POST["user_id"] = 5;

    if (isset($_POST["user_id"])) {

        $user_id = $_POST["user_id"];
        $sql = "select * from mdl_user where id = $user_id";
        $res1 = $DB->get_records_sql($sql);

        foreach ($res1 as $rec) {
            $data = new stdClass;
            $data->username = $rec->username;
            $data->firstname = $rec->firstname;
            $data->lastname = $rec->lastname;
            $data->email = $rec->email;
            $data->city = $rec->city;
            $data->country = $rec->country;
            $response[] = $data;
        }
        $arrResults['Data'] = $response;
    }
    return $arrResults;
}

function updateUser()
{
    global $CFG;
    $response = new stdClass();
    $user_id = $_POST["user_id"];
    $wsfunction = $_POST['wsfunction'];
    $wstoken = $_POST['wstoken'];

    $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

    $firstname = $_POST['first_name'];
    $lastname = $_POST['surname'];
    $city = $_POST['location'];
    $country = $_POST['country'];

    $username = array('id' => $user_id, 'firstname' => $firstname, 'lastname' => $lastname, 'city' => $city, 'country' => $country);

    $users[] = $user_id;

    $params = array('users' => array($username));

    $curl = new curl();
    $response->status = $curl->post($server_url, $params);

    $arrResults['Data'] = $response->status;

    return json_encode($response);
}

function deleteUser()
{

    global $DB, $CFG;
    $response = new stdClass();

    if (isset($_POST["user_id"])) {

        $user_id = $_POST["user_id"];
        $wsfunction = $_POST['wsfunction'];
        $wstoken = $_POST['wstoken'];

        $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

        $users[] = $user_id;

        $params = array('userids' => $users);

        $curl = new curl();
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

        $user_id = $_POST["user_id"];
        $mode = $_POST["mode"];
        $wsfunction = $_POST['wsfunction'];
        $wstoken = $_POST['wstoken'];

        $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

        $username = array('id' => $user_id, 'suspended' => $mode);

        $users[] = $user_id;

        $params = array('users' => array($username));

        $curl = new curl();
        $response->status = $curl->post($server_url, $params);

        $arrResults['Data'] = $response;

    }
    return $arrResults;
}

function listCategories()
{
    global $DB;
    $response = array();
    if (isset($_POST['userId'])) {
        $q = "SELECT * FROM {course_categories} where visible=1 and id > 1";
        $categories = $DB->get_records_sql($q);
        foreach ($categories as $rec) {
            $new_data = new stdClass();
            $new_data->category_name = $rec->name;
            $new_data->category_id = $rec->id;
            $new_data->category_courses_cnt = get_course_cnt_by_cat($rec->id);
            $response[] = $new_data;
        }
        $arrResults['Data'] = $response;
    }
    return $arrResults;
}

function get_course_cnt_by_cat($cat_id)
{
    global $DB;
    $cnt_cid = null;
    $sql = "SELECT count(id) as cnt_cid FROM {course} WHERE category='$cat_id'";
    $rec = $DB->get_record_sql($sql);
    $cnt_cid = $rec->cnt_cid;
    return $cnt_cid;
}

function createCategory()
{
    global $DB, $CFG;
    $response = new stdClass();
    if (isset($_POST['wsfunction'])) {
        $wsfunction = $_POST['wsfunction'];
        $wstoken = $_POST['wstoken'];
        $category_name = $_POST['category_name'];
        $description = $_POST['category_description'];
        $category_code = '';

        $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

        $params = array(
            'categories' => array(
                array(
                    'name' => $category_name,
                    'parent' => '0',
                    'idnumber' => $category_code,
                    'description' => $description,
                    'descriptionformat' => '1',
                ),
            ),
        );
        $curl = new curl();
        $curl_response = $curl->post($server_url, $params);
        if (!empty($curl_response)) {

            $json_arr = json_decode($curl_response, true);
            foreach ($json_arr as $rec) {
                $response->name = $rec['shortname'];
                $response->id = $rec['id'];
            }
            $response->result = (!empty($response->id)) ? 1 : null;
            $arrResults['Data'] = $response;
        }

    }
    return $arrResults;
}

function get_category_by_id()
{
    global $DB;

    $response = new stdClass();

    if (isset($_POST['cat_id'])) {
        $cat_id = $_POST['cat_id'];

        $sql = "select * from mdl_course_categories where id='$cat_id'";
        $rec = $DB->get_record_sql($sql);

        $response->name = $rec->name;
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
        $wsfunction = $_POST['wsfunction'];
        $wstoken = $_POST['wstoken'];
        $category_id = $_POST['cat_id'];
        $category_name = $_POST['category_name'];
        $description = $_POST['category_description'];
        $category_code = '';

        $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

        $params = array(
            'categories' => array(
                array(
                    'id' => $category_id,
                    'name' => $category_name,
                    'parent' => '0',
                    'idnumber' => $category_code,
                    'description' => $description,
                    'descriptionformat' => '1',
                ),
            ),
        );

        $curl = new curl();
        $curl_response = $curl->post($server_url, $params);
        if (!empty($curl_response)) {
            $response->status = 'success';
            $arrResults['Data'] = $response;
        }
    }
    return $arrResults;
}

function listCourses()
{
    global $DB;
    $response = array();
    if (isset($_POST['catId'])) {
        $catId = $_POST['catId'];
    }
    $categoryFilter = isset($catId) ? "category=$catId" : "category > 0";
    if (isset($_POST['userId'])) {
        $q = "SELECT * FROM {course} where visible=1 and $categoryFilter";
        $categories = $DB->get_records_sql($q);
        foreach ($categories as $rec) {
            $new_data = new stdClass();
            $cat = new stdClass();
            $cat->tablename = 'course_categories';
            $cat->fieldname = 'name';
            $cat->recid = 'id';
            $cat->id = $rec->category;
            $new_data->course_fullname = $rec->fullname;
            $new_data->course_shortname = $rec->shortname;
            $category = get_field_by_id($cat);
            $new_data->category_id = $category->id;
            $new_data->category_name = $category->name;
            $new_data->course_id = $rec->id;
            $response[] = $new_data;
        }
        $arrResults['Data'] = $response;
    }
    return $arrResults;
}

function get_field_by_id($cat)
{
    global $DB, $CFG;
    $q = "Select $cat->fieldname,$cat->recid from $CFG->prefix" . "$cat->tablename where id =$cat->id";
    $response = $DB->get_record_sql($q);
    return $response;
}

function create_course()
{
    global $DB, $CFG;
    $response = new stdClass();
    if (isset($_POST['wsfunction'])) {
        $wsfunction = $_POST['wsfunction'];
        $wstoken = $_POST['wstoken'];

        $course_category = $_POST['course_category'];
        $course_name = $_POST['course_full_name'];
        $course_shortname = $_POST['course_short_name'];
        $course_summary = $_POST['course_description'];
        $enrollment_type = $_POST['enroll_type'];

        if ($enrollment_type == 'self') {
            $enroll_methods = 'Learning self';
        } else if ($enrollment_type == 'admin') {
            $enroll_methods = 'Learning manual';
        }

        $num_sections = $_POST['topicCnt'];
        $course_code = '';

        $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

        $params = array(
            'courses' => array(
                array(
                    'fullname' => $course_name,
                    'shortname' => $course_shortname,
                    'categoryid' => $course_category,
                    'idnumber' => $course_code,
                    'summary' => $course_summary,
                    'format' => 'topcoll',
                    'numsections' => $num_sections,
                ),
            ),
        );

        $curl = new curl();
        $curl_response = $curl->post($server_url, $params);
        if (!empty($curl_response)) {
            $json_arr = json_decode($curl_response, true);
            foreach ($json_arr as $rec) {
                $response->name = $rec['shortname'];
                $response->id = $rec['id'];
            }
            $arrResults['Data'] = $response;
        }
    }
    return $arrResults;
}

function get_course_by_id()
{
    global $DB, $CFG;

    $response = new stdClass();

    if (isset($_POST['course_id'])) {

        $wsfunction = $_POST['wsfunction'];
        $wstoken = $_POST['wstoken'];

        $course_id = $_POST['course_id'];

        $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

        $ids = array($course_id);

        $params = array('options' => array('ids' => $ids));

        $curl = new curl();
        $curl_response = $curl->post($server_url, $params);
        if (!empty($curl_response)) {
            $json_arr = json_decode($curl_response, true);
            foreach ($json_arr as $rec) {
                $response->fullname = $rec['fullname'];
                $response->shortname = $rec['shortname'];
                $response->description = strip_tags($rec['summary']);
                $response->category = $rec['categoryid'];
                $response->topics_cnt = $rec['numsections'];
                $response->enrol_method = 'manual';
            }
            $arrResults['Data'] = $response;
        }

    }
    return $arrResults;
}

function update_course()
{
    global $DB, $CFG;
    $response = new stdClass();
    if (isset($_POST['wsfunction'])) {

        $wsfunction = $_POST['wsfunction'];
        $wstoken = $_POST['wstoken'];

        $course_id = $_POST['course_id'];
        $course_category = $_POST['course_category'];
        $course_name = $_POST['course_full_name'];
        $course_shortname = $_POST['course_short_name'];
        $course_summary = $_POST['course_description'];
        $enrollment_type = $_POST['enroll_type'];
        $old_enroll_id = $_POST['old_enroll_id'];

        if ($enrollment_type == 'self') {
            $enroll_methods = 'Learning self';
        } else if ($enrollment_type == 'admin') {
            $enroll_methods = 'Learning manual';
        }

        $num_sections = $_POST['topicCnt'];
        $course_code = '';

        $server_url = $CFG->wwwroot . "/webservice/rest/server.php?moodlewsrestformat=json&wsfunction=$wsfunction&wstoken=$wstoken";

        $params = array(
            'courses' => array(
                array(
                    'id' => $course_id,
                    'fullname' => $course_name,
                    'shortname' => $course_shortname,
                    'categoryid' => $course_category,
                    'idnumber' => $course_code,
                    'summary' => $course_summary,
                    'format' => 'topcoll',
                    'numsections' => $num_sections,
                ),
            ),
        );

        $curl = new curl();
        $curl_response = $curl->post($server_url, $params);

        if (!empty($curl_response)) {
            $arrResults['Data'] = $curl_response;
        }

    }
    return $arrResults;

}
