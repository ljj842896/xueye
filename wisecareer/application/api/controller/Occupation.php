<?php
namespace app\api\controller;

use app\common\Charts;
use app\common\model\Anli_class;
use app\common\model\Anli_search;
use app\common\model\Coach;
use app\index\controller\Action;
use think\Cache;
use think\Controller;
use think\Db;
use think\facade\Config;

/**
 * Class Index
 * @package app\api\controller
 */
class Occupation extends Action
{
    public function occuSearch(){

        $tpid= intval(input('get.tpid'));
        $id= intval(input('get.id'));
        $jibie= intval(input('get.jibie'));

        if(empty($tpid) && empty($id)) return $this->echo_json(250001);
        if(empty($jibie)) $jibie =[1,2,3];

        if($tpid){
            $data = Anli_search::scope('AnliSearchHangyeJijie',$tpid,$jibie)->order('jibie','desc')->select();
        }elseif($id){
            $data = Anli_search::scope('AnliSearchLxJijie',$id,$jibie)->order('jibie','desc')->select();
        }

        $titlename = Anli_class::where('tpid',$tpid)->value('tpname');
        foreach ($data as $key =>$value){
            $sql =  "SELECT count(id) as num   from (select * from (select a.* from `kw_app_user_search` a LEFT JOIN kw_center_user b on a.uid = b.id WHERE b.oid = 2  order by b.datetime desc) `temp`  group by uid order by `datetime` desc ) f  where  zhiye ='{$value['titlename']}'";
            $d = Db::query($sql);
            $data[$key]['num'] = $d[0]['num'];
            if($value['jibie'] == 1){
                $jibiestr = '初级';
            }elseif($value['jibie'] == 2){
                $jibiestr = '中级';
            }elseif($value['jibie'] == 3){
                $jibiestr = '高级';
            }
            $data[$key]['jibiestr'] = $jibiestr;
        }
        $result['titlename'] = $titlename;
        $result['list'] = $data;

        return $this->echo_json(0,'',$result);

    }


    /**
     * 职业代言教练列表
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function occuSearch_list(){

        $id= intval(input('get.id'));

        if(empty($id) ) return $this->echo_json(250001);
        $titlename = Anli_search::where('id',$id)->value('titlename');
        if($titlename==null){
            return $this->echo_json(250001);
        }
        $uid= $this->uid;

        $sql="SELECT uid  from (select * from (select * from `kw_app_user_search` order by `datetime` desc) `temp`  group by uid order by `datetime` desc ) f  where zhiye ='{$titlename}' ";
        $da = Db::query($sql);


        if(empty($da))   return $this->echo_json(0,'','');

        $uids = array_column($da,'uid');

        $data = Coach::get_star($uid,$uids);

        $result['title'] = $titlename;
        $result['list'] = $data;
        return $this->echo_json(0,'',$result);

    }

}
