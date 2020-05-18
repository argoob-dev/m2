<?php

namespace Algolia\AlgoliaSearch\Model\Source;

class Facets extends AbstractTable
{
    protected function getTableData()
    {
        $productHelper = $this->productHelper;

        return [
            'attribute' => [
                'label'  => 'Attribute',
                'values' => function () use ($productHelper) {
                    $options = [];

                    $attributes = $productHelper->getAllAttributes();

                    foreach ($attributes as $key => $label) {
                        $options[$key] = $key ? $key : $label;
                    }

                    return $options;
                },
            ],
            'type' => [
                'label'  => 'Facet type',
                'values' => [
                    'conjunctive' => 'Conjunctive',
                    'disjunctive' => 'Disjunctive',
                    'slider'      => 'Slider',
                    'priceRanges' => 'Price Ranges',
                ],
            ],
            'label' => [
                'label' => 'Label',
            ],
            'searchable' => [
                'label'  => 'Searchable?',
                'values' => ['1' => 'Yes', '2' => 'No'],
            ],
            'create_rule' => [
                'label'  => 'Create Query rule?',
                'values' => ['1' => 'Yes', '2' => 'No'],
            ],
        ];
    }
}
