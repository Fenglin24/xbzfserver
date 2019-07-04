<?php
require_once('htmlMimeMail5.php');

class Mail extends htmlMimeMail5{
	public static $contentTypeMap = array(
		'jpg'  => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'jpe'  => 'image/jpeg',
		'png'  => 'image/png',
		'gif'  => 'image/gif',
	);
	public function __construct($config, $subject = '', $content = '', $attachments = array()) {
		parent::__construct();
		$this->setHeadCharset ( 'utf-8' );
		$this->setTextCharset ( 'utf-8' );
		$this->setHtmlCharset ( 'utf-8' );
		$this->setTextEncoding ( new Base64Encoding() );
		$this->setHTMLEncoding ( new Base64Encoding() );
		$mailer = $config;
		$this->setFrom ( $mailer ['name'] . ' <' . $mailer ['from'] . '>' );
		$this->setSMTPParams( $mailer['smtp']['host'], $mailer['smtp']['port'], $mailer['smtp']['helo'], $mailer ['smtp'] ['auth'], $mailer ['smtp'] ['user'], $mailer ['smtp'] ['pass'] );
		$this->setSubject ( $subject );
		$this->setHTML ( $content );
		foreach ($attachments as $filename) {
			$ext_name = substr(strrchr($filename, '.'), 1);
			$contentType = isset(self::$contentTypeMap[$ext_name]) ? self::$contentTypeMap[$ext_name] : 'application/octet-stream';
			$attachment = new fileAttachment($filename, $contentType);
			$this->addAttachment($attachment);
		}
	}
	
	public function sendTo($email) {
		$res = $this->send ( $email, 'smtp' );
		if (!$res) {
			return array('code' => -1, 'msg' => $this->errors[0]);
		}
		return array('code' => 0, 'msg' => 'ok');
	}
}

function sendMimeMail($config, $rcpt_to, $subject, $body, $attachements = array()) {
	$mail = new Mail($config, $subject, $body, $attachements);
	return $mail->sendTo(array($rcpt_to));
}