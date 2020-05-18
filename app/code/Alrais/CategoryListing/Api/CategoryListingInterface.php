<?php

/**
 * Copyright © 2018 Alrais. All rights reserved.
 */

namespace Alrais\CategoryListing\Api;

interface CategoryListingInterface {

    /**
     *
     * @api
     * @return $this
     */
    public function categories();

    /**
     *
     * @api
     * @return $this
     */
    public function getStoreCategories();
}
