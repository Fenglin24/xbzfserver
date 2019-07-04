<?php
namespace ExHelper;
class Email {
	/**
	 * 系统邮件发送函数
	 * @param string $to 接收邮件者邮箱
	 * @param string $toName 接收邮件者名称
	 * @param string $subject 邮件主题
	 * @param string $body 邮件内容
	 * @param string $attachment 附件列表
	 * @return boolean
	 * @author static7 <static7@qq.com>
	 * 用法举例：
	$res = \ExHelper\Email::sendEmail(
	'canlynet@qq.com',
	'丁华能',
	'测试邮件',
	'<div style="font-size:18px;">收到请回复。</div>',
	[APP_PATH . '/../网站开发规范.docx']
	);
	if ($res === true) {
	echo '邮件发送成功！';
	}
	注意参数$to，可以传字符串'canlynet@qq.com'，也可以传数组['丁华能' => 'canlynet@qq.com', 'xxx@qq.com']
	 */
	public static function sendEmail($to, $subject = '', $body = '', $attachment = null) {
		$mail = new \PHPMailer\PHPMailer\PHPMailer(); //实例化PHPMailer对象
		$mail->CharSet = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
		$mail->IsSMTP(); // 设定使用SMTP服务
		$mail->SMTPDebug = 0; // SMTP调试功能 0=关闭 1 = 错误和消息 2 = 消息
		$mail->SMTPAuth = true; // 启用 SMTP 验证功能
		$mail->SMTPSecure = 'tls'; // 使用安全协议
		$mail->Host = "smtp.gmail.com"; // SMTP 服务器
		$mail->Port = 587; // SMTP服务器的端口号
		$mail->Username = "onepointmain123@gmail.com"; // SMTP服务器用户名
		$mail->Password = "lu6268lu"; // SMTP服务器密码
		$mail->SetFrom('onepointmain123@gmail.com', '一站式表单');
		$replyEmail = 'onepointmain123@gmail.com'; //留空则为发件人EMAIL
		$replyName = ''; //回复名称（留空则为发件人名称）
		$mail->AddReplyTo($replyEmail, $replyName);
		$mail->Subject = $subject;
		$mail->MsgHTML($body);
		// 处理发件人
		if (is_string($to)) { // $to = 'canlynet@xx.com'
			$mail->AddAddress($to);
		} else {
			$addAddressHasDone = false;
			foreach ($to as $key => $value) {
				$address = $value;
				$toName = ''; // $to = ['xxx@163.com']
				if (is_string($key)) { // $to = ['丁华能' => 'xxx@163.com']
					$address = $value;
					$toName = $key;
				}
				if ($addAddressHasDone == false) {
					$addAddressHasDone = true;
					$mail->AddAddress($address, $toName);
				} else {
					$mail->addCC($address, $toName);
				}
			}
		}
		
		if (is_array($attachment)) {
			// 添加附件
			foreach ($attachment as $file) {
				is_file($file) && $mail->AddAttachment($file);
			}
		}

		return $mail->Send() ? true : $mail->ErrorInfo;
	}
}