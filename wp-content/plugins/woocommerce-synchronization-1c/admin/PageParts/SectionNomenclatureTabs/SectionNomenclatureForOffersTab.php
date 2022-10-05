<?php

namespace Itgalaxy\Wc\Exchange1c\Admin\PageParts\SectionNomenclatureTabs;

class SectionNomenclatureForOffersTab
{
    public static function getSettings()
    {
        return [
            'title' => esc_html__('For offers', 'itgalaxy-woocommerce-1c'),
            'id' => 'nomeclature-offers',
            'fields' => [
                'products_stock_null_rule' => [
                    'type' => 'select',
                    'title' => esc_html__('Products with a stock <= 0:', 'itgalaxy-woocommerce-1c'),
                    'options' => [
                        '0' => esc_html__(
                            'Hide (not available for viewing and ordering)',
                            'itgalaxy-woocommerce-1c'
                        ),
                        '1' => esc_html__(
                            'Do not hide and give the opportunity to put in the basket',
                            'itgalaxy-woocommerce-1c'
                        ),
                        'not_hide_and_put_basket_with_disable_manage_stock_and_stock_status_onbackorder' => esc_html__(
                            'Do not hide and give the opportunity to put in the basket (Manage stock - '
                            . 'disable, Stock status - On back order)',
                            'itgalaxy-woocommerce-1c'
                        ),
                        '2' => esc_html__(
                            'Do not hide, but do not give the opportunity to put in the basket',
                            'itgalaxy-woocommerce-1c'
                        ),
                        'with_negative_not_hide_and_put_basket_with_zero_hide_and_not_put_basket' => esc_html__(
                            'Do not hide with a negative stock and give an opportunity to put in a basket, '
                            . 'with a zero stock hide and do not give an opportunity to put in a basket.',
                            'itgalaxy-woocommerce-1c'
                        ),
                    ],
                    'description' => esc_html__(
                        'Only products with a non-empty price can be opened.',
                        'itgalaxy-woocommerce-1c'
                    ),
                    'fieldsetStart' => true,
                    'legend' => esc_html__('Stock actions', 'itgalaxy-woocommerce-1c'),
                ],
                'products_onbackorder_stock_positive_rule' => [
                    'type' => 'select',
                    'title' => esc_html__('Products with a stock > 0 (Allow backorders?):', 'itgalaxy-woocommerce-1c'),
                    'options' => [
                        'no' => esc_html__('Do not allow', 'itgalaxy-woocommerce-1c'),
                        'notify' => esc_html__('Allow, but notify customer', 'itgalaxy-woocommerce-1c'),
                        'yes' => esc_html__('Allow', 'itgalaxy-woocommerce-1c'),
                    ],
                    'fieldsetEnd' => true,
                ],
                'offers_delete_variation_if_offer_marked_deletion' => [
                    'type' => 'checkbox',
                    'title' => esc_html__('Remove variation if the variable offer is marked for removal', 'itgalaxy-woocommerce-1c'),
                    'description' => esc_html__(
                        'If enabled, when an variable offer related to variation is received marked for deletion '
                        . '(that is, a characteristic was marked for deletion), the variation will be deleted. '
                        . 'By default, variation is only disabled.',
                        'itgalaxy-woocommerce-1c'
                    ),
                ],
            ],
        ];
    }
}
