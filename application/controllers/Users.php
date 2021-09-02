<?php
defined('BASEPATH') or exit('No direct script access allowed');
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}
class Users extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $response = $this->db->from('users')->get()->result();
        $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function save()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $username = $request->username;
        $email = $request->email;
        $password = $request->password;
        $data = array(
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'updated_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        );
        if ($this->db->insert('users', $data)) {
            $this->get($this->db->insert_id());
        }
    }

    public function destroy($id)
    {
        if ((int) $id == 0) {
            $response['durum'] = 'User not found.';
            $response['error'] = true;
        }else{
            $this->db->where('id', $id)->delete('users');
            $response['message'] = "User deleted.";
            $response['error'] = false;
        }
        $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function update()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $id = $request->id;
        $username = $request->username;
        $email = $request->email;
        $password = $request->password;
        $data = array(
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->where('id', $id)->update('users', $data);
        if ($this->db->insert('users', $data)) {
            $this->get($id);
        }
    }

    public function get($id)
    {
        $response = $this->db->where('id', $id)->from('users')->get()->result();
        $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response));
    }
}
