<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace API\Controller;

use Think\Controller;

define('N', 20);

class UserController extends Controller
{
    public function user_register()
    {
        index();
        if (IS_POST) {
            die('error input type!');
        }
        $param = array('clientId' => I('clientId'),
            'userName' => I('userName'),
            'passWord' => I('passWord'),
            'nickName' => I('nickName'),
            'timestamp' => I('timestamp'),
            'apiSecret' => I('apiSecret'),
            'accessToken' => I('accessToken'));
        if (!verifyInPut($param)) {
            die();
        }
    }
    /**
     * 把数据存入数据库
     */
    public function getData()
    {
        $data = json_decode(file_get_contents('Public/text.txt'), true);
        $model = M('speed');
        $model->query('TRUNCATE TABLE qj_speed');
        for ($index = 1; $index < count($data); $index++) {
            $arr1 = array('recordId' => $data[$index - 1]['rideExperience_id'],
                'latitude' => $data[$index - 1]['latitude'],
                'longtitude' => $data[$index - 1]['longtitude'],
                'recordTime' => $data[$index - 1]['recordTime']);
            $arr2 = array('recordId' => $data[$index]['rideExperience_id'],
                'latitude' => $data[$index]['latitude'],
                'longtitude' => $data[$index]['longtitude'],
                'recordTime' => $data[$index]['recordTime']);
            if ($arr1 != $arr2) {
                $arr1Time = $arr1['recordTime'];
                array_pop($arr1);
                $arr = $arr1;
                $arr['distance'] = self::getDistance($arr1['latitude'], $arr1['longtitude'], $arr2['latitude'], $arr2['longtitude']);
                $arr['interval'] = ($arr2['recordTime'] - $arr1Time) / 1000;
                $arr['speed1'] = $arr['distance'] / $arr['interval'];
                $model->data($arr)->add();
            }
        }
    }
    /**
     * 采用最小二乘法拟合斜率、速度
     */
    public function test()
    {
        $model = M('speed');
        //速度栈
        $speedStack = array();
        $data = $model->field('id,speed1')->where('recordId=1')->select();
        //dump($data);exit();
        foreach ($data as $value) {
            array_push($speedStack, $value['speed1']);
            if (count($speedStack) === N - 1) {
                $sys = $this->_leastSquare($speedStack);
                $speed = $sys['a'] + $sys['b'] * (N - 1);
                $k.=$sys['b'] . ',';
                $speed2.= $speed . ',';
                $speed1.= $value['speed1'] . ',';
                array_shift($speedStack);
                //$model->field('speed2,k')->where('id=' . $value['id'])->save(array('speed2' => $speed2, 'k' => $sys['b']));
            }
        }
        echo 'speed1=['.  substr($speed1, 0,  strlen($speed1)-1).']<br>';
        echo 'speed2=['.substr($speed2, 0,  strlen($speed2)-1).']<br>';
        echo 'k=['.substr($k, 0,  strlen($k)-1).']<br>';
    }

    public function aaa()
    {
        $model = M(speed);
        $data1 = $model->field('k')->where('recordId=1')->select();
        $data2 = $model->field('speed2')->where('recordId=1')->select();
        $data3 = $model->field('speed1')->where('recordId=1 and id>3')->select();
        foreach ($data1 as $value) {
            $k.=$value['k'] . ',';
        }
        $k = substr($k, N-2, strlen(k)-N+1);
        echo 'k=[' . $k . ']<br>';
        foreach ($data2 as $value) {
            $speed2.= $value['speed2'] . ',';
        }
        $speed2 = substr($speed2, N-2, strlen($speed2) - N+1);
        echo 'speed2=[' . $speed2 . ']<br>';
        foreach ($data3 as $value) {
            $speed1.= $value['speed1'] . ',';
        }
        $speed1 = substr($speed1, 0, strlen($speed1) - 1);
        echo 'speed1=[' . $speed1 . ']';
    }

    /**
     * 最小二乘法
     */
    private function _leastSquare($speedStack = array())
    {
        foreach ($speedStack as $key => $value) {
            $powAddX+=pow($key, 2);   //计算x的平方和
            $addX+=$key;              //计算x的和
            $addXY+=$key * $value;    //计算xy的和
            $addY+=$value;            //计算y的和
        }
        $a = ($powAddX * $addY - $addX * $addXY) / (count($speedStack) * $powAddX - pow($addX, 2));
        $b = (count($speedStack) * $addXY - $addX * $addY) / (count($speedStack) * $powAddX - pow($addX, 2));

        return array('a' => $a, 'b' => $b);
    }

    /**
     *
     * @param  type $lat1 点1的纬度
     * @param  type $lng1 点1的经度
     * @param  type $lat2 点2的纬度
     * @param  type $lng2 点2的经度
     * @return type
     */
    private function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6378137;
        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;
        $lat2 = ($lat2 * pi() ) / 180;
        $lng2 = ($lng2 * pi() ) / 180;
        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;

        return $calculatedDistance;
    }

}
