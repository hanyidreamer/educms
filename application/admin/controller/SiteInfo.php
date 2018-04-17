<?php
/**
 * Created by PhpStorm.
 * User: tzx
 * Date: 2016/10/25
 * Time: 15:43
 */
namespace app\admin\controller;

use think\Request;
use app\common\model\Site;
use app\common\model\Admin;
use app\base\controller\Upload;

class SiteInfo extends AdminBase
{
    /**
     * 我的网站列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $admin_username = session('username');
        $admin_info = Admin::get(['username'=>$admin_username]);
        $admin_id = $admin_info['id'];

        $site_info_sql['admin_id'] = $admin_id;
        $site_info_data = new Site();
        $site_info = $site_info_data->where($site_info_sql) ->paginate(10);
        $site_count = count($site_info);

        $this->assign('site_info',$site_info);
        $this->assign('site_count',$site_count);

        return $this->fetch($this->template_path);
    }

    /**
     * 编辑网站基本信息
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function info()
    {
        $site_id = $this->site_id;
        $site_info = Site::get($site_id);
        // 获取站长名称
        $admin_id = $site_info['admin_id'];
        $admin_info = Admin::get($admin_id);

        $site_info['admin_username'] = $admin_info['username'];

        $this->assign('site_info',$site_info);
        return $this->fetch($this->template_path);

    }

    /**
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function save(Request $request)
    {
        // 获取icon文件
        $file_icon = $request->file('icon');
        if(!empty($file_icon)){
            $local_icon = $file_icon->getInfo('tmp_name');
            $icon_filename = $file_icon->getInfo('name');
            $icon_file_info = new Upload();
            $post_icon=$icon_file_info->qcloud_file($local_icon,$icon_filename);
        }

        // 获取logo文件
        $file_logo = $request->file('logo');
        if(!empty($file_logo)) {
            $local_logo = $file_logo->getInfo('tmp_name');
            $logo_filename = $file_logo->getInfo('name');
            $logo_file_info = new Upload();
            $post_logo = $logo_file_info->qcloud_file($local_logo, $logo_filename);
        }

        // 获取 网站略缩图 thumb文件
        $file_thumb = $request->file('thumb');
        if(!empty($file_thumb)){
            $local_thumb = $file_thumb->getInfo('tmp_name');
            $thumb_filename = $file_thumb->getInfo('name');
            $thumb_file_info = new Upload();
            $post_thumb=$thumb_file_info->qcloud_file($local_thumb,$thumb_filename);
        }

        // 获取 二维码图片文件
        $file_qrcode = $request->file('qrcode');
        if(!empty($file_qrcode)){
            $local_qrcode = $file_qrcode->getInfo('tmp_name');
            $qrcode_filename = $file_qrcode->getInfo('name');
            $qrcode_file_info = new Upload();
            $post_qrcode = $qrcode_file_info->qcloud_file($local_qrcode,$qrcode_filename);
        }

        $post_id= $request->post('id');
        $post_title= $request->post('title');
        $post_home_title= $request->post('home_title');
        $post_desc= $request->post('desc');
        $post_keywords= $request->post('keywords');

        $post_stats = $request->post('stats');
        $post_icp = $request->post('icp');
        $post_copyright = $request->post('copyright');

        if($post_title=='' or $post_id==''){
            $this->error('域名和网站名称不能为空');
        }

        $user = Site::get($post_id);
        $user['title'] = $post_title;
        $user['home_title'] = $post_home_title;
        $user['desc'] = $post_desc;
        $user['keywords'] = $post_keywords;
        if(!empty($post_icon)){
            $user['icon'] = $post_icon;
        }
        if(!empty($post_thumb)){
            $user['thumb'] = $post_thumb;
        }
        if(!empty($post_logo)){
            $user['logo'] = $post_logo;
        }
        if(!empty($post_qrcode)){
            $user['qrcode'] = $post_qrcode;
        }

        $user['stats'] = $post_stats;
        $user['icp'] = $post_icp;
        $user['copyright'] = $post_copyright;

        if ($user->save()) {
            $this->success('保存网站信息成功', '/admin/site_info/info');
        } else {
            $user->getError();
        }

    }

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function contact()
    {
        $site_id = $this->site_id;
        $site_info = Site::get($site_id);

        $this->assign('site_info',$site_info);
        return $this->fetch($this->template_path);
    }

    /**
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function contact_save(Request $request)
    {
        $post_id= $request->post('id');
        $post_tel= $request->post('tel');
        $post_qq= $request->post('qq');
        $post_email= $request->post('email');
        $post_address= $request->post('address');

        $user = Site::get($post_id);
        $user['tel'] = $post_tel;
        $user['qq'] = $post_qq;
        $user['email'] = $post_email;
        $user['address'] = $post_address;
        if ($user->save()) {
            $this->success('保存网站信息成功', '/admin/site_info/contact');
        } else {
            $user->getError();
        }
    }

}