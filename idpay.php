<?php
$rec = $_model->load('invoice','id',$_bankId);
if(!count($rec)) $_alert->alert(1003);
$rec = $rec[0];
if($rec['totalPrice']>10000000) $_alert->alert(2017);

	$Api_Token = _idpayAPI_; //Mashhadhost Merchant Id In Saman Bank...
	$BankUrl = _idpayURL_;
	$ReturnURL = _httpRoot_.'/pages/user/detail?invoice='.$rec['id'].'&check';
	$OrderId = $rec['id'];
	$amount = $rec['totalPrice']*10;



		
//$_controller->resetToken();

$params = array(
  'order_id' => $rec['id'],
  'amount' => $rec['totalPrice']*10,
  'name' => 'قاسم رادمان',
  'phone' => '09370330850',
  'mail' => 'my@site.com',
  'desc' => 'توضیحات پرداخت کننده',
  'callback' => _httpRoot_.'/pages/user/detail?invoice='.$rec['id'].'&check',
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.idpay.ir/v1.1/payment');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  'Content-Type: application/json',
  'X-API-KEY: '._idpayAPI_,
  'X-SANDBOX: 0'
));

$result = curl_exec($ch);
curl_close($ch);
$bank_idpay = json_decode($result);
if(isset($bank_idpay->id) && $bank_idpay->id <> ''){
    $_alert->ok(114,false);	
    //$_model->update('invoice',array('bankCode'=>$bank_idpay->id),array('id'=>$rec['id']));
    echo '<form id="idpay" style="display:none;" action="'.$bank_idpay->link.'" method="post">
</form>';

	
echo '
<script type="text/javascript">
$(document).ready(function(){
	$("#idpay").submit();
});
</script>
';

}

