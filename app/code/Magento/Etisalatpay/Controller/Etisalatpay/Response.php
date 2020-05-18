<?php 

namespace  Magento\Etisalatpay\Controller\Etisalatpay;

class Response extends \Magento\Etisalatpay\Controller\Etisalatpay
{
	/*
	 * Finalization call
	 */
	public function execute()
	{
		$response = $this->getRequest()->getPostValue();

		$etisalat['TransactionID'] = $response['TransactionID'];
		$transactionId =  $response['TransactionID'];
		

		$result = $this->etisalatCurl($etisalat, 'Finalization');

		$response = json_decode($result, true);

		\Magento\Framework\App\ObjectManager::getInstance()
    ->get(\Psr\Log\LoggerInterface::class)->info('Etisalat Transaction Id :::: '.$transactionId.' Response Code ::: '.$response['Transaction']['ResponseCode']);

		if( $response['Transaction']['ResponseCode'] == 0 ) {

			$order = $this->_order->loadByIncrementId($response['Transaction']['OrderID']);

			\Magento\Framework\App\ObjectManager::getInstance()
    ->get(\Psr\Log\LoggerInterface::class)->info('Order placed successfully. Order Number :::: '.$response['Transaction']['OrderID']);

			//success | proceed

			//load order status from config
			$passed_status = $this->_scopeConfig->getValue('payment/etisalatpay/payment_success_order_status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
			
			$message = __('Payment has been processed successfully');
			
			// $this->_orderManagement->unHold($order->getId());

			$order->setState($passed_status, true, $message);
			$order->addStatusHistoryComment($message);
			$order->setStatus($passed_status);
			
			$order->save();

			return $this->_redirect('checkout/onepage/success');

		}
		else if( $response['Transaction']['ResponseCode'] == 5 ) {

			//payment error | failure
			$errorMessage = __('Payment failed due to \'Do Not Honor\'');
			$order = $this->_session->getLastRealOrder();
			// $this->_orderManagement->unHold($orderId);
			$this->_orderManagement->cancel($order->getId());


			\Magento\Framework\App\ObjectManager::getInstance()
    ->get(\Psr\Log\LoggerInterface::class)->info('Order Failed due to <<<Do Not Honor>>> . Order Number :::: '.$order->getId());

			$order->registerCancellation($errorMessage)->save();
			$order->save();

			$this->_session->restoreQuote();

			$this->messageManager->addError($errorMessage);

			return $this->_redirect('checkout/cart');

		}
		else if( $response['Transaction']['ResponseCode'] == 91 ) {

			//payment error | failure
			$errorMessage = __('Payment failured due to \'Issuer switch inoperative\'');
			$order = $this->_session->getLastRealOrder();
			// $this->_orderManagement->unHold($orderId);
			$this->_orderManagement->cancel($order->getId());

			\Magento\Framework\App\ObjectManager::getInstance()
    ->get(\Psr\Log\LoggerInterface::class)->info('Order Failed due to <<<Issuer switch inoperative>>> Order Number :::: '.$order->getId());

			$order->registerCancellation($errorMessage)->save();
			$order->save();

			$this->_session->restoreQuote();

			$this->messageManager->addError($errorMessage);

			return $this->_redirect('checkout/cart');

		} 
		else if( $response['Transaction']['ResponseCode'] == 51 ) {


			//payment error | failure
			$errorMessage = __('Payment failed due to insufficient fund');
			$order = $this->_session->getLastRealOrder();
			// $this->_orderManagement->unHold($orderId);
			$this->_orderManagement->cancel($order->getId());

			\Magento\Framework\App\ObjectManager::getInstance()
    ->get(\Psr\Log\LoggerInterface::class)->info('Order Failed due to <<<Insufficient Fund>>> Order Number :::: '.$order->getId());

			$order->registerCancellation($errorMessage)->save();
			$order->save();

			$this->_session->restoreQuote();

			$this->messageManager->addError($errorMessage);

			return $this->_redirect('checkout/cart');

			
		}
		else{

			//payment error | failure
			$errorMessage = __('Payment failed');
			$order = $this->_session->getLastRealOrder();
			// $this->_orderManagement->unHold($orderId);
			$this->_orderManagement->cancel($order->getId());
			\Magento\Framework\App\ObjectManager::getInstance()
    ->get(\Psr\Log\LoggerInterface::class)->info('Order Failed due to <<<Unknow Reason>>> Order Number :::: '.$order->getId());
			$order->registerCancellation($errorMessage)->save();
			$order->save();

			$this->_session->restoreQuote();

			$this->messageManager->addError($errorMessage);

			return $this->_redirect('checkout/cart');


		}
	}
}