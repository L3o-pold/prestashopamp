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
<!doctype html>
<html amp>
	<head>
		<meta charset="utf-8">
		<title>{$meta_datas['meta_title']}</title>
		{if isset($meta_datas['meta_description'])}
			<meta name="description" content="{$meta_datas['meta_description']}">
		{/if}
        <link rel="canonical" href="{$canonical}">
		<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
        {literal}
			<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
			<script async src="https://cdn.ampproject.org/v0.js"></script>
			<script async custom-element="amp-carousel" src="https://cdn.ampproject.org/v0/amp-carousel-0.1.js"></script>
        {/literal}
		<style amp-custom>
            {$css nofilter}
        </style>
	</head>
	<body>
	<div class="page-body-amp">
		<div class="header-column-amp">
			<a href="{$urls.pages.index}">
				<amp-img src="{$shop.logo}"
					width="250"
					height="99"
					id="shop-logo-amp"
					alt="{l s='Shop logo' mod='amp'}">
				</amp-img>
			</a>
		</div>
		<div class="page-content-amp">
			{if $category->id_image}
				<div id="category-image-amp">
					<amp-img src="{url entity='categoryImage' id=$category->id_category name='category_default'}"
						width="141"
						height="180"
						layout="responsive"
						alt="{$category->name}"></amp-img>
					</amp-carousel>
				</div>
			{/if}

			<h1 id="category-name-amp">
				<a href="{url entity='category' id=$category->id_category id_lang=$language.id}">
					{$category->name}
				</a>
			</h1>
			<div class="rte width-full float-left">
				{$category->clean_description nofilter}
			</div>
            {foreach from=$catProducts item=product}
				<div class="float-left product-header">
					<div>
						<amp-img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}"
								 width="80"
								 height="80"
								 layout="responsive"
								 class="product-image-amp"
								 alt="{$product.name}"></amp-img>
					</div>
					<h5 class="width-float product-name-amp">
						<a href="{$product.link}" title="{$product.name}">
                            {$product.name|truncate:40:'...'}
						</a>
					</h5>
					<p class="product-price-amp">
						<span>{$product.price} {$currency.sign}</span>
					</p>
					<p class="product-add-to-cart-amp {if $product.quantity == 0} disabled {/if}">
						<a class="btn btn-primary" {if $product.quantity == 0} href="#" {else} href="{$product.addToCartLink}" {/if}>
							{l s='Add to cart' mod='amp'}
						</a>
					</p>
				</div>
            {/foreach}
		</div>
	</div>
</body>
</html>