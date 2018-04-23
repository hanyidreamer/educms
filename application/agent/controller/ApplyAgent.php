<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/9/21
 * Time: 23:28
 */
namespace app\index\controller;

use app\base\controller\Base;
use think\Request;
use app\common\model\AgentApply as AgentApplyModel;

class ApplyAgent extends Base
{
    /**
     * 代理申请
     * @return mixed
     */
    public function index()
    {
        return $this->fetch($this->template_path);
    }

    /**
     * 保存代理申请信息
     * @param Request $request
     */
    public function save(Request $request)
    {
        // $post_site_id = $request->param('site_id');
        $post_contact_person = $request->param('contact_person');
        $post_tel = $request->param('tel');
        // $post_province = $request->param('province');
        $post_city = $request->param('city');

        $data = new AgentApplyModel;
        $data['contact_person'] = $post_contact_person;
        $data['tel'] = $post_tel;
        $data['city'] = $post_city;
        if ($data->save()) {
            echo '<h1>成功提交申请，请等待管理员审核！</h1>';
        } else {
            $this->error('提交申请失败');
        }
    }
}