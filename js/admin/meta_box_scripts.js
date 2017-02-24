jQuery(document).ready(function() {
  jQuery('#woocommerce-campaign-scripts textarea').focus(function() {
    jQuery(this).select();
    jQuery(this).next().addClass('focus');
    jQuery('textarea.copied').removeClass('copied');
  }).blur(function() {
    //jQuery(this).next().removeClass('focus');
  }).after('<span class="script_action"><a href="#" class="copy_script">copiar</a></span>');

  jQuery('.script_action .copy_script').click(function() {
    var $textarea = jQuery(this).parent().prev();
    $textarea.select();
    try {
      // execute the copy command  on selected the text in copy area
      var copyStatus = document.execCommand('copy');
      if (copyStatus) {
        $textarea.addClass('copied');
      }
      var msg = copyStatus ? 'copied' : 'failed';
      console.log(msg);  // console the copy status
    } catch(error) {
      console.log('Oops!, unable to copy');
    }
    return false;
  });
});
