<section class="clearfix">
  <div class="addon-title fix-title mb-20">
    <h3>
        {l s='Categories' d='messages'}
    </h3>
  </div>

  <div class="row propular-carousel">
      {foreach from=$categories item="category"}
          <div>
              <a href="{$link->getCategoryLink($category.id_category, $category.link_rewrite)|escape:'html':'UTF-8'}">

                  <div>
                      {if $category.id_image2}
                          <img class="replace-2x home-categories-images" src="{$link->getCatImageLink($category.link_rewrite, $category.id_image2)|escape:'html':'UTF-8'}"/>
                      {else}
                          <img class="replace-2x home-categories-images" src="{$link->getCatImageLink($category.link_rewrite, $category.id_image)|escape:'html':'UTF-8'}" />
                      {/if}
                  </div>
                  <div class="home-category-name">
                      {$category.name}
                  </div>
              </a>

          </div>

      {/foreach}
  </div>
</section>