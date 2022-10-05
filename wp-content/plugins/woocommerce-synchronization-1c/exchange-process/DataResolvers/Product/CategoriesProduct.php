<?php

namespace Itgalaxy\Wc\Exchange1c\ExchangeProcess\DataResolvers\Product;

use Itgalaxy\Wc\Exchange1c\ExchangeProcess\Helpers\Term;
use Itgalaxy\Wc\Exchange1c\Includes\SettingsHelper;

class CategoriesProduct
{
    /**
     * Main logic.
     *
     * Example xml structure (position - Товар -> Группы)
     *
     * ```xml
     * <Группы>
     *     <Ид>6d615f3c-4266-11e4-ae62-1c6f65cec896</Ид>
     *     <Ид>8de28a6b-1903-11e2-bc2c-10bf4876822f</Ид>
     * </Группы>
     *
     * @param \SimpleXMLElement $element Node object "Товар".
     *
     * @return array
     */
    public static function process(\SimpleXMLElement $element)
    {
        if (!isset($element->Группы->Ид)) {
            return [];
        }

        $categoryIds = self::getCategoryList();

        if (empty($categoryIds)) {
            return [];
        }

        $resolvedList = [];

        foreach ($element->Группы->Ид as $groupXmlId) {
            if (!isset($categoryIds[(string) $groupXmlId])) {
                continue;
            }

            $resolvedList[] = $categoryIds[(string) $groupXmlId];
        }

        return array_unique($resolvedList);
    }

    /**
     * @return array
     */
    private static function getCategoryList()
    {
        if (!isset($_SESSION['IMPORT_1C']['categoryIds'])) {
            $_SESSION['IMPORT_1C']['categoryIds'] = !self::isDisabled() ? Term::getProductCatIDs() : [];
        }

        return $_SESSION['IMPORT_1C']['categoryIds'];
    }

    /**
     * Checking whether the processing categories is disabled in the settings.
     *
     * @return bool
     */
    private static function isDisabled()
    {
        return !SettingsHelper::isEmpty('skip_categories');
    }
}
