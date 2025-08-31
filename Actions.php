<?php
require_once('DBConnection.php');

class Actions extends DBConnection
{
    function __construct()
    {
        parent::__construct();
    }
    function __destruct()
    {
        parent::__destruct();
    }
    function login()
    {
        extract($_POST);
        $sql = "SELECT * FROM admin_list where username = '{$username}' ";
        @$qry = $this->query($sql)->fetchArray();
        if (!$qry) {
            $resp['status'] = "failed";
            $resp['msg'] = "Invalid username or password.";
        } else {
            $resp['status'] = "success";
            $resp['msg'] = "Login successfully.";
            foreach ($qry as $k => $v) {
                if (!is_numeric($k))
                    $_SESSION[$k] = $v;
            }
        }
        return json_encode($resp);
    }
    function logout()
    {
        session_destroy();
        header("location:./admin");
    }
    function e_login()
    {
        extract($_POST);
        $election_id = $_SESSION['election']['election_id'];
        $sql = "SELECT *,(lastname || ', ' || firstname || ' ' || middlename) as name FROM voter_list where username = '{$username}' and `password` = '" . md5($password) . "' and election_id = '{$election_id}' ";
        @$qry = $this->query($sql)->fetchArray();
        if (!$qry) {
            $resp['status'] = "failed";
            $resp['msg'] = "Invalid username or password.";
        } else {
            if ($qry['status'] == 1) {
                $resp['status'] = "success";
                $resp['msg'] = "Login successfully.";
                foreach ($qry as $k => $v) {
                    if (!is_numeric($k))
                        $_SESSION[$k] = $v;
                }
            } else {
                $resp['status'] = "failed";
                $resp['msg'] = "Your Account is not yet validated.";
            }
        }
        return json_encode($resp);
    }
    function e_logout()
    {
        session_destroy();
        header("location:./");
    }
    function e_update_credentials()
    {
        extract($_POST);
        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id', 'old_password')) && !empty($v)) {
                if (!empty($data))
                    $data .= ",";
                if ($k == 'password')
                    $v = md5($v);
                $data .= " `{$k}` = '{$v}' ";
            }
        }
        if (!empty($password) && md5($old_password) != $_SESSION['password']) {
            $resp['status'] = 'failed';
            $resp['msg'] = "Old password is incorrect.";
        } else {
            $sql = "UPDATE `voter_list` set {$data} where voter_id = '{$_SESSION['voter_id']}'";
            @$save = $this->query($sql);
            if ($save) {
                $resp['status'] = 'success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Credential successfully updated.';
                foreach ($_POST as $k => $v) {
                    if (!in_array($k, array('id', 'old_password')) && !empty($v)) {
                        if (!empty($data))
                            $data .= ",";
                        if ($k == 'password')
                            $v = md5($v);
                        $_SESSION[$k] = $v;
                    }
                    $_SESSION['name'] = $_SESSION['lastname'] . ", " . $_SESSION['firstname'] . " " . $_SESSION['middlename'];
                }
            } else {
                $resp['status'] = 'failed';
                $resp['msg'] = 'Updating Credentials Failed. Error: ' . $this->lastErrorMsg();
                $resp['sql'] = $sql;
            }
        }
        return json_encode($resp);
    }
    function save_admin()
    {
        extract($_POST);
        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id'))) {
                if (!empty($id)) {
                    if (!empty($data))
                        $data .= ",";
                    $data .= " `{$k}` = '{$v}' ";
                } else {
                    $cols[] = $k;
                    $values[] = "'{$v}'";
                }
            }
        }
        if (empty($id)) {
            $cols[] = 'password';
            $values[] = "'" . md5($username) . "'";
        }
        if (isset($cols) && isset($values)) {
            $data = "(" . implode(',', $cols) . ") VALUES (" . implode(',', $values) . ")";
        }



        @$check = $this->query("SELECT count(admin_id) as `count` FROM admin_list where `username` = '{$username}' " . ($id > 0 ? " and admin_id != '{$id}' " : ""))->fetchArray()['count'];
        if (@$check > 0) {
            $resp['status'] = 'failed';
            $resp['msg'] = "Username already exists.";
        } else {
            if (empty($id)) {
                $sql = "INSERT INTO `admin_list` {$data}";
            } else {
                $sql = "UPDATE `admin_list` set {$data} where admin_id = '{$id}'";
            }
            @$save = $this->query($sql);
            if ($save) {
                $resp['status'] = 'success';
                if (empty($id))
                    $resp['msg'] = 'New User successfully saved.';
                else
                    $resp['msg'] = 'User Details successfully updated.';
            } else {
                $resp['status'] = 'failed';
                $resp['msg'] = 'Saving User Details Failed. Error: ' . $this->lastErrorMsg();
                $resp['sql'] = $sql;
            }
        }
        return json_encode($resp);
    }
    function delete_admin()
    {
        extract($_POST);

        @$delete = $this->query("DELETE FROM `admin_list` where rowid = '{$id}'");
        if ($delete) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'User successfully deleted.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_user()
    {
        $_POST['election_id'] = $_SESSION['election']['election_id'];
        if (isset($_POST['cpassword']))
            unset($_POST['cpassword']);

        if (isset($_POST['password']) && !empty($_POST['password']))
            $_POST['password'] = md5($_POST['password']);
        if (isset($_POST['password']) && empty($_POST['password']))
            unset($_POST['password']);

        extract($_POST);
        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id'))) {
                if (!empty($id)) {
                    if (!empty($data))
                        $data .= ",";
                    if (!is_numeric($v))
                        $v = $this->escapeString($v);
                    $data .= " `{$k}` = '{$v}' ";
                } else {
                    $cols[] = $k;
                    $values[] = "'{$v}'";
                }
            }
        }
        if (isset($cols) && isset($values)) {
            $data = "(" . implode(',', $cols) . ") VALUES (" . implode(',', $values) . ")";
        }

        @$check = $this->query("SELECT * FROM voter_list where `username` = '{$username}' and `election_id` = '{$election_id}' " . ($id > 0 ? " and id != '{$id}' " : ""))->num_rows;
        if (@$check > 0) {
            $resp['status'] = 'failed';
            $resp['msg'] = "Username already exists.";
        } else {
            if (empty($id)) {
                $sql = "INSERT INTO `voter_list` {$data}";
            } else {
                $sql = "UPDATE `voter_list` set {$data} where user_id = '{$id}'";
            }
            @$save = $this->query($sql);
            if ($save) {
                $resp['status'] = 'success';
                if (empty($id)) {
                    $resp['msg'] = 'New User successfully saved.';
                    $user_id = $this->query('SELECT last_insert_rowid()')->fetchArray()[0];
                } else {
                    $resp['msg'] = 'User Details successfully updated.';
                    $user_id = $id;
                }

            } else {
                $resp['status'] = 'failed';
                $resp['msg'] = 'Saving User Details Failed. Error: ' . $this->lastErrorMsg();
                $resp['sql'] = $sql;
            }
        }
        return json_encode($resp);
    }
    function delete_user()
    {
        extract($_POST);
        @$delete = $this->query("DELETE FROM `voter_list` where id = '{$id}'");
        if ($delete) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'User successfully deleted.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function update_credentials()
    {
        extract($_POST);
        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id', 'old_password')) && !empty($v)) {
                if (!empty($data))
                    $data .= ",";
                if ($k == 'password')
                    $v = md5($v);
                $data .= " `{$k}` = '{$v}' ";
            }
        }
        if (!empty($password) && md5($old_password) != $_SESSION['password']) {
            $resp['status'] = 'failed';
            $resp['msg'] = "Old password is incorrect.";
        } else {
            $sql = "UPDATE `admin_list` set {$data} where admin_id = '{$_SESSION['admin_id']}'";
            @$save = $this->query($sql);
            if ($save) {
                $resp['status'] = 'success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Credential successfully updated.';
                foreach ($_POST as $k => $v) {
                    if (!in_array($k, array('id', 'old_password')) && !empty($v)) {
                        if (!empty($data))
                            $data .= ",";
                        if ($k == 'password')
                            $v = md5($v);
                        $_SESSION[$k] = $v;
                    }
                }
            } else {
                $resp['status'] = 'failed';
                $resp['msg'] = 'Updating Credentials Failed. Error: ' . $this->lastErrorMsg();
                $resp['sql'] = $sql;
            }
        }
        return json_encode($resp);
    }
    function save_settings()
    {
        extract($_POST);
        $update = file_put_contents('./about.html', htmlentities($about));
        if ($update) {
            $resp['status'] = "success";
            $resp['msg'] = "Settings successfully updated.";
        } else {
            $resp['status'] = "failed";
            $resp['msg'] = "Failed to update settings.";
        }
        return json_encode($resp);
    }
    function save_election()
    {
        extract($_POST);
        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id'))) {
                $v = trim($v);
                $v = $this->escapeString($v);
                $$k = $v;
                if (empty($id)) {
                    $cols[] = "`{$k}`";
                    $vals[] = "'{$v}'";
                } else {
                    if (!empty($data))
                        $data .= ", ";
                    $data .= " `{$k}` = '{$v}' ";
                }
            }
        }
        if (isset($cols) && isset($vals)) {
            $cols_join = implode(",", $cols);
            $vals_join = implode(",", $vals);
        }

        if (empty($id)) {
            $sql = "INSERT INTO `election_list` ({$cols_join}) VALUES ($vals_join)";
        } else {
            $sql = "UPDATE `election_list` set {$data} where election_id = '{$id}'";
        }

        $check = $this->query("SELECT count(election_id) as `count` FROM `election_list` where `title` = '{$title}' " . ($id > 0 ? " and election_id != '{$id}'" : ""))->fetchArray()['count'];
        if ($check > 0) {
            $resp['status'] = "failed";
            $resp['msg'] = "Election name is already exists.";
        } else {
            @$save = $this->query($sql);
            if ($save) {
                $resp['status'] = "success";
                if (empty($id))
                    $resp['msg'] = "Election successfully saved.";
                else
                    $resp['msg'] = "Election successfully updated.";

                if (empty($id))
                    $id = $this->query("SELECT last_insert_rowid()")->fetchArray()[0];

                if ($status == 1) {
                    $this->query("UPDATE `election_list` set status = 0 where election_id != '{$id}'");
                }
            } else {
                $resp['status'] = "failed";
                if (empty($id))
                    $resp['msg'] = "Saving New Election Failed.";
                else
                    $resp['msg'] = "Updating Election Failed.";
                $resp['error'] = $this->lastErrorMsg();
            }
        }

        return json_encode($resp);
    }
    function delete_election()
    {
        extract($_POST);

        @$delete = $this->query("DELETE FROM `election_list` where election_id = '{$id}'");
        if ($delete) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Election successfully deleted.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function update_stat_elec()
    {
        extract($_POST);

        $update = $this->query("UPDATE election_list set status = '{$status}' where election_id = '{$id}'");
        if ($update) {
            $resp['status'] = 'success';
            $resp['msg'] = 'Election\'s status successfully updated';
            $_SESSION['flashdata']['type'] = $resp['status'];
            $_SESSION['flashdata']['msg'] = $resp['msg'];
            if ($status == 1)
                $this->query("UPDATE `election_list` set status = 0 where election_id != '{$id}'");
        } else {
            $resp['status'] = 'failed';
            $resp['msg'] = 'Election\'s status has failed to update.';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_position()
    {
        extract($_POST);
        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id'))) {
                $v = trim($v);
                $v = $this->escapeString($v);
                $$k = $v;
                if (empty($id)) {
                    $cols[] = "`{$k}`";
                    $vals[] = "'{$v}'";
                } else {
                    if (!empty($data))
                        $data .= ", ";
                    $data .= " `{$k}` = '{$v}' ";
                }
            }
        }
        if (empty($id)) {
            $order_by = $this->query("SELECT order_by FROM position_list order by order_by desc limit 1");
            $res = $order_by->fetchArray();
            $order_by = 0;
            if ($res)
                $order_by = $res['order_by'] + 1;
            $cols[] = "`order_by`";
            $vals[] = "'{$order_by}'";
        }
        if (isset($cols) && isset($vals)) {
            $cols_join = implode(",", $cols);
            $vals_join = implode(",", $vals);
        }

        if (empty($id)) {
            $sql = "INSERT INTO `position_list` ({$cols_join}) VALUES ($vals_join)";
        } else {
            $sql = "UPDATE `position_list` set {$data} where position_id = '{$id}'";
        }

        $check = $this->query("SELECT count(position_id) as `count` FROM `position_list` where `name` = '{$name}' " . ($id > 0 ? " and position_id != '{$id}'" : ""))->fetchArray()['count'];
        if ($check > 0) {
            $resp['status'] = "failed";
            $resp['msg'] = "Position name is already exists.";
        } else {
            @$save = $this->query($sql);
            if ($save) {
                $resp['status'] = "success";
                if (empty($id))
                    $resp['msg'] = "Position successfully saved.";
                else
                    $resp['msg'] = "Position successfully updated.";
            } else {
                $resp['status'] = "failed";
                if (empty($id))
                    $resp['msg'] = "Saving New Position Failed.";
                else
                    $resp['msg'] = "Updating Position Failed.";
                $resp['error'] = $this->lastErrorMsg();
            }
        }

        return json_encode($resp);
    }
    function delete_position()
    {
        extract($_POST);

        @$delete = $this->query("DELETE FROM `position_list` where position_id = '{$id}'");
        if ($delete) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Position successfully deleted.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function update_stat_position()
    {
        extract($_POST);

        $update = $this->query("UPDATE position_list set status = '{$status}' where position_id = '{$id}'");
        if ($update) {
            $resp['status'] = 'success';
            $resp['msg'] = 'Position\'s status successfully updated';
            $_SESSION['flashdata']['type'] = $resp['status'];
            $_SESSION['flashdata']['msg'] = $resp['msg'];
        } else {
            $resp['status'] = 'failed';
            $resp['msg'] = 'Position\'s status has failed to update.';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_position_order()
    {
        extract($_POST);
        foreach ($order as $k => $v) {
            $save = $this->query("UPDATE `position_list` set order_by = '{$k}' where position_id = '{$v}'");
            if (!$save) {
                $resp['status'] = 'failed';
                $resp['msg'] = $this->lastErrorMsg();
                break;
            }
        }
        $resp['status'] = 'success';
        if ($resp['status'] == 'success') {
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Position List Order Successfully Updated';
        }
        return json_encode($resp);
    }
    function save_region()
    {
        extract($_POST);
        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id'))) {
                $v = trim($v);
                $v = $this->escapeString($v);
                $$k = $v;
                if (empty($id)) {
                    $cols[] = "`{$k}`";
                    $vals[] = "'{$v}'";
                } else {
                    if (!empty($data))
                        $data .= ", ";
                    $data .= " `{$k}` = '{$v}' ";
                }
            }
        }
        if (isset($cols) && isset($vals)) {
            $cols_join = implode(",", $cols);
            $vals_join = implode(",", $vals);
        }

        if (empty($id)) {
            $sql = "INSERT INTO `region_list` ({$cols_join}) VALUES ($vals_join)";
        } else {
            $sql = "UPDATE `region_list` set {$data} where region_id = '{$id}'";
        }

        $check = $this->query("SELECT count(region_id) as `count` FROM `region_list` where `name` = '{$name}' " . ($id > 0 ? " and region_id != '{$id}'" : ""))->fetchArray()['count'];
        if ($check > 0) {
            $resp['status'] = "failed";
            $resp['msg'] = "Region name is already exists.";
        } else {
            @$save = $this->query($sql);
            if ($save) {
                $resp['status'] = "success";
                if (empty($id))
                    $resp['msg'] = "Region successfully saved.";
                else
                    $resp['msg'] = "Region successfully updated.";
            } else {
                $resp['status'] = "failed";
                if (empty($id))
                    $resp['msg'] = "Saving New Region Failed.";
                else
                    $resp['msg'] = "Updating Region Failed.";
                $resp['error'] = $this->lastErrorMsg();
            }
        }

        return json_encode($resp);
    }
    function delete_region()
    {
        extract($_POST);

        @$delete = $this->query("DELETE FROM `region_list` where region_id = '{$id}'");
        if ($delete) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Region successfully deleted.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_province()
    {
        extract($_POST);
        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id'))) {
                $v = trim($v);
                $v = $this->escapeString($v);
                $$k = $v;
                if (empty($id)) {
                    $cols[] = "`{$k}`";
                    $vals[] = "'{$v}'";
                } else {
                    if (!empty($data))
                        $data .= ", ";
                    $data .= " `{$k}` = '{$v}' ";
                }
            }
        }
        if (isset($cols) && isset($vals)) {
            $cols_join = implode(",", $cols);
            $vals_join = implode(",", $vals);
        }

        if (empty($id)) {
            $sql = "INSERT INTO `province_list` ({$cols_join}) VALUES ($vals_join)";
        } else {
            $sql = "UPDATE `province_list` set {$data} where province_id = '{$id}'";
        }

        $check = $this->query("SELECT count(province_id) as `count` FROM `province_list` where `name` = '{$name}' and `region_id` = '{$region_id}' " . ($id > 0 ? " and province_id != '{$id}'" : ""))->fetchArray()['count'];
        if ($check > 0) {
            $resp['status'] = "failed";
            $resp['msg'] = "Province name is already exists.";
        } else {
            @$save = $this->query($sql);
            if ($save) {
                $resp['status'] = "success";
                if (empty($id))
                    $resp['msg'] = "Province successfully saved.";
                else
                    $resp['msg'] = "Province successfully updated.";
            } else {
                $resp['status'] = "failed";
                if (empty($id))
                    $resp['msg'] = "Saving New Province Failed.";
                else
                    $resp['msg'] = "Updating Province Failed.";
                $resp['error'] = $this->lastErrorMsg();
            }
        }

        return json_encode($resp);
    }
    function delete_province()
    {
        extract($_POST);

        @$delete = $this->query("DELETE FROM `province_list` where province_id = '{$id}'");
        if ($delete) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Province successfully deleted.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_district()
    {
        extract($_POST);
        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id'))) {
                $v = trim($v);
                $v = $this->escapeString($v);
                $$k = $v;
                if (empty($id)) {
                    $cols[] = "`{$k}`";
                    $vals[] = "'{$v}'";
                } else {
                    if (!empty($data))
                        $data .= ", ";
                    $data .= " `{$k}` = '{$v}' ";
                }
            }
        }
        if (isset($cols) && isset($vals)) {
            $cols_join = implode(",", $cols);
            $vals_join = implode(",", $vals);
        }

        if (empty($id)) {
            $sql = "INSERT INTO `district_list` ({$cols_join}) VALUES ($vals_join)";
        } else {
            $sql = "UPDATE `district_list` set {$data} where district_id = '{$id}'";
        }

        $check = $this->query("SELECT count(district_id) as `count` FROM `district_list` where `name` = '{$name}' and `province_id` = '{$province_id}' " . ($id > 0 ? " and district_id != '{$id}'" : ""))->fetchArray()['count'];
        if ($check > 0) {
            $resp['status'] = "failed";
            $resp['msg'] = "District name is already exists.";
        } else {
            @$save = $this->query($sql);
            if ($save) {
                $resp['status'] = "success";
                if (empty($id))
                    $resp['msg'] = "District successfully saved.";
                else
                    $resp['msg'] = "District successfully updated.";
            } else {
                $resp['status'] = "failed";
                if (empty($id))
                    $resp['msg'] = "Saving New District Failed.";
                else
                    $resp['msg'] = "Updating District Failed.";
                $resp['error'] = $this->lastErrorMsg();
            }
        }

        return json_encode($resp);
    }
    function delete_district()
    {
        extract($_POST);

        @$delete = $this->query("DELETE FROM `district_list` where district_id = '{$id}'");
        if ($delete) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'District successfully deleted.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_city()
    {
        extract($_POST);
        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id'))) {
                $v = trim($v);
                $v = $this->escapeString($v);
                $$k = $v;
                if (empty($id)) {
                    $cols[] = "`{$k}`";
                    $vals[] = "'{$v}'";
                } else {
                    if (!empty($data))
                        $data .= ", ";
                    $data .= " `{$k}` = '{$v}' ";
                }
            }
        }
        if (isset($cols) && isset($vals)) {
            $cols_join = implode(",", $cols);
            $vals_join = implode(",", $vals);
        }

        if (empty($id)) {
            $sql = "INSERT INTO `city_list` ({$cols_join}) VALUES ($vals_join)";
        } else {
            $sql = "UPDATE `city_list` set {$data} where city_id = '{$id}'";
        }

        $check = $this->query("SELECT count(city_id) as `count` FROM `city_list` where `name` = '{$name}' and `district_id` = '{$district_id}' " . ($id > 0 ? " and city_id != '{$id}'" : ""))->fetchArray()['count'];
        if ($check > 0) {
            $resp['status'] = "failed";
            $resp['msg'] = "City/Municipal name is already exists.";
        } else {
            @$save = $this->query($sql);
            if ($save) {
                $resp['status'] = "success";
                if (empty($id))
                    $resp['msg'] = "City/Municipal successfully saved.";
                else
                    $resp['msg'] = "City/Municipal successfully updated.";
            } else {
                $resp['status'] = "failed";
                if (empty($id))
                    $resp['msg'] = "Saving New City/Municipal Failed.";
                else
                    $resp['msg'] = "Updating City/Municipal Failed.";
                $resp['error'] = $this->lastErrorMsg();
            }
        }

        return json_encode($resp);
    }
    function delete_city()
    {
        extract($_POST);

        @$delete = $this->query("DELETE FROM `city_list` where city_id = '{$id}'");
        if ($delete) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'City/Municipal successfully deleted.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_candidate()
    {
        $_POST['election_id'] = $_SESSION['election']['election_id'];
        extract($_POST);
        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id'))) {
                $v = trim($v);
                $v = $this->escapeString($v);
                $$k = $v;
                if (empty($id)) {
                    $cols[] = "`{$k}`";
                    $vals[] = "'{$v}'";
                } else {
                    if (!empty($data))
                        $data .= ", ";
                    $data .= " `{$k}` = '{$v}' ";
                }
            }
        }
        if (isset($cols) && isset($vals)) {
            $cols_join = implode(",", $cols);
            $vals_join = implode(",", $vals);
        }

        if (empty($id)) {
            $sql = "INSERT INTO `candidate_list` ({$cols_join}) VALUES ($vals_join)";
        } else {
            $sql = "UPDATE `candidate_list` set {$data} where candidate_id = '{$id}'";
        }

        @$save = $this->query($sql);
        if ($save) {
            $resp['status'] = "success";
            if (empty($id)) {
                $resp['msg'] = "Candidate successfully saved.";
                $eid = $this->query("SELECT last_insert_rowid()")->fetchArray()[0];
            } else {
                $resp['msg'] = "Candidate successfully updated.";
                $eid = $id;
            }
            $fname = __DIR__ . '/avatars/' . $eid . '.png';
            if (isset($_FILES['avatar']['tmp_name']) && !empty($_FILES['avatar']['tmp_name'])) {
                $upload = $_FILES['avatar']['tmp_name'];
                $type = mime_content_type($upload);
                $allowed = array('image/png', 'image/jpeg');
                if (!in_array($type, $allowed)) {
                    $resp['msg'] .= " But Image failed to upload due to invalid file type.";
                } else {
                    $new_height = 200;
                    $new_width = 200;

                    list($width, $height) = getimagesize($upload);
                    $t_image = imagecreatetruecolor($new_width, $new_height);
                    $gdImg = ($type == 'image/png') ? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
                    imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    if ($gdImg) {
                        if (is_file($fname))
                            unlink($fname);
                        $uploaded_img = imagepng($t_image, $fname);
                        imagedestroy($gdImg);
                        imagedestroy($t_image);
                    } else {
                        $resp['msg'] .= " But Image failed to upload due to unkown reason.";
                    }
                }
            }
        } else {
            $resp['status'] = "failed";
            if (empty($id))
                $resp['msg'] = "Saving New Candidate Failed.";
            else
                $resp['msg'] = "Updating Candidate Failed.";
            $resp['error'] = $this->lastErrorMsg();
        }

        return json_encode($resp);
    }
    function delete_candidate()
    {
        extract($_POST);

        @$delete = $this->query("DELETE FROM `candidate_list` where candidate_id = '{$id}'");
        if ($delete) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Candidate successfully deleted.';
            if (is_file(__DIR__ . '/avatars/' . $id . '.png'))
                unlink(__DIR__ . '/avatars/' . $id . '.png');
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_attendance()
    {
        extract($_POST);
        $jdata = json_decode($json_data);
        $json_data = $this->escapeString($json_data);
        $ip = isset($jdata->ip) ? $jdata->ip : '';
        $location = isset($jdata->loc) ? $jdata->loc : '';
        $dtype = $this->isMobileDevice() ? 'mobilde' : 'desktop';
        $datetime = date("Y-m-d H:i");
        $sql = "INSERT INTO `attendance_list` (`voter_id`,`device_type`,`att_type`,`ip`,`location`,`json_data`,`date_created`) VALUES 
        ('{$_SESSION['voter_id']}','{$dtype}','{$type}','{$ip}','{$location}','{$json_data}','{$datetime}')
        ";
        $save = $this->query($sql);
        if ($save) {
            $resp['status'] = 'success';
        } else {
            $resp['status'] = 'failed';
        }
        return json_encode($resp);
    }
    function delete_attendance()
    {
        extract($_POST);

        @$delete = $this->query("DELETE FROM `attendance_list` where attendance_id = '{$id}'");
        if ($delete) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Attendance Log successfully deleted.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function validate_voter()
    {
        extract($_POST);
        @$update = $this->query("UPDATE `voter_list` set `status` = 1 where voter_id = '{$id}'");
        if ($update) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Voter successfully validated.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_vote()
    {
        extract($_POST);
        $data = "";
        $voter_id = $_SESSION['voter_id'];
        $election_id = $_SESSION['election']['election_id'];
        $tbl_fields = "(`election_id`,`voter_id`,`position_id`,`candidate_id`)";
        foreach ($votes as $position_id => $position) {
            foreach ($position as $candidate_id) {
                if (!empty($data))
                    $data .= ", ";
                $data .= "('{$election_id}','{$voter_id}','{$position_id}','{$candidate_id}')";
            }
        }

        $sql = "INSERT INTO `vote_list` {$tbl_fields} VALUES {$data}";
        $save = $this->query($sql);
        if ($save) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Your Ballot was successfully submitted.';
        } else {
            $resp['status'] = 'failed';
            $resp['msg'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function updated_result()
    {
        $data = array();
        foreach ($_POST['candidates'] as $id) {
            $vote = $this->query("SELECT count(vote_id) as total FROM vote_list where candidate_id = '{$id}' ")->fetchArray()['total'];
            $vote = $vote > 0 ? $vote : 0;
            $data[] = array('id' => $id, 'count' => $vote);
        }
        return json_encode($data);
    }

    function delete_voter()
    {
        if (!isset($_POST['voter_id']) || empty($_POST['voter_id'])) {
            $resp['status'] = 'failed';
            $resp['error'] = 'No voter_id provided.';
            return json_encode($resp);
        }
        $voter_id = $this->escapeString($_POST['voter_id']);

        // Delete related votes first
        $this->query("DELETE FROM `vote_list` WHERE voter_id = '{$voter_id}'");
        $delete = $this->query("DELETE FROM `voter_list` WHERE voter_id = '{$voter_id}'");
        if ($delete) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Voter successfully deleted.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
}
$a = isset($_GET['a']) ? $_GET['a'] : '';
$action = new Actions();
switch ($a) {
    case 'login':
        echo $action->login();
        break;
    case 'logout':
        echo $action->logout();
        break;
    case 'e_login':
        echo $action->e_login();
        break;
    case 'e_logout':
        echo $action->e_logout();
        break;
    case 'e_update_credentials':
        echo $action->e_update_credentials();
        break;
    case 'save_admin':
        echo $action->save_admin();
        break;
    case 'delete_admin':
        echo $action->delete_admin();
        break;
    case 'save_user':
        echo $action->save_user();
        break;
    case 'delete_user':
        echo $action->delete_user();
        break;
    case 'update_credentials':
        echo $action->update_credentials();
        break;
    case 'save_settings':
        echo $action->save_settings();
        break;
    case 'save_election':
        echo $action->save_election();
        break;
    case 'delete_election':
        echo $action->delete_election();
        break;
    case 'update_stat_elec':
        echo $action->update_stat_elec();
        break;
    case 'save_position':
        echo $action->save_position();
        break;
    case 'delete_position':
        echo $action->delete_position();
        break;
    case 'update_stat_position':
        echo $action->update_stat_position();
        break;
    case 'save_position_order':
        echo $action->save_position_order();
        break;
    case 'save_region':
        echo $action->save_region();
        break;
    case 'delete_region':
        echo $action->delete_region();
        break;
    case 'save_province':
        echo $action->save_province();
        break;
    case 'delete_province':
        echo $action->delete_province();
        break;
    case 'save_district':
        echo $action->save_district();
        break;
    case 'delete_district':
        echo $action->delete_district();
        break;
    case 'save_city':
        echo $action->save_city();
        break;
    case 'delete_city':
        echo $action->delete_city();
        break;
    case 'save_candidate':
        echo $action->save_candidate();
        break;
    case 'delete_candidate':
        echo $action->delete_candidate();
        break;
    case 'validate_voter':
        echo $action->validate_voter();
        break;
    case 'save_vote':
        echo $action->save_vote();
        break;
    case 'updated_result':
        echo $action->updated_result();
        break;
    case 'delete_voter':
        echo $action->delete_voter();
        break;
    default:
        // default action here
        break;
}