<?php

require_once(dirname(__FILE__).'/../../../config/defines.inc.php');
require_once(dirname(__FILE__).'/../../../config/config.inc.php');

if (!Module::isInstalled('amp') || !intval(Tools::getValue('idProduct'))) {
    Controller::getController('PageNotFoundController')->run();
    exit;
}

if (in_array(Tools::getValue('lang'), LanguageCore::getIDs())) {
    $id_lang = Tools::getValue('lang');
} else {
    $id_lang = Configuration::get('PS_LANG_DEFAULT');
}

$product = new Product((int) Tools::getValue('idProduct'), false, $id_lang);

if (!Validate::isLoadedObject($product)) {
    Controller::getController('PageNotFoundController')->run();
    exit;
}

$smarty = Context::getContext()->smarty;
$link   = new Link();

$images         = $product->getImages((int) $id_lang);
$product_images = array();

if (isset($images[0])) {
    $smarty->assign('mainImage', $images[0]);
}

foreach ($images as $k => $image) {
    if ($image['cover']) {
        $smarty->assign('mainImage', $image);
        $cover                  = $image;
        $cover['id_image']      = (Configuration::get('PS_LEGACY_IMAGES') ? ($product->id.'-'.$image['id_image'])
            : $image['id_image']);
        $cover['id_image_only'] = (int) $image['id_image'];
        continue;
    }
    $product_images[(int) $image['id_image']] = $image;
}

if (!isset($cover)) {
    if (isset($images[0])) {
        $cover                  = $images[0];
        $cover['id_image']      = (Configuration::get('PS_LEGACY_IMAGES') ? ($product->id.'-'.$images[0]['id_image'])
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
$smarty->assign(array(
    'have_image'  => (isset($cover['id_image']) && (int) $cover['id_image']) ? array((int) $cover['id_image'])
        : Product::getCover((int) Tools::getValue('idProduct')),
    'cover'       => $cover,
    'imgWidth'    => (int) $size['width'],
    'mediumSize'  => Image::getSize(ImageType::getFormatedName('medium')),
    'largeSize'   => Image::getSize(ImageType::getFormatedName('large')),
    'homeSize'    => Image::getSize(ImageType::getFormatedName('home')),
    'cartSize'    => Image::getSize(ImageType::getFormatedName('cart')),
    'col_img_dir' => _PS_COL_IMG_DIR_,
));

if (count($product_images)) {
    $smarty->assign('images', $product_images);
}

$protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
$smarty->assign('canonical', $link->getProductLink($product->id, $product->link_rewrite));
$smarty->assign('meta_datas', Meta::getProductMetas($product->id, Context::getContext()->language->name, 'product'));
$smarty->assign('product', $product);
$smarty->assign('link', $link);
$smarty->assign('cover', Product::getCover((int) $product->id));
$smarty->assign('static_token', Tools::getToken(false));
$smarty->assign('logo_url', 'img/'.Configuration::get('PS_LOGO', null, null));
$smarty->assign('css', file_get_contents(__DIR__.'/../css/amp.css'));
$smarty->assign([
    'base_dir'     => _PS_BASE_URL_.__PS_BASE_URI__,
    'base_dir_ssl' => $protocol_link.Tools::getShopDomainSsl().__PS_BASE_URI__,
    'force_ssl'    => Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'),
]);

$smarty->display(__DIR__.'/../views/templates/hook/amp_product_template.tpl');