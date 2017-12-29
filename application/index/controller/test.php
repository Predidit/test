    public function square(){//单词广场
        $a = new Words();
        $condition = ['status'=>'1'];
        $a_sel = $a->where(1)->select();
        $a_res=[];
        foreach($a_sel as $v){
            array_push($a_res,['word'=>$v->word,'xiugai'=>$v->xiugai,'belong'=>$v->belong]);
        }
        // natsort($a_res);
        $xiugai = array();
        foreach ($a_sel as $user) {
        $xiugai[] = $user['xiugai'];
        }
        array_multisort($xiugai,SORT_DESC,$a_res);//玄幻的日期排序


        $this->assign('user',Authority::getLoginedUser()['username']);
        $this->assign('data_a',$a_res);
        return $this->fetch('square');
    }