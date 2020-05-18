<?php
/**
 * Copyright © 2015 Alrais. All rights reserved.
 */

namespace Alrais\Banner\Api;

interface BannerInterface
{
	/**
	*
	* @api
	* @param int $storeId
	* @return $this
	*/
    public function banners($storeId);
}