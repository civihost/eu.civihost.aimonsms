<?php


use PhpXmlRpc\Client as XmlRpcClient;
use PhpXmlRpc\Encoder as XmlRpcEncoder;
use PhpXmlRpc\Request as XmlRpcRequest;

class eu_civihost_aimonsms extends CRM_SMS_Provider
{

  /**
   * api type to use to send a message
   * @var string
   */
  protected $_apiType = 'xml';

  /**
   * provider details
   * @var string
   */
  protected $_providerInfo = [];

  public $_apiURL = "https://secure.apisms.it/xmlrpc/BCP/provisioning.py";

  protected $_messageType = [];
  protected $_messageStatus = [];

  /**
   * XmlRpcClient client object
   * @var XmlRpcClient
   */
  protected $_xmlRcpClient = null;

  /**
   * We only need one instance of this object. So we use the singleton
   * pattern and cache the instance in this variable
   *
   * @var object
   * @static
   */
  static private $_singleton = [];

  /**
   * Constructor
   *
   * Create and auth a Twilio session.
   * This is not needed for Twilio
   *
   * @return void
   */
  function __construct($provider = [], $skipAuth = TRUE)
  {
    // initialize vars
    $this->_apiType = CRM_Utils_Array::value('api_type', $provider, 'xml');
    $this->_providerInfo = $provider;

    $this->_xmlRcpClient = new XmlRpcClient($this->_providerInfo['api_url'] ?? $this->_apiURL);
    $this->_xmlRcpClient->setOption(XmlRpcClient::OPT_VERIFY_HOST, false);
    $this->_xmlRcpClient->setOption(XmlRpcClient::OPT_VERIFY_PEER, false);

    if ($skipAuth) {
      return TRUE;
    }

    $this->authenticate();
  }

  /**
   * singleton function used to manage this object
   *
   * @return object
   */
  static function &singleton($providerParams = [], $force = FALSE)
  {
    $providerID = $providerParams['provider_id'] ?? NULL;
    $skipAuth   = $providerID ? FALSE : TRUE;
    $cacheKey   = (int) $providerID;

    if (!isset(self::$_singleton[$cacheKey]) || $force) {
      $provider = [];
      if ($providerID) {
        $provider = CRM_SMS_BAO_Provider::getProviderInfo($providerID);
      }
      self::$_singleton[$cacheKey] = new eu_civihost_aimonsms($provider, $skipAuth);
    }
    return self::$_singleton[$cacheKey];
  }

  /**
   * Authenticate to the Twilio Server.
   * Not needed in Twilio
   * @return boolean TRUE
   * @access public
   * @since 1.1
   */
  function authenticate()
  {
    return TRUE;
  }

  /**
   * Send an SMS Message via the Aimon API Server
   *
   * @param array the message with a to/from/text
   *
   * @return mixed SID on success or PEAR_Error object
   * @access public
   */
  function send($recipients, $header, $message, $jobID = NULL, $userID = NULL)
  {
    $params =  [
      'authlogin' => $this->_providerInfo['username'],
      'authpasswd' => $this->_providerInfo['password'],
      'sms' => [
        [
          'sender' =>  base64_encode($this->_providerInfo['api_params']['From']),
          'body' =>  base64_encode(mb_convert_encoding($message, 'ISO-8859-1', 'UTF-8')),
          'destination' => self::normalize($header['To']),
          'id_api' => (int) $this->_providerInfo['api_params']['api_id'],
          'report_type' => 'F',
        ]
      ]
    ];

    $msg = new XmlRpcRequest('send_sms', array((new XmlRpcEncoder())->encode($params)));

    $r = $this->_xmlRcpClient->send($msg);
    if (!$r->faultCode()) {
      $sid = (new XmlRpcEncoder())->decode($r->value())[0]['id_sms'];
      $this->createActivity($sid, $message, $header, $jobID, $userID);
      return $sid;
    } else {
        return PEAR::raiseError(
          $r->faultString(),
          $r->faultCode(),
          PEAR_ERROR_RETURN
        );
    }
  }

  function callback()
  {
    return TRUE;
  }

  function inbound()
  {
    $like      = "";
    $fromPhone = $this->retrieve('From', 'String');
    return parent::processInbound($fromPhone, $this->retrieve('Body', 'String'), NULL, $this->retrieve('SmsSid', 'String'));
  }

  static protected function normalize($mobile)
  {
    $mobile = str_replace(' ', '', $mobile);
    if (strpos($mobile, '+') === false) {
      // Remove the two zeros in front for international numbers
      if (substr($mobile, 0, 2) == '00') {
        $mobile = substr($mobile, 2, strlen($mobile));
      } else {
        $mobile = '39' . $mobile;
      }
    } else {
      $mobile = str_replace('+', '', $mobile);
    }
    return $mobile;
  }
}
