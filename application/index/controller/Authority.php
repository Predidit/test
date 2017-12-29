<?php
namespace app\index\controller;
use think\Controller;

use app\index\model\User;
use app\index\model\Words;
use app\index\model\Wordlist;
use app\index\model\Shoucang;
class Authority extends Controller
{
	public function index(){//用于测试错误的url定位
        // $user = Authority::getLoginedUser();
        // if(is_null( $user )){
        //     return $this->fetch('login');
        // }
        // else{
        //     $this->redirect('index/index');
        // }
        return $this->fetch('login');
    }
	public function loginer(){
		return $this->fetch('login');
	}
    public function register(){
        return $this->fetch('register');
    }
	public function login_api(){
        if(!is_null( Authority::getLoginedUser() )){
            $this->assign('user',Authority::getLoginedUser()->username);
            $b = new User();
            $condition = ['username'=>Authority::getLoginedUser()->username];
            $c = $b->where($condition)->find();
            $picture = $c->picture;
            $this->assign('picture',$picture);
            return $this->fetch('major');
        }
        $username = input('post.username');
        $paw = input('post.password');

        $res = $this->login($username,$paw);
        if(is_int($res)){
            switch($res){
                case -1:
                    echo "用户名不存在";
                break;
                case -2:
                    echo "密码错误";
                break;
            echo "用户名不存在或密码错误";
            return $this->fetch('error');
        }    
        }else{
            $b = new User();
            $condition = ['username'=>Authority::getLoginedUser()->username];
            $c = $b->where($condition)->find();
            $picture = $c->picture;
            $this->assign('picture',$picture);
            $this->assign('user',$res->username);
            return $this->fetch('major');
            }
        }
    public function login($username,$paw){
        $user = new User();
        $userA = $user->where('username',$username)->find();
        if(is_null($userA))
            return -1; //-1:用户不存在
        
        if($paw == $userA->password){
            session('user1',$userA);//the key
            return $userA;
        
        }else{
            return -2; //-2:密码错误
        }
    }

    public static function getLoginedUser(){
        return session('user1');
    }

    
    public function logout(){
        if(session('?user1')){
            session('user1',null);
        }
        return $this->fetch('index/index');
    }



    public function register_api($username,$password,$password2){
        $username = input('post.username');
        $password = input('post.password');
        $password2 = input('post.password2');
        $user = new User();
        if(is_null($user->where('username',$username)->find()) && $password == $password2){
            $user->username = $username;
            $user->password = $password2;
            $user->save();
            return $this->fetch('login'); 
        }else{
            return $this->fetch('error');
        }
    }



// $this->assign('user',Authority::getLoginedUser());





    public function my(){
    	$this->assign('user',Authority::getLoginedUser()['username']);
        $b = new User();
        $condition = ['username'=>Authority::getLoginedUser()->username];
        $c = $b->where($condition)->find();
        $picture = $c->picture;
        $this->assign('picture',$picture);
    	return $this->fetch('index/my');
    }
    public function mywords(){//单词集
        $a = new Words();
        $condition = ['belong'=>Authority::getLoginedUser()->username];
        $a_sel = $a->where($condition)->select();
        $a_res=[];
        foreach($a_sel as $v){
            array_push($a_res,['word'=>$v->word,'xiugai'=>$v->xiugai,'status'=>$v->status]);
        }
        $xiugai = array();
        foreach ($a_sel as $user) {
            $xiugai[] = $user['xiugai'];
        }
        array_multisort($xiugai,SORT_DESC,$a_res);//玄幻的日期排序

        $b = new User();
        $condition = ['username'=>Authority::getLoginedUser()->username];
        $c = $b->where($condition)->find();
        $picture = $c->picture;
        $this->assign('user',Authority::getLoginedUser()['username']);
        $this->assign('data_a',$a_res);
        $this->assign('picture',$picture);
        return $this->fetch('mywords');
    }
    public function square(){//单词广场
        $a = new Words();
        $condition = ['status'=>'1'];
        $a_sel = $a->where($condition)->select();
        $a_res=[];
        foreach($a_sel as $v){
            $d = new Shoucang();
            $condition2 = ['shoucanger'=>Authority::getLoginedUser()->username,'word'=>$v->word,'belong'=>$v->belong];
            $e = $d->where($condition2)->select();
            if($e == []){
                array_push($a_res,['word'=>$v->word,'xiugai'=>$v->xiugai,'belong'=>$v->belong]);
                $xiugai = array();
                foreach ($a_sel as $user) {
                    $xiugai[] = $user['xiugai'];

                }

            }
        }
        $b = new User();
        $condition = ['username'=>Authority::getLoginedUser()->username];
        $c = $b->where($condition)->find();
        $picture = $c->picture;
        $this->assign('user',Authority::getLoginedUser()['username']);
        $this->assign('picture',$picture);
        $this->assign('data_a',$a_res);
        return $this->fetch('square');
    }
    public function shoucang(){
        $word = input('post.word');
        $belong = input('post.belong');
        $xiugai = input('post.xiugai');
        $shoucanger = Authority::getLoginedUser()['username'];
        $b = new Shoucang();
        $b->word = $word;
        $b->belong = $belong;
        $b->xiugai = $xiugai;
        $b->shoucanger = $shoucanger;
        $b->save();
        return $this->redirect('square');
    }
    public function shoucanglook(){
        $a = new Shoucang();

        $condition = ['shoucanger'=>Authority::getLoginedUser()->username];
        $a_sel = $a->where($condition)->select();
        $a_res=[];
        foreach($a_sel as $v){
            array_push($a_res,['word'=>$v->word,'xiugai'=>$v->xiugai,'belong'=>$v->belong]);
        }
        $xiugai = array();
        foreach ($a_sel as $user) {
        $xiugai[] = $user['xiugai'];
        }
        array_multisort($xiugai,SORT_DESC,$a_res);//玄幻的日期排序


        $b = new User();
        $condition = ['username'=>Authority::getLoginedUser()->username];
        $c = $b->where($condition)->find();
        $picture = $c->picture;
        $this->assign('picture',$picture);


        $this->assign('user',Authority::getLoginedUser()['username']);
        $this->assign('data_a',$a_res);
        return $this->fetch('shoucang');
    }
    public function shoucang_del(){
        $word = input("post.word");
        $a = new Shoucang();
        $condition = ['word'=>$word];
        $a_sel = $a->where($condition)->find();
        $a_sel->delete();
        return $this->redirect('shoucanglook');
    }
    public function cancel(){
        $word = input('post.word');
        $b = new Words();
        $condition = ['word'=>$word,'belong'=>Authority::getLoginedUser()->username];
        $c = $b->where($condition)->find();
        $c->status = '0';
        $c->save();
        $d = new Shoucang();
        $condition = ['word'=>$word];
        $e = $d->where($condition)->find();
        if(!$e == []){
            $e->delete();   
        }
        return $this->redirect('mywords');
    }
    public function fabu(){
        $word = input('post.word');
        $b = new Words();
        $condition = ['word'=>$word,'belong'=>Authority::getLoginedUser()->username];
        $c = $b->where($condition)->find();
        // var_dump($c);
        $c->status = '1';
        $c->save();
        return $this->redirect('mywords');
    }
    public function del(){
        $word = input('post.word');
        $b = new Words();
        $condition = ['word'=>$word,'belong'=>Authority::getLoginedUser()->username];
        $c = $b->where($condition)->find();
        $c->delete();
        $d = new Wordlist();
        $condition = ['belong'=>$word];
        $e = $d->where($condition)->find();
        if(!$e == []){
            $e->delete();
        }
        return $this->redirect('mywords');
    }

    public function look(){
        $word = input('post.word');
        $a = new Wordlist();
        $condition = ['belong'=>$word];
        $a_sel = $a->where($condition)->select();
        $a_res=[];
        foreach($a_sel as $v){
            array_push($a_res,['word'=>$v->word,'translate'=>$v->translate]);
        }
        $this->assign('data_a',$a_res);
        return $this->fetch('protect_mywordlist');
    }
    public function xiugai(){
        $word = input('post.word');
        $a = new Wordlist();
        $condition = ['belong'=>$word];
        $a_sel = $a->where($condition)->select();
        $a_res=[];
        foreach($a_sel as $v){
            array_push($a_res,['word'=>$v->word,'translate'=>$v->translate]);
        }



        $xiugai = date("Y-m-d H:i:s");
        $b = new Words();
        $condition = ['word'=>$word];
        $c = $b->where($condition)->find();//修改时间
        $c->xiugai = $xiugai;
        $c->save();




        $this->assign('data_a',$a_res);
        $this->assign('wordname',$word);
        return $this->fetch('mywordlist_2');
    }
    public function xiugai_pri($belong){//用于返回
        $a = new Wordlist();
        $condition = ['belong'=>$belong];
        $a_sel = $a->where($condition)->select();
        $a_res=[];
        foreach($a_sel as $v){
            array_push($a_res,['word'=>$v->word,'translate'=>$v->translate]);
        }
        $this->assign('data_a',$a_res);
        $this->assign('wordname',$belong);
        return $this->fetch('mywordlist_2');
    }
    public function list_del(){
        $word = input('post.word');
        $belong = input('post.belong');
        $b = new Wordlist();
        $condition = ['word'=>$word];
        $c = $b->where($condition)->find();
        $c->delete();
        return $this->xiugai_pri($belong);
    }//可能的坑
    public function add(){
        $word = input('post.word');
        $belong = input('post.user');
        $status = '0';
        $xiugai = date("Y-m-d H:i:s");
        $b = new Words();
        $b->word = $word;
        $b->status = $status;
        $b->xiugai = $xiugai;
        $b->belong = $belong;
        $b->save();
        return $this->redirect('mywords');
    }
    public function list_add(){
        $word = input('post.word');
        $translate = input('post.translate');
        $belong = input('post.belong');
        $b = new Wordlist();
        $b->word = $word;
        $b->translate = $translate;
        $b->belong = $belong;
        $b->save();
        return $this->xiugai_pri($belong);
    }











    public function upload(){
        $name = input('post.user');
        $file = request()->file('image');
    if (!is_null($file)) {
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads'); // 移动到框架应用根目录/public/uploads/ 目录下
        $explode = explode( DS ,$info->getPath());
        $filename_arr = $explode[count($explode)-2]. DS . $explode[count($explode)-1] . DS .$info->getFilename();//获取路径
        $b = new User();
        $condition = ['username'=>$name];
        $c = $b->where($condition)->find();
        $c->picture = $filename_arr;
        $c->save();
            if($info){        
                return $this->redirect('my');
            } 
        }else{
            return $this->redirect('my');
        }
    }

        
    function upload_word(){
        vendor("PHPexcel.PHPExcel"); //下载PHPExcel类
        //获取表单上传文件
        $file = request()->file('excel'); 
        // $file = $_FILES['excel'];
    if (!is_null($file)) {
        $info = $file->validate(['ext' => 'xlsx'])->move(ROOT_PATH . 'public' . DS . 'uploads');
        $filename_arr = $file -> getInfo()['name'];//强势补锅
        if ($info) {
             //echo $info->getFilename();die;
            $excelPath = $info->getSaveName();  //获取文件名
            $file_name = ROOT_PATH . 'public' . DS . 'uploads' . DS . $excelPath;   //上传文件的地址
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            $obj_PHPExcel = $objReader->load($file_name, $encode = 'utf-8');  //加载文件内容,编码utf-8
            echo "<pre>";
            $excel_array = $obj_PHPExcel->getsheet(0)->toArray();   //转换为数组格式
            // array_shift($excel_array);  //删除第一个数组(标题);
            $filename_arr = $file -> getInfo()['name'];//一个大坑
            $filename = substr($filename_arr,0,-5);
            $city = [];
            foreach ($excel_array as $k => $v) {
                $city[$k]['word'] = $v[0];
                $city[$k]['translate'] = $v[1];
                $b = new Wordlist;
                $b->word = $city[$k]['word'];
                $b->translate = $city[$k]['translate'];
                $b->belong = $filename;
                $b->save();
                $c = new Words;
                $c->status = '0';
                $c->word = $filename;
                $xiugai = date("Y-m-d H:i:s");
                $c->xiugai = $xiugai;
                $c->belong = Authority::getLoginedUser()['username'];
                $c->save();
                return $this->redirect('mywords');
            }
        }else{
            return $this->redirect('mywords');
            // var_dump(1);
        }
    }
    }
}


