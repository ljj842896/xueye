<?php

namespace app\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'user_name'  =>  'token',
        'email'  =>  'email|token',
        'schoolname'  =>  'token',
        'nickname'  =>  'token',
        'sex'  =>  'token',
        'birthday'  =>  'token',
        'classname'  =>  'token',
    ];

}