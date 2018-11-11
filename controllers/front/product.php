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
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 *
 * @author    Léopold Jacquot {@link https://www.leopoldjacquot.com}
 * @copyright Léopold Jacquot
 * @license   MIT
 **/

/**
 * @author    Léopold Jacquot {@link https://www.leopoldjacquot.com}
 * @copyright Léopold Jacquot
 * @license   MIT
 * @package   prestashopamp
 */
class AmpProductModuleFrontController extends ModuleFrontController
{
    /**
     * @throws PrestaShopException
     */
    public function initContent()
    {
        $this->display_header       = false;
        $this->display_footer       = false;
        $this->display_column_left  = false;
        $this->display_column_right = false;

        $product = new Product(
            (int) Tools::getValue('idProduct'), false, $this->context->language->id
        );

        if (!Validate::isLoadedObject($product)) {
            Controller::getController('PageNotFoundController')->run();
        }

        $link = new Link();

        $images         = $product->getImages($this->context->language->id);
        $product_images = array();

        if (isset($images[0])) {
            $this->context->smarty->assign('mainImage', $images[0]);
        }

        foreach ($images as $image) {
            if ($image['cover']) {
                $this->context->smarty->assign('mainImage', $image);
                $cover                  = $image;
                $cover['id_image']      = (Configuration::get('PS_LEGACY_IMAGES')
                    ? ($product->id.'-'.$image['id_image']) : $image['id_image']);
                $cover['id_image_only'] = (int) $image['id_image'];
                continue;
            }
            $product_images[(int) $image['id_image']] = $image;
        }

        if (!isset($cover)) {
            if (isset($images[0])) {
                $cover                  = $images[0];
                $cover['id_image']      = (Configuration::get('PS_LEGACY_IMAGES')
                    ? ($product->id.'-'.$images[0]['id_image'])
                    : $images[0]['id_image']);
                $cover['id_image_only'] = (int) $images[0]['id_image'];
            } else {
                $cover = array(
                    'id_image' => $this->context->language->iso_code.'-default',
                    'legend'   => 'No picture',
                    'title'    => 'No picture',
                );
            }
        }
        $size = Image::getSize(ImageType::getFormatedName('large'));
        $this->context->smarty->assign(
            array(
                'have_image'  => (isset($cover['id_image'])
                                  && (int) $cover['id_image'])
                    ? array((int) $cover['id_image'])
                    : Product::getCover(
                        (int) Tools::getValue('idProduct')
                    ),
                'cover'       => $cover,
                'imgWidth'    => (int) $size['width'],
                'mediumSize'  => Image::getSize(
                    ImageType::getFormatedName('medium')
                ),
                'largeSize'   => Image::getSize(ImageType::getFormatedName('large')),
                'homeSize'    => Image::getSize(ImageType::getFormatedName('home')),
                'cartSize'    => Image::getSize(ImageType::getFormatedName('cart')),
                'col_img_dir' => _PS_COL_IMG_DIR_,
            )
        );

        if (count($product_images)) {
            $this->context->smarty->assign('images', $product_images);
        }

        /**
         * @todo Move this to an helper
         */
        $product->clean_description = preg_replace(
            '/(<[^>]+) style=".*?"/i', '$1', $product->description
        );
        $product->clean_description = preg_replace(
            '/(<[^>]+) xml\:lang=".*?"/i', '$1', $product->description
        );
        $product->clean_description = preg_replace(
            '/(<[^>]+) lang=".*?"/i', '$1', $product->description
        );
        $product->clean_description = preg_replace(
            '/<img[^>]+\>/i', '', $product->clean_description
        );
        $product->clean_description = preg_replace(
            '/<iframe.*?\/iframe>/i', '', $product->clean_description
        );
        $product->clean_description = preg_replace(
            '/<video.*?\/video>/i', '', $product->clean_description
        );

        $this->context->smarty->assign(
            'canonical', $link->getProductLink($product->id, $product->link_rewrite)
        );
        $this->context->smarty->assign(
            'meta_datas', Meta::getProductMetas(
            $product->id, Context::getContext()->language->id, 'product'
        )
        );
        $this->context->smarty->assign('product', $product);
        $this->context->smarty->assign('link', $link);
        $this->context->smarty->assign(
            'cover', Product::getCover((int) $product->id)
        );
        $this->context->smarty->assign(
            'css', Tools::file_get_contents(_PS_MODULE_DIR_.'amp/views/css/amp.css')
        );

        /**
         * @todo Find a better way to handle price in PS 1.7
         */
        if (version_compare(_PS_VERSION_, '1.7.0', '>=')) {
            $priceDisplay                 = Product::getTaxCalculationMethod(
                (int) $this->context->cookie->id_customer
            );
            $productPrice                 = 0;
            $productPriceWithoutReduction = 0;

            if (!$priceDisplay || $priceDisplay == 2) {
                $productPrice                 = $product->getPrice(true, null, 6);
                $productPriceWithoutReduction = $product->getPriceWithoutReduct(
                    false, null
                );
            } elseif ($priceDisplay == 1) {
                $productPrice                 = $product->getPrice(false, null, 6);
                $productPriceWithoutReduction = $product->getPriceWithoutReduct(
                    true, null
                );
            }

            $this->context->smarty->assign(
                array(
                    'price'                        => $productPrice,
                    'priceDisplay'                 => $priceDisplay,
                    'productPriceWithoutReduction' => $productPriceWithoutReduction,
                    'addToCartLink'                => $link->getPageLink(
                        'cart', true, $this->context->language->id, array(
                        'add'        => 1,
                        'id_product' => $product->id,
                        'token'      => Tools::getToken(false),
                    ), false, $this->context->shop->id
                    ),
                )
            );

            $this->setTemplate('module:amp/views/templates/front/product_17.tpl');
        } else {
            $this->setTemplate('product.tpl');
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
