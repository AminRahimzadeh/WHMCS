<?php
$_bankERR = array(
	"1" => "پرداخت انجام نشده است",
	"2" => "پرداخت ناموفق بوده است",
	"3" => "خطا رخ داده است",
	"4" => "بلوکه شده",
	"5" => "برگشت به پرداخت کننده",
	"6" => "برگشت خورده سیستمی",
	"7" => "انصراف از پرداخت",
	"8" => "به درگاه پرداخت منتقل شد",
	"10" => "در انتظار تایید پرداخت",
	"100" => "پرداخت تایید شده است",
	"101" => "پرداخت قبلا تایید شده است",
	"200" => "به دریافت کننده واریز شد",
);

$idpay_status = @$_POST['status'];
$idpay_id = @$_POST['id'];
$idpay_order_id = @$_POST['order_id'];
$idpay_amount = @$_POST['amount'];
$idpay_date =@$_POST['date']; 

if (empty($idpay_status))
{
    $_alert->alertTxt('هیچ داده ای از بانک دریافت نشد.',false);
}
else
    {
            if( intval($idpay_status) == 10 and
            intval($idpay_order_id) == @$_detail['id'] and
            intval($idpay_amount) == intval($_detail['totalPrice'])*10)
            {
                
               			
                $params = array(
                  'id' => $idpay_id,
                  'order_id' => @$_detail['id'],
                );
                
                 
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://api.idpay.ir/v1.1/payment/verify');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                  'Content-Type: application/json',
                  'X-API-KEY: '._idpayAPI_,
                  'X-SANDBOX: 0',
                ));
                
                $result = curl_exec($ch);
                curl_close($ch);
                $bank_idpay = json_decode($result);
                
                if(isset($bank_idpay->status) && $bank_idpay->status > 0)
                {
                    if($bank_idpay->status == 100)
                    {
                        if($bank_idpay->order_id == @$_detail['id'] && intval($bank_idpay->amount) == intval($_detail['totalPrice'])*10 )
                        {
                           $_model->update('invoice',array('payDate'=>date('Y-m-d H:i:s'),'status'=>4,'bankCode'=>$bank_idpay->track_id),array('id'=>@$_detail['id']));
    			           $_alert->ok(115,false);
    			           
    			            $_that = $_model;
                		    $_sysErrorTxt = 'سفارشی جدید پرداخت شد.<br /><a href="'._httpRoot_.'/pages/admin/todo?onWait">'._httpRoot_.'/pages/admin/todo?onWait</a>';
                			include_once(_root_.'/php/submit/user/error_reporting.php');

                        }
                    }else{
                        $_alert->alertTxt($_bankERR[$bank_idpay->status],false);
                    }
                }
                
            }

    }

?>