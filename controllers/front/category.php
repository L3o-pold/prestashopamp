<?php
/**
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 * @author  Léopold Jacquot {@link https://www.leopoldjacquot.com}
 * @copyright Léopold Jacquot
 * @license  MIT
 **/

/**
 * @author  Léopold Jacquot {@link https://www.leopoldjacquot.com}
 * @copyright Léopold Jacquot
 * @license  MIT
 * @package prestashopamp
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

        /**
         * @todo Move this to an helper
         */
        $this->category->clean_description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $this->category->description);
        $this->category->clean_description = preg_replace('/(<[^>]+) xml\:lang=".*?"/i', '$1', $this->category->description);
        $this->category->clean_description = preg_replace('/(<[^>]+) lang=".*?"/i', '$1', $this->category->description);
        $this->category->clean_description = preg_replace('/<img[^>]+\>/i', '', $this->category->clean_description);
        $this->category->clean_description = preg_replace('/<iframe.*?\/iframe>/i', '', $this->category->clean_description);
        $this->category->clean_description = preg_replace('/<video.*?\/video>/i', '', $this->category->clean_description);

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
                $product['addToCartLink'] = $link->getPageLink('cart', true, $idLang, array('add' => 1, 'id_product' => $product['id_product'], 'token' => Tools::getToken(false)), false, $idShop);

                /**
                 * @todo Find a better way to handle price in PS 1.7
                 */
                if (version_compare(_PS_VERSION_, '1.7.0', '>=')) {
                    $tmpProduct = new Product($product['id_product']);
                    $priceDisplay = Product::getTaxCalculationMethod($this->context->cookie->id_customer);
                    $productPrice                 = 0;

                    if (!$priceDisplay || $priceDisplay == 2) {
                        $productPrice = $tmpProduct->getPrice(true, null, 6);
                    } elseif ($priceDisplay == 1) {
                        $productPrice = $tmpProduct->getPrice(false, null, 6);
                    }

                    $product['price'] = $productPrice;
                }
            }
        }

        /**
         * @todo Clean smarty vars
         */
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
        $this->context->smarty->assign('css', Tools::file_get_contents(_PS_MODULE_DIR_.'amp/views/css/amp.css'));
        $this->context->smarty->assign($smartyVars);
        $this->context->smarty->assign('canonical', $smartyVars['categoryLink']);
        $this->context->smarty->assign('meta_datas', Meta::getCategoryMetas($this->category->id, Context::getContext()->language->id, 'category'));

        if (version_compare(_PS_VERSION_, '1.7.0', '>=')) {
            $this->setTemplate('module:amp/views/templates/front/category_17.tpl');
        } else {
            $this->setTemplate('category.tpl');
        }

        parent::initContent();
    }

    /**
     * @return bool
     */
    public function setMedia()
    {
        return false;
    }

    /**
     * Renders controller templates and generates page content
     *
     * @param array|string $content Template file(s) to be rendered
     *
     * @throws Exception
     * @throws SmartyException
     */
    protected function smartyOutputContent($content)
    {
        if (!Configuration::get('PS_JS_DEFER')) {
            parent::smartyOutputContent($content);
            return;
        }

        Configuration::set('PS_JS_DEFER', 0);

        parent::smartyOutputContent($content);

        Configuration::set('PS_JS_DEFER', 1);
    }
}
