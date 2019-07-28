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
			{if $category->id_image}
				<div id="category-image-amp">
					<amp-img src="{$link->getCatImageLink($category->link_rewrite, $category->id_image, 'category_default')|escape:'html':'UTF-8'}"
						width="870"
						height="217"
						layout="responsive"
						alt="{$category->name|escape:'html':'UTF-8'}"></amp-img>
					</amp-carousel>
				</div>
			{/if}

			<h1 id="category-name-amp">
				<a href="{$link->getCategoryLink($category->id_category, $category->link_rewrite)|escape:'html':'UTF-8'}">
					{$category->name|escape:'html':'UTF-8'}
				</a>
			</h1>
			<div class="rte width-full float-left">
				{$category->clean_description|escape:'UTF-8'}
			</div>
	        <div class="width-full float-left">
		        <div class="float-left pagination-text">
					<span>
						{l s='Showing ' mod='amp'} {$currentStart|escape:'html':'UTF-8'} - {$currentStop|escape:'html':'UTF-8'} {l s=' of ' mod='amp'} {$nbProducts|escape:'html':'UTF-8'} {l s=' items.' mod='amp'}
					</span>
				</div>
                {if !isset($current_url)}
                    {assign var='requestPage' value=$link->getPaginationLink('category', $category, false, false, true, false)}
                {else}
                    {assign var='requestPage' value=$current_url}
                {/if}
                {if $start!=$stop}
					<div class="pagination-block">
						<ul class="pagination">
                            {if $p != 1}
                                {assign var='p_previous' value=$p-1}
								<li id="pagination_previous{if isset($paginationId)}_{$paginationId|escape:'quotes':'UTF-8'}{/if}" class="pagination_previous">
									<a href="{$link->goPage($requestPage, $p_previous)|escape:'quotes':'UTF-8'}" rel="prev">
										&lt; <b>{l s='Previous' mod='amp'}</b>
									</a>
								</li>
                            {else}
								<li id="pagination_previous{if isset($paginationId)}_{$paginationId|escape:'html':'UTF-8'}{/if}" class="disabled pagination_previous">
									<span>
										&lt; <b>{l s='Previous' mod='amp'}</b>
									</span>
								</li>
                            {/if}
                            {if $start==3}
								<li>
									<a href="{$link->goPage($requestPage, 1)|escape:'quotes':'UTF-8'}">
										<span>1</span>
									</a>
								</li>
								<li>
									<a href="{$link->goPage($requestPage, 2)|escape:'quotes':'UTF-8'}">
										<span>2</span>
									</a>
								</li>
                            {/if}
                            {if $start==2}
								<li>
									<a href="{$link->goPage($requestPage, 1)|escape:'quotes':'UTF-8'}">
										<span>1</span>
									</a>
								</li>
                            {/if}
                            {if $start>3}
								<li>
									<a href="{$link->goPage($requestPage, 1)|escape:'quotes':'UTF-8'}">
										<span>1</span>
									</a>
								</li>
								<li class="truncate">
									<span>
										<span>...</span>
									</span>
								</li>
                            {/if}
                            {section name=pagination start=$start loop=$stop+1 step=1}
                                {if $p == $smarty.section.pagination.index}
									<li class="active current">
										<span>
											<span>{$p|escape:'html':'UTF-8'}</span>
										</span>
									</li>
                                {else}
									<li>
										<a href="{$link->goPage($requestPage, $smarty.section.pagination.index)|escape:'quotes':'UTF-8'}">
											<span>{$smarty.section.pagination.index|escape:'html':'UTF-8'}</span>
										</a>
									</li>
                                {/if}
                            {/section}
                            {if $pages_nb>$stop+2}
								<li class="truncate">
									<span>
										<span>...</span>
									</span>
								</li>
								<li>
									<a href="{$link->goPage($requestPage, $pages_nb)|escape:'quotes':'UTF-8'}">
										<span>{$pages_nb|intval}</span>
									</a>
								</li>
                            {/if}
                            {if $pages_nb==$stop+1}
								<li>
									<a href="{$link->goPage($requestPage, $pages_nb)|escape:'quotes':'UTF-8'}">
										<span>{$pages_nb|intval}</span>
									</a>
								</li>
                            {/if}
                            {if $pages_nb==$stop+2}
								<li>
									<a href="{$link->goPage($requestPage, $pages_nb-1)|escape:'quotes':'UTF-8'}">
										<span>{$pages_nb-1|intval}</span>
									</a>
								</li>
								<li>
									<a href="{$link->goPage($requestPage, $pages_nb)|escape:'quotes':'UTF-8'}">
										<span>{$pages_nb|intval}</span>
									</a>
								</li>
                            {/if}
                            {if $pages_nb > 1 AND $p != $pages_nb}
                                {assign var='p_next' value=$p+1}
								<li id="pagination_next{if isset($paginationId)}_{$paginationId|escape:'html':'UTF-8'}{/if}" class="pagination_next">
									<a href="{$link->goPage($requestPage, $p_next)|escape:'quotes':'UTF-8'}" rel="next">
										<b>{l s='Next' mod='amp'}</b> &gt;
									</a>
								</li>
                            {else}
								<li id="pagination_next{if isset($paginationId)}_{$paginationId|escape:'quotes':'UTF-8'}{/if}" class="disabled pagination_next">
									<span>
										<b>{l s='Next' mod='amp'}</b> &gt;
									</span>
								</li>
                            {/if}
						</ul>
					</div>
                {/if}
			</div>
            {foreach from=$catProducts item=product}
				<div class="float-left product-header">
					<div>
						<amp-img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}"
								 width="80"
								 height="80"
								 layout="responsive"
								 class="product-image-amp"
								 alt="{$product.name|escape:'html':'UTF-8'}"></amp-img>
					</div>
					<h5 class="width-float product-name-amp">
						<a href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}">
                            {$product.name|truncate:40:'...'|escape:'html':'UTF-8'}
						</a>
					</h5>
					<p class="product-price-amp">
						<span>{convertPrice price=($product.price)}</span>
					</p>
					<p class="product-add-to-cart-amp {if $product.quantity == 0} disabled {/if}">
						<a class="btn btn-primary" {if $product.quantity == 0} href="#" {else} href="{$product.addToCartLink|escape:'html':'UTF-8'}" {/if}>
							{l s='Add to cart' mod='amp'}
						</a>
					</p>
				</div>
            {/foreach}
		</div>
	</div>
</body>
</html>