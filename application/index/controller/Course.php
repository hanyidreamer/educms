<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/8/28
 * Time: 13:39
 */
namespace app\index\controller;

use app\base\model\CourseCategory;
use app\base\model\Course as CourseModel;
use app\base\controller\BrowserCheck;

class Course extends Base
{
    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $course_info = new CourseModel();
        $pages = 10;
        $course_data = $course_info->where(['site_id'=>$this->site_id])-> order('sort asc') -> paginate($pages);
        $this->assign('course',$course_data);

        // 判断是否为微信浏览器
        $user_browser = new BrowserCheck();
        $user_browser_info = $user_browser->info();
        if($user_browser_info=='wechat_browser'){
            /*
            $weixin_user_info = new Weixin();
            $openid = $weixin_user_info->info($site_id,$mid);
            $this->assign('openid',$openid);
            // 获取会员信息
            $member_weixin_info = MemberWeixin::get(['openid'=>$openid]);
            $member_weixin_id = $member_weixin_info['id'];

            $member_info = Member::get(['weixin_id'=>$member_weixin_id]);
            if(!empty($member_info)){
                $member_info['name'] = $member_info['real_name'];

                $this->assign('member_data',$member_info);
            }else{
                $member_weixin_info['name'] = $member_weixin_info['nickname'];
                $this->assign('member_data',$member_weixin_info);
            }
            */
            return $this->fetch($this->template_path);
        }

        return $this->fetch($this->template_path);
    }

    /**
     * @param string $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function category($id='')
    {
        // 文章内容  判断id是否为数字
        if(is_numeric($id)){
            // id 为数字，按照id查询对应的文章数据
            $article_data = CourseCategory::get($id);

            // 判断查询结果是否为空
            if(!empty($article_data)){
                // 不为空
                $article_unique_code = $article_data['unique_code'];
                if(!empty($article_unique_code)){
                    // 不为空,301重定向到自定义文章后缀的网址
                    $this->redirect('/index/course/category/id/'.$article_unique_code,301);
                }
                else{
                    // 为空,没有定义文章后缀的网址
                    $this->error('文章不存在','/404.html');
                }
            }
            else{
                // 为空，跳转到404错误页面
                $this->error('文章不存在','/404.html');
            }
        }
        // id 不为数字，按照unique_code查询对应的文章数据
        else{
            $article_data = CourseCategory::get(['unique_code'=>$id]);
            $category_id = $article_data['id'];

            // 判断查询结果是否为空
            if(!empty($article_data)){
                $this->assign('data',$article_data);
                // 资讯列表
                $article_info = new CourseModel();
                $article_data = $article_info->where(['site_id'=>$this->site_id,'category_id'=>$category_id])-> order('sort asc') ->limit(10) ->select();
                $this->assign('course',$article_data);

            }
            else{
                // 为空，跳转到404错误页面
                $this->error('文章不存在','/404.html');
            }
        }

        return $this->fetch($this->template_path);
    }

    /**
     * @param string $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function view($id='')
    {
        if(is_numeric($id)){
            // id 为数字，按照id查询对应的文章数据
            $article_data = CourseModel::get($id);
            // 判断查询结果是否为空
            if(!empty($article_data)){
                // 不为空
                $article_unique_code = $article_data['unique_code'];
                if(!empty($article_unique_code)){
                    // 不为空,301重定向到自定义文章后缀的网址
                    $this->redirect('/course/'.$article_unique_code,301);
                }
                else{
                    // 为空,没有定义文章后缀的网址
                    $this->error('文章不存在','/404.html');
                }
            }
            else{
                // 为空，跳转到404错误页面
                $this->error('文章不存在','/404.html');
            }
        }
        // id 不为数字，按照unique_code查询对应的文章数据
        else{
            $article_data = CourseModel::get(['unique_code'=>$id]);
            // 判断查询结果是否为空
            if(!empty($article_data)){
                $category_id = $article_data['category_id'];
                $course_category_data = CourseCategory::get($category_id);

                $this->assign('course_category',$course_category_data);

                $this->assign('course',$article_data);

            }
            else{
                // 为空，跳转到404错误页面
                $this->error('文章不存在','/404.html');
            }
        }


        return $this->fetch($this->template_path);
    }

}