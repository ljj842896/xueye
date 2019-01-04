<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/15
 * Time: 13:40
 */

namespace app\common;

use PHPMailer\PHPMailer\PHPMailer;

class Smtp
{
    function send_mail($tomail, $name, $subject = '', $body = '', $attachment = null) {
        $mail = new PHPMailer();           //实例化PHPMailer对象
        $mail->CharSet = 'UTF-8';           //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $mail->IsSMTP();                    // 设定使用SMTP服务
        $mail->SMTPDebug = 0;               // SMTP调试功能 0=关闭 1 = 错误和消息 2 = 消息
        $mail->SMTPAuth = true;             // 启用 SMTP 验证功能
        $mail->SMTPSecure = 'ssl';          // 使用安全协议
        $mail->Host ="smtp.exmail.qq.com";// "smtp.qq.com"; // SMTP 服务器
        $mail->Port = 465;                  // SMTP服务器的端口号
        $mail->Username ='service@zhituoedu.com';// "rainbowa@wisecareer.org";    // SMTP服务器用户名
        $mail->Password = "jkxR6baFHNsCxcKu";     // SMTP服务器密码
        $mail->SetFrom('service@zhituoedu.com', '彩虹联');
        $replyEmail = '';                   //留空则为发件人EMAIL
        $replyName = '';                    //回复名称（留空则为发件人名称）
        $mail->AddReplyTo($replyEmail, $replyName);
        $mail->Subject = $subject;
        $mail->MsgHTML($body);
        $mail->AddAddress($tomail, $name);
        if (is_array($attachment)) { // 添加附件
            foreach ($attachment as $file) {
                is_file($file) && $mail->AddAttachment($file);
            }
        }
        return $mail->Send() ? true : $mail->ErrorInfo;
    }


    function get_content($name,$url){
        $content=<<<EOF
<head lang="en">
    <meta charset="UTF-8">
    <title></title>
    <style>
        *{
            margin:0;
            padding:0;
            -webkit-tap-highlight-color:rgba(255,0,0,0);
        }
        li{
            list-style:none;
        }
        a{
            text-decoration:none;
            blr:expression_r(this.onFocus=this.blur()); outline:none;
        }
        area {blr:expression_r(this.onFocus=this.blur())}
        h1,h2,h3,h4,h5,h6{
            font-weight:normal;
        }
        input,textarea,select{
            outline:none;
        }
        input[type="submit"], input[type="reset"], input[type="button"], button { -webkit-appearance: none; }
        textarea{  resize:none; overflow: hidden;background: #ffffff;outline: none;}
        .clear{
            clear:both;
        }
        img{
            border:none;
        }
        body{
            font-family:Arial,'微软雅黑', Helvetica, sans-serif;
        }
        select {
            /*Chrome和Firefox里面的边框是不一样的，所以复写了一下*/
            border: solid 1px #000;


            /*为下拉小箭头留出一点位置，避免被文字覆盖*/
            padding-right: 14px;
        }
        h2{
            font-size: 16px;
            font-weight: bold;
            line-height: 36px;
        }
        p{
            font-size: 14px;
            line-height: 24px;
        }
    h5{
        font-size: 14px;
        font-weight: bold;
    }
    
    </style>
    
</head>
<body>
<div align='center'><div class="open_email" style="margin-left: 8px; margin-top: 8px; margin-bottom: 8px; margin-right: 8px;">
    <div>
<span class="genEmailNicker">

</span>
        <br  />
<span class="genEmailContent">



<table width="100%" border="0" style="border-collapse: collapse;max-width:680px;margin:auto;">
    <tbody><tr>
        <td style="text-align:center; height:66px">
            <img src="http://www.wisecareer.org/images/public/logo.png"  width="100"/>
        </td>
    </tr>
    <tr><td style="height:6px;background-color:#f58020;font-size:0">&nbsp;</td></tr>
    <tr><td style="box-shadow: -2px 2px 2px #ddd, 0 0 0, 0 0 0, 2px 0 2px #ddd; background-color: #fff">
        <table border="0" width="100%" style="border-collapse: collapse;font-family:微软雅黑;">
            <tbody><tr><td height="40"></td></tr>
            <tr><td style="padding: 0 40px; word-wrap:break-word; word-break:break-all;text-align:left;font-size:16px;">
                <h2 style="font-size: 18px">{$name}同学：</h2>
                <p style="text-indent:30px;font-family:微软雅黑;">你申请的报告已生成，请查看邮箱附件。查看报告过程中有任何问题，请联系4000740520 </p>
                <p>&nbsp;</p>
                <div style="height: 40px"></div>
                <p style="text-align: center"><a target="_blank" href="{$url}" style="color:#fff; background-color: #f58020; font-size: 16px; padding: 12px 50px; text-decoration: none; border-radius: 5px">前往查看</a></p>
            </td></tr>
            <tr>
                <td>
                    <div style="height: 40px"></div>
                    <div style="height:20px;text-align:center;background:url(https://mc.qcloudimg.com/static/img/4311fa66da8749e76b4da5623dafecc1/line.jpg) repeat-x center center"><span style="display:inline-block;line-height:20px;background-color: #fff; padding: 0 20px">
                        此致，彩虹联团队</span></div>
                    <div style="height: 40px"></div>
                </td>
            </tr>
            </tbody>
        </table>
    </td></tr>
    <tr>
        <td style="text-align:center;color:#555;font-size: 14px">
            <div style="padding-top: 20px">
                <table width="100%">
                    <tr>
                        <td width="33%" align="center"><img width="100" src="http://www.wisecareer.org/images/public/qrcode_zt.jpg"/></td>
                        <td width="34%" align="center"><img width="100" src="http://www.wisecareer.org/images/public/qrcode_xy.jpg"/></td>
                        <td width="33%" align="center"><img width="100" src="http://www.wisecareer.org/images/public/qrcode_zd.jpg"/></td>
                    </tr>
                    <tr>
                        <td width="33%" align="center"><h5>职拓 e 站</h5></td>
                        <td width="34%" align="center"><h5>学业 e 站</h5></td>
                        <td width="33%" align="center"><h5>职道 e 站</h5></td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
    </tbody>
</table>
</span>
        <br  />
<span class="genEmailTail">

</span>
    </div>
</div></div>
</body>
EOF;

        return $content;
    }
}

