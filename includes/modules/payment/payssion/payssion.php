<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2011-2014 Payssion
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * 类
 */
class payssion
{
	
	protected $pm_id = '';
	protected $title = '';
	protected $api_key = 'aba89d0d4a90a8d9';
	protected $secret_key = '5c36b03248193816f0d14f2efb114006';

    /**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function payssion()
    {
    }

    function __construct()
    {
        $this->payssion();
    }

    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $payment    支付方式信息
     */
    function get_code($order, $payment)
    {
        if (!defined('EC_CHARSET'))
        {
            $charset = 'utf-8';
        }
        else
        {
            $charset = EC_CHARSET;
        }
        
        $return_url = return_url(get_class($this));
        $parameter = array(
        		'source'        => 'ecshop',
        		'api_key'       => $this->api_key,
        		'pm_id'         => $this->pm_id,
        		'amount'        => $order['order_amount'],
        		'currency'      => $order['order_curr'],
        		'redirect_url'  => $return_url,
        		'description'   => $GLOBALS['_CFG']['shop_name'] . ' - Order #' . $order['order_sn'],
        		'track_id'      => $order['log_id'],
        		'sub_track_id'  => '',
        		'notify_url'    => $return_url,
        		'payer_ref'     => '',
        		'payer_name'    => '',
        		'country'       => '',
        		'payer_email'   => ''
        );
        $msg = $this->generateSignature($parameter, $this->pm_id, $this->secret_key);
        $parameter['api_sig'] = md5($msg);
        $button  = '<br /><form style="text-align:center;" method="post" action="https://www.payssion.com/payment/create.html" target="_blank" style="margin:0px;padding:0px" >';
        foreach ($parameter AS $key=>$val)
        {
            $button  .= "<input type='hidden' name='$key' value='$val' />";
        }
        $button  .= '<input style="padding:10px;" type="submit" value="' . "Pay with " . $this->title. '" /></form><br />';
        
        return $button;
    }
    
    private function generateSignature(&$req, $pm_id, $secretKey)
    {
    	$arr = array($req['api_key'], $pm_id, $req['amount'], $req['currency'],
    			$req['track_id'], '', $secretKey);
    	$msg = implode('|', $arr);
    	return $msg;
    }

    /**
     * 响应操作
     */
    function respond()
    {
    	$payment  = get_payment($_GET['code']);
    	if ($_POST) {
    		// Assign payment notification values to local variables
    		$pm_id = $_POST['pm_id'];
    		$amount = $_POST['amount'];
    		$currency = $_POST['currency'];
    		$track_id = $_POST['track_id'];
    		$sub_track_id = $_POST['sub_track_id'];
    		$state = $_POST['state'];
    		 
    		$check_array = array(
    				$this->api_key,
    				$pm_id,
    				$amount,
    				$currency,
    				$track_id,
    				$sub_track_id,
    				$state,
    				$this->secret_key
    		);
    		$check_msg = implode('|', $check_array);
    		$check_sig = md5($check_msg);
    		$notify_sig = $_POST['notify_sig'];
    		if ($notify_sig == $check_sig) {
    			switch ($state) {
    				case 'completed':
    					order_paid($track_id, PS_PAYED, 'Payssion trans id: ' . $_POST['transaction_id']);
    					echo "OK";
    					break;
    				default:
    					break;
    			}
    			return true;
    		}
    	}
    	
    	return false;
    }
}

?>