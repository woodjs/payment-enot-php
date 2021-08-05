<?php
class Enot
{
  const URL = 'https://enot.io/pay';

  public $sum = 0;
  public $order_id = null;
  public $success_url = 0;
  public $fail_url = 0;
  public $params = 0;

  public function __construct($merchant_id, $secret_word, $desecret_word, $currency = 'RUB')
  {
    $this->merchant_id = $merchant_id;
    $this->secret_word = $secret_word;
    $this->desecret_word = $desecret_word;
    $this->currency = $currency;
  }

  public function checkError()
  {
    if (!$this->merchant_id) return ['ok' => false, 'message' => 'merchant_id not declared'];
    if (!$this->secret_word) return ['ok' => false, 'message' => 'secret_word not declared'];
    if (!$this->desecret_word) return ['ok' => false, 'message' => 'desecret_word not declared'];
    if (!$this->order_id) return ['ok' => false, 'message' => 'order_id not declared. Call the setUp function'];

    return ['ok' => true, 'message' => 'count errors: 0'];
  }

  public function getInfo()
  {
    return json_encode([
      'merchantId' => $this->merchant_id,
      'secret' => $this->secret_word,
      'desecret' => $this->desecret_word,
      'currency' => $this->currency,
      'sum' => $this->sum,
      'orderId' => $this->order_id,
    ]);
  }

  public function setUpUrl($array)
  {
    foreach ($array as $key => $value) {
      if ($key == 'success_url') $this->success_url = $value;
      if ($key == 'fail_url') $this->fail_url = $value;
      if ($key == 'params') {
        if (is_array($value)) $this->params = $value;
      }
    }
  }

  public function setUpOrder($sum, $order_id)
  {
    $this->sum = $sum;
    $this->order_id = $order_id;
  }

  public function getSignature()
  {
    $error = $this->checkError();

    if (!$error['ok']) return json_encode($error);

    return md5($this->merchant_id . ':' . $this->sum . ':' . $this->secret_word . ':' . $this->order_id);
  }

  public function getOrderSignature()
  {
    $error = $this->checkError();

    if (!$error['ok']) return json_encode($error);

    return md5($this->merchant_id . ':' . $this->sum . ':' . $this->desecret_word . ':' . $this->order_id);
  }

  public function generateUrlPayment()
  {
    $error = $this->checkError();

    if (!$error['ok']) return json_encode($error);

    return self::URL . '?' . http_build_query([
      'm' => $this->merchant_id,
      'oa' => $this->sum,
      'o' => $this->order_id,
      's' => $this->getSignature(),
      'cr' => $this->currency,
      'success_url' => $this->success_url,
      'fail_url' => $this->fail_url,
      'cf' => $this->params,
    ]);
  }
}
