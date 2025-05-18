<div class="bg-black/90 fixed inset-0 flex flex-col gap-3 p-3 select-none" x-show="gallery.open" x-on:wheel.prevent="navGallery" x-on:touchmove.prevent="navGallery" x-transition x-cloak>
   <div class="grow flex items-center justify-center">
      <img class="max-h-[calc(100dvh-9rem)] w-auto" x-bind:src="gallery.images[gallery.current]" alt="" />
   </div>
   <div class="absolute bottom-32 right-3 flex gap-3">
      <button class="btn-alt aspect-square" x-on:click="navGallery(false)" x-on:keyup.left.window="navGallery(false)"
         x-show="gallery.images.length>1">
         <i class="fa-solid fa-chevron-left"></i>
      </button>
      <button class="btn-alt aspect-square text-xl" title="<?php esc_html_e('Fechar', 'dbands') ?>" x-on:click="gallery.open = false" x-on:keyup.escape.window="gallery.open = false">
         <i class="fas fa-times"></i>
      </button>
      <button class="btn-alt aspect-square" x-on:click="navGallery()"
         x-on:keyup.right.window="navGallery()" x-show="gallery.images.length>1">
         <i class="fa-solid fa-chevron-right"></i>
      </button>
   </div>

   <div class="flex justify-center w-full">
      <ul id="galleryImages" class="flex gap-3 overflow-x-auto" x-show="gallery.images.length>1">
         <template x-for="(image, idx) in gallery.images">
            <li class="galleryImage border-2 border-transparent shrink-0" x-bind:class="{'border-zinc-200':idx===gallery.current}">
               <button x-on:click="gallery.current = idx">
                  <img class="size-20 object-cover" x-bind:src="image" alt="" />
               </button>
            </li>
         </template>
      </ul>
   </div>
</div>
