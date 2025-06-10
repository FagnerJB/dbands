jQuery(document).ready(function () {

   'use strict';

   var metaImageFrame;

   jQuery('body').click(function (e) {

      var btn = e.target;

      if (!btn || !jQuery(btn).attr('data-media-uploader-target')) return;

      var field = jQuery(btn).data('media-uploader-target');
      var thumb = jQuery(btn).data('media-thumbnail-target');

      e.preventDefault();

      metaImageFrame = wp.media.frames.metaImageFrame = wp.media({
         title: meta_image.title,
         button: { text: meta_image.button },
      });

      metaImageFrame.on('select', function () {

         var media_attachment = metaImageFrame.state().get('selection').first().toJSON();
         jQuery(field).val(media_attachment.id);

         var img = new Image()
         img.setAttribute('src', media_attachment.sizes.medium.url)

         var content = jQuery(thumb)
         content.text('')
         content[0].appendChild(img)

      });

      metaImageFrame.open();

   });

});
