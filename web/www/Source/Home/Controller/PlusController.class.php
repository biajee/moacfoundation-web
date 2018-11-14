<?php
namespace Home\Controller;
class PlusController extends BaseController {
	public function verify() {
        $verify = new \Think\Verify();
        $verify->fontttf = '4.ttf';
        $verify->length = 4;
        $verify->fontSize = 18;
        $verify->entry('home');
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
                $path = "$updir/".$info['savepath'].$info['savename'];
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
    
    public function sms() {
        $res = array(error=>0, message=>'验证短信已发送，请注意查收');
        /*$myhash = I('hash');
        if (!$this->checkHash($myhash)) {
            $res['error'] = 1;
            $res['message'] = '验证码已过期，请重新获取';
            $this->ajaxReturn($res);
        }*/
        $checkcode = trim($_POST['checkcode']);
        $verify = new \Think\Verify();
        if (!$verify->check($checkcode, 'home')) {
            $res['error'] = 1;
            $res['message'] = '验证码错误';
            $this->ajaxReturn($res);
        }
        $src = I('src');
        if (empty($src))
            $src = 'all';
        $mobile = I('mobile');
        $now = time();
        $key = $src . '_' . $mobile;
        $sess = session($key);
		if (empty($mobile)) {
			$mobile = $this->member['mobile'];
		}
        if (empty($mobile)) {
            $res['error'] = 1;
            $res['message'] = '请输入有效的手机号';
            $this->ajaxReturn($res);
        }
        if (!empty($sess) && $now-$sess<60) {
            $res['error'] = 1;
            $res['message'] = '请'. ($now-$sess) .'秒后重新发送';
            $this->ajaxReturn($res);
        }
        
        $smsSvc = D('Sms', 'Service');
        $code = rand(111111, 999999);
        session('smscode/'.$mobile, $code);
		session('lastmobile', $mobile);
        $time = 5;
        $message = "【天津购房网】您的验证码为{$code}，在{$time}分钟内有效。";
        try {
            $smsSvc->send($mobile, $message);
            session($key, $now);
        } catch (\Exception $e) {
            $res['error'] = 1;
            $res['message'] = $e->getMessage();
        }
        
        //$res['message'] = $code;
        $this->ajaxReturn($res);
    }
}