jQuery(document).ready(function () {
  var frame, $grid;
  jQuery('#woocommerce-product-banners .add_product_banners a').click(function() {
    if (frame) {
      frame.open();
      return;
    }

    // Create a new media frame
    frame = wp.media({
      title: 'Add ads banner',
      button: {
        text: 'Use this media'
      },
      multiple: false  // Set to true to allow multiple files to be selected
    });


    // When an image is selected in the media frame...
    frame.on( 'select', function() {

      // Get media attachment details from the frame state
      var attachment = frame.state().get('selection').first().toJSON();
console.table(attachment)      ;
      var size = typeof banners.sizes[attachment.width+'x'+attachment.height] != 'undefined'? attachment.width+'x'+attachment.height:'x';
      var $container = jQuery('#product_banners_container .product_banners .product_banners_group_'+size);
      if ($container.length == 0) {
        jQuery('#product_banners_container .product_banners').append('<h3>'+banners.sizes[size]+'</h3><div class="product_banners_group_'+size+'"></div>');
        $container = jQuery('#product_banners_container .product_banners .product_banners_group_'+size);
      }
console.log($container);
      // Send the attachment URL to our custom image input field.
      $container.append( '<div class="image"><img src="'+attachment.url+'" alt="" style="max-width:100%;"/></div>' );

      // Send the attachment id to our hidden input
      var $input = jQuery('#product_image_banner');
      var ids = $input.val().split(',');
      ids.push(attachment.id);
      $input.val( ids.join(',').replace(/^,/, '') );
      $grid.masonry('layout');

    });

    // Finally, open the modal on click
    frame.open();

    return false;
  });

  jQuery('.product_banners .delete').click(function() {
    var $this = jQuery(this);
    $this.parents('.image:first').remove();
    var ids = [];
    jQuery('#product_banners_container .image').each(function() {ids.push(jQuery(this).data('attachment_id'));});
    jQuery('#product_image_banner').val(ids.join(','));
    return false;
  });

});
