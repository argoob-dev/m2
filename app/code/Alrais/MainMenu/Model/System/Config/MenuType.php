<?php
 
namespace Alrais\MainMenu\Model\System\Config;
 
use Magento\Framework\Option\ArrayInterface;
 
class MenuType implements ArrayInterface
{
	const TOP_MENU = 0;
	const SIDE_MENU = 1;
 
	/**
	* @return array
	*/
	public function toOptionArray()
	{
		$options = [
		self::TOP_MENU => __('Top menu'),
		self::SIDE_MENU => __('Side menu')
		];
 
		return $options;
	}
}
