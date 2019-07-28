{*
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
*}
{if !isset($priceDisplayPrecision)}
    {assign var='priceDisplayPrecision' value=2}
{/if}
{if !$priceDisplay || $priceDisplay == 2}
    {assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL, 6)}
    {assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL)}
{elseif $priceDisplay == 1}
    {assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL, 6)}
    {assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL)}
{/if}
<!doctype html>
<html amp>
    <head>
        <meta charset="utf-8">
        <script async src="https://cdn.ampproject.org/v0.js"></script>
        <title>{$meta_datas['meta_title']}</title>
        {if isset($meta_datas['meta_description'])}
            <meta name="description" content="{$meta_datas['meta_description']}">
        {/if}
        <link rel="canonical" href="{$canonical}">
        <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
        {literal}
            <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style>
            <noscript>
            <style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style>
        </noscript>
        {/literal}
        <script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>

        <style amp-custom>
            {$css}
        </style>
    </head>
    <body>
        <div class="page-body-amp">
            <div class="header-column-amp">
                <a href="{$link->getPageLink('index')|escape:'html':'UTF-8'}">
                    <amp-img src="{$logo_url|escape:'html':'UTF-8'}"
                             width="250"
                             height="99"
                             id="shop-logo-amp"
                             alt="{l s='Shop logo' mod='amp'}">
                    </amp-img>
                </a>
            </div>
            <div class="page-content-amp">
                <div id="product-image-amp">
                    <amp-carousel width="400"
                            height="300"
                            layout="responsive"
                            type="slides"
                            autoplay
                            delay="2000">
                        <amp-img
                                src="//{$link->getImageLink($product->id, $cover.id_image, 'large_default')|escape:'html':'UTF-8'}"
                                width="{$largeSize['width']}"
                                height="{$largeSize['height']}"
                                alt="{if !empty($cover.legend)}{$cover.legend|escape:'html':'UTF-8'}{else}{$product->name|escape:'html':'UTF-8'}{/if}"
                                layout="responsive">
                        </amp-img>
                        {if isset($images) && count($images) > 0}
                            {foreach from=$images item=image name=thumbnails}
                                {assign var=imageIds value="`$product->id`-`$image.id_image`"}
                                {if !empty($image.legend)}
                                    {assign var=imageTitle value=$image.legend|escape:'html':'UTF-8'}
                                {else}
                                    {assign var=imageTitle value=$product->name|escape:'html':'UTF-8'}
                                {/if}
                                <amp-img
                                        src="//{$link->getImageLink($product->link_rewrite, $imageIds, 'large_default')|escape:'html':'UTF-8'}"
                                        width="{$largeSize['width']}"
                                        height="{$largeSize['height']}"
                                        layout="responsive"
                                        alt="{$imageTitle}">
                                </amp-img>
                            {/foreach}
                        {/if}
                    </amp-carousel>
                </div>
                <h1 id="product-name-amp">
                    {$product->name|escape:'html':'UTF-8'}
                </h1>
                <p>{l s='Reference' mod='amp'}: {$product->reference|escape:'html':'UTF-8'}</p>
                <p>
                    {$product->clean_description|escape:'UTF-8'}
                </p>
                <p>
                    <span id="product-price-amp">
                        {convertPrice price=$productPrice|floatval}
                    </span>
                </p>
                <p id="product-add-to-cart-amp">
                    {capture}add=1&amp;id_product={$product->id|intval}{if isset($static_token)}&amp;token={$static_token}{/if}{/capture}
                    <a href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" class="btn btn-primary">
                        {l s='Add to cart' mod='amp'}
                    </a>
                </p>
            </div>
        </div>
    </body>
</html>