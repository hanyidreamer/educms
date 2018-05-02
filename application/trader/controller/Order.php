<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/4/14
 * Time: 23:35
 */
namespace app\trader\controller;

use think\Controller;
use think\Db;
use app\base\model\Curl;

ini_set('max_execution_time','0');

class Order extends Controller
{
    /**
     * @param string $account
     * @throws \think\Exception
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function view($account = '')
    {
        $mid = 1;
        $order_table_name = "account_".$account;

        // 判断account 是否为大于等于3位的数字
        if(is_numeric($account) and strlen($account)>=3){
            $account_table = new AccountTable();
            $account_table->check($account);
        }

        // 采集订单数据开始
        $account_url = 'http://ftp.qianbailang.com/' . $account . '/statement.htm';
        // curl基础信息配置
        $timeout = 300;
        $user_agent = 'Mozilla/4.0+(compatible;+MSIE+6.0;+Windows+NT+5.1;+SV1)';

        // 获取远程文件是否存在
        $file_url = 'http://ftp.qianbailang.com/file_info.php?id=' . $account;
        $my_json = file_get_contents($file_url,FILE_USE_INCLUDE_PATH);

        // 获取code
        preg_match('/"code":"(.*)"/isU', $my_json, $code_info);
        $code =$code_info[1];
        if($code=='0'){
            echo '订单文件不存在<br />';
            exit;
        }

        // 获取full
        preg_match('/"full":"(.*)"/isU', $my_json, $full_info);
        $full =$full_info[1];
        if($full=='0'){
            echo '订单文件不完整<br />';
            exit;
        }

        // 获取 file_size
        preg_match('/"file_size":"(.*)"/isU', $my_json, $file_size_info);
        // $file_size = $file_size_info[1];

        // 获取 order_num
        preg_match('/"order_num":"(.*)"/isU', $my_json, $order_num_info);
        $order_num =$order_num_info[1];
        echo '一共发现 '.$order_num.'个订单数据<br />';

        // 获取 update_time
        preg_match('/"update_time":"(.*)"/isU', $my_json, $update_time_info);
        $update_time =$update_time_info[1];
        $now_time = time();
        $this_time = $now_time-$update_time;
        if($this_time<1){
            echo '最近更新时间：'.date('Y-m-d h:m:s',$update_time).'，一分钟前刚刚更新过了，请稍后再更新！';
            exit;
        }

        // 判断本地缓存文件是否存在
        $list_file = '../runtime/account/'.$account.".html";
        if (file_exists($list_file)) {
            $file_update_time = filemtime($list_file);
            $now_time = time();
            $update_time = $now_time - $file_update_time;
            $file_size = filesize($list_file);
            if ($update_time > 300 or $file_size < 1000) {
                $list_data = new Curl();
                $html_list_data = $list_data->get_info($account_url, $timeout, $user_agent);
                file_put_contents($list_file, $html_list_data);
            } else {
                $html_list_data = file_get_contents($list_file);
            }
        } else {
            $list_data = new Curl();
            $html_list_data = $list_data->get_info($account_url, $timeout, $user_agent);
            file_put_contents($list_file, $html_list_data);
        }

        // 采集数据
        $html_list_data = str_replace(' bgcolor=#E0E0E0', '', $html_list_data);
        $html_list_data = str_replace('<td colspan=10 align=left></td>', '<td colspan=10 align=left>0</td>', $html_list_data);
        // 判断account 是否一致
        preg_match('/<td colspan=2><b>Account: (.*)<\/b><\/td>/isU', $html_list_data, $get_account_info);
        $get_account = $get_account_info[1];
        if($get_account == $account){
            // 账户号一致
            echo '<br />当前账户：'.$get_account.'<br/>';
        }else{
            echo $account.'不存在<br />';
            exit;
        }

        // 获取账户名称
        preg_match('/<td colspan=5><b>Name: (.*)<\/b><\/td>/isU', $html_list_data, $name_info);
        $get_name = $name_info[1];
        $get_name = str_replace(' ', '', $get_name);
        echo $get_name.'<br/>';

        // 获取外汇服务商名称
        preg_match('/<div style=\"font: 20pt Times New Roman\"><b>(.*)<\/b><\/div><br>/isU', $html_list_data, $company_info);
        $company_name = $company_info[1];
        echo $company_name.'<br/>';

        // 获取currency
        preg_match('/<td colspan=2><b>Currency: (.*)<\/b><\/td>/isU', $html_list_data, $currency_info);
        $get_currency = $currency_info[1];
        echo $get_currency.'<br/>';

        // 获取leverage
        preg_match('/<td colspan=2><b>Leverage: (.*)<\/b><\/td>/isU', $html_list_data, $leverage_info);
        $get_leverage = $leverage_info[1];
        echo $get_leverage.'<br/>';

        // 获取update time
        preg_match('/<td colspan=3 align=right><b>(.*)<\/b><\/td><\/tr>/isU', $html_list_data, $update_time_info);
        $get_update_time = $update_time_info[1];
        $get_update_time = str_replace(array("January","February","March","April","May","June","July","August","September","October","November","December"),array("-01-","-02-","-03-","-04-","-05-","-06-","-07-","-08-","-09-","-10-","-11-","-12-"),$get_update_time);
        $get_update_time = str_replace(' ', '', $get_update_time);
        $get_update_time = str_replace(',', ' ', $get_update_time);
        $get_update_time = strtotime($get_update_time);
        echo $get_update_time.'<br/>';

        // 将数据写入到数据库 fin_trade_account
        $my_trade_account_list = Db::connect('firdc')->query("select * from fin_trade_account where account=$account");
        if($my_trade_account_list){
            // account 在数据库中存在
            foreach($my_trade_account_list as $key=>$data)
            {
                $mid = $data['mid'];
                if($mid=="" or $mid==0){
                    $mid = 1;
                }
                $tsid =$data['tsid'];
                if($tsid=="" or $tsid==0){
                    $tsid = 3;
                }
                $msid =$data['msid'];
                if($msid=="" or $msid==0){
                    $msid = 9;
                }
                // $status =$data['status'];

                $account_type =$data['account_type'];
                if($account_type=="" or $account_type==0){
                    $account_type = 1;
                }
                $name =$data['name'];
                if($name=="" or $name==0){
                    $name = $get_name;
                }
                $company =$data['company'];
                if($company=="" or $company==0){
                    $company = $company_name;
                }
                $currency =$data['currency'];
                if($currency=="" or $currency==0){
                    $currency = $get_currency;
                }
                $leverage =$data['leverage'];
                if($leverage=="" or $leverage==0){
                    $leverage = $get_leverage;
                }

                // 更新数据库中的记录
                Db::connect('firdc')->table('fin_trade_account')
                    ->where('account', $account)
                    ->update(['mid' => $mid,'tsid'=>$tsid,'msid'=>$msid,'account_type'=>$account_type,'name'=>$name,'currency'=>$currency,'company'=>$company,'leverage'=>$leverage,'update_time'=>time()]);

            }
        }
        else{
            // account 在数据库中不存在,插入新的记录
            Db::connect('firdc')->table('fin_trade_account')
                ->insert(['account' => $account,'mid' => $mid,'tsid'=>3,'msid'=>9,'account_type'=>1,'name'=>$get_name,'currency'=>$get_currency,'company'=>$company_name,'leverage'=>$get_leverage,'status'=>1,'create_time'=>time(),'update_time'=>time()]);

        }

        // 获取订单列表
        preg_match_all('/<tr align=right>(.*?)<\/tr>/is', $html_list_data, $close_order_list, PREG_PATTERN_ORDER);
        $close_order_list_info = $close_order_list[1];

        foreach ($close_order_list_info as $key => $data) {

            $get_my_order=array();
            preg_match_all('/<td.*?>(.+?)<\/td>/', $data, $close_order);
            foreach ($close_order[1] as $mydata) {
                $get_my_order[]=$mydata;
            }
            $my_order_count =  count($get_my_order);

            // 匹配入金订单数据
            if($my_order_count==5){
                // 规则2
                if($get_my_order[2]=="balance")
                {
                    $get_close_order_result = Db::table($order_table_name)->where('order_id',$get_my_order[0])->find();
                    if($get_close_order_result){
                        // echo '成交订单:'.$get_my_order[0].'---已存在<br/>';

                    }else{
                        $get_my_order[4] = str_replace(' ', '', $get_my_order[4]);
                        // 将记录插入到数据库中
                        Db::table($order_table_name)->insert(["mid"=>1,"aid"=>1,"order_id"=>$get_my_order[0],"open_time"=>$get_my_order[1],"order_type"=>$get_my_order[2],"trade_lots"=>0,"trade_symbol"=>0,"open_price"=>0,"stop_loss"=>0,"take_profit"=>0,"close_time"=>0,"close_price"=>0,"commission"=>0,"taxes"=>0,"swap"=>0,"profit"=>$get_my_order[4],"comment"=>"","status"=>1,"create_time"=>time(),"update_time"=>time()]);
                        echo '订单:'.$get_my_order[0].'---已经插入<br/>';
                    }

                }
            }
            // 匹配 订单记录
            if($my_order_count==14){
                // 匹配 订单数据
                if($get_my_order[2]=="buy" or $get_my_order[2]=="sell"){
                    // 规则3
                    $get_close_order_result = Db::table($order_table_name)->where('order_id',$get_my_order[0])->find();
                    if($get_close_order_result){
                        // echo '成交订单:'.$get_my_order[0].'---已存在<br/>';
                        // 判断订单是否已经平仓
                        $my_close_time=$get_close_order_result['close_time'];

                        $close_time = $get_my_order[8];

                        if($close_time!='' and $my_close_time=='0000-00-00 00:00:00'){
                            Db::table($order_table_name)->where('order_id',$get_my_order[0])->update(["mid"=>$mid,"aid"=>1,"open_time"=>$get_my_order[1],"order_type"=>$get_my_order[2],"trade_lots"=>$get_my_order[3],"trade_symbol"=>$get_my_order[4],"open_price"=>$get_my_order[5],"stop_loss"=>$get_my_order[6],"take_profit"=>$get_my_order[7],"close_time"=>$get_my_order[8],"close_price"=>$get_my_order[9],"commission"=>$get_my_order[10],"taxes"=>$get_my_order[11],"swap"=>$get_my_order[12],"profit"=>$get_my_order[13],"comment"=>"千百浪智能交易系统","status"=>1,"update_time"=>time()]);
                            echo $get_my_order[0].'需要更新--'.$close_time.'--'.$get_my_order[8].'<br />';
                        }


                    }else {
                        $get_my_order[4] = str_replace(' ', '', $get_my_order[4]);
                        $get_my_order[10] = str_replace(' ', '', $get_my_order[10]);
                        $get_my_order[11] = str_replace(' ', '', $get_my_order[11]);
                        $get_my_order[12] = str_replace(' ', '', $get_my_order[12]);
                        $get_my_order[13] = str_replace(' ', '', $get_my_order[13]);

                        if($get_my_order[8]==''){
                            $status = 0;
                            echo $get_my_order[8].'<br />';
                        }else{
                            $status = 1;
                        }
                        Db::table($order_table_name)->insert(["mid"=>$mid,"aid"=>1,"order_id"=>$get_my_order[0],"open_time"=>$get_my_order[1],"order_type"=>$get_my_order[2],"trade_lots"=>$get_my_order[3],"trade_symbol"=>$get_my_order[4],"open_price"=>$get_my_order[5],"stop_loss"=>$get_my_order[6],"take_profit"=>$get_my_order[7],"close_time"=>$get_my_order[8],"close_price"=>$get_my_order[9],"commission"=>$get_my_order[10],"taxes"=>$get_my_order[11],"swap"=>$get_my_order[12],"profit"=>$get_my_order[13],"comment"=>"千百浪智能交易系统","status"=>$status,"create_time"=>time(),"update_time"=>time()]);
                        echo '订单:' . $get_my_order[0] . '---已经插入<br/>';
                    }
                }
            }
        }
    }

    public function import($id){
        // 获取所有的账户列表
        $my_trade_account_list = Db::connect('firdc')->query("select * from fin_trade_account where status=1");
        $num = count($my_trade_account_list)-1;
        $this_account = $my_trade_account_list[$id]['account'];
        $next_id = $id + 1;
        if($next_id<$num){
            $next_account = $my_trade_account_list[$next_id]['account'];
        }else{
            $next_account = '';
        }


        if($id<$num){
            // 采集订单数据开始
            $account_url = 'http://waihui.qianbailang.com/trade/order/index/id/'.$this_account;
            file_get_contents($account_url,FILE_USE_INCLUDE_PATH);

            $this->success('账户：'.$this_account.'的订单数据导入成功，正准备导入下一个账户:'.$next_account, 'http://waihui.qianbailang.com/trade/order/import/id/'.$next_id);

        }else{
            // 采集订单数据开始
            $account_url = 'http://waihui.qianbailang.com/trade/order/index/id/'.$this_account;
            file_get_contents($account_url,FILE_USE_INCLUDE_PATH);
            echo '所有账户的订单数据全部导入完成！';
        }

    }

    public function update(){
        // 获取所有的账户列表
        $my_trade_account_list = Db::connect('firdc')->query("select * from fin_trade_account where status=1");
        $num = count($my_trade_account_list)-1;
        for($i=0;$i<=$num;$i++){
            $this_account = $my_trade_account_list[$i]['account'];

            if($i<$num){
                // 采集订单数据开始
                $account_url = 'http://waihui.qianbailang.com/trade/order/index/id/'.$this_account;
                file_get_contents($account_url,FILE_USE_INCLUDE_PATH);
            }else{
                // 采集订单数据开始
                $account_url = 'http://waihui.qianbailang.com/trade/order/index/id/'.$this_account;
                file_get_contents($account_url,FILE_USE_INCLUDE_PATH);
                echo '所有账户的订单数据全部导入完成！';
            }
        }

    }

    public function day_data($account,$days='0'){
        $order_table_name = "account_" . $account . '_day';
        // 判断account 是否为大于等于7位的数字
        if(is_numeric($account) and strlen($account)>=6) {

            // 判断数据表是否存在，并查询数据
            $show_table_result = Db::query('SHOW TABLES');

            $table_status = "";
            foreach ($show_table_result as $key => $data) {
                if ($data['Tables_in_trade_order'] == $order_table_name) {
                    // 数据表已经存在,跳出循环
                    // echo '数据表:【'.$order_table_name.'】已存在<br />';
                    $table_status = 1;
                    break;
                } else {
                    $table_status = 0;
                }
            }


            // 如果数据库表不存在，创建新表
            if ($table_status == 0) {
                $sql_file = '../runtime/account/date.sql';
                $sql_info = file_get_contents($sql_file);
                $sql_info = str_replace('fin_account_', 'account_' . $account . '_day', $sql_info);
                Db::query($sql_info);
                // echo '数据表不存在，重新创建数据表：'.$order_table_name;
            }
        }

        // 找出 第一张交易订单的日期
        $first_day_order = Db::connect('trade_order')->query("SELECT * FROM account_$account WHERE order_type='sell' OR order_type='buy' ORDER BY open_time ASC LIMIT 1;");
        if(empty($first_day_order)){
            echo '没有数据';
            exit;
        }

        $first_day = strtotime($first_day_order[0]['open_time']);
        $first_day = date('Y-m-d',$first_day);
        // 获取今天的日期
        // $today = date('Y-m-d',time());
        $last_day = date("Y-m-d",strtotime("-1 day"));

        $first_day_time = strtotime($first_day);
        // $today_time = strtotime($today);
        $last_day_time  = strtotime($last_day);
        $day_num = round(($last_day_time-$first_day_time)/3600/24)+1;
        // days = 1 更新前一天的数据，days=0 更新所有的数据
        if($days!=0){
            $day_num = $days;
        }
        for($i=0;$i<$day_num;$i++){
            $this_num = $i-$day_num;
            $this_day_time = strtotime("$this_num day");
            $this_day = date('Y-m-d',$this_day_time);

            $order_record_list = Db::connect('trade_order')->query("SELECT * FROM  account_$account WHERE  `close_time` LIKE  '%$this_day%'");

            $my_order_lots_data = array();
            $my_order_commission_data = array();
            $my_order_taxes_data = array();
            $my_order_swap_data = array();
            $my_order_profit_data = array();
            foreach ($order_record_list as $data) {
                $my_order_lots_data[]=$data['trade_lots'];
                $my_order_commission_data[]=$data['commission'];
                $my_order_taxes_data[]=$data['taxes'];
                $my_order_swap_data[]=$data['swap'];
                $my_order_profit_data[]=$data['profit'];
            }
            // 订单记录数
            $my_order_num = count($order_record_list);
            $my_order_lots =  array_sum($my_order_lots_data);
            $my_order_commission =  array_sum($my_order_commission_data);
            $my_order_taxes =  array_sum($my_order_taxes_data);
            $my_order_swap =  array_sum($my_order_swap_data);
            $my_order_profit=  array_sum($my_order_profit_data);

            // 判断订单记录是否存在
            $order_day_info = Db::connect('trade_order')->query("SELECT * FROM $order_table_name WHERE date='$this_day';");

            if(empty($order_day_info)){
                Db::table($order_table_name)->insert(["date"=>$this_day,"order_num"=>$my_order_num,"lots"=>$my_order_lots,"commission"=>$my_order_commission,"taxes"=>$my_order_taxes,"swap"=>$my_order_swap,"profit"=>$my_order_profit,"status"=>1,"create_time"=>time(),"update_time"=>time()]);
                echo $this_day . '订单统计数据---已经插入<br/>';
            }else{
                // echo '有数据更新';
                if($my_order_num!=$order_day_info[0]['order_num']){
                    Db::table($order_table_name)->where(["date"=>$this_day])->update(["order_num"=>$my_order_num,"lots"=>$my_order_lots,"commission"=>$my_order_commission,"taxes"=>$my_order_taxes,"swap"=>$my_order_swap,"profit"=>$my_order_profit,"status"=>1,"create_time"=>time(),"update_time"=>time()]);
                    echo $order_day_info[0]['date'].'订单统计数据已经更新<br />';
                }
            }
        }
        echo '所有数据全部更新完毕！';
    }

    public function day_update()
    {
        // 获取所有的账户列表
        $my_trade_account_list = Db::connect('firdc')->query("select * from fin_trade_account where status=1");
        $num = count($my_trade_account_list)-1;
        for($i=0;$i<=$num;$i++){
            $this_account = $my_trade_account_list[$i]['account'];

            if($i<$num){
                // 采集订单数据开始
                $account_url = 'http://waihui.qianbailang.com/trade/order/day_data/account/'.$this_account.'/days/1';
                file_get_contents($account_url,FILE_USE_INCLUDE_PATH);
            }else{
                // 采集订单数据开始
                $account_url = 'http://waihui.qianbailang.com/trade/order/day_data/account/'.$this_account.'/days/1';
                file_get_contents($account_url,FILE_USE_INCLUDE_PATH);
            }
        }
    }

}

