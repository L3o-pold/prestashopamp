<?php

/**
 * @package prestashopamp
 * @author  Léopold Jacquot {@link https://www.leopoldjacquot.com}
 */
class amphomeModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $this->display_header = false;
        $this->display_footer = false;
        $this->display_column_left = false;
        $this->display_column_right = false;
        $idLang = $this->context->language->id;
        $link = $this->context->link;
        $idShop = $this->context->shop->id;

        if ($idLang && $link) {
            $smartyVars = array();
            $smartyVars['idLang'] = $idLang;
            $smartyVars['idShop'] = $idShop;
            $smartyVars['contactUrl'] = 'contact';
            $smartyVars['module_dir'] = _MODULE_DIR_;
            $smartyVars['slides'] = $this->getSlides(true);
            $smartyVars['showContact'] = Configuration::get('FOOTER_CONTACT');
            $smartyVars['showSitemap'] = Configuration::get('FOOTER_SITEMAP');
            $smartyVars['emailInfo'] = Configuration::get('BLOCKCONTACTINFOS_EMAIL');
            $smartyVars['phoneInfo'] = Configuration::get('BLOCKCONTACTINFOS_PHONE');
            $smartyVars['addressInfo'] = Configuration::get('BLOCKCONTACTINFOS_ADDRESS');
            $smartyVars['companyInfo'] = Configuration::get('BLOCKCONTACTINFOS_COMPANY');
            $smartyVars['homeNewArrivals'] = Configuration::get('WK-HOME-NEW-ARRIVALS-AMP');
            $smartyVars['homeBestSeller'] = Configuration::get('WK-HOME-BEST-SELLER-AMP');
            $smartyVars['displayStoresFooter'] = Configuration::get('PS_STORES_DISPLAY_FOOTER');
            $smartyVars['homeFeaturedProducts'] = Configuration::get('WK-HOME-FEATURED-PRODUCTS-AMP');
            $smartyVars['homeBestSellerPosition'] = Configuration::get('WK-HOME-BEST-SELLER-POSITION');
            $smartyVars['homeNewArrivalsPosition'] = Configuration::get('WK-HOME-NEW-ARRIVALS-POSITION');
            $smartyVars['homeFeaturedProductsPosition'] = Configuration::get('WK-HOME-FEATURED-PRODUCTS-POSITION');
            $smartyVars['homePageLink'] = $link->getPageLink('index', true, $idLang, null, false, $smartyVars['idShop']);
            $this->context->smarty->assign('css', file_get_contents(__DIR__.'/../../css/amp.css'));
            if ($bestSeller = $this->getBestSellers()) {
                // if products available get product add to cart link
                foreach ($bestSeller as &$product) {
                    $product['addToCartLink'] = $link->getPageLink('cart', true, $idLang, ['add' => 1, 'id_product' => $product['id_product'], 'token' => Tools::getToken(false)], false, $idShop);
                }
                $smartyVars['bestSeller'] = $bestSeller;
            }
            if ($newArrivalProducts = $this->getNewArrival()) {
                foreach ($newArrivalProducts as &$product) {
                    $product['addToCartLink'] = $link->getPageLink('cart', true, $idLang, ['add' => 1, 'id_product' => $product['id_product'], 'token' => Tools::getToken(false)], false, $idShop);
                }
                $smartyVars['newArrivalProducts'] = $newArrivalProducts;
            }
            if ($featuredProducts = $this->getFeaturedProducts()) {
                foreach ($featuredProducts as &$product) {
                    $product['addToCartLink'] = $link->getPageLink('cart', true, $idLang, ['add' => 1, 'id_product' => $product['id_product'], 'token' => Tools::getToken(false)], false, $idShop);
                }
                $smartyVars['featuredProducts'] = $featuredProducts;
            }

            $this->context->smarty->assign($smartyVars);
        }
        $this->setTemplate('home-amp.tpl');
        parent::initContent();
    }

    public function getNewArrival()
    {
        $link = $this->context->link;
        $idLang = $this->context->language->id;
        $idShop = $this->context->shop->id;
        if (!Configuration::get('NEW_PRODUCTS_NBR')) {
            return;
        }
        $newProducts = false;
        if (Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) {
            $newProducts = Product::getNewProducts((int) $idLang, 0, (int)Configuration::get('NEW_PRODUCTS_NBR'));
        }

        if (!$newProducts && Configuration::get('PS_BLOCK_NEWPRODUCTS_DISPLAY')) {
            return;
        }

        $newProducts = array_slice($newProducts, 0, 6, true); // keep array till 6 rest removed.
        foreach ($newProducts as &$product) {
            $product['addToCartLink'] = $link->getPageLink('cart', true, $idLang, ['add' => 1, 'id_product' => $product['id_product'], 'token' => Tools::getToken(false)], false, $idShop);
        }

        return $newProducts;
    }

    public function getSlides($active = null)
    {
        $idShop = $this->context->shop->id;
        $idLang = $this->context->language->id;

        return Db::getInstance()->executeS(
            'SELECT hs.`id_homeslider_slides` as id_slide, hss.`position`, hss.`active`, hssl.`title`,
            hssl.`url`, hssl.`legend`, hssl.`description`, hssl.`image`
            FROM '._DB_PREFIX_.'homeslider hs
            LEFT JOIN '._DB_PREFIX_.'homeslider_slides hss ON (hs.id_homeslider_slides = hss.id_homeslider_slides)
            LEFT JOIN '._DB_PREFIX_.'homeslider_slides_lang hssl ON (hss.id_homeslider_slides = hssl.id_homeslider_slides)
            WHERE id_shop = '.(int)$idShop.'
            AND hssl.id_lang = '.(int)$idLang.
            ($active ? ' AND hss.`active` = 1' : ' ').'
            ORDER BY hss.position'
        );
    }

    public function getConfigFieldsValues()
    {
        $idShopGroup = Shop::getContextShopGroupID();
        $idShop = Shop::getContextShopID();

        return array(
            'HOMESLIDER_WIDTH' => Tools::getValue('HOMESLIDER_WIDTH', Configuration::get('HOMESLIDER_WIDTH', null, $idShopGroup, $idShop)),
            'HOMESLIDER_SPEED' => Tools::getValue('HOMESLIDER_SPEED', Configuration::get('HOMESLIDER_SPEED', null, $idShopGroup, $idShop)),
            'HOMESLIDER_PAUSE' => Tools::getValue('HOMESLIDER_PAUSE', Configuration::get('HOMESLIDER_PAUSE', null, $idShopGroup, $idShop)),
            'HOMESLIDER_LOOP' => Tools::getValue('HOMESLIDER_LOOP', Configuration::get('HOMESLIDER_LOOP', null, $idShopGroup, $idShop)),
        );
    }

    public function getBestSellers()
    {
        if (Configuration::get('PS_CATALOG_MODE')) {
            return false;
        }

        $idLang = $this->context->language->id;
        if (!($result = ProductSale::getBestSalesLight((int)$idLang, 0, (int)Configuration::get('PS_BLOCK_BESTSELLERS_TO_DISPLAY')))) {
            return (Configuration::get('PS_BLOCK_BESTSELLERS_DISPLAY') ? array() : false);
        }

        $currency = new Currency($this->context->cookie->id_currency);
        $usetax = (Product::getTaxCalculationMethod((int)$this->context->customer->id) != PS_TAX_EXC);
        foreach ($result as &$row) {
            $row['price'] = Tools::displayPrice(Product::getPriceStatic((int)$row['id_product'], $usetax), $currency);
        }

        return $result;
    }

    public function getFeaturedProducts()
    {
        $category = new Category((int)Configuration::get('HOME_FEATURED_CAT'), (int)Context::getContext()->language->id);
        $nb = (int)Configuration::get('HOME_FEATURED_NBR');
        if (Configuration::get('HOME_FEATURED_RANDOMIZE')) {
            $homeFeaturedProducts = $category->getProducts((int)Context::getContext()->language->id, 1, ($nb ? $nb : 8), null, null, false, true, true, ($nb ? $nb : 8));
        } else {
            $homeFeaturedProducts = $category->getProducts((int)Context::getContext()->language->id, 1, ($nb ? $nb : 8), 'position');
        }

        if ($homeFeaturedProducts === false || empty($homeFeaturedProducts)) {
            return false;
        }
        return $homeFeaturedProducts;
    }
}
