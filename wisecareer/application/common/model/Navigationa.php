<?php

namespace app\common\model;

use app\common\Caches;
use think\facade\Cache;
use think\Model;

/**
 * Class Myuser   用户信息类
 * @package app\common\model
 */
class Navigationa extends Model
{
    /**
     *
     */
    protected static function init()
    {

    }

    /**
     * 做决策模型
     * @param $uid
     * @param $array
     */
    public static function makeEduwensr($uid, $array){
        $has =self::name('edu_wen_sr')->where('uid',$uid)->find();

        if(empty($has)){
            $array['uid']= $uid;
            $res = self::name('edu_wen_sr')->data($array)->insert();
        }else{
            $res = self::name('edu_wen_sr')->where('uid',$uid)->data($array)->update();
        }
        if($res){
            $data =self::name('edu_wen_sr')->where('uid',$uid)->find();
            if( (!empty($data['list12']) || !empty($data['list13']) ||  !empty($data['list14'])  )  ){
                self::name('edu_wen_sr')->where('uid',$uid)->data('openok',1)->update();
                self::name('myuser')->where('id',$uid)->data('wensrid',1)->update();
                Caches::MyuserInfoFiled($uid,1);
            }
        }

        Caches::EduWenSrUid($uid,1);

        return $res;
    }
    /**
     * @param $uid
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function planningedit($uid){
        $myinfo = Caches::MyuserInfoFiled($uid);
        $data['userinfo'] = userinfo_is($myinfo);
        if($data['userinfo']==0){
            return false;
        }
        $schoolid = $myinfo['schoolname'];
        $viptype = $myinfo['viptype'];
        $classid = $myinfo['classname'];
        $subject = $myinfo['subject'];
        $myuserStatus=Caches::MyuserXyStatus($uid,$viptype);
        $status=$myuserStatus['status'];
        if($myinfo['wensrid'] ==1 ){
            $data2 =  self::get_planning($uid, $schoolid, $viptype, $classid);
            $num = 0;
            $data2['interestprompt'] && $num++;
            $data2['industryprompt'] && $num++;
            $data2['targetprompt'] && $num++;
            $data2['advantageprompt'] && $num++;
            $data2['advantageprompt1'] && $num++;
            $data2['disciplineprompt'] && $num++;
            $data2['disciplineprompt1'] && $num++;
            $result['planning_num'] = $num;
            $result['planning']=1;
        }else{
            $result['planning_num']=0;
            $result['planning']=0;
        }

        //  学科能力评估

//        $result['set_edu_sc_num'] = Class_class::where('cid',14)->where('tbid',$classid)->order('tpname','desc')->column('tpname');
        //  d定位目标专业

        $sc_edu_num = count(Caches::EduUserScId($uid));
        if($sc_edu_num  >=1){
            $result['edu_sc'] = 1;
            $result['edu_sc_num'] = $sc_edu_num;
            $cids= array(3,7,10,30,39,46);
            $data = Shcool::collection($uid,$schoolid,$cids);
            $numplociy = 0;
            $num = 0;
            foreach ($data as $key => $value) {
                $numplociy = $numplociy+$value['tages'];
                $num+=1;
            }
            $result['edu_sc_policynum'] =is_null( @round(($numplociy / $num),2))?0: @round(($numplociy / $num),2);
        }else{
            $result['edu_sc_num']=0;
            $result['edu_sc']=0;
            $result['edu_sc_policynum']=0;
        }

        $result['subjectno'] = 0;

        $data = Navigationa::get_combination($uid,$schoolid,$subject,$viptype,$classid);
        if(!empty($data)){
            foreach($data['arrno'] as $value){
                if($value['tpid'] == $subject){
                    $result['subjectno'] = 1;
                }
            }
        }

        $EduGkClassdata = Caches::EduGkClass();
        $result['subject_str']=$subject? preg_replace("/#/","-",$EduGkClassdata[$subject]):'';
        $score_has = Scores::name('user_scores_avg')->where('uid',$uid)->find();
        $result['score']  = $score_has ===null ? 0 :1;
        unset($result['id']);
        unset($result['uid']);
        return $result;
    }

    /**
     * 学业决策参数
     * @param $uid
     * @param $schoolid
     * @param $viptype
     * @param $classid
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function get_planning($uid, $schoolid, $viptype, $classid){
        $data= Caches::EduWenSrUid($uid);
        if(!$data ||  !$data['openok']){
            return false;
        }
        $where =  '';
        $wherenk =  '';
        //学业目标
        if($data['list1'] == 1){
            $str ="高等专科(高职)院校";
            $where .= " and (  type = '1') ";
        }elseif($data['list1']==2){
            $str ="本科生";
            $where .= " and ( type = '2' ) ";
        }else{
            $str ="研究生";
            $where .= " and ( type = '2' ) ";
        }
        $result['target'] = $str;
        $EduLilundata = Caches::EduLilun();
        $EduLddata = Caches::EduLd();
        if($data['list6']){
            $list6 = empty($EduLilundata[$data['list6']])?'':$EduLilundata[$data['list6']];
            $where .= " and  lilun not  like '%".$list6."%' ";
            $wherenk .= " and   tpname not  like '%".$list6."%' ";
            $result['discipline'][] =$list6;
        }

        if($data['list7']){
            $list7= empty($EduLilundata[$data['list7']])?'':$EduLilundata[$data['list7']];
            $where .= " and   lilun  not  like '%".$list7."%' ";
            $wherenk .= " and   tpname  not  like '%".$list7."%' ";
            $result['discipline'][] =$list7;
        }

        if(!$data['list6'] && !$data['list7'] ){
            $result['discipline'][]='无';
        }

        if($data['list8']){
            $list8= empty($EduLilundata[$data['list8']])?'':$EduLilundata[$data['list8']];
            $where .= " and   lilun  not  like '%".$list8."%' ";
            $wherenk .= " and  tpname  not  like '%".$list8."%' ";
        }
        ////////////////////////
        if($data['list9']){
            $list9= empty($EduLddata[$data['list9']])?'':$EduLddata[$data['list9']];
            $where .= " and   ld not  like '%".$list9."%' ";
            $result['interest'][] =$list9;
        }

        if($data['list10']){
            $list10= empty($EduLddata[$data['list10']])?'':$EduLddata[$data['list10']];
            $where .=  " and  ld  not like '%".$list10."%' ";
            $result['interest'][] =$list10;
        }

        if($data['list11']){
            $list11= empty($EduLddata[$data['list11']])?'':$EduLddata[$data['list11']];
            $where .=  " and  ld  not  like '%".$list11."%' ";
            $result['interest'][] =$list11;
        }

        if($data['list21'] == 4 &&($data['list9'] == 26 ||$data['list10'] ==26||$data['list11']==26 )){
            $result['interestprompt'] = 1;
            $result['interestptmsg'] = "你所排除的“数字分析”与你的学科优势（数学）相矛盾。建议调整";
        }else{
            $result['interestprompt'] = 0;
            $result['interestptmsg'] = '';
        }

        if($data['list21'] ==27 &&($data['list12'] == 34 ||$data['list13'] ==34||$data['list14']==34 )){
            $result['industryprompt'] = 1;
            $result['industryptmsg'] = "你所排除的“政治”与你的学科优势（思想政治）相矛盾。建议调整";
        }else{
            $result['industryprompt'] = 0;
            $result['industryptmsg'] = '';
        }

        if($data['list1']==0 && ($data['list9'] ==24 ||$data['list10']  ==24 || $data['list11'] ==24 ) ){
            $result['targetprompt'] = 1;
            $result['targetpromptmsg'] = "你所排除的“理论研究”与你的学业目标（研究生）相矛盾。建议调整";
        }else{
            $result['targetprompt'] = 0;
            $result['targetpromptmsg'] = " ";
        }
        if(!$data['list9'] && !$data['list10'] && !$data['list11'] ){
            $result['interest'][]='无';
        }
        if($data['list12']){
            $list12= empty($EduLddata[$data['list12']])?'':$EduLddata[$data['list12']];
            $where .=  " and  ld  not  like '%".$list12."%' ";
            $result['industry'][] =$list12;
        }
        if($data['list13']){
            $list13= empty($EduLddata[$data['list13']])?'':$EduLddata[$data['list13']];
            $where .=  " and  ld  not  like '%".$list13."%' ";
            $result['industry'][] =$list13;
        }
        if($data['list14']){
            $list14= empty($EduLddata[$data['list14']])?'':$EduLddata[$data['list14']];
            $where .=  " and  ld  not  like '%".$list14."%' ";
            $result['industry'][] =$list14;
        }
        if(!$data['list12'] && !$data['list13'] && !$data['list14'] ){
            $result['industry'][]='无';
        }
        $wheres = $where ;
        ////////////////////////

        if($data['list21']){
            $list21 =  empty($EduLilundata[$data['list21']])?'':$EduLilundata[$data['list21']];
            $wheres .= " and   lilun  like '%".$list21."%'";
            if($data['list21']!=2 && $data['list21']!=4 && $data['list21']!=5 ){
                $wherenk .= " and   tpname  like '%".$list21."%'";
            }
            $result['advantage'] =$list21;
        }
        $combination= Caches::SchoolSetId($schoolid);//
        //优势学科
        if(!empty($combination['xk_strong']) && $viptype){
            $tpids = explode(',',$combination['xk_strong']);
            $tpids = array_filter($tpids);
            $result['scadvantage']=[];
            foreach ($tpids as $val){
                $result['scadvantage'][]= $EduLilundata[$val];
            }
        }else{
            if($data['list22']){
                $list22 = empty($EduLilundata[$data['list22']] )?'':$EduLilundata[$data['list22']] ;
                $result['scadvantage'][] =$list22;
            }else{
                $result['scadvantage'] ='';
            }
        }
        //弱势学科
        if(  !empty($combination['xk_feeble']) && $viptype){
            $tpids2 = explode(',',$combination['xk_feeble']);
            $tpids2 = array_filter($tpids2);
            $result['scweak']=[];
            foreach ($tpids2 as $val){
                $result['scweak'][]= $EduLilundata[$val];
            }


            if( in_array($data['list21'] ,explode(',',$combination['xk_feeble']) ) ){
                $result['advantageprompt'] = 1;
                $result['advantagepromptmsg'] = "你的“优势学科”正好就是本校的弱势教学科目。建议规避！或者对本校的学科教学强项和弱项进行深入调研！";
            }else{
                $result['advantageprompt'] = 0;
                $result['advantagepromptmsg'] = " ";
            }
        }else{
            if($data['list23']){
                $list23 =  empty($EduLilundata[$data['list23']] )?'':$EduLilundata[$data['list23']] ;
                $result['scweak'][] = $list23;
            }else{
                $result['scweak'] ='';
            }
            if($data['list21'] == $data['list23']){
                $result['advantageprompt'] = 1;
                $result['advantagepromptmsg'] = "你的“优势学科”正好就是本校的弱势教学科目。建议规避！或者对本校的学科教学强项和弱项进行深入调研！";
            }else{
                $result['advantageprompt'] = 0;
                $result['advantagepromptmsg'] = " ";
            }
        }
        //符合专业数
        $sql = "SELECT count(id) as num FROM kw_edu_info where id > 0  and  `open` = '1' ".$where."  ";

        $arr = self::query($sql);

        $result['accordnum']= $arr[0]['num'];
        //优先推荐专业数
        $sqlsfuhe = "SELECT count(id) as num FROM kw_edu_info where id > 0 and  `open` = '1' ".$wheres." ";


        $arrfuhe = self::query($sqlsfuhe);
        $result['recommendnum']= $arrfuhe[0]['num'];
        // 考区
        $gaokao_diqu =Shcool::get_district($schoolid);
        if($gaokao_diqu == 82){
            $tporders = '1';
        }else{
            $tporders = '4';
        }
        $xk_strong =empty( $combination['xk_strong'])? '':$combination['xk_strong'];
        $xk_feeble =empty( $combination['xk_feeble'])? '':$combination['xk_feeble'];
        //优势
        if($xk_strong && $viptype){
            $tpids = explode(',',$combination['xk_strong']);
            $tpids = array_filter($tpids);
            $search=[];
            foreach ($tpids as $val){
                $search[]= $EduLilundata[$val];
            }
        }else{
            if($data['list22']){
                $search = $EduLilundata[$data['list22']];
            }else{
                $search ='';
            }
        }
        //弱势
        if($xk_feeble && $viptype){
            $list23= $EduLilundata[$xk_feeble];
            $wherenk .= " and   tpname not like '%".$list23."%'";
        }else{
            if($data['list23']){

                $list23= $EduLilundata[$data['list23']];
                $wherenk .= " and   tpname not like '%".$list23."%'";
            }
        }
        $sqlk = "SELECT tpname FROM kw_edu_gk_class where  tporder = '".$tporders."' ".$wherenk."   order by tpid desc";
        $arrfuhe =self::query($sqlk);
        foreach($arrfuhe as $key=>$value){
            $tpnames = explode("#",$value['tpname']);
            unset($arrfuhe[$key]);
            if(in_array($search,$tpnames)){
                $arrfuhe['red'][$key] =$tpnames ;
                $arrfuhe['rednum'] =isset($arrfuhe['rednum'])?$arrfuhe['rednum']+1:1;
            }else{
                $arrfuhe['blue'][$key] =$tpnames;
                $arrfuhe['bluenum'] = isset($arrfuhe['bluenum'])?$arrfuhe['bluenum']+1:1;
            }
        }
        $arrfuhe['red']=empty($arrfuhe['red'])?'':$arrfuhe['red'];
        $arrfuhe['rednum']=empty($arrfuhe['rednum'])?'0':$arrfuhe['rednum'];
        $arrfuhe['blue']=empty($arrfuhe['blue'])?'':$arrfuhe['blue'];
        $arrfuhe['bluenum']=empty($arrfuhe['bluenum'])?'0':$arrfuhe['bluenum'];

        //你所排出的学科中包含高考必考的语文、数学、外语，建议调整！
        if($data['list6']== 2 or $data['list6']== 4 or $data['list6']== 5 or $data['list7']== 2 or $data['list7']== 4 or $data['list7']== 5){

            $result['disciplineprompt'] = 1;
            $result['disciplinepromptmsg'] = "你所排除的学科包含语、数、外三门高考必考科目。建议调整";
        }else{
            $result['disciplineprompt'] = 0;
            $result['disciplinepromptmsg'] = " ";
        }
        $hasscores = self::name('user_scores_avg')->where('uid',$uid)->find();

        $result['disciplineprompt1'] = 0;
        $result['disciplinepromptmsg1'] = " ";
        $result['advantageprompt1'] = 0;
        $result['advantagepromptmsg1'] = " ";

        if($hasscores){
            //年级排名预警
            $scoresdata = Scores::user_score_branch($uid,$hasscores);
            $scoresdata = array_sort($scoresdata, 'perc');
            if($scoresdata){
                //$res = array_sort($scoresdata,'score');
                /**
                 * 预警：你所选择的优势学科并非本人学科综合能力最优（前2名）的。建议进一步优化。
                预警：你所排除的学科并非本人学科综合能力较弱（倒数2名）的。建议进一步优化。
                 */
                //$inarray = array(2,4,5);

//                foreach ($scoresdata as $key => $value) {
//                    if($value['score']=='无'){
//                        unset($scoresdata[$key]);
//                    }
////                    if(in_array($value['tpid'],$inarray)){
////                        unset($res[$key]);
////                    }
//                }

                  foreach ($scoresdata as $key => $value) {
                        if($value['perc']==0){
                            unset($scoresdata[$key]);
                        }
//                    if(in_array($value['tpid'],$inarray)){
//                        unset($res[$key]);
//                    }
                  }

//                $res = sortByCols($scoresdata, array(
//                    'score' => SORT_ASC,
//                    'perc' => SORT_DESC,
//                ));


                $scores = array_slice($scoresdata,-2);
                if(count($scoresdata) >=4){
                    $scores1 = array_slice($scoresdata,0,2);
                    $scorsasc = array_column($scores,'tpid');
                    $scorsdesc = array_column($scores1,'tpid');
                    if( !empty($data['list6']) && (  (!in_array($data['list6'],$scorsdesc  ) && !empty($data['list6']))  ||   (!in_array($data['list7'],$scorsdesc  ) && !empty($data['list7']))) ){
                        $result['disciplineprompt1'] = 1;
                        $result['disciplinepromptmsg1'] = "你所排除的学科并非本人学科综合能力较弱（倒数2名）的。建议进一步优化";
                    }
                    if( !in_array(	$data['list21'] ,  $scorsasc ) ){
                        $result['advantageprompt1'] = 1;
                        $result['advantagepromptmsg1'] = "你所选择的优势学科并非本人学科综合能力最优（前2名）的。建议进一步优化";
                    }
                }
            }
        }
        //$this->class_rank($uid,$schoolid,$classid);
        $result['arrfuhe'] = $arrfuhe;
        return $result;
    }

    /**选科组合
     * @param $uid
     * @param $schoolid
     * @param $subject
     * @param $viptype
     * @param $classid
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function get_combination($uid,$schoolid,$subject,$viptype,$classid){
        $data= Caches::EduWenSrUid($uid);
        if(!$data['openok']){
            return false;
        }
        $where =  '';
        $wherenk =  '';
        $wherenkw =  '';
        $EduLilundata = Caches::EduLilun();
        $EduLddata = Caches::EduLd();
        if($data['list6']){
            $list6 = empty($EduLilundata[$data['list6']])?'':$EduLilundata[$data['list6']];
            $where .= " and  lilun not  like '%".$list6."%' ";
            $wherenkw =$wherenk .= " and   tpname not  like '%".$list6."%' ";
        }
        if($data['list7']){
            $list7= empty($EduLilundata[$data['list7']])?'':$EduLilundata[$data['list7']];
            $where .= " and   lilun  not  like '%".$list7."%' ";
            $wherenkw =$wherenk .= " and   tpname  not  like '%".$list7."%' ";
        }
        //
        if($data['list8']){
            $list8=empty($EduLilundata[$data['list8']])?'':$EduLilundata[$data['list8']];
            $where .= " and   lilun  not  like '%".$list8."%' ";
            $wherenkw =$wherenk .= " and  tpname  not  like '%".$list8."%' ";
        }
        $wheres = $where ;
        ////////////////////////
        $wherenks='';
        if($data['list21']){
            $list21 =empty($EduLilundata[$data['list21']])?'':$EduLilundata[$data['list21']];
            $wheres .= " and   lilun  like '%".$list21."%'";
            if($data['list21']!=2 && $data['list21']!=4 && $data['list21']!=5 ){
                $wherenks = " and   tpname  like '%".$list21."%'";
            }else{
                $wherenks = '';
            }
        }

        $school_set_data= Caches::SchoolSetId($schoolid);//
        if(!empty($school_set_data)){
            $combination = $school_set_data['combination'];
            $xk_strong = $school_set_data['xk_strong'];
            $xk_feeble = $school_set_data['xk_feeble'];
        }else{
            $combination='';
            $xk_strong='';
            $xk_feeble='';
        }

        //优势
        if($xk_strong && $viptype){
            $tpids = explode(',',$xk_strong );
            $tpids = array_filter($tpids);
            $search=[];
            foreach ($tpids as $val){
                $search[]= $EduLilundata[$val];
            }
        }else{
            if($data['list22']){
                $search = empty($EduLilundata[$data['list22']])?'':$EduLilundata[$data['list22']];
            }else{
                $search ='';
            }
        }
        //弱势
        if($xk_feeble && $viptype){
            $list23= empty($EduLilundata[$xk_feeble])?'':$EduLilundata[$xk_feeble];
            $wherenk .= " and   tpname not like '%".$list23."%'";
        }else{
            if($data['list23']){
                $list23= empty($EduLilundata[$data['list23']])?'':$EduLilundata[$data['list23']];
                $wherenk .= " and   tpname not like '%".$list23."%'";
            }
        }
        $gaokao_diqu = Shcool::get_district($schoolid);
        if($gaokao_diqu == 82){
            $tporders = '1';
        }else{
            $tporders = '4';
        }
        if($combination && $viptype){
            $notcombin = " and  tpid not in ( $combination )";
        }else{
            $notcombin = ' ';
        }
        $has = self::name('user_scores_avg')->where('uid',$uid)->find();
        $result1['tpid'] = 0;
        $result2['tpid'] = 0;
        if($has){
            //年级排名预警
            $scoresdata = Scores::user_score_branch($uid,$has);
            $scoresdata = array_sort($scoresdata,'perc');
            if($scoresdata){
                $inarray = array(2,4,5);
                foreach ($scoresdata as $key => $value) {
                    if(in_array($value['tpid'],$inarray)){
                        unset($scoresdata[$key]);
                    }
                }

                $recom_sub_data= Recommend::recomm_lilun_subject($uid);
                if($recom_sub_data ){
                    $tpnames = array_column($recom_sub_data,'tpname');
                    $edu_gkdata = self::name('edu_gk_class')->whereIn('tporder',$tporders)->field('tpid,tpname')->select();
                    $nocombination = explode(',',$combination);
                    foreach($edu_gkdata as $key =>$value){
                        if(!empty($nocombination) && !in_array($value['tpid'], $nocombination)){
                            if(empty(array_diff($tpnames,explode("#",$value['tpname'])))){
                                $edu_gkdata[$key]['tpname']  =preg_replace("/#/","-",$edu_gkdata[$key]['tpname']);   ;
                                $result2 =  $edu_gkdata[$key]   ;
                            }
                        }
                        //$objtpid[] = $value['tpid'];
                    }
                    if(!empty($result2)){
                        $result2['hot'] = 2; //学科
                    }
                }

                $restpname = array_slice($scoresdata,3);
                $tpnames = array_column($restpname,'tpname');
                $edu_gkdata = self::name('edu_gk_class')->whereIn('tporder',$tporders)->field('tpid,tpname')->select();
                $nocombination = explode(',',$combination);
                foreach($edu_gkdata as $key =>$value){
                    if(!empty($nocombination) && !in_array($value['tpid'], $nocombination)){
                        if(empty(array_diff($tpnames,explode("#",$value['tpname'])))){
                            $edu_gkdata[$key]['tpname']  =preg_replace("/#/","-",$edu_gkdata[$key]['tpname']);   ;
                            $result1 =  $edu_gkdata[$key]   ;
                        }
                    }
                    //$objtpid[] = $value['tpid'];
                }
                if(!empty($result1)){
                    $result1['hot'] = 1;
                }

            }
        }


        $sqlk1 = "SELECT tpid,tpname FROM kw_edu_gk_class where tpid !={$result1['tpid']} and tpid !={$result2['tpid']}   and  tporder = '".$tporders."' ".$wherenk.$wherenks.$notcombin."   order by tpid desc";
        $arrfuhe = self::query($sqlk1);
        $objtpid = [];
        foreach($arrfuhe as $key =>$value){
            $arrfuhe[$key]['tpname']  =preg_replace("/#/","-",$value['tpname']);
            $objtpid[] = $value['tpid'];
        }
        if($has && $result1['tpid'] !=0){
            array_unshift($arrfuhe,$result1);
        }
        if($has && $result2['tpid'] !=0){
            array_unshift($arrfuhe,$result2);
        }
        if($objtpid){
            $objtpids = implode(',',$objtpid);
            $wheretpids = " and tpid not in(".$objtpids.")";
        }else{
            $wheretpids = ' ';
        }
        $sqlk2 = "SELECT tpid,tpname FROM kw_edu_gk_class where tpid !={$result1['tpid']}  and   tporder = ".$tporders.$wheretpids.$wherenkw.$notcombin."   order by tpid desc";
        $arrqita = self::query($sqlk2);
        $objtpidqita = [];
        foreach($arrqita as $key =>$value){
            $arrqita[$key]['tpname'] =preg_replace("/#/","-",$value['tpname']);
            $objtpidqita[] = $value['tpid'];
        }
        $objtpidqitas = implode(',',$objtpidqita);
        if( $objtpidqitas != ""){
            $whereqitas = " and tpid not in(".$objtpidqitas.")";
        }else{
            $whereqitas = ' ';
        }
        $sqlk3 = "SELECT tpid,tpname FROM kw_edu_gk_class where tpid !={$result1['tpid']}  and  tporder = ".$tporders.$wheretpids.$whereqitas.$notcombin."   order by tpid desc";
        $arrno = self::query($sqlk3);
        foreach($arrno as $key =>$value){
            $arrno[$key]['tpname']  =preg_replace("/#/","-",$value['tpname']);
        }
        $result['arrfuhe'] = $arrfuhe;
        $result['arrqita'] = $arrqita;
        $result['arrno'] = $arrno;
        return $result;

    }


    /**
     * @param $uid
     * @param $schoolid
     * @param $type
     * @param string $limit
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public static function recommended_profession($uid, $schoolid, $type, $limit=''){
        $data = Caches::EduWenSrUid($uid);
        if(!$data['openok']){
            return false;
        }
        $where =  '';
        $wherenk =  '';
        //学业目标
        if($data['list1'] == 1){
            $where .= " and (  type = '1') ";
        }elseif($data['list1']==2){
            $where .= " and ( type = '2' ) ";
        }else{
            $where .= " and ( type = '2' ) ";
        }

        $EduLilundata  =  Caches::EduLilun();
        $EduLdata  =  Caches::EduLd();
        if($data['list6']){
            $list6 = empty($EduLilundata[$data['list6']])?'':$EduLilundata[$data['list6']];
            $where .= " and   i.lilun not  like '%".$list6."%' ";
            $wherenk .= " and   tpname not  like '%".$list6."%' ";
        }
        if($data['list7']){
            $list7= empty($EduLilundata[$data['list7']])?'':$EduLilundata[$data['list7']];
            $where .= " and    i.lilun  not  like '%".$list7."%' ";
            $wherenk .= " and   tpname  not  like '%".$list7."%' ";
        }
        if($data['list8']){
            $list8= empty($EduLilundata[$data['list8']])?'':$EduLilundata[$data['list8']];
            $where .= " and    i.lilun  not  like '%".$list8."%' ";
            $wherenk .= " and  tpname  not  like '%".$list8."%' ";
        }
        ////////////////////////
        if($data['list9']){
            $list9 = empty($EduLdata[$data['list9']])?'':$EduLdata[$data['list9']];
            $where .= " and   ld not  like '%".$list9."%' ";
        }

        if($data['list10']){
            $list10 = empty($EduLdata[$data['list10']])?'':$EduLdata[$data['list10']];
            $where .=  " and  ld  not like '%".$list10."%' ";

        }
        if($data['list11']){
            $list11=  empty($EduLdata[$data['list11']])?'':$EduLdata[$data['list11']];
            $where .=  " and  ld  not  like '%".$list11."%' ";
        }
        if($data['list12']){
            $list12=  empty($EduLdata[$data['list12']])?'':$EduLdata[$data['list12']];
            $where .=  " and  ld  not  like '%".$list12."%' ";
        }
        if($data['list13']){
            $list13=empty($EduLdata[$data['list13']])?'':$EduLdata[$data['list13']];
            $where .=  " and  ld  not  like '%".$list13."%' ";
            $result['industry'][] =$list13;
        }
        if($data['list14']){
            $list14=empty($EduLdata[$data['list14']])?'':$EduLdata[$data['list14']];
            $where .=  " and  ld  not  like '%".$list14."%' ";
        }
        $wheres = $where ;
        ////////////////////////
        if($data['list21']){
            $list21 = empty($EduLilundata[$data['list21']])?'':$EduLilundata[$data['list21']];
            $wheres .= " and   i.lilun  like '%".$list21."%'";
        }

        $wheretag = 0;

        $EduUserScIddata = Caches::EduUserScId($uid);
        if($type ==1){
            $data2	= Shcool::search_major_recom($schoolid,$where,'',$wheretag,$limit);
        }elseif($type ==2){
            $data2 = Shcool::search_major_recom($schoolid,$wheres,'',$wheretag,$limit);
        }

        if(is_array($data2)){
            $res =[];
            foreach($data2 as $key =>$value){
                $res['list'][$key]['titlename'] = $value['titlename'];
                $res['list'][$key]['lilun'] = $value['lilun'];
                $res['list'][$key]['alclass'] = $value['alclass'];
                $res['list'][$key]['zid'] = $value['id'];
                $res['list'][$key]['closer'] = $value['closer'];
                $res['list'][$key]['has'] = in_array($value['id'],$EduUserScIddata)?1:0;
                $res['list'][$key]['colleges'] = $value['colleges'];
                if($data['list21']){
                    $res['list'][$key]['priority'] = strpos($value['lilun'],$list21)===false?0:1;
                }else{
                    $res['list'][$key]['priority'] = 0;
                }

            }
            return $res;
        }else{
            return $data2;
        }




    }
    /**    //决策辅助
     * @param $uid
     * @param $eid
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function policy($uid, $eid){
        // 没有完成决策树
        $data= Caches::EduWenSrUid($uid);
        if(!$data || !$data['openok']){
            return false;
        }
        //学业目标
        if($data['list1'] == 1){
            $str ="高等专科(高职)院校";
        }elseif($data['list1']==2){
            $str ="本科生";
        }else{
            $str ="研究生";
        }

        $type= self::name('edu_info')->whereid($eid)->value('type');
        $result['target'] = $str;
        if(($type == 2 && $data['list1']!=1) || ($type ==$data['list1']) ){
            $result['targethas'] = 1;
        }else{
            $result['targethas'] = 0;
        }
        $wherelist4 =$wherelist5=$wherelist6= '';
        $EduLilundata = Caches::EduLilun();
        $EduLddata = Caches::EduLd();
        if($data['list6']){
            $list6 = empty($EduLilundata[$data['list6']])?'': $EduLilundata[$data['list6']];
            $wherelist4.= " and  lilun not  like '%".$list6."%' ";
            $result['discipline'][] =$list6;
        }
        if($data['list7']){
            $list7=  empty($EduLilundata[$data['list7']])?'': $EduLilundata[$data['list7']];
            $wherelist4 .= " and   lilun  not  like '%".$list7."%' ";
            $result['discipline'][] =$list7;
        }
        if(!$data['list6'] && !$data['list7'] ){
            $result['discipline']='';
        }
        if($data['list8']){
            $list8= empty($EduLilundata[$data['list8']])?'': $EduLilundata[$data['list8']];
            $wherelist4 .= " and   lilun  not  like '%".$list8."%' ";

        }
        ////////////////////////
        if($data['list9']){
            $list9= empty($EduLddata[$data['list9']])?'': $EduLddata[$data['list9']];
            $wherelist5 .= " and   ld not  like '%".$list9."%' ";
            $result['interest'][] =$list9;
        }
        if($data['list10']){
            $list10=empty($EduLddata[$data['list10']])?'': $EduLddata[$data['list10']];
            $wherelist5 .=  " and  ld  not like '%".$list10."%' ";
            $result['interest'][] =$list10;
        }
        if($data['list11']){
            $list11=empty($EduLddata[$data['list11']])?'': $EduLddata[$data['list11']];
            $wherelist5 .=  " and  ld  not  like '%".$list11."%' ";
            $result['interest'][] =$list11;
        }
        if(!$data['list9'] && !$data['list10'] && !$data['list11'] ){
            $result['interest']='';
        }
        if($data['list12']){
            $list12=empty($EduLddata[$data['list12']])?'': $EduLddata[$data['list12']];
            $wherelist6 .=  " and  ld  not  like '%".$list12."%' ";
            $result['industry'][] =$list12;
        }
        if($data['list13']){
            $list13=empty($EduLddata[$data['list13']])?'': $EduLddata[$data['list13']];
            $wherelist6 .=  " and  ld  not  like '%".$list13."%' ";
            $result['industry'][] =$list13;
        }
        if($data['list14']){
            $list14= empty($EduLddata[$data['list13']])?'': $EduLddata[$data['list13']];
            $wherelist6 .=  " and  ld  not  like '%".$list14."%' ";
            $result['industry'][] =$list14;
        }
        if(!$data['list12'] && !$data['list13'] && !$data['list14'] ){
            $result['industry'][]='无';
        }
        ////////////////////////

        if($data['list21']){
            $list21 =empty($EduLilundata[$data['list21']])?'': $EduLilundata[$data['list21']];
            $wheres = " and   lilun  like '%".$list21."%'";
            $result['advantage'] =$list21;
        }

        $sql2 = "SELECT count(id) as num  FROM kw_edu_info where  `open` = '1' ".$wheres." and id = ".$eid."";
        $advantagehasnum= self::query($sql2);
        $result['advantagehasnum'] = $advantagehasnum[0]['num'] ;

        $sql4 = "SELECT count(id) as num  FROM kw_edu_info where `open` = '1' ".$wherelist4." and id = ".$eid."";
        $disciplinehasnum = self::query($sql4);
        $result['disciplinehasnum'] = $disciplinehasnum[0]['num'];

        $sql5= "SELECT count(id) as num  FROM kw_edu_info where   `open` = '1' ".$wherelist5."  and id = ".$eid."";
        $interesthasnum = self::query($sql5);
        $result['interesthasnum'] = $interesthasnum[0]['num'];

        $sql6 = "SELECT count(id) as num  FROM kw_edu_info where `open` = '1' ".$wherelist6." and id = ".$eid."";
        $industryhasnum = self::query($sql6);
        $result['industryhasnum'] = $industryhasnum[0]['num'];
        //是否匹配
        if($advantagehasnum[0]['num'] && $disciplinehasnum[0]['num'] && $interesthasnum[0]['num'] && $industryhasnum[0]['num']){
            $result['policy'] =1;
        }else{
            $result['policy'] =0;
        }

        $num = 0;
        $result['advantagehasnum'] && $num++;
        $result['disciplinehasnum'] && $num++;
        $result['industryhasnum'] && $num++;
        $result['interesthasnum'] && $num++;
        $result['targethas'] && $num++;
        $result['tages'] = ($num/5)*100;
        return $result;
    }

    /** 专业  决策辅助
     * @param $uid
     * @param $eid
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public static function userPolicy($uid, $eid){
        $titlename = self::name('edu_info')->whereid($eid)->value('titlename');
        $eduwendata = Caches::EduWenSrUid($uid);
        $uidwenkey = md5(implode('',$eduwendata));
        $usereidwenploicykey  = 'Navigationa:policy:uid:'.$uid.':eid'.$eid.$uidwenkey;
        $data  = Cache::get($usereidwenploicykey);
        if(!$data){
            $data= self::policy($uid,$eid);
            $data  = Cache::get($usereidwenploicykey,$data,CACHE_DATA_TIME);
        }
        $data['titlename'] =$titlename;
        return $data;
    }
}

