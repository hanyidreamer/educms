<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/22
 * Time: 11:05
 */
namespace app\base\model;

use think\Model;

class Course extends Model
{
    // 把$teacher_id 修改为教师真实姓名
    protected function getTeacheridAttr($value)
    {
        $teacher_info = Teacher::get($value);
        $value = $teacher_info['real_name'];
        return $value;
    }
    // 把update_time 修改为Y-m-d格式
    protected function getUpdatetimeAttr($update_time)
    {
        return date('Y-m-d', $update_time);
    }

}