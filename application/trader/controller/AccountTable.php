<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2018/5/2
 * Time: 14:40
 */
namespace app\trader\controller;

use app\base\controller\CreateFolder;
use think\Controller;
use think\Db;

class AccountTable extends Controller
{
    /**
     * @param string $account
     */
    public function check($account = '')
    {
        // 判断数据表是否存在
        $show_table_result = Db::query('SHOW TABLES'); // 获取所有的表名
        $table_status = 0;
        $table_name = 'account_' . $account;
        foreach ($show_table_result as $key=>$data) {
            if($data['Tables_in_trade_order'] == $table_name){
                $table_status = 1;
                break;
            }
        }

        // 如果数据库表不存在，创建新表
        if($table_status==0){
            $sql_file_path = '../runtime/account';

            $data_sql_path = new CreateFolder();
            $data_sql_path->path($sql_file_path);

            $sql_file = $sql_file_path .'\data.sql';

            $sql_info = file_get_contents($sql_file);
            $sql_info = str_replace('fin_account_', 'account_'.$account, $sql_info);
            Db::query($sql_info);
        }
    }
}