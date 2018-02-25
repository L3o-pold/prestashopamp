<?php

/**
 * @package prestashopamp
 * @author  Léopold Jacquot {@link https://www.leopoldjacquot.com}
 */
class AmpCategoryModuleFrontController extends ModuleFrontController
{
    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function initContent()
    {
        $this->display_header = false;
        $this->display_footer = false;
        $this->display_column_left = false;
        $this->display_column_right = false;

        $idLang = $this->context->language->id;
        $link = $this->context->link;
        $idShop = $this->context->shop->id;
        $idCategory = Tools::getValue('idCategory');
        $page = Tools::getValue('p', 1);

        $this->category = new Category($idCategory, $idLang, $idShop);

        $this->category->clean_description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $this->category->description);

        if (!Validate::isLoadedObject($this->category)) {
            Controller::getController('PageNotFoundController')->run();
        }

        $this->p = $page;
        $list = array(0 => 'name', 1 => 'price', 2 => 'date_add', 3 => 'date_upd', 4 => 'position', 5 => 'manufacturer_name', 6 => 'quantity', 7 => 'reference');
        $this->orderBy = $list[Configuration::get('PS_PRODUCTS_ORDER_BY')];
        if (Configuration::get('PS_PRODUCTS_ORDER_WAY') == 0) {
            $this->orderWay = 'asc';
        } else {
            $this->orderWay = 'desc';
        }
        $this->n = Configuration::get('PS_PRODUCTS_PER_PAGE');


        $this->nbProducts = $this->category->getProducts(null, null, null, $this->orderBy, $this->orderWay, true);
        if (version_compare(_PS_VERSION_, '1.7.0', '<')) {
            $this->pagination((int) $this->nbProducts); // Pagination must be call after "getProducts"
        }
        $this->cat_products = $this->category->getProducts($this->context->language->id, (int)$this->p, (int)$this->n, $this->orderBy, $this->orderWay);

        if (version_compare(_PS_VERSION_, '1.7.0', '<')) {
            $currentStart = (($this->p-1)*$this->n)+1;
            $currentStop = ($this->p*$this->n);
            $currentStop = ($currentStop < $this->nbProducts) ? $currentStop : $this->nbProducts;
            // in case when no product in category
            if ($currentStop < $currentStart) {
                $currentStart = $currentStop;
            }
        }

        // if products available get product add to cart link
        if ($this->cat_products) {
            foreach ($this->cat_products as &$product) {
                $product['addToCartLink'] = $link->getPageLink('cart', true, $idLang, ['add' => 1, 'id_product' => $product['id_product'], 'token' => Tools::getToken(false)], false, $idShop);

                if (version_compare(_PS_VERSION_, '1.7.0', '>=')) {
                    $tmpProduct = new Product($product['id_product']);
                    $priceDisplay                 = Product::getTaxCalculationMethod(
                        (int) $this->context->cookie->id_customer
                    );
                    $productPrice                 = 0;

                    if (!$priceDisplay || $priceDisplay == 2) {
                        $productPrice = $tmpProduct->getPrice(
                            true, null, 6
                        );
                    } elseif ($priceDisplay == 1) {
                        $productPrice = $tmpProduct->getPrice(
                            false, null, 6
                        );
                    }

                    $product['price'] = $productPrice;
                }
            }
        }

        $smartyVars = array();
        $smartyVars['idLang'] = $idLang;
        $smartyVars['idShop'] = $idShop;
        $smartyVars['link'] = $link;
        $smartyVars['noOfPages'] = $this->nbProducts/$this->n;
        $smartyVars['currentPage'] = $this->p;
        $smartyVars['contactUrl'] = 'contact';
        $smartyVars['idCategory'] = $idCategory;
        $smartyVars['module_dir'] = _MODULE_DIR_;
        $smartyVars['category'] = $this->category;
        if (version_compare(_PS_VERSION_, '1.7.0', '<')) {
            $smartyVars['currentStop']  = $currentStop;
            $smartyVars['currentStart'] = $currentStart;
        }
        $smartyVars['nbProducts'] = $this->nbProducts;
        $smartyVars['catProducts'] = $this->cat_products;
        $smartyVars['categoryLink'] = $link->getCategoryLink($this->category, null, $idLang, null, $idShop);
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $smartyVars['categoryLink'] .= '?p='.$this->p;
        } else {
            $smartyVars['categoryLink'] .= '&p='.$this->p;
        }

        $smartyVars['homePageLink'] = $link->getPageLink('index', true, $idLang, null, false, $smartyVars['idShop']);
        $this->context->smarty->assign('meta_datas', Meta::getCategoryMetas($idCategory, $this->context->language->id, 'category'));
        $this->context->smarty->assign('css', file_get_contents(__DIR__.'/../../css/amp.css'));

        $this->context->smarty->assign($smartyVars);

        $this->context->smarty->assign('canonical', $smartyVars['categoryLink']);
        $this->context->smarty->assign('meta_datas', Meta::getCategoryMetas($this->category->id, Context::getContext()->language->id, 'category'));
        $this->context->smarty->assign('css', file_get_contents(__DIR__.'/../../css/amp.css'));

        if (version_compare(_PS_VERSION_, '1.7.0', '>=')) {
            $this->setTemplate('module:amp/views/templates/front/category_17.tpl');
        } else {
            $this->setTemplate('category.tpl');
        }

        parent::initContent();
    }
}
