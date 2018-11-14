<?php
namespace Service\Controller;
use Think\Controller;

class ImageController extends Controller
{
    public function thumb() {
        //动态生成缩略图
        $dstPath = I('file');
        if (!empty($dstPath)) {
            $dstPath = './'.$dstPath;
            $match = array();
            preg_match('#-\d+-\d+#', $dstPath, $match);
            $size = $match[0];
            $srcPath = strtr($dstPath, array($size => ''));
            $srcPath = strtr($srcPath, array('thumb'=> 'image'));
            //$srcPath =  $srcPath;
            if (is_file($srcPath)) {
                $dstDir = dirname($dstPath);
                if (!is_dir($dstDir))
                    mkdir($dstDir, 0755, true);
                $info = explode('-', $size);
                $width = $info[1];
                $height = $info[2];

                $image = new \Think\Image();
                $image->open($srcPath);
                $image->thumb($width, $height, \Think\Image::IMAGE_THUMB_FILLED);
                $image->save($dstPath);
                $mime = $image->mime();
                //输出
                header('content-type:'.$mime);
                echo file_get_contents($dstPath);
                exit;
            }
        }
        header('HTTP/1.1 404 Not Found');

    }
}