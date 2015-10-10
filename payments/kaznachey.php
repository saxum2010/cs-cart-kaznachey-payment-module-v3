<?php
if ( !defined('AREA') ) {
	if (!empty($_REQUEST['MNT_TRANSACTION_ID'])) {
		// Settle data is received
		DEFINE ('AREA', 'C');
		DEFINE ('AREA_NAME' ,'customer');
		require './../prepare.php';
		require './../init.php';

		$order_id = (int) $_REQUEST['MNT_TRANSACTION_ID'];
		$order_info = fn_get_order_info($order_id);
		$processor_data = $order_info['payment_method'];

		if (isset($_REQUEST['MNT_ID']) && isset($_REQUEST['MNT_TRANSACTION_ID']) && isset($_REQUEST['MNT_OPERATION_ID'])
			&& isset($_REQUEST['MNT_AMOUNT']) && isset($_REQUEST['MNT_CURRENCY_CODE']) && isset($_REQUEST['MNT_TEST_MODE'])
			&& isset($_REQUEST['MNT_SIGNATURE'])) 
		{
			$signature = md5("{$_REQUEST['MNT_ID']}{$_REQUEST['MNT_TRANSACTION_ID']}{$_REQUEST['MNT_OPERATION_ID']}{$_REQUEST['MNT_AMOUNT']}{$_REQUEST['MNT_CURRENCY_CODE']}{$_REQUEST['MNT_TEST_MODE']}{$processor_data['params']['mnt_dataintegrity_code']}");
			if ($_REQUEST['MNT_SIGNATURE'] == $signature){
				fn_change_order_status($order_id, 'P', $order_info['status'], false);
				die('SUCCESS');
			} else {
				die('FAIL');
			}
		} else {
			die('FAIL');
		}
		
	} else {
		die('Access denied');
	}
}

if (defined('PAYMENT_NOTIFICATION')) {
	$ExternalLibPath =realpath(dirname(__FILE__)).DS.'kaznacheyLib.php';
	require_once ($ExternalLibPath);
	$kaznachey = new kaznacheyLib();	

	if ($mode == 'done') {

	$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : file_get_contents('php://input');

	$hrpd = json_decode($HTTP_RAW_POST_DATA);
	$order_id = intval($hrpd->MerchantInternalPaymentId); 
	
	if(!$order_id){
		$kaznachey->home_url(); die; 
	}
	
	$order_info = fn_get_order_info($order_id);
	$processor_data = fn_get_payment_method_data($order_info['payment_id']);
	$kaznachey = new kaznacheyLib($processor_data);	
	
	if(isset($hrpd->MerchantInternalPaymentId))
	{
		if($hrpd->ErrorCode == 0)
		{
			$pp_response = array();

			if (!empty($order_info['payment_info']['order_status']) && ($order_info['payment_info']['order_status'] == 'F')) {
				$pp_response = $order_info['payment_info'];
			} else {
				$pp_response['order_status'] = 'O';
	//			$pp_response['lmi_sys_invs_no'] = $_REQUEST['LMI_SYS_INVS_NO'];
	//			$pp_response['lmi_sys_trans_no'] = $_REQUEST['LMI_SYS_TRANS_NO'];
	//			$pp_response['lmi_sys_trans_date'] = $_REQUEST['LMI_SYS_TRANS_DATE'];
			}
			fn_finish_payment($order_id, $pp_response);
			fn_order_placement_routines($order_id);
		}
	}
	}elseif ($mode == 'success') {

		$order_id = isset($_GET['order_id'])?$_GET['order_id']:false;
		if ($_GET['Result'] == 'success'){
			$kaznachey->success_page($order_id);
			die;
		}
		
		if ($_GET['Result'] == 'deferred'){
			$kaznachey->deferred_page($order_id);
			die;
		}

	} 

} else {
	if (!defined('AREA') ) { die('Access denied'); }

	$success_url = Registry::get('config.current_location') . "/$index_script?dispatch=payment_notification.success&payment=kaznachey";
	$fail_url = Registry::get('config.current_location') . "/$index_script?dispatch=payment_notification.fail&payment=kaznachey";
	$mnt_amount = fn_kaznachey_format_price($order_info['total'], $processor_data['params']['currency']);
	$payment_system = $processor_data['params']['payment_system'];

	require_once ($_SERVER["DOCUMENT_ROOT"] . '/payments/kaznacheyLib.php');
	$kaznachey = new kaznacheyLib($processor_data);
	$kaznachey->createOrder($order_id, $order_info);
	
die();
}

function fn_kaznachey_format_price ($price, $currency = 'UAH')
{
	$currencies = Registry::get('currencies');
	$coefficient = $currencies[$currency]['coefficient']?$currencies[$currency]['coefficient']:1;
	return number_format($price / $coefficient, 2, '.', '');
}

?>
