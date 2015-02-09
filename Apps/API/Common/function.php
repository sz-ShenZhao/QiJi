<?php

/**
 * 验证输入
 */
function verifyInPut($array=array())
{
    if (!is_array($array)) {
        die();
    }
    $key=array_keys($array);
    $value=  array_values($array);
    for ($i=0;$i<count($key)-1;$i++) {
        $res.=$key[$i].'='.$value[$i].'#';
        $res=substr($res, 0,strlen($res)-1);
    }
    if (md5($res)===$array[$key[count($key)-1]]) {
        return true;
    } else {
        return false;
    }
}
/**
 *获取毫秒时间戳
 * @return type int
 */
function getMillisecond()
{
    list($s1, $s2) = explode(' ', microtime());

    return (float) sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
}
