<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/26
 * Time: 14:45
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\Member;
use app\common\model\MemberOrder as MemberOrderModel;
use app\common\model\MemberServicePackage;

class MemberOrder extends AdminBase
{
    /**
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(Request $request)
    {
        $post_username= $request->post('username');
        if($post_username==!''){
            $member_sql['username'] = $post_username;
            $member_list = Member::get(['username'=>$post_username]);
            if(isset($member_list)){
                $mid=$member_list['id'];
                $data_sql['mid'] = $mid;
            }

        }

        $data_sql['status'] = ['<>',''];
        $member_data = new MemberOrderModel();
        $data_list = $member_data->where($data_sql) -> select();
        $data_count = count($data_list);
        foreach($data_list as $data)
        {
            $data->id;
            $data->order_number;
            $mid=$data->mid;

            $members_list = Member::get($mid);
            $username=$members_list['username'];
            $data->username=$username;

            $package_id=$data->package_id;
            $package_list = MemberServicePackage::get($package_id);
            $name=$package_list['name'];
            $data->name=$name;

            $data->payment;
            $data->buy_time;
            $data->expired_time;
            $data->status;
            $data->update_time;

        }
        $this->assign('data_list',$data_list);
        $this->assign('data_count',$data_count);

        return $this->fetch();
    }

}