<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use app\common\Jssdk;


Route::domain('wapi','api');
Route::domain('part', 'index');


$host = $this->request->domain();
$jssdk = new Jssdk($host);
$signPackage = $jssdk->GetSignPackage();




Route::domain('fx', 'fx');

Route::domain('xy', [
    // 动态注册域名的路由规则
    '/'=>function() use($signPackage ){return view('xueye/index/index',['signPackage'=>$signPackage]);},
    'index/information_2'=>function()use ($signPackage){return view('xueye/index/information_2',['signPackage'=>$signPackage]);},
    'index/information_1'=>function()use ($signPackage){return view('xueye/index/information_1',['signPackage'=>$signPackage]);},
    'index/index'=>function() use($signPackage ){return view('xueye/index/index',['signPackage'=>$signPackage]);},
    'reg/explain'   => function() use($signPackage ){return view('xueye/reg/explain',['signPackage'=>$signPackage]);},
    'reg/agreement'   => function() use($signPackage ){return view('xueye/reg/agreement',['signPackage'=>$signPackage]);},
    'reg/school'   => function() use($signPackage ){return view('xueye/reg/school',['signPackage'=>$signPackage]);},
    'reg'   => function() use($signPackage ){return view('xueye/reg/index',['signPackage'=>$signPackage]);},

    'login/password'   => function() use($signPackage ){return view('xueye/login/password',['signPackage'=>$signPackage]);},
    'login/examine'   => function() use($signPackage ){return view('xueye/login/examine',['signPackage'=>$signPackage]);},
    'login/newpass'   => function() use($signPackage ){return view('xueye/login/newpass',['signPackage'=>$signPackage]);},
    'login/duanxin'   => function() use($signPackage ){return view('xueye/login/duanxin',['signPackage'=>$signPackage]);},
    'login'   => function() use($signPackage ){return view('xueye/login/index',['signPackage'=>$signPackage]);},


    'user/picture'   => function() use($signPackage ){return view('xueye/user/picture',['signPackage'=>$signPackage]);},
    'user/apply'   => function() use($signPackage ){return view('xueye/user/apply',['signPackage'=>$signPackage]);},
    'user/experience'   => function() use($signPackage ){return view('xueye/user/experience',['signPackage'=>$signPackage]);},
    'user/family_situation'   => function() use($signPackage ){return view('xueye/user/family_situation',['signPackage'=>$signPackage]);},
    'user/gonggao'   => function() use($signPackage ){return view('xueye/user/gonggao',['signPackage'=>$signPackage]);},
    'user/home'   => function() use($signPackage ){return view('xueye/user/home',['signPackage'=>$signPackage]);},
    'user/input'   => function() use($signPackage ){return view('xueye/user/input',['signPackage'=>$signPackage]);},
    'user/integral'   => function() use($signPackage ){return view('xueye/user/integral',['signPackage'=>$signPackage]);},
    'user/label'   => function() use($signPackage ){return view('xueye/user/label',['signPackage'=>$signPackage]);},
    'user/label_fillIn'   => function() use($signPackage ){return view('xueye/user/label_fillIn',['signPackage'=>$signPackage]);},
    'user/logout'   => function() use($signPackage ){
        return view('xueye/user/logout',['signPackage'=>$signPackage]);
    },
    'user/modify'   => function() use($signPackage ){return view('xueye/user/modify',['signPackage'=>$signPackage]);},
    'user/modify_picture'   => function() use($signPackage ){return view('xueye/user/modify_picture',['signPackage'=>$signPackage]);},
    'user/modify_zy'   => function() use($signPackage ){return view('xueye/user/modify_zy',['signPackage'=>$signPackage]);},
    'user/not_pass'   => function() use($signPackage ){return view('xueye/user/not_pass',['signPackage'=>$signPackage]);},
    'user/password'   => function() use($signPackage ){return view('xueye/user/password',['signPackage'=>$signPackage]);},
    'user/picturelog'   => function() use($signPackage ){return view('xueye/user/picturelog',['signPackage'=>$signPackage]);},
    'user/qr_code'   => function() use($signPackage ){return view('xueye/user/qr_code',['signPackage'=>$signPackage]);},
    'user/recharge'   => function() use($signPackage ){return view('xueye/user/recharge',['signPackage'=>$signPackage]);},
    'user/scores_list'   => function() use($signPackage ){return view('xueye/user/scores_list',['signPackage'=>$signPackage]);},
    'user/userinfo'   => function() use($signPackage ){return view('xueye/user/userinfo',['signPackage'=>$signPackage]);},
    'user/apply'   => function() use($signPackage ){return view('xueye/user/apply',['signPackage'=>$signPackage]);},
    'user/userinfo-2'   => function() use($signPackage ){return view('xueye/user/userinfo-2',['signPackage'=>$signPackage]);},
    'user/userinfo-3'   => function() use($signPackage ){return view('xueye/user/userinfo-3',['signPackage'=>$signPackage]);},
    'user/verification'   => function() use($signPackage ){return view('xueye/user/verification',['signPackage'=>$signPackage]);},
    'user/vip'   => function() use($signPackage ){return view('xueye/user/vip',['signPackage'=>$signPackage]);},
    'user/vip_text'   => function() use($signPackage ){return view('xueye/user/vip_text',['signPackage'=>$signPackage]);},
    'user/volume'   => function() use($signPackage ){return view('xueye/user/volume',['signPackage'=>$signPackage]);},
    'user/wallet'   => function() use($signPackage ){return view('xueye/user/wallet',['signPackage'=>$signPackage]);},
    'user/write_home'   => function() use($signPackage ){return view('xueye/user/write_home',['signPackage'=>$signPackage]);},
    'user/write_scores'   => function() use($signPackage ){return view('xueye/user/write_scores',['signPackage'=>$signPackage]);},
    'user/writing'   => function() use($signPackage ){return view('xueye/user/writing',['signPackage'=>$signPackage]);},
    'user/auth'   => function() use($signPackage ){return view('xueye/user/auth',['signPackage'=>$signPackage]);},
    'user/card'   => function() use($signPackage ){return view('xueye/user/card',['signPackage'=>$signPackage]);},
    'user/collar'   => function() use($signPackage ){return view('xueye/user/collar',['signPackage'=>$signPackage]);},
    'user'   => function() use($signPackage ){return view('xueye/user/index',['signPackage'=>$signPackage]);},



    'aggregate/major_ben'   => function() use($signPackage ){return view('xueye/aggregate/major_ben',['signPackage'=>$signPackage]);},
    'aggregate/major_gk'   => function() use($signPackage ){return view('xueye/aggregate/major_gk',['signPackage'=>$signPackage]);},
    'aggregate/major_list'   => function() use($signPackage ){return view('xueye/aggregate/major_list',['signPackage'=>$signPackage]);},
    'aggregate/major_rank'   => function() use($signPackage ){return view('xueye/aggregate/major_rank',['signPackage'=>$signPackage]);},
    'aggregate/major_search'   => function() use($signPackage ){return view('xueye/aggregate/major_search',['signPackage'=>$signPackage]);},
    'aggregate/major_zhuan'   => function() use($signPackage ){return view('xueye/aggregate/major_zhuan',['signPackage'=>$signPackage]);},
    'aggregate/school_list'   => function() use($signPackage ){return view('xueye/aggregate/school_list',['signPackage'=>$signPackage]);},
    'aggregate/school_search'   => function() use($signPackage ){return view('xueye/aggregate/school_search',['signPackage'=>$signPackage]);},
    'aggregate/major'   => function() use($signPackage ){return view('xueye/aggregate/major',['signPackage'=>$signPackage]);},


     'character/index2'   => function() use($signPackage ){return view('xueye/character/index2',['signPackage'=>$signPackage]);},
       'character/index-1'   => function() use($signPackage ){return view('xueye/character/index-1',['signPackage'=>$signPackage]);},
       'character/index-2'   => function() use($signPackage ){return view('xueye/character/index-2',['signPackage'=>$signPackage]);},
       'character/index-3'   => function() use($signPackage ){return view('xueye/character/index-3',['signPackage'=>$signPackage]);},
        'character/notice2'   => function() use($signPackage ){return view('xueye/character/notice2',['signPackage'=>$signPackage]);},
       'character/notice'   => function() use($signPackage ){return view('xueye/character/notice',['signPackage'=>$signPackage]);},

       'character/see-1'   => function() use($signPackage ){return view('xueye/character/see-1',['signPackage'=>$signPackage]);},
       'character/see-2'   => function() use($signPackage ){return view('xueye/character/see-2',['signPackage'=>$signPackage]);},
       'character/see-3'   => function() use($signPackage ){return view('xueye/character/see-3',['signPackage'=>$signPackage]);},
   	'character/deviation'   => function() use($signPackage ){return view('xueye/character/deviation',['signPackage'=>$signPackage]);},
   	  'character/see2'   => function() use($signPackage ){return view('xueye/character/see2',['signPackage'=>$signPackage]);},
       'character/see'   => function() use($signPackage ){return view('xueye/character/see',['signPackage'=>$signPackage]);},

       'character/guide'   => function() use($signPackage ){return view('xueye/character/guide',['signPackage'=>$signPackage]);},

       'character/tongdao'   => function() use($signPackage ){return view('xueye/character/tongdao',['signPackage'=>$signPackage]);},
       'character'   => function() use($signPackage ){return view('xueye/character/index',['signPackage'=>$signPackage]);},

    'collection/major'   => function() use($signPackage ){return view('xueye/collection/major',['signPackage'=>$signPackage]);},
    'collection'   => function() use($signPackage ){return view('xueye/collection/index',['signPackage'=>$signPackage]);},


    'dynamic/data_content'   => function() use($signPackage ){return view('xueye/dynamic/data_content',['signPackage'=>$signPackage]);},
    'dynamic/dynamic_content'   => function() use($signPackage ){return view('xueye/dynamic/dynamic_content',['signPackage'=>$signPackage]);},
    'dynamic/dynamic_list'   => function() use($signPackage ){return view('xueye/dynamic/dynamic_list',['signPackage'=>$signPackage]);},

    'dynamic/subject_list'   => function() use($signPackage ){return view('xueye/dynamic/subject_list',['signPackage'=>$signPackage]);},
    'dynamic/apply'   => function() use($signPackage ){return view('xueye/dynamic/apply',['signPackage'=>$signPackage]);},
    'dynamic/chart'   => function() use($signPackage ){return view('xueye/dynamic/chart',['signPackage'=>$signPackage]);},
    'dynamic/data'   => function() use($signPackage ){return view('xueye/dynamic/data',['signPackage'=>$signPackage]);},
    'dynamic'   => function() use($signPackage ){return view('xueye/dynamic/index',['signPackage'=>$signPackage]);},
    'index/index_beifen'   => function() use($signPackage ){return view('xueye/index/index_beifen',['signPackage'=>$signPackage]);},
    'index/activity'   => function() use($signPackage ){return view('xueye/index/activity',['signPackage'=>$signPackage]);},
    'index/index-1'   => function() use($signPackage ){return view('xueye/index/index-1',['signPackage'=>$signPackage]);},
    'index/index2'   => function() use($signPackage ){return view('xueye/index/index2',['signPackage'=>$signPackage]);},
    'index/selection'   => function() use($signPackage ){return view('xueye/index/selection',['signPackage'=>$signPackage]);},
    'index/vip'   => function() use($signPackage ){return view('xueye/index/vip',['signPackage'=>$signPackage]);},
    'index'   => function() use($signPackage ){return view('xueye/index/index',['signPackage'=>$signPackage]);},
    'jcyj/combination'   => function() use($signPackage ){return view('xueye/jcyj/combination',['signPackage'=>$signPackage]);},
    'jcyj/details'   => function() use($signPackage ){return view('xueye/jcyj/details',['signPackage'=>$signPackage]);},
    'jcyj/job'   => function() use($signPackage ){return view('xueye/jcyj/job',['signPackage'=>$signPackage]);},
    'jcyj/major_list'   => function() use($signPackage ){return view('xueye/jcyj/major_list',['signPackage'=>$signPackage]);},
    'jcyj/option1'   => function() use($signPackage ){return view('xueye/jcyj/option1',['signPackage'=>$signPackage]);},
    'jcyj/option2'   => function() use($signPackage ){return view('xueye/jcyj/option2',['signPackage'=>$signPackage]);},
    'jcyj/option3'   => function() use($signPackage ){return view('xueye/jcyj/option3',['signPackage'=>$signPackage]);},
    'jcyj/option4'   => function() use($signPackage ){return view('xueye/jcyj/option4',['signPackage'=>$signPackage]);},
    'jcyj/option5'   => function() use($signPackage ){return view('xueye/jcyj/option5',['signPackage'=>$signPackage]);},
    'jcyj/option6'   => function() use($signPackage ){return view('xueye/jcyj/option6',['signPackage'=>$signPackage]);},
    'jcyj/option7'   => function() use($signPackage ){return view('xueye/jcyj/option7',['signPackage'=>$signPackage]);},
    'jcyj/option8'   => function() use($signPackage ){return view('xueye/jcyj/option8',['signPackage'=>$signPackage]);},
    'jcyj/presentation'   => function() use($signPackage ){return view('xueye/jcyj/presentation',['signPackage'=>$signPackage]);},
    'jcyj/professional_details'   => function() use($signPackage ){return view('xueye/jcyj/professional_details',['signPackage'=>$signPackage]);},
    'jcyj/professional_report'   => function() use($signPackage ){return view('xueye/jcyj/professional_report',['signPackage'=>$signPackage]);},
    'jcyj/recommend'   => function() use($signPackage ){return view('xueye/jcyj/recommend',['signPackage'=>$signPackage]);},
    'jcyj/school_details'   => function() use($signPackage ){return view('xueye/jcyj/school_details',['signPackage'=>$signPackage]);},
    'jcyj/school'   => function() use($signPackage ){return view('xueye/jcyj/school',['signPackage'=>$signPackage]);},
    'jcyj/strategy'   => function() use($signPackage ){return view('xueye/jcyj/strategy',['signPackage'=>$signPackage]);},
    'jcyj'   => function() use($signPackage ){return view('xueye/jcyj/index',['signPackage'=>$signPackage]);},
    'message/gg_details'   => function() use($signPackage ){return view('xueye/message/gg_details',['signPackage'=>$signPackage]);},
    'message/index-1'   => function() use($signPackage ){return view('xueye/message/index-1',['signPackage'=>$signPackage]);},
    'message'   => function() use($signPackage ){return view('xueye/message/index',['signPackage'=>$signPackage]);},

    'presentation/branch$'   => function() use($signPackage ){return view('xueye/presentation/branch',['signPackage'=>$signPackage]);},
       'presentation/branch_list'   => function() use($signPackage ){return view('xueye/presentation/branch_list',['signPackage'=>$signPackage]);},
       'presentation/branch1'   => function() use($signPackage ){return view('xueye/presentation/branch1',['signPackage'=>$signPackage]);},
       'presentation/branch2'   => function() use($signPackage ){return view('xueye/presentation/branch2',['signPackage'=>$signPackage]);},
       'presentation/branch3'   => function() use($signPackage ){return view('xueye/presentation/branch3',['signPackage'=>$signPackage]);},
       'presentation/character_list2'   => function() use($signPackage ){return view('xueye/presentation/character_list2',['signPackage'=>$signPackage]);},
       'presentation/character_list'   => function() use($signPackage ){return view('xueye/presentation/character_list',['signPackage'=>$signPackage]);},

      'presentation/dycp4'   => function() use($signPackage ){return view('xueye/presentation/dycp4',['signPackage'=>$signPackage]);},
      'presentation/dycp3'   => function() use($signPackage ){return view('xueye/presentation/dycp3',['signPackage'=>$signPackage]);},
      'presentation/dycp2'   => function() use($signPackage ){return view('xueye/presentation/dycp2',['signPackage'=>$signPackage]);},
      'presentation/dycp1'   => function() use($signPackage ){return view('xueye/presentation/dycp1',['signPackage'=>$signPackage]);},
      'presentation/dycp'   => function() use($signPackage ){return view('xueye/presentation/dycp',['signPackage'=>$signPackage]);},

       'presentation/character4'   => function() use($signPackage ){return view('xueye/presentation/character4',['signPackage'=>$signPackage]);},
       'presentation/character3'   => function() use($signPackage ){return view('xueye/presentation/character3',['signPackage'=>$signPackage]);},
       'presentation/character2'   => function() use($signPackage ){return view('xueye/presentation/character2',['signPackage'=>$signPackage]);},
       'presentation/character'   => function() use($signPackage ){return view('xueye/presentation/character',['signPackage'=>$signPackage]);},
       'presentation/decision_parameter'   => function() use($signPackage ){return view('xueye/presentation/decision_parameter',['signPackage'=>$signPackage]);},
       'presentation/hangye'   => function() use($signPackage ){return view('xueye/presentation/hangye',['signPackage'=>$signPackage]);},
       'presentation/major'   => function() use($signPackage ){return view('xueye/presentation/major',['signPackage'=>$signPackage]);},
       'presentation/strategy'   => function() use($signPackage ){return view('xueye/presentation/strategy',['signPackage'=>$signPackage]);},
       'presentation/xuankao'   => function() use($signPackage ){return view('xueye/presentation/xuankao',['signPackage'=>$signPackage]);},
       'presentation/xkyqfb'   => function() use($signPackage ){return view('xueye/presentation/xkyqfb',['signPackage'=>$signPackage]);},
       'presentation/xueke'   => function() use($signPackage ){return view('xueye/presentation/xueke',['signPackage'=>$signPackage]);},
       'presentation/zhuanye'   => function() use($signPackage ){return view('xueye/presentation/zhuanye',['signPackage'=>$signPackage]);},
       'presentation'   => function() use($signPackage ){return view('xueye/presentation/index',['signPackage'=>$signPackage]);},

    'scores/result$'   => function() use($signPackage ){return view('xueye/scores/result',['signPackage'=>$signPackage]);},
    'scores/result3_1'   => function() use($signPackage ){return view('xueye/scores/result3_1',['signPackage'=>$signPackage]);},
    'scores/result1'   => function() use($signPackage ){return view('xueye/scores/result1',['signPackage'=>$signPackage]);},
    'scores/result2'   => function() use($signPackage ){return view('xueye/scores/result2',['signPackage'=>$signPackage]);},
    'scores/result3'   => function() use($signPackage ){return view('xueye/scores/result3',['signPackage'=>$signPackage]);},
    'scores/result4'   => function() use($signPackage ){return view('xueye/scores/result4',['signPackage'=>$signPackage]);},
    'scores/result5_1'   => function() use($signPackage ){return view('xueye/scores/result5_1',['signPackage'=>$signPackage]);},
    'scores/result5$'   => function() use($signPackage ){return view('xueye/scores/result5',['signPackage'=>$signPackage]);},
    'scores/search1'   => function() use($signPackage ){return view('xueye/scores/search1',['signPackage'=>$signPackage]);},
    'scores/search2'   => function() use($signPackage ){return view('xueye/scores/search2',['signPackage'=>$signPackage]);},
    'scores/search3'   => function() use($signPackage ){return view('xueye/scores/search3',['signPackage'=>$signPackage]);},
    'scores/search4'   => function() use($signPackage ){return view('xueye/scores/search4',['signPackage'=>$signPackage]);},
    'scores/search5'   => function() use($signPackage ){return view('xueye/scores/search5',['signPackage'=>$signPackage]);},
    'scores/search$'   => function() use($signPackage ){return view('xueye/scores/search',['signPackage'=>$signPackage]);},
    'scores'   => function() use($signPackage ){return view('xueye/scores/index',['signPackage'=>$signPackage]);},
    'search/major'   => function() use($signPackage ){return view('xueye/search/major',['signPackage'=>$signPackage]);},
    'search'   => function() use($signPackage ){return view('xueye/search/index',['signPackage'=>$signPackage]);},
    'sdfs'   => function() use($signPackage ){return view('xueye/sdfs/index',['signPackage'=>$signPackage]);},
// 引导页面
    'transition'   => function()use ($signPackage){return view('xueye/transition/index',['signPackage'=>$signPackage]);},
]);
Route::domain('zt', [
    // 动态注册域名的路由规则
    '/'=>function()use ($signPackage){return view('zhituo/index/index',['signPackage'=>$signPackage]);},
    //首页
    'index/information_2'=>function()use ($signPackage){return view('zhituo/index/information_2',['signPackage'=>$signPackage]);},
    'index/information_1'=>function()use ($signPackage){return view('zhituo/index/information_1',['signPackage'=>$signPackage]);},
    'index/index'=>function()use ($signPackage){return view('zhituo/index/index',['signPackage'=>$signPackage]);},
    //登录
    'login/password'   => function()use ($signPackage){return view('zhituo/login/password',['signPackage'=>$signPackage]);},
    'login/examine'   => function()use ($signPackage){return view('zhituo/login/examine',['signPackage'=>$signPackage]);},
    'login/newpass'   => function()use ($signPackage){return view('zhituo/login/newpass',['signPackage'=>$signPackage]);},
    'login/duanxin'   => function()use ($signPackage){return view('zhituo/login/duanxin',['signPackage'=>$signPackage]);},
    'login'   => function()use ($signPackage){return view('zhituo/login/index',['signPackage'=>$signPackage]);},
    //案例体验
    'experience/index'   => function()use ($signPackage){return view('zhituo/experience/index',['signPackage'=>$signPackage]);},
    'experience/content'   => function()use ($signPackage){return view('zhituo/experience/content',['signPackage'=>$signPackage]);},
    'experience/evaluate'   => function()use ($signPackage){return view('zhituo/experience/evaluate',['signPackage'=>$signPackage]);},
    'experience/experience'   => function()use ($signPackage){return view('zhituo/experience/experience',['signPackage'=>$signPackage]);},
    'experience/loading'   => function()use ($signPackage){return view('zhituo/experience/loading',['signPackage'=>$signPackage]);},
    'experience/mark'   => function()use ($signPackage){return view('zhituo/experience/mark',['signPackage'=>$signPackage]);},
    'experience/plan_log'   => function()use ($signPackage){return view('zhituo/experience/plan_log',['signPackage'=>$signPackage]);},
    'experience/plan'   => function()use ($signPackage){return view('zhituo/experience/plan',['signPackage'=>$signPackage]);},
    'experience/presentation'   => function()use ($signPackage){return view('zhituo/experience/presentation',['signPackage'=>$signPackage]);},
    'experience/write_plan2'   => function()use ($signPackage){return view('zhituo/experience/write_plan2',['signPackage'=>$signPackage]);},
    'experience/write_plan'   => function()use ($signPackage){return view('zhituo/experience/write_plan',['signPackage'=>$signPackage]);},
    'experience/recommend'   => function()use ($signPackage){return view('zhituo/experience/recommend',['signPackage'=>$signPackage]);},
	 'experience/set1'   => function()use ($signPackage){return view('zhituo/experience/set1',['signPackage'=>$signPackage]);},
	 'experience/set2'   => function()use ($signPackage){return view('zhituo/experience/set2',['signPackage'=>$signPackage]);},
	 'experience/set3'   => function()use ($signPackage){return view('zhituo/experience/set3',['signPackage'=>$signPackage]);},
     'experience/payment'   => function()use ($signPackage){return view('zhituo/experience/payment',['signPackage'=>$signPackage]);},
    'experience'   => function()use ($signPackage){return view('zhituo/experience/index',['signPackage'=>$signPackage]);},
    //实习报名
    'invitation/index'   => function()use ($signPackage){return view('zhituo/invitation/index',['signPackage'=>$signPackage]);},
    'invitation/journal'   => function()use ($signPackage){return view('zhituo/invitation/journal',['signPackage'=>$signPackage]);},
    'invitation/jihui'   => function()use ($signPackage){return view('zhituo/invitation/jihui',['signPackage'=>$signPackage]);},
    'invitation'   => function()use ($signPackage){return view('zhituo/invitation/index',['signPackage'=>$signPackage]);},

    //行业和职业
    'hangye/index'   => function()use ($signPackage){return view('zhituo/hangye/index',['signPackage'=>$signPackage]);},
    'hangye/new_dynamic'   => function()use ($signPackage){return view('zhituo/hangye/new_dynamic',['signPackage'=>$signPackage]);},
    'hangye/dynamic_list'   => function()use ($signPackage){return view('zhituo/hangye/dynamic_list',['signPackage'=>$signPackage]);},
    'hangye/dynamic_content'   => function()use ($signPackage){return view('zhituo/hangye/dynamic_content',['signPackage'=>$signPackage]);},
    'hangye/zy_search'   => function()use ($signPackage){return view('zhituo/hangye/zy_search',['signPackage'=>$signPackage]);},
    'hangye/zy_list'   => function()use ($signPackage){return view('zhituo/hangye/zy_list',['signPackage'=>$signPackage]);},
    'hangye/zy_content'   => function()use ($signPackage){return view('zhituo/hangye/zy_content',['signPackage'=>$signPackage]);},
    'hangye'   => function()use ($signPackage){return view('zhituo/hangye/index',['signPackage'=>$signPackage]);},
    //教练
    'jiaolian/index'   => function()use ($signPackage){return view('zhituo/jiaolian/index',['signPackage'=>$signPackage]);},
    'jiaolian/dynamic'   => function()use ($signPackage){return view('zhituo/jiaolian/dynamic',['signPackage'=>$signPackage]);},
    'jiaolian/dynamic_details'   => function()use ($signPackage){return view('zhituo/jiaolian/dynamic_details',['signPackage'=>$signPackage]);},
    'jiaolian/evaluate'   => function()use ($signPackage){return view('zhituo/jiaolian/evaluate',['signPackage'=>$signPackage]);},
    'jiaolian/experience'   => function()use ($signPackage){return view('zhituo/jiaolian/experience',['signPackage'=>$signPackage]);},
    'jiaolian/jiaolian_list'   => function()use ($signPackage){return view('zhituo/jiaolian/jiaolian_list',['signPackage'=>$signPackage]);},
    'jiaolian/jiaolian_search'   => function()use ($signPackage){return view('zhituo/jiaolian/jiaolian_search',['signPackage'=>$signPackage]);},
    'jiaolian/launch'   => function()use ($signPackage){return view('zhituo/jiaolian/launch',['signPackage'=>$signPackage]);},
    'jiaolian/read'   => function()use ($signPackage){return view('zhituo/jiaolian/read',['signPackage'=>$signPackage]);},
    'jiaolian/seek'   => function()use ($signPackage){return view('zhituo/jiaolian/seek',['signPackage'=>$signPackage]);},
    'jiaolian/space'   => function()use ($signPackage){return view('zhituo/jiaolian/space',['signPackage'=>$signPackage]);},
    'jiaolian/star'   => function()use ($signPackage){return view('zhituo/jiaolian/star',['signPackage'=>$signPackage]);},
    'jiaolian/daiyan'   => function()use ($signPackage){return view('zhituo/jiaolian/daiyan',['signPackage'=>$signPackage]);},
    'jiaolian/demo'   => function()use ($signPackage){return view('zhituo/jiaolian/demo',['signPackage'=>$signPackage]);},
    'jiaolian'   => function()use ($signPackage){return view('zhituo/jiaolian/index',['signPackage'=>$signPackage]);},
    //朋友圈
    'friend/index'   => function()use ($signPackage){return view('zhituo/friend/index',['signPackage'=>$signPackage]);},
    'friend'   => function()use ($signPackage){return view('zhituo/friend/index',['signPackage'=>$signPackage]);},
    //消息
    'message/index'   => function()use ($signPackage){return view('zhituo/message/index',['signPackage'=>$signPackage]);},
    'message'   => function()use ($signPackage){return view('zhituo/message/index',['signPackage'=>$signPackage]);},
    //报告
    'presentation/index'   => function()use ($signPackage){return view('zhituo/presentation/index',['signPackage'=>$signPackage]);},
    'presentation/experience_log'   => function()use ($signPackage){return view('zhituo/presentation/experience_log',['signPackage'=>$signPackage]);},
    'presentation/demo'   => function()use ($signPackage){return view('zhituo/presentation/demo',['signPackage'=>$signPackage]);},
	'presentation/tsgw'   => function()use ($signPackage){return view('zhituo/presentation/tsgw',['signPackage'=>$signPackage]);},
	'presentation/hyph'   => function()use ($signPackage){return view('zhituo/presentation/hyph',['signPackage'=>$signPackage]);},
	'presentation/zysy'   => function()use ($signPackage){return view('zhituo/presentation/zysy',['signPackage'=>$signPackage]);},
	'presentation/xxnl'   => function()use ($signPackage){return view('zhituo/presentation/xxnl',['signPackage'=>$signPackage]);},
    'presentation'   => function()use ($signPackage){return view('zhituo/presentation/index',['signPackage'=>$signPackage]);},
    //注册
    'reg/index'   => function()use ($signPackage){return view('zhituo/reg/index',['signPackage'=>$signPackage]);},
    'reg/agreement'   => function()use ($signPackage){return view('zhituo/reg/agreement',['signPackage'=>$signPackage]);},
    'reg'   => function()use ($signPackage){return view('zhituo/reg/index',['signPackage'=>$signPackage]);},
    //现场实习
    'scene/index'   => function()use ($signPackage){return view('zhituo/scene/index',['signPackage'=>$signPackage]);},
    'scene/baogao'   => function()use ($signPackage){return view('zhituo/scene/baogao',['signPackage'=>$signPackage]);},
    'scene/close'   => function()use ($signPackage){return view('zhituo/scene/close',['signPackage'=>$signPackage]);},
    'scene/entry'   => function()use ($signPackage){return view('zhituo/scene/entry',['signPackage'=>$signPackage]);},
    'scene/history'   => function()use ($signPackage){return view('zhituo/scene/history',['signPackage'=>$signPackage]);},
    'scene/industry'   => function()use ($signPackage){return view('zhituo/scene/industry',['signPackage'=>$signPackage]);},
    'scene/input'   => function()use ($signPackage){return view('zhituo/scene/input',['signPackage'=>$signPackage]);},
    'scene/internship'   => function()use ($signPackage){return view('zhituo/scene/internship',['signPackage'=>$signPackage]);},
    'scene/jiaolian_list'   => function()use ($signPackage){return view('zhituo/scene/jiaolian_list',['signPackage'=>$signPackage]);},
    'scene/leave'   => function()use ($signPackage){return view('zhituo/scene/leave',['signPackage'=>$signPackage]);},
    'scene/post'   => function()use ($signPackage){return view('zhituo/scene/post',['signPackage'=>$signPackage]);},
    'scene/sendout'   => function()use ($signPackage){return view('zhituo/scene/sendout',['signPackage'=>$signPackage]);},
    'scene/transfer'   => function()use ($signPackage){return view('zhituo/scene/transfer',['signPackage'=>$signPackage]);},
    'scene/waitfor'   => function()use ($signPackage){return view('zhituo/scene/waitfor',['signPackage'=>$signPackage]);},
    'scene/demo'   => function()use ($signPackage){return view('zhituo/scene/demo',['signPackage'=>$signPackage]);},
    'scene'   => function()use ($signPackage){return view('zhituo/scene/index',['signPackage'=>$signPackage]);},
    //个人中心
    'user/index'   => function()use ($signPackage){return view('zhituo/user/index',['signPackage'=>$signPackage]);},
    'user/apply'   => function()use ($signPackage){return view('zhituo/user/apply',['signPackage'=>$signPackage]);},
    'user/auth'   => function()use ($signPackage){return view('zhituo/user/auth',['signPackage'=>$signPackage]);},
    'user/convention'   => function()use ($signPackage){return view('zhituo/user/convention',['signPackage'=>$signPackage]);},
    'user/experience'   => function()use ($signPackage){return view('zhituo/user/experience',['signPackage'=>$signPackage]);},
    'user/input'   => function()use ($signPackage){return view('zhituo/user/input',['signPackage'=>$signPackage]);},
    'user/integral'   => function()use ($signPackage){return view('zhituo/user/integral',['signPackage'=>$signPackage]);},
    'user/jifen'   => function()use ($signPackage){return view('zhituo/user/jifen',['signPackage'=>$signPackage]);},
    'user/label'   => function()use ($signPackage){return view('zhituo/user/label',['signPackage'=>$signPackage]);},
    'user/label_fillIn'   => function()use ($signPackage){return view('zhituo/user/label_fillIn',['signPackage'=>$signPackage]);},
    'user/not_pass'   => function()use ($signPackage){return view('zhituo/user/not_pass',['signPackage'=>$signPackage]);},
    'user/password'   => function()use ($signPackage){return view('zhituo/user/password',['signPackage'=>$signPackage]);},
    'user/picture'   => function()use ($signPackage){return view('zhituo/user/picture',['signPackage'=>$signPackage]);},
    'user/privacy'   => function()use ($signPackage){return view('zhituo/user/privacy',['signPackage'=>$signPackage]);},
    'user/demo'   => function()use ($signPackage){return view('zhituo/user/demo',['signPackage'=>$signPackage]);},
    'user/qr_code'   => function()use ($signPackage){return view('zhituo/user/qr_code',['signPackage'=>$signPackage]);},
    'user/recharge'   => function()use ($signPackage){return view('zhituo/user/recharge',['signPackage'=>$signPackage]);},
    'user/userinfo'   => function() use ( $signPackage  ){return view('zhituo/user/userinfo',['signPackage'=>$signPackage]);},
    'user/verification'   => function()use ($signPackage){return view('zhituo/user/verification',['signPackage'=>$signPackage]);},
    'user/vip'   => function()use ($signPackage){return view('zhituo/user/vip',['signPackage'=>$signPackage]);},
    'user/vip_text'   => function()use ($signPackage){return view('zhituo/user/vip_text',['signPackage'=>$signPackage]);},
    'user/wallet'   => function() use ($signPackage){ return view('zhituo/user/wallet',['signPackage'=>$signPackage]);},
    'user/writing'   => function()use ($signPackage){return view('zhituo/user/writing',['signPackage'=>$signPackage]);},
    'user/increase'   => function()use ($signPackage){return view('zhituo/user/increase',['signPackage'=>$signPackage]);},
    'user/set_pj'   => function()use ($signPackage){return view('zhituo/user/set_pj',['signPackage'=>$signPackage]);},
    'user/gai1'   => function()use ($signPackage){return view('zhituo/user/gai1',['signPackage'=>$signPackage]);},
    'user/gai2'   => function()use ($signPackage){return view('zhituo/user/gai2',['signPackage'=>$signPackage]);},
    'user/gai3'   => function()use ($signPackage){return view('zhituo/user/gai3',['signPackage'=>$signPackage]);},
    'user/jiesuan'   => function()use ($signPackage){return view('zhituo/user/jiesuan',['signPackage'=>$signPackage]);},
    'user'   => function()use ($signPackage){return view('zhituo/user/index',['signPackage'=>$signPackage]);},
    //
    'index'   => function()use ($signPackage){return view('zhituo/index/index',['signPackage'=>$signPackage]);},

]);


//Route::rule('reg','api/reg/index');
// 公共指向  index 模型
// 需要登录的

Route::rule('publics/wirteHomeList','index/publics/wirteHomeList');
Route::rule('publics/userWirtehome','index/publics/userWirtehome');
Route::rule('classname/roomClass','index/classname/roomClass');
Route::rule('classname/industry','index/classname/industry');
Route::rule('classname/getoccupation','index/classname/getoccupation');
Route::rule('classnamel/city','index/classnamel/city');
Route::rule('classnamel/calsstype','index/classnamel/calsstype');
Route::rule('classnamel/zyclass','index/classnamel/zyclass');
Route::rule('classnamel/cjclass','index/classnamel/cjclass');
Route::rule('classnamel/lxclass','index/classnamel/lxclass');
Route::rule('classnamel/fjclass','index/classnamel/fjclass');
Route::rule('classnamel/lilunclass','index/classnamel/lilunclass');
Route::rule('classnamel/schoollx','index/classnamel/schoollx');
Route::rule('classnamel/areaClass','index/classnamel/areaClass');
Route::rule('classnamel/nameClass','index/classnamel/nameClass');
Route::rule('classnamel/zhuanye','index/classnamel/zhuanye');
Route::rule('classnamel/occupation','index/classnamel/occupation');
Route::rule('classnamel/roomClass','index/classnamel/roomClass');
Route::rule('classnamel/testphp','index/classnamel/testphp');
Route::rule('classnamel/getoccupation','index/classnamel/getoccupation');
Route::rule('classnamel/industry2','index/classnamel/industry2');
Route::rule('classnamel/industry','index/classnamel/industry');
Route::rule('publics/send_email','index/publics/send_email');



Route::rule('publics/update_pic','index/publics/update_pic');
// 不需要登录的
Route::rule('ajax/code','index/ajax/code');
Route::rule('ajax/vcode','index/ajax/vcode');
Route::rule('ajax/vcode','index/ajax/vcode');
Route::rule('ajax/searchAll','index/ajax/searchAll');
Route::rule('ajax/person','index/ajax/person');




Route::post('user/midfyUserinfo','user/midfyUserinfo');
Route::post('user/shcoolVerification','user/shcoolVerification');
Route::post('user/midfy_passwd','user/midfy_passwd');

return [

];
