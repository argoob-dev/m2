<?php 

namespace  Magento\Etisalatpay\Controller\Etisalatpay;

class Request extends \Magento\Etisalatpay\Controller\Etisalatpay
{
	/*
	 * Registration call
	 */
	public function execute()
	{
		$orderId = $this->_session->getLastRealOrderId();

		if (!isset($orderId) || !$orderId) {
			$message = 'Invalid order ID, please try again later';
			return $this->_redirect('checkout/cart');
		}

		$order = $this->_order->loadByIncrementId($orderId);

		// prepare registration call for etisalat
		$etisalat['Channel']  	= 'Web';
		$etisalat['Currency']  	= 'AED';
		
		$etisalat['Amount']  	= $order->getGrandTotal();
		$etisalat['OrderID']  	= $orderId;
		$etisalat['OrderName']  = 'Your Argoob Order';
		$etisalat['ReturnPath'] = $this->_url->getUrl('etisalatpay/etisalatpay/response');
		
		$etisalat['OrderInfo']  = $order->getId(); //O -
		$etisalat['TransactionHint'] = 'CPT:Y;VCC:Y'; //O -

		//$etisalat['Store']    = ''; //O -
		//$etisalat['Terminal'] = ''; //O -
		//$etisalat['Language'] = "en"; //O -
		//$etisalat['version']  = "2.0"; //O -

		$result = $this->etisalatCurl($etisalat, 'Registration');

		$response = json_decode($result, true);


		if( $response['Transaction']['ResponseCode'] != 0 ) {

			//payment error | failure
			$errorMessage = __('Payment canceled by customer');

			//$this->_orderManagement->unHold($orderId);
			//$this->_orderManagement->cancel($orderId);

			$order->registerCancellation($errorMessage)->save();
			$order->save();

			$this->_session->restoreQuote();

			$this->messageManager->addError($errorMessage);

			return $this->_redirect('checkout/cart');

		} else {

			//success | proceed
			echo '<center>You are being re-directed to Payment Gateway.</b></center><form method="post" id="etisalat-redirect-form" name="redirect" action="'.$response['Transaction']['PaymentPage'].'"><input type="Hidden" name="TransactionID" value="'.$response['Transaction']['TransactionID'].'"/></form><script type="text/javascript">document.redirect.submit();</script>';
			exit(0);
		}
	}
}