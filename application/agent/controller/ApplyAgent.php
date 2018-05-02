<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/9/21
 * Time: 23:28
 */
namespace app\agent\controller;

use app\base\controller\Base;
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
     */
    public function save()
    {
        $post_data = $this->request->param();
        if(empty($post_data['contact_person'])){
            $this->error('姓名不能为空');
        }

        $data = new AgentApplyModel;
        $data_array = array('contact_person','tel','city');
        $data_save = $data->allowField($data_array)->save($post_data);
        if ($data_save) {
            echo '<h1>成功提交申请，请等待管理员审核！</h1>';
        } else {
            $this->error('提交申请失败');
        }
    }
}