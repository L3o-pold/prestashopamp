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
        $this->version       = '1.0';
        $this->author        = 'Paprika Agency';
        $this->need_instance = 0;
        $this->bootstrap     = true;

        parent::__construct();

        $this->displayName = $this->l('AMP');
        $this->description = $this->l(
            'This module add AMP to home, product and news page'
        );
    }

    /**
     * @return bool
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
     * @param $params
     *
     * @return string
     */
    public function hookDisplayHeader($params)
    {
        if (!isset($this->context->controller->php_self) || $this->context->controller->php_self != 'product') {
            return '';
        }

        $product = $this->context->controller->getProduct();

        if (!Validate::isLoadedObject($product)) {
            return '';
        }
        if (!$this->isCached('amp_header.tpl', $this->getCacheId('amp_header|'.(isset($product->id) && $product->id ? (int)$product->id : ''))))
        {
            $this->context->smarty->assign(array(
                'product' => $product,
                'link_rewrite' => isset($product->link_rewrite) && $product->link_rewrite ? $product->link_rewrite : '',
            ));
        }

        return $this->display(__FILE__, 'amp_header.tpl',
            $this->getCacheId('socialsharing_header|'.(isset($product->id) && $product->id ? (int) $product->id : '')));
    }
}