<?php
namespace app\admin\controller;

class Index extends AdminController {
	public function _initialize() {
		parent::_initialize();
	}

	public function index() {
		$this->assign('title', '后台首页');
		//房源统计
		$this->view->house_count = model('Houses')->count();
		//用户统计
		$this->view->user_count = model('User')->count();
		// dd($this->view->user_count);
		//房源收藏
		$this->view->collection = model('Houses')->field('sum(collection) as num')->find();
		// dd($this->view->collection);
		$this->view->day_house_count = model('Houses')->where(['cdate'=>['EGT',date('Y-m-d')]])->count();
		//房东统计
		$this->view->user_house_count = model('User')->where('status', 1)->count();
		//锦囊收藏
		$this->view->news_count = model('News')->field('sum(collection) as num')->find();

		//城市
		$list = model('Houses')->field('city as name')->group('city')->select();
		foreach ($list as &$row) {
			$row['value'] = model('Houses')->where('city', $row['name'])->count();
		}
		$this->view->circle = json_encode($list);
		// dd($list);
		//直线条
		$arr = [
			date('Y-m-d'),
			date("Y-m-d",strtotime("-1 day")),
			date("Y-m-d",strtotime("-2 day")),
			date("Y-m-d",strtotime("-3 day")),
			date("Y-m-d",strtotime("-4 day")),
			date("Y-m-d",strtotime("-5 day")),
			date("Y-m-d",strtotime("-6 day")),
		];
		$rows = model('Houses')->field('cdate as day')->group('cdate')->order('cdate DESC')->limit(7)->select();
		// dd($arr);
		$count = [];
		foreach ($arr as $v) {
			$count[] = model('Houses')->where(['cdate'=>['LIKE',$v."%"]])->count();
		}
		$this->view->arr = json_encode(array_reverse($arr));
		$this->view->count = json_encode(array_reverse($count));
		// dd($count);
		return $this->fetch('index', ['version' => THINK_VERSION]);
	}

	public function get_code() {
		$code = rand(10000, 99999);
		session('code', $code);
		return array('code' => 0, 'msg' => 'ok', 'data' => array('code' => $code));
	}

	public function login() {
		return $this->fetch();
	}

	public function logout() {
		model('Admin')->logout();
		$this->redirect('/admin');
	}

	public function ajax_login() {
		$data = input('post.');
		if (model('Config')->get_one_config_value_by_name('login_need_img_verify') != 0) {
			$result = $this->check_verify($data['verify_code']);
			if (!$result) {
				return ['code' => -1, 'msg' => '验证码错误'];
			}
		}

		$res = model('Admin')->login($data);
		return $res;
	}

	private function check_verify($verify_code) {
		$captcha = new \think\captcha\Captcha();
		return $captcha->check($verify_code);
	}

	public function clear() {
		$this->view->title = '清除缓存';

		return $this->fetch();
	}

	public function c() {
		$this->deldir('../runtime/'); 
		return ['code' => 0, 'msg' => 'ok'];
	}

	public function deldir($path){
	   //如果是目录则继续
	   if(is_dir($path)){
		    //扫描一个文件夹内的所有文件夹和文件并返回数组
		   $p = scandir($path);
		// dd($p);exit;

			   foreach($p as $val){
				    //排除目录中的.和..
				    if($val !="." && $val !=".."){
					    // echo $path.$val.'<br/>';

					     //如果是目录则递归子目录，继续操作
					     if(is_dir($path.$val)){
						      //子目录中操作删除文件夹和文件
						      $this->deldir($path.$val.'/');
						      //目录清空后删除空文件夹
						      @rmdir($path.$val.'/');
					     }else{
						      //如果是文件直接删除
						      unlink($path.$val);
					     }
					}
			    }
		   }
	}

	public function bak() {
		$this->view->title = '数据库备份';
		$condition = $this->_get_search_condition();
		$this->view->pageList = model('Bak')->getPageList($condition);
		return $this->fetch();
	}

	public function b() {
		$res = $this->dataBackup();
		return $res;
	}

	public function dataBackup(){
	   $doc_root=$_SERVER['DOCUMENT_ROOT'];
	   $file_path_name=$doc_root.'/sqlbackup';//保存到的路径
	   $name='backup_'. date('YmdHis').".sql";
	   if(!file_exists($file_path_name)){mkdir($file_path_name,0777);}
	   $mysqldump_url='mysql';//mysqldump.exe的绝对路径，安装mysql自带的有，可以搜索一下路径
	   $host='127.0.0.1';//数据库所在的服务器地址
	   $User='root';//数据库用户名
	   $Password='root';//数据库密码
	   $databaseName='hxb';//数据库名
	   $process=$mysqldump_url." -h".$host." -u".$User."  -p".$Password."  ".$databaseName." >".$file_path_name."/".$name;
	   $er=system($process);//system()执行外部程序，并且显示输出
	   if($er!==false){
	   		$data['name'] = $name;
	   		$data['src'] = $file_path_name .'/'.$name;
	   		$data['cdate'] = date('Y-m-d H:i:s');
	   		model('Bak')->insert($data);
	   	// echo $file_path_name.'/'.$name;
	      // echo json_encode('success!');
	      return ['code' => 0, 'msg' => '备份成功'];
	   }else{
	   		return ['code' => -1, 'msg' => '备份失败'];
	      // echo json_encode('error!');
	   }
	}


	/**

 * [copyDb description]  备份数据库

 * @param  [type] $dbname   [description]  数据库名

 * @param  [type] $fileName [description]  存储的文件名

 * @return [type]           [description]

 */public function copyDb($dbname, $fileName){

    $myfile = fopen($fileName, "w") or die("Unable to open file!");//打开存储文件

    $this->link->query("use {$dbname}");//切换数据库

    $this->changeDb($dbname);

    $tables = $this->link->query('show tables');//获取当期数据库所有表名称

    while($re = $tables->fetch(PDO::FETCH_ASSOC)){

        //var_dump($re);//查看数组构成

        $tableName = $re['Tables_in_'.$dbname];//构成特定的下标

        $sql = "show create table {$tableName};";

        $tableSql = $this->link->query($sql);

 

        fwrite($myfile, "DROP TABLE IF EXISTS `{$tableName}`;\r\n");//加入默认删除表的遇见

        //下面备份表结构，这个循环之执行一次

        while($re = $tableSql->fetch(PDO::FETCH_ASSOC)){

            // echo "<pre>";

            // var_dump($re);

            // echo "</pre>";

            echo "正在备份表{$re['Table']}结构<br/>";

            fwrite($myfile, $re['Create Table'].";\r\n\r\n");

            echo "正在备份表{$re['Table']}结构完成<br/>";

        }

        //下面备份表数据

        echo "正在备份表{$tableName}数据<br/>";

        $sql = "select * from {$tableName};";

        $valueSql = $this->link->query($sql);

        while($re = $valueSql->fetch(PDO::FETCH_ASSOC)){

            $keyArr = array_keys($re);//获得对应的键值

            $valueArr = array_values($re);//获得对应的值

     

            $keyStr = '';

            foreach ($keyArr as $key => $value) {

                $keyStr .= "`".$value."`,";

            }

            $keyStr = substr($keyStr,0,strlen($keyStr)-1); //取出最后一个逗号

 

 

            $valueStr = '';

            // var_dump($valueArr);

            foreach ($valueArr as $key => $value) {

                $valueStr .= "'".$value."',";

                        }

            //以上的处理只是对应sql的写法

 

            $valueStr = substr($valueStr,0,strlen($valueStr)-1); //取出最后一个逗号

            $sql = "insert into `{$tableName}`({$keyStr}) values({$valueStr})";

            fwrite($myfile, $sql.";\r\n\r\n");

             

        }

        echo "正在备份表{$tableName}数据完成<br/>";

        echo "<br/><hr/>";

    }

    fclose($myfile);}    
}
