<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/6/23
 * Time: 5:44
 */
namespace app\member\controller;

use app\base\controller\Base;
use app\base\controller\BrowserCheck;
use app\base\controller\Wechat;
use app\common\model\WechatOfficialAccounts;
use EasyWeChat\Factory;

class PayVip extends Base
{
    /**
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function index()
    {
        // 判断是否为微信浏览器
        $user_browser = new BrowserCheck();
        $user_browser_info = $user_browser->info();
        if($user_browser_info=='wechat'){
            $wechat_data = new Wechat();
            $wechat_data->client();
        }

        return $this->fetch($this->template_path);
    }

    /**
     * @return mixed
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function buy()
    {
        // 判断是否为微信浏览器
        $user_browser = new BrowserCheck();
        $user_browser_info = $user_browser->info();
        if($user_browser_info=='wechat'){
            $wechat_data = new Wechat();
            $wechat_data->client();
        }

        // 微信支付配置信息
        $wx_pay = WechatOfficialAccounts::get(['site_id'=>$this->site_id]);
        $notify_url = '/callback';
        $config = [
            // 必要配置
            'app_id'             => $wx_pay['app_id'],
            'mch_id'             => $wx_pay['merchant_id'],
            'key'                => $wx_pay['api_key'],   // API 密钥
            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            'cert_path'          => '../runtime/key/nlp5/apiclient_cert.pem', // XXX: 绝对路径！！！！
            'key_path'           => '../runtime/key/nlp5/apiclient_key.pem',      // XXX: 绝对路径！！！！
            'notify_url'         => $notify_url,     // 你也可以在下单时单独设置来想覆盖它
        ];

        // 统一下单信息
        $out_trade_no = time().rand(1000,9999);
        $app = Factory::payment($config);
        $result = $app->order->unify([
            'body' => '测试充值',
            'out_trade_no' => $out_trade_no,
            'total_fee' => 39800, // 金额不能有小数
            // 'spbill_create_ip' => '123.12.12.123', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
            'notify_url' => 'https://pay.weixin.qq.com/wxpay/pay.action', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type' => 'JSAPI',
            'openid' => session('open_id'),
        ]);

        // js sdk 支付
        $payment = Factory::payment($config);
        $js_sdk = $payment->jssdk;
        $pay_info = $js_sdk->sdkConfig($result['prepay_id']);
        $this->assign('pay',$pay_info);

        return $this->fetch($this->template_path);
    }
}