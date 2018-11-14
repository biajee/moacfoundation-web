<?php
namespace Home\Controller;
use Think\Controller;
class MiscController extends Controller {
    public function index() {
        $this->show('');
    }
    
    public function verify() {
     
        $verify = new \Think\Verify();
        $verify->fontttf = '4.ttf';
        $verify->length = 4;
        $verify->fontSize = 18;
        $verify->entry();
    }
    
    public function upload() {
        $maxWidth = 1024;
        $maxHeight = 1024;
        $updir = C('UPLOAD_PATH');
		$upurl = C('UPLOAD_URL');
        $dir = empty($_REQUEST['dir'])?$_REQUEST['dir']:'image';
        $upload = new \Think\Upload();// 实例化上传类    
        $upload->maxSize = 3145728 ;// 设置附件上传大小    
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg','doc','docx','xsl','xslx','pdf');// 设置附件上传类型 
        $upload->rootPath = $updir;   
        $upload->savePath = "$dir/"; // 设置附件上传目录
        $upload->subName = array('date','Ym'); 
        $info = $upload->uploadOne($_FILES['imgFile']);    
        if(!$info) {// 上传错误提示错误信息        
            $data = array('error'=>1,'message'=>$upload->getError());    
        }else{// 上传成功
            if (empty($_GET['noscale']) && in_array($info['ext'], array('jpg', 'gif', 'png', 'jpeg'))) { //如果是图片
                $path = "$updir".$info['savepath'].$info['savename'];
                $image = new \Think\Image();
                $image->open($path);
                if ($image->width() > $maxWidth || $image->height() > $maxHeight) { //图片太大，缩略一下
                    $image->thumb(1024, 768, \Think\Image::IMAGE_THUMB_SCALE);
                    $image->save($path);
                }
            }
            $url = "$upurl/".$info['savepath'].$info['savename'];
            $data = array('error'=>0,'url'=>$url);
            $att = array(
                'hash' => $_REQUEST['hash'],
                'name' => $info['name'],
                'savename' => $info['savename'],
                'savepath' => $info['savepath'],
                'ext' => $info['ext'],
                'size' => $info['size'],
                'addtime'=> time()
            );
            M('Attachment')->add($att);
        }
        header('Content-Type:text/html; charset=utf-8');
        die(json_encode($data));
    }
}