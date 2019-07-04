<?php
/**
 * 阿里云短信发送业务，对外接口就一个：sendSms
 */
namespace Aliyun;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Core\Config;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Core\Profile\DefaultProfile;

Config::load();
class AliyunSms {

	/**
	 * 发送手机验证码的方法
	 * @param  [type] $mobile [description]
	 * @param  [type] $code   [description]
	 * @return [type]         [description]
	 */
	public static function sendPhoneVerifyCode($mobile, $code) {
		$config = config('aliyunSMS.config');
		$template = config('aliyunSMS.template');
		$params = ['code' => $code];
		$acsResponse = self::_sendSms($mobile, $config, $template['tel_verify_code'], $params);
		if ($acsResponse->Code == 'OK') {
			return ['code' => 0, 'msg' => 'ok'];
		} else {
			return ['code' => -1, 'msg' => $acsResponse->Message];
		}
	}

	/**
	 * 发送手机抽奖通知信息
	 * @param  [type] $mobile [description]
	 * @param  [type] $gift_code   领奖密码
	 * @param  [type] $expire_code   过期时间
	 * @return [type]         [description]
	 */
	public static function sendPhoneNotifyMsg($mobile, $gift_code, $expire_code) {
		$config = config('aliyunSMS.config');
		$template = config('aliyunSMS.template');
		$params = [
			'gift_code' => $gift_code,
			'expire_code' => $expire_code
				];
		$acsResponse = self::_sendSms($mobile, $config, $template['tel_notify_msg'], $params);
		if ($acsResponse->Code == 'OK') {
			return ['code' => 0, 'msg' => 'ok'];
		} else {
			return ['code' => -1, 'msg' => $acsResponse->Message];
		}
	}

	/**
	 * 发送短信基础函数。
	 * @param  string $mobile   手机号码
	 * @param  array  $config   配置信息，但不包括VerificationCode
	 * @param  string $template VerificationCode的内容：短信模板
	 * @param  array  $params   短信模板中的参数赋值的数组，键值对，键名即为模板中的参数名。
	 * @return
	成功返回：
	{
	"Message": "OK",
	"RequestId": "750AC9EC-28DA-4A21-A9CB-DEA572B5DF3D",
	"BizId": "517710913652148811^0",
	"Code": "OK"
	}
	失败返回：
	{
	"Message": "模板不合法(不存在或被拉黑)",
	"RequestId": "707093C8-A25E-4506-B1C0-612D9B0F9DB0",
	"Code": "isv.SMS_TEMPLATE_ILLEGAL"
	}
	 */
	private static function _sendSms($mobile, $config, $template, $params) {
		$config['VerificationCode'] = $template;
		// 初始化SendSmsRequest实例用于设置发送短信的参数
		$request = new SendSmsRequest();

		// 必填，设置短信接收号码
		$request->setPhoneNumbers($mobile);

		// 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
		$request->setSignName($config['ApplicationSignature']);

		// 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
		$request->setTemplateCode($config['VerificationCode']);

		// 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
		$request->setTemplateParam(json_encode($params));

		// 可选，设置流水号
		// $request->setOutId("");

		// 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
		// $request->setSmsUpExtendCode("1234567");

		// 发起访问请求
		$acsResponse = static::getAcsClient($config)->getAcsResponse($request);

		return $acsResponse;
	}

	private static $acsClient = null;

	/**
	 * 取得AcsClient
	 *
	 * @return DefaultAcsClient
	 */
	private static function getAcsClient($config) {
		//产品名称:云通信流量服务API产品,开发者无需替换
		$product = "Dysmsapi";

		//产品域名,开发者无需替换
		$domain = "dysmsapi.aliyuncs.com";

		// TODO 此处需要替换成开发者自己的AK (https://ak-console.aliyun.com/)
		$accessKeyId = $config['AccessKeyID']; // AccessKeyId

		$accessKeySecret = $config['AccessKeySecret']; // AccessKeySecret

		// 暂时不支持多Region
		$region = "cn-hangzhou";

		// 服务结点
		$endPointName = "cn-hangzhou";

		if (static::$acsClient == null) {

			//初始化acsClient,暂不支持region化
			$profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

			// 增加服务结点
			DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

			// 初始化AcsClient用于发起请求
			static::$acsClient = new DefaultAcsClient($profile);
		}
		return static::$acsClient;
	}
}