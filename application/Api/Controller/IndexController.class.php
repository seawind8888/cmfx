<?php
namespace Api\Controller;
use Think\Controller\RestController;

class IndexController extends RestController
{

    protected $allowMethod = array('get', 'post', 'put', 'delete'); // REST允许的请求类型列表
    protected $defaultType = 'json';

    public function read($id)
    {
        $dataModel = D($_GET['table']);
        $condition['id'] = $id;
        $result = $dataModel->where($condition)->find();
        echo $this->responseFactory("read", $dataModel, $result, "数据不存在");
    }
    public function login($user_login,$user_pass)
    {
        header("Access-Control-Allow-Origin: *");
        $dataModel = D($_GET['table']);
        $condition['user_login'] = $user_login;
        $result = $dataModel->where($condition)->find();
        if($result){
            if(sp_compare_password($user_pass,$result['user_pass'])){
                echo json_encode(array(
                    "status" => "success"
                ));
            }else{
                echo json_encode(array(
                    "error" => "password fail!"
                ));
            }
        }else{
            echo json_encode(array(
                "error" => $dataModel->getError()
            ));
        }
    }
    public function regist($user_login)
    {
        $inputs = json_decode($GLOBALS['HTTP_RAW_POST_DATA'], true);//backbone使用application/json，必须使用$GLOBALS['HTTP_RAW_POST_DATA']来接收数据

        $dataModel = D($_GET['table']);
        $user = $dataModel->where("user_login = '%s'", array($user_login))->find();
        if ($user) {
            echo json_encode(array(
                "status" => "error",
                "error" => "账号已存在"
            ));
        } else {
            if (!$dataModel->create($inputs)) {
                echo json_encode(array(
                    "status" => "failure",
                    "error" => $dataModel->getError()
                ));
                return false;
            } else {
                $dataModel->add();
                echo json_encode(array(
                    "status" => "success",
                ));
            }
        }

    }

    /*新建数据记录*/

    public function update()
    {
//        echo json_encode(array(
//            "status" => "success",
//            "data" => I('put.'),//使用I('put.')获取put来的数据
//            "type" => $this->_type
//        ));
    }

    public function delete($id)
    {
        $dataModel = D($_GET['table']);
        $condition['id'] = $id;
        $result = $dataModel->where($condition)->delete();
//        if(false === $result) {
//            echo json_encode(array(
//                "status" => "failure",
//                "error" => $dataModel->getDbError()
//            ));
//        } else if(0 === $result) {
//            echo json_encode(array(
//                "status" => "failure",
//                "error" => "数据不存在"
//            ));
//        } else {
//            echo json_encode(array(
//                "status" => "success"
//            ));
//        }
        echo $this->responseFactory("delete", $dataModel, $result, "数据不存在");
    }

    public function responseFactory($action, $dataModel, $result, $error)
    {
        switch ($action) {
            case 'create':
                if ($result) {
                    return json_encode(array(
                        "status" => "success",
                        "result" => $result
                    ));
                } else {
                    return json_encode(array(
                        "status" => "failure",
                        "error" => $dataModel->getDbError()
                    ));
                }
                break;
            case 'read':
                if (false === $result) {
                    return json_encode(array(
                        "status" => "failure",
                        "error" => $dataModel->getDbError()
                    ));
                } else if (null === $result) {
                    return json_encode(array(
                        "status" => "failure",
                        "error" => $error
                    ));
                } else {
                    return json_encode(array(
                        "status" => "success",
                        "data" => $result
                    ));
                }
                break;
            case "delete":
                if (false === $result) {
                    return json_encode(array(
                        "status" => "failure",
                        "error" => $dataModel->getDbError()
                    ));
                } else if (0 === $result) {
                    return json_encode(array(
                        "status" => "failure",
                        "error" => $error
                    ));
                } else {
                    return json_encode(array(
                        "status" => "success"
                    ));
                }
                break;
        }
    }


}