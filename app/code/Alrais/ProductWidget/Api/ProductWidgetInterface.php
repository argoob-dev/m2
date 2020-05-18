<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\ProductWidget\Api;

interface ProductWidgetInterface
{
	
	/**
	*
	* @api
	* @param string $type
	* @param int $pagefrom
	* @param int $pageto
	* @return $this
	*/
    public function GetWidgetDetails($type,$pagefrom,$pageto);
	
	
}