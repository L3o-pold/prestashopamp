<?php

/**
 * @package prestashopamp
 * @author  Léopold Jacquot {@link https://www.leopoldjacquot.com}
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
        $this->name          = 'amp';
        $this->version       = '2.0.5';
        $this->author        = 'Leopold Jacquot';
        $this->need_instance = 0;
        $this->bootstrap     = true;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.7.99.99');

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
                $ampLink = $this->context->link->getModuleLink('amp', 'product', ['idProduct' => $product->id], true, $this->context->language->id, $this->context->shop->id, true);

                break;

            case 'category':
                $category = $this->context->controller->getCategory();

                if (!Validate::isLoadedObject($category)) {
                    return '';
                }

                $cacheId = 'amp_header|category|'.$category->id;
                $ampLink = $this->context->link->getModuleLink('amp', 'category', ['idCategory' => $category->id], true, $this->context->language->id, $this->context->shop->id, true);

                break;

            case 'index':
                $cacheId = 'amp_header|index';
                $ampLink = $this->context->link->getModuleLink('amp', 'home', [], true, $this->context->language->id, $this->context->shop->id, true);

                break;

            default:
                return '';
                break;
        }

        if (!$this->isCached('amp_header.tpl', $this->getCacheId($cacheId)))
        {
            $this->context->smarty->assign(array('amp_link' => $ampLink,));
        }

        return $this->display(__FILE__, 'amp_header.tpl', $this->getCacheId($cacheId));
    }
}