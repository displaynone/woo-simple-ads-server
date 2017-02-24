jQuery(document).ready(function() {
  jQuery('#campaign_products_list_add').click(function() {
    var id = jQuery('#campaign_products_list').val();
    var $product = jQuery('.campaign_products div#product-'+id);
    if ($product.length == 0 && id) {
      $selected = jQuery('#campaign_products_list option:selected');
      var banners = $selected.data('sizes');
      var count = 0;
      var help = '<div class="banners_help">';
      for(banner in banners) {
        count += banners[banner];
        help += '<p>'+banners[banner]+' '+products.sizes[banner]+'</p>';
      }
      if (count == 0) help += '<p>No banners</p>';
      help += '</div>';
      help = ' <div class="product_tooltip">'+count+' banners '+help+'</div>';
      jQuery('.campaign_products').append('<div class="product" id="product-'+id+'" data-product-id="'+id+'"><a href="'+$selected.data('link')+'" target="_blank"><img src="'+$selected.data('thumbnail')+'" />'+$selected.text()+'</a>'+help+' <button class="button delete-button">Delete</button></div>');
    }
    set_ids();
    return false;
  });

  jQuery('.campaign_products').on('click', '.product button', function() {
    jQuery(this).parent().remove();
    set_ids();
    return false;
  });

  function set_ids() {
    var ids = [];
    jQuery('.campaign_products .product').each(function() {ids.push(jQuery(this).data('product-id'));});
    jQuery('#campaign_products').val(ids.join(','));
  }
});
