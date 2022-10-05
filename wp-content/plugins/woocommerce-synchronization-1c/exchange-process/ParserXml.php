<?php

namespace Itgalaxy\Wc\Exchange1c\ExchangeProcess;

use Itgalaxy\Wc\Exchange1c\ExchangeProcess\Base\Parser;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers\Offer\Offer;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers\PriceTypes;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers\Product\ImagesProduct;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers\Product\ResolverProduct;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers\Stocks;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\EndProcessors\DataDeletingOnFullExchange;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\EndProcessors\ProductUnvariable;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\EndProcessors\ProductVariableSync;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\EndProcessors\ProductVariableVisibility;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\EndProcessors\SetVariationAttributeToProducts;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\Exceptions\ProgressException;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\Helpers\HeartBeat;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\Helpers\Product;
use Itgalaxy\Wc\Exchange1c\ExchangeProcess\Helpers\ProductVariation;
use Itgalaxy\Wc\Exchange1c\Includes\Cron;
use Itgalaxy\Wc\Exchange1c\Includes\Logger;
use Itgalaxy\Wc\Exchange1c\Includes\SettingsHelper;

class ParserXml extends Parser
{
    /**
     * @throws Exceptions\ProgressException
     * @throws \Exception
     */
    public function parse(\XMLReader $reader)
    {
        $onlyChanges = '';
        //custom
        $temp_stocks = [];
        //custom*

        while ($reader->read()) {
            if ($reader->name === 'Каталог' && $onlyChanges === '') {
                $onlyChanges = $reader->getAttribute('СодержитТолькоИзменения');
            }

            // node - "Классификатор"
            $this->parseClassificator($reader);

            if ($reader->name === 'Товары') {
                if (!SettingsHelper::isEmpty('skip_products')) {
                    Logger::log('[skip] enabled `skip_products`');
                }

                $all1cProducts = get_option('all1cProducts', []);

                if (!isset($_SESSION['IMPORT_1C']['products_parse'])) {
                    while (
                        $reader->read()
                        && !($reader->name === 'Товары' && $reader->nodeType === \XMLReader::END_ELEMENT)
                    ) {

                        if (!SettingsHelper::isEmpty('skip_products')) {
                            continue;
                        }

                        if (!ResolverProduct::isProductNode($reader)) {
                            continue;
                        }

                        if (!HeartBeat::next('Товар', $reader)) {
                            $count = isset($_SESSION['IMPORT_1C']['heartbeat']['Товар'])
                                ? $_SESSION['IMPORT_1C']['heartbeat']['Товар']
                                : 0;

                            throw new ProgressException("products processing, node count {$count}...");
                        }

                        $element = simplexml_load_string(trim($reader->readOuterXml()));

                        if (!$element instanceof \SimpleXMLElement) {
                            continue;
                        }

                        if (ResolverProduct::customProcessing($element)) {
                            continue;
                        }

                        $element = \apply_filters('itglx_wc1c_product_xml_data', $element);

                        if (ResolverProduct::skipByXml($element)) {
                            continue;
                        }

                        $productID = Product::getSiteProductId($element, ResolverProduct::getNomenclatureGuid($element));

                        // if duplicate product
                        if ($productID && in_array($productID, $_SESSION['IMPORT_1C_PROCESS']['allCurrentProducts'])) {
                            if (
                                version_compare($_SESSION['xmlVersion'], '2.04', '<=')
                                && ResolverProduct::isOfferAsProduct($element)
                            ) {
                                ProductVariation::resolveOldVariant($element);
                            }

                            continue;
                        }

                        if (ResolverProduct::isRemoved($element, $productID, $all1cProducts)) {
                            continue;
                        }

                        $productEntry = [
                            'ID' => $productID,
                        ];

                        $isNewProduct = empty($productEntry['ID']);

                        if (!$isNewProduct) {
                            do_action('itglx_wc1c_before_exists_product_info_resolve', $productEntry['ID'], $element);
                        } else {
                            do_action('itglx_wc1c_before_new_product_info_resolve', $element);
                        }

                        $productHash = md5(json_encode((array)$element));

                        if (
                            !$isNewProduct
                            && SettingsHelper::isEmpty('force_update_product')
                            && $productHash == get_post_meta($productEntry['ID'], '_md5', true)
                        ) {
                            $_SESSION['IMPORT_1C_PROCESS']['allCurrentProducts'][] = $productEntry['ID'];
                            $all1cProducts[] = $productEntry['ID'];
                            $currentPostStatus = Product::getStatus($productEntry['ID']);

                            update_option('all1cProducts', array_unique($all1cProducts));

                            // restore product from trash
                            if ($currentPostStatus === 'trash' && !SettingsHelper::isEmpty('restore_products_from_trash')) {
                                \wp_update_post([
                                    'ID' => $productEntry['ID'],
                                    'post_status' => 'publish',
                                ]);

                                Logger::log(
                                    '(product) restore from trash, ID - ' . $productEntry['ID'],
                                    [get_post_meta($productEntry['ID'], '_id_1c', true)]
                                );
                            }

                            if (
                                !SettingsHelper::isEmpty('more_check_image_changed')
                                && SettingsHelper::isEmpty('skip_post_images')
                            ) {
                                // it is necessary to check the change of images,
                                // since the photo can be changed without changing the file name,
                                // which means the hash matches
                                ImagesProduct::process($element, $productEntry);
                            }

                            Logger::log(
                                '(product) not changed - skip, ID - ' . $productEntry['ID']
                                . ', status - ' . $currentPostStatus,
                                [get_post_meta($productEntry['ID'], '_id_1c', true)]
                            );

                            continue;
                        }

                        $productEntry = Product::mainProductData($element, $productEntry, $productHash);

                        if (empty($productEntry)) {
                            continue;
                        }

                        if (
                            version_compare($_SESSION['xmlVersion'], '2.04', '<=')
                            && ResolverProduct::isOfferAsProduct($element)
                        ) {
                            ProductVariation::resolveOldVariant($element);
                        }

                        /**
                         * Fires after processing a "Товар" node.
                         *
                         * Image processing takes place after that.
                         *
                         * @param int $productId
                         * @param \SimpleXMLElement $element
                         * @since 1.9.1
                         *
                         */
                        do_action('itglx_wc1c_after_product_info_resolve', $productEntry['ID'], $element);

                        $_SESSION['IMPORT_1C_PROCESS']['allCurrentProducts'][] = $productEntry['ID'];
                        $all1cProducts[] = $productEntry['ID'];

                        update_option('all1cProducts', array_unique($all1cProducts));

                        // is new or not disabled image data processing
                        if ($isNewProduct || SettingsHelper::isEmpty('skip_post_images')) {
                            ImagesProduct::process($element, $productEntry);
                        }
                    }

                    $_SESSION['IMPORT_1C']['products_parse'] = true;
                }

                /**
                 * Filters the behavior of deleting data during full exchange at the stage of nomenklature data processing.
                 *
                 * It can be useful if the offers are not uploaded at all.
                 * Should not be used if batch (`порционная`) unloading is used, , i.e. all data should come in 1 file `import`.
                 *
                 * @param bool $enable
                 * @since 1.99.0
                 *
                 */
                $useOldVariant = \apply_filters('itglx_wc1c_remove_full_exchange_data_on_nomenklature_stage', false);

                if (
                    $useOldVariant
                    && isset($_SESSION['IMPORT_1C']['products_parse'])
                    && !isset($_SESSION['IMPORT_1C_PROCESS']['missingDataProcessed'])
                    && $onlyChanges === 'false'
                ) {
                    $this->removeMissingData();
                }

                delete_option('product_cat_children');
                wp_cache_flush();

                // recalculate product counts
                if (isset($_SESSION['IMPORT_1C']['products_parse'])) {
                    $cron = Cron::getInstance();
                    $cron->createCronTermRecount();
                }

                SetVariationAttributeToProducts::process();
            }

            if (in_array($reader->name, ['ПакетПредложений', 'ИзмененияПакетаПредложений'])) {

                if (!isset($_SESSION['IMPORT_1C_PROCESS']['allCurrentOffers'])) {
                    $_SESSION['IMPORT_1C_PROCESS']['allCurrentOffers'] = [];
                }

                /**
                 * New variant delete data on full exchange which also takes into account issue #67.
                 *
                 * @see https://www.php.net/manual/xmlreader.getattribute.php
                 */
                if (
                    !isset($_SESSION['IMPORT_1C_PROCESS']['missingDataProcessed'])
                    && $this->isOldModuleOffersFile()
                    && $reader->getAttribute('СодержитТолькоИзменения') !== null
                ) {
                    if (
                        DataDeletingOnFullExchange::isEnabled()
                        && $reader->getAttribute('СодержитТолькоИзменения') === 'false'
                    ) {
                        $this->removeMissingData();
                    } else {
                        DataDeletingOnFullExchange::clearCache();
                    }

                    $_SESSION['IMPORT_1C_PROCESS']['missingDataProcessed'] = true;
                }

                if (!isset($_SESSION['IMPORT_1C']['offers_parse'])) {
                    while ($reader->read()
                        && !(in_array($reader->name, ['ПакетПредложений', 'ИзмененияПакетаПредложений'])
                            && $reader->nodeType === \XMLReader::END_ELEMENT)
                    ) {
                        // resolve price types
                        if (PriceTypes::isPriceTypesNode($reader)) {
                            PriceTypes::process($reader);
                        }

                        // resolve stocks
//                        if (!Stocks::isParsed() && Stocks::isStocksNode($reader)) {
//                            Stocks::process($reader);
//                        }

                        //custom
                        if ($reader->name === 'Склады') {
                            while (
                                $reader->read()
                                && !($reader->name === 'Склады' && $reader->nodeType === \XMLReader::END_ELEMENT)
                            ) {
                                if ($reader->name !== 'Склад' || $reader->nodeType !== \XMLReader::ELEMENT) {
                                    continue;
                                }

                                $element = simplexml_load_string(trim($reader->readOuterXml()));
                                $temp_stocks[(string)$element->Ид] = (string)$element->Наименование;
                            }
                        }
                        //custom*

                        if ($reader->name === 'Предложения') {
                            if (!SettingsHelper::isEmpty('skip_offers')) {
                                Logger::log('[skip] enabled `skip_offers`');
                            }

                            while (
                                $reader->read()
                                && !($reader->name === 'Предложения' && $reader->nodeType === \XMLReader::END_ELEMENT)
                            ) {

                                // enabled skip offers
                                if (!SettingsHelper::isEmpty('skip_offers')) {
                                    continue;
                                }

                                if (!Offer::isOfferNode($reader)) {
                                    continue;
                                }


                                //custom
                                $element = simplexml_load_string(trim($reader->readOuterXml()));

                                if (!$element instanceof \SimpleXMLElement) {
                                    continue;
                                }
                                if (ResolverProduct::customProcessing($element)) {
                                    continue;
                                }

                                $element = apply_filters('itglx_wc1c_product_xml_data', $element);

                                if (ResolverProduct::skipByXml($element)) {
                                    continue;
                                }

                                $productID = Product::getSiteProductId($element, ResolverProduct::getNomenclatureGuid($element));
                                if (empty($productID))
                                    continue;
                                $multisklad = [];
                                $in_stock = 0;
                                foreach ($element->Склад as $sklad) {
                                    $name = $temp_stocks[(string)$sklad->attributes()['ИдСклада']];
                                    $count = (int)$sklad->attributes()['КоличествоНаСкладе'];
                                    $multisklad[] = $name . '&0;' . $count;
                                    if ($count > 0)
                                        $in_stock = 1;
                                }
                                $multisklad = implode('&1;', $multisklad);

                                $params = array(
                                    'ID' => $productID,
                                    'meta_input' => array(
                                        'multi_sklad' => $multisklad,
                                        'in_stock' => $in_stock
                                    )
                                );
                                wp_update_post($params);
                                file_put_contents('import_log.log', file_get_contents('import_log.log') . $productID . ':' . $in_stock . PHP_EOL);
                                //custom*

                                Offer::process($reader, $this->rate, $productID); //тут ошибка
                            }
                        }
                    }

                    $_SESSION['IMPORT_1C']['offers_parse'] = true;
                }

                ProductUnvariable::process();
                ProductVariableSync::process();
                ProductVariableVisibility::process();
                SetVariationAttributeToProducts::process();

                // recalculate product cat counts
                $cron = Cron::getInstance();
                $cron->createCronTermRecount();

                // clear sitemap cache
                if (class_exists('\\WPSEO_Sitemaps_Cache')) {
                    remove_filter('wpseo_enable_xml_sitemap_transient_caching', '__return_false');

                    /** @psalm-suppress UndefinedClass */
                    \WPSEO_Sitemaps_Cache::clear();
                }
            } // end 'Предложения'
        } // end parse
        $orders = wc_get_orders([
            'numberposts' => '-1',
            'status' => array('wc-pending', 'wc-processing', 'wc-on-hold'),

        ]);

        $items_counter = [];
        foreach ($orders as $wc_order) {
            $items = $wc_order->get_items();
            foreach ($items as $item) {
                $item_id = $item->get_product_id();
                $address = $wc_order->get_shipping_address_1();
                if (empty($item_id)) {
                    continue;
                }
                if (empty($items_counter[$item_id][$address])) {
                    $items_counter[$item_id][$address] = 1;
                } else {
                    $items_counter[$item_id][$address]++;
                }
            }
        }

        //пересчёт мультисклада в связи с заказами
        foreach ($items_counter as $id => $ordered_data) {
            $product = wc_get_product($id);
            if (!$product || empty(get_post_meta($id, 'multi_sklad', true)))
                continue;
            $stock_diff = 0;
            $multisklad = explode('&1;', get_post_meta($id, 'multi_sklad', true));
            foreach ($multisklad as $key => $sklad) {
                $sklad = explode('&0;', $sklad);
                foreach ($ordered_data as $sklad_id => $count) {
                    if ($sklad[0] == $sklad_id) {
                        $new_count = intval($sklad[1]) - intval($count);
                        $stock_diff += intval($count);
                        $multisklad[$key] = $sklad[0] . '&0;' . (($new_count > 0) ? $new_count : 0);
                        break;
                    }
                }
            }
            $multisklad = implode('&1;', $multisklad);
            wc_update_product_stock($id, $stock_diff, 'decrease');
        }
        //пересчёт мультисклада

        \wp_defer_term_counting(false);
    }

    /**
     * @return void
     * @throws ProgressException
     *
     */
    private function removeMissingData()
    {
        DataDeletingOnFullExchange::products();
        DataDeletingOnFullExchange::categories();

        DataDeletingOnFullExchange::clearCache();

        $_SESSION['IMPORT_1C_PROCESS']['missingDataProcessed'] = true;
    }

    /**
     * @return bool
     * @throws \Exception
     *
     */
    private function isOldModuleOffersFile()
    {
        $fileName = basename(RootProcessStarter::getCurrentExchangeFileAbsPath());

        if (
            $fileName === 'offers.xml'
            || preg_match('/offers[0-9]/', $fileName)
            || preg_match('/offers_[0-9]/', $fileName)
        ) {
            return true;
        }

        return false;
    }
}
