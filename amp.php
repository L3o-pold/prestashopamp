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
 * @package prestashopamp
 */
class Amp extends Module
{
    /**
     * @var bool
     */
    public $bootstrap;

    /**
     * Odoo constructor.
     */
    public function __construct()
    {
        $this->name                   = 'amp';
        $this->version                = '2.0.9';
        $this->author                 = 'Leopold Jacquot';
        $this->need_instance          = 0;
        $this->bootstrap              = true;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.7.99.99');
        $this->tab                    = 'front_office_features';

        parent::__construct();

        $this->displayName = 'AMP';
        $this->description = $this->l('This module add AMP to product and category pages');
    }

    /**
     * @return bool
     * @throws PrestaShopException
     */
    public function install()
    {
        if (Configuration::get('PS_JS_DEFER')) {
            $this->_errors[] = $this->l('This module needs to have the option Move JavaScript to the end of the HTML document disabled in Advanced settings / Optimization menu');
            return false;
        }

        return parent::install() && $this->registerHook('displayHeader');
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        return parent::uninstall() && $this->unregisterHook('displayHeader');
    }

    /**
     * @return string
     */
    public function hookDisplayHeader()
    {
        if (!isset($this->context->controller->php_self)) {
            return '';
        }

        switch ($this->context->controller->php_self) {
            case 'product':
                $product = $this->context->controller->getProduct();

                if (!Validate::isLoadedObject($product)) {
                    return '';
                }

                $cacheId = 'amp_header|product|'.$product->id;
                $ampLink = $this->context->link->getModuleLink('amp', 'product', array('idProduct' => $product->id), true, $this->context->language->id, $this->context->shop->id, true);

                break;

            case 'category':
                $category = $this->context->controller->getCategory();

                if (!Validate::isLoadedObject($category)) {
                    return '';
                }

                $cacheId = 'amp_header|category|'.$category->id;
                $ampLink = $this->context->link->getModuleLink('amp', 'category', array('idCategory' => $category->id), true, $this->context->language->id, $this->context->shop->id, true);

                break;

            default:
                return '';
        }

        if (!$this->isCached('amp_header.tpl', $this->getCacheId($cacheId))) {
            $this->context->smarty->assign(array('amp_link' => $ampLink,));
        }

        return $this->display(__FILE__, 'amp_header.tpl', $this->getCacheId($cacheId));
    }
}
