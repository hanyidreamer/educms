<?php
/**
 * Created by PhpStorm.
 * User: tanzhenxing
 * Date: 2017/11/13
 * Time: 14:21
 */
namespace app\index\controller;

use think\Image;
use think\Request;
use Endroid\QrCode\QrCode as QrCodeMode;

class ShareImages extends Base
{
    public function index()
    {
        // $image = Image::open(request()->file('image'));
        $image = Image::open('../web/images/bg.jpg');

        // 返回图片的宽度
        $width = $image->width();
        // 返回图片的高度
        $height = $image->height();
        // 返回图片的类型
        $type = $image->type();
        // 返回图片的mime类型
        $mime = $image->mime();
        // 返回图片的尺寸数组 0 图片宽度 1 图片高度
        $size = $image->size();

        //将图片裁剪为300x300并保存为crop.png
        // $image->crop(300, 300)->save('../web/images/crop.jpg');
        //将图片裁剪为300x300并保存为crop.png
        // $image->crop(300, 300,100,30)->save('../web/images/crop.png');

        // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png
       // $image->thumb(150, 150)->save('../web/images/thumb.png');

        // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png
        // $image->thumb(150,150,\think\Image::THUMB_CENTER)->save('../web/images/thumb.png');

        // 给原图左上角添加水印并保存water_image.png
        // $image->water('../web/images/ggk_bg.png')->save('../web/images/water_image.png');
        // $image->open($images[$i])->water($water,$location,0)->save($images[$i]);
        $upload_img = '../web/images/bg.jpg';
        $save_img = '../web/images/bg-save.jpg';
        $water_img = '../web/images/erweima.png';
        $location = array(220,750);
        // $image->open($upload_img)->water($water_img,$location)->save($save_img);

        // 添加文字水印
        // 给原图左上角添加水印并保存water_image.png
        $text = '十年磨一剑 - 为API开发设计的高性能框架';
        $font_path = '../vendor/endroid/qrcode/assets/noto_sans.otf';
        $font_size = '20';
        $font_color = '#ffffff';
        $locate = array(50,400); // 起始位置数组
        $image_save_path = '../web/images/text_image.png';
        // $image->text($text,$font_path,$font_size,$font_color,$locate)->save($image_save_path);

        $image = Image::open($upload_img)->water($water_img,$location)->text($text,$font_path,$font_size,$font_color,$locate)->save($save_img);

    }

    public function make_water(Request $request)
    {
        $domain = $request->domain();
        $font_path = '../vendor/endroid/qrcode/assets/noto_sans.otf'; // 字体文件路径
        // 获取二维码图片路径
        $qr_code_url = $domain.'/test/share_images/qr_code/mid/3'; // 原始图片
        $mid = preg_replace('/http(.*)mid\//','',$qr_code_url);
        $qr_code_file = file_get_contents($qr_code_url);
        $qr_code_file_path = '../web/images/qr_code/'.$mid.'.jpg';
        file_put_contents($qr_code_file_path,$qr_code_file);
        $upload_img = '../web/images/bg.jpg';

        $save_img = '../web/images/bg-save.jpg'; // 处理之后的图片

        // 文字水印参数
        $text = '这里是文字水印';
        $font_size = '20';
        $font_color = '#ffffff';
        $text_location = array(50,400); // 起始位置数组

        // 水印图片参数设置
        $water_img = $qr_code_file_path; // 水印图片
        $water_location = array(220,750);
        $image = Image::open($upload_img)->water($water_img,$water_location)->text($text,$font_path,$font_size,$font_color,$text_location)->save($save_img);

        echo '<img src="'.$save_img.'" />';


    }

    public function qr_code(Request $request)
    {
        $username = $this->username ;
        $this->assign('username',$username);
        $site_id = $this->site_id;
        $template_path = $this->template_path;
        $mid = $this->mid;
        $this->assign('mid',$mid);

        // 生成二维码
        $text = '长按二维码，识别领取';
        $font_path = '../vendor/endroid/qrcode/assets/noto_sans.otf';
        $img_size = 130;
        $font_size = 10;
        $bg_color = ['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0];
        $img_color = ['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0];
        $domain = $request->domain();
        $base_url = $request->baseUrl();
        $url = $domain.$base_url;

        $qrCode=new QrCodeMode();
        $qrCode->setText($url)
            ->setSize($img_size)//大小
            ->setLabelFontPath($font_path)
            ->setErrorCorrectionLevel('high')
            ->setForegroundColor($bg_color)
            ->setBackgroundColor($img_color)
            ->setLabel($text)
            ->setLabelFontSize($font_size);
        header('Content-Type: '.$qrCode->getContentType());
        echo $qrCode->writeString();
        exit;
    }

}