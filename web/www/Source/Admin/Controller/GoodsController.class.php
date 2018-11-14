<?php
namespace Admin\Controller;
class GoodsController extends BaseController {
    public function index() {
        $model = D('Goods');
        $where = '1=1';
        $oe = I('oe');
        $brand = I('brand');
        $supid = I('supid');
        $supplier = $this->cacheSvc->getData('SupplierMlt');
        if (!empty($supid)) {
        	$where .= " AND supid= '$supid'";
        }
        if (!empty($oe)) {
            $where .= " AND oecode LIKE '%{$oe}%'";
        }
        if (!empty($brand))
            $where .= " AND title LIKE '%{$brand}%'";
        $count = $model->where($where)->count();
        $pager = new \Think\Page($count, 20);
        $multi = $pager->show();
        $list = $model->field('*')
                ->where($where)
                ->order("id DESC")
                ->limit($pager->firstRow.','.$pager->listRows)
                ->select();
        foreach($list as $k=>$v) {
            $list[$k]['supplier'] = $supplier[$v['supid']];
        }
        $this->assign('multi', $multi);
        $this->assign('list', $list);
        $this->prepare();
        $this->assign('caption','管理商品信息');
        $this->display();
    }

    public function add() {
        $this->prepare();
        $data = array(
            'hash'=> uniqid2(),
            'price' => '',
            'addtime' => time(),
            'updatetime' => time(),
            'ishot' => 0
        );
        $this->assign('data', $data);
        $this->assign('caption','增加商品信息');
        $this->display('edit');
    }

    public function edit() {
        $id = I('id');
        $model = D('Goods');
        $data = $model->find($id);
        $this->assign('data', $data);
        $this->prepare();
        $this->assign('caption', '编辑商品信息');
        $this->display();
    }

    public function update() {
        $model = D('Goods');
        $data = $model->create();
        $data['forall'] = "$data[forbrand] $data[forbrand2] $data[forvb] $data[foryear]";
        if (empty($data['title']))
            $data['title'] = $data['forall'];
        if (!empty($data['images'])) {
        	$arr = explode(',', $data['images']);
        	$data['image'] = $arr[0];
        }
        if (empty($data['addtime'])) {
            $data['addtime'] = time();
        } else {
            $data['addtime'] = strtotime($data['addtime']);
        }
        if (empty($data['price']))
            $data['price'] = 0;
        $data['updatetime'] = time();
        if (empty($data['id'])) {
            $data['id'] = $model->add($data);
        } else {
            $model->save($data);
        }
        //更新附件信息
        $key = 'Goods/'.$data['id'];
        D('Attachment')->attachTo($data['hash'],$key);

        $this->success('保存成功', U('Goods/index'));
    }

    public function delete() {
        $id = I('id');
        D('Goods')->deleteOne($id);
        $this->success('删除成功', U('Goods/index'));
    }

    public function batch() {
        $id = $_POST['id'];
        if (empty($id))
            $this->error('请选择项目');
            $model = D('Goods');
        $ids = implode($id, ',');
        $op = $_POST['op'];
        switch($op) {
            case 'hot':
                $model->where("id IN($ids)")->save(array('ishot'=>time()));
                break;
            case 'unhot':
                $model->where("id IN($ids)")->save(array('ishot'=>0));
                break;
            case 'delete':
                D('Goods')->deleteBatch($id);
                break;
            case 'up':
                $model->where("id IN($ids)")->save(array('status'=>1));
                break;
            case 'down':
                $model->where("id IN($ids)")->save(array('status'=>0));
                break;
        }
        $this->success('执行成功',U('Goods/index'));
    }
    //导入商品
    public function import() {
    	if (IS_POST) {
			//超时时间 5分钟
			set_time_limit(300);
    		$file = $_FILES['file'];
    		$updir = TEMP_PATH;
	        $dir = "Import";
	        $upload = new \Think\Upload();// 实例化上传类
	        $upload->maxSize = 3145728 ;// 设置附件上传大小
	        $upload->exts = array('xls');// 设置附件上传类型
	        $upload->rootPath = $updir;
	        $upload->savePath = "$dir/"; // 设置附件上传目录
	        $upload->autoSub = false;
	        $info = $upload->uploadOne($_FILES['file']);
	        if($info) {// 上传成功
	        	$fields = array(
	        			'faccode'=>1,
	        			'oecode'=>2,
	        			'forbrand'=>3,
	        			'forbrand2'=>4,
	        			'forvb'=>5,
	        			'foryear'=>6,
	        			'current'=>7,
	        			'voltage'=>8,
	        			'opencoil'=>9,
	        			'resistance'=>10,
	        			'inductance'=>11,
	        			'pulsewidth'=>12,
	        			'sparkenergy'=>13,
	        			'temperature'=>14,
	        			'lifetime'=>15,
	        			'number'=>16,
	        			'price'=>17,
	        			'delivery'=>18,
	        			'images'=>19,
	        			'inssize'=>20,
	        			'content'=>21
	        	);
	        	$data = array();
	        	$data['supid'] = $_POST['supid'];
	        	$data['forbrand'] = $_POST['forbrand'];
	        	$data['forbrand2'] = $_POST['forbrand2'];
	        	$data['forvb'] = $_POST['forvb'];
	        	$data['foryear'] = $_POST['foryear'];
	        	$data['title'] = "$data[forbrand] $data[forbrand2] $data[forvb] $data[foryear]";
	        	$data['status'] = 1;
	           $path = $updir.$info['savepath'].$info['savename'];
	           Vendor("Excel.PHPExcel.IOFactory");
	           $reader = \PHPExcel_IOFactory::createReader('Excel5');
	           try{
	           		$model = D('Goods');
					$attModel=D('Attachment');
	           		$stats = array(
	           				'total'=>0,
	           				'success'=>0,
	           				'error' => 0
	           		); //统计信息
					$workbook = $reader->load($path);
					$sheets = $workbook->getAllSheets();
					$sheet = $sheets[0];
					$sheetname=$sheet->getTitle();
					//处理图片
					$imginfo = array();
					$images= $sheet->getDrawingCollection();
					$arrtmp="";
					$uppath = C('UPLOAD_PATH');
					$savepath = 'image/'.date('Ym').'/';
					$upurl = C('UPLOAD_URL');
					foreach($images as $drawing){
						$class = get_class($drawing);
						if($class=='PHPExcel_Worksheet_MemoryDrawing'){
							$image = $drawing->getImageResource();
							$filename=$drawing->getIndexedFilename();
							$xy=$drawing->getCoordinates();
							if (!empty($xy)) {
								$cell = $sheet->getCell($xy);
								$col = $cell->getColumn();
								$row = $cell->getRow();
								//把图片存起来
								$savename = uniqid2() . '.jpg';
								$filepath = "{$uppath}{$savepath}{$savename}";
								imagejpeg($image, $filepath);
								unset($image);
								$url = "{$upurl}/{$savepath}{$savename}";
								if (!isset($imginfo[$row]))
									$imginfo[$row] = array('hash'=>uniqid2());
								$imginfo[$row][$col][] = $url;
								//保存图片信息
								$att = array(
									'hash' =>$imginfo[$row]['hash'],
									'name' => $filename,
									'savename' => $savename,
									'savepath' => $savepath,
									'ext' => 'jpg',
									'size' => filesize($filepath),
									'addtime'=> time()
								);
								$attModel->add($att);
							}
						}
					}
					$allRow = $sheet->getHighestRow();
					$maxCol = $sheet->getHighestColumn();
					$allCol = \PHPExcel_Cell::columnIndexFromString($maxCol);
					$stats['total'] = $allRow-1;
					$txt = "";
					for($row=2;$row<=$allRow;$row++) {
						$data['image'] = '';
						if (isset($imginfo[$row]['hash']))
							$data['hash'] = $imginfo[$row]['hash'];
						else
							$data['hash'] = uniqid2();
						foreach($fields as $k=>$v) {
							$data[$k] = '';
							$col = $v;
							$cell =$sheet->getCellByColumnAndRow($col, $row);
							$r = $cell->getRow();
							$c = $cell->getColumn();
							if (isset($imginfo[$r][$c])) {
								$data[$k] = implode(',', $imginfo[$r][$c]);
								if ($k=='images')
									$data['image'] = $imginfo[$r][$c][0];
							} else {
								$str = $cell->getFormattedValue();
								if ($str)
									$data[$k] = $str;
							}
						}
						$data['title'] = "$data[forbrand] $data[forbrand2] $data[forvb] $data[foryear]";
						$data['addtime'] = time();
						$id = $model->add($data);
						if ($imginfo[$row]) {
							//更新附件信息
							$key = 'Goods/'.$id;
							$attModel->attachTo($data['hash'],$key);
						}
						$stats['success'] ++;
	           		}
					unset($sheet);
					unset($reader);
					unset($workbook);
					unlink($path);
	           }catch(Exception $e){
	           		$this->error('处理文件失败');
	           }
			   $msg = "导入成功({$stats[success]}/{$stats[total]})";
	           $this->success($msg, U('Goods/index'),5);
	        }else{ // 上传错误提示错误信息
	        	$this->error($upload->getError());
	        }

    	} else {
			$this->assign('caption', '商品导入');
			$this->prepare();
			$this->display();
		}
    }
    protected function prepare() {
    	$supplier = $this->cacheSvc->getData('SupplierLst');
        $carinfo = $this->cacheSvc->getData('CarInfoLst');
        $this->assign('supplier',$supplier);
        $this->assign('carinfo', $carinfo);
    }
}
