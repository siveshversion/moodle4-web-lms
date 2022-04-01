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
