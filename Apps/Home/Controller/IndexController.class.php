<?php

namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller {

    public function index() {
        $this->display();
    }

    public function test() {
        $this->display();
    }

    /**
     * [pregTest description]
     * @return [type] [description]
     */
    public function pregTest() {
        $debugArray = array(1, 2, 3);
        foreach ($debugArray as $key => $value) {
            $value*=2;
            debug_zval_dump($value);
        }
        dump($debugArray);
        reset($debugArray);
        foreach ($debugArray as $key => &$value) {
            $value*=2;
            debug_zval_dump($value);
        }
        dump($debugArray);
    }

}
