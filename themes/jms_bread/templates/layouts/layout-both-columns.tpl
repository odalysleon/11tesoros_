{**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<!doctype html>
<html lang="{$language.iso_code}">
  <head>
    {block name='head'}
      {include file='_partials/head.tpl'}
    {/block}
  </head>

  <body id="{$page.page_name}" class="{$page.body_classes|classnames} {if isset($jpb_homeclass) && $jpb_homeclass}{$jpb_homeclass}{/if}{if $jpb_rtl} rtl{/if}">

    {hook h='displayAfterBodyOpeningTag'}
	{if $jpb_mobilemenu}
		<div class="menu-wrap">
			<nav id="off-canvas-menu">
				<div id="off-canvas-title">{l s='MENU'}</div>
					{hook h='displayTopColumn'}
			</nav>				
				<button id="close-button" class="close-button">Close Menu</button>
		</div>
	{/if}
    <div class="main-site">
      {block name='product_activation'}
        {include file='catalog/_partials/product-activation.tpl'}
      {/block}
      <header id="header">
        {block name='header'}
          {include file='_partials/header.tpl'}
        {/block}
      </header>
      {block name='notifications'}
     
      {/block}
		{if $page.page_name != 'index'}
			{block name='breadcrumb'}
			   {include file='_partials/breadcrumb.tpl'}
			{/block}
		{/if}
        <div id="wrapper" {if $page.page_name != 'index'}class="container-fluid"{/if}> 
		{if $page.page_name != 'index'}
		<div class="row">
		{/if}
          {block name="left_column"}
            <div id="left-column" class="page-column col-sm-12 col-md-3 col-lg-3 col-xs-12 page-small-column">
              {if $page.page_name == 'product'}
                {hook h='displayLeftColumnProduct'}
              {else}
                {hook h="displayLeftColumn"}
              {/if}
            </div>
          {/block}

          {block name="content_wrapper"}
            <div id="content-wrapper" class="left-column right-column col-sm-12 col-md-6">
              {block name="content"}
                <p>Hello world! This is HTML5 Boilerplate.</p>
              {/block}
            </div>
          {/block}

          {block name="right_column"}
            <div id="right-column" class="col-xs-12 col-sm-12 col-md-3 col-md-pull-9 page-small-column">
              {if $page.page_name == 'product'}
                {hook h='displayRightColumnProduct'}
              {else}
                {hook h="displayRightColumn"}
              {/if}
            </div>
          {/block}
			 {if $page.page_name == 'product'}
				<div class="related-product col-lg-12">
					 {hook h='displayOrderConfirmation2'}
				</div>
			{/if}
        {if $page.page_name != 'index'}
		</div>
		{/if}
      </div>

      <footer id="footer">
        {block name="footer"}
          {include file="_partials/footer.tpl"}
        {/block}
      </footer>

    </div>

    {block name='javascript_bottom'}
      {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
    {/block}

    {hook h='displayBeforeBodyClosingTag'}
	<!-- Go to www.addthis.com/dashboard to customize your tools -->
		<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-585b980bd32999fe"></script>
  </body>

</html>
