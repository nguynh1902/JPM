<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/client/guest/ChiTietTinTuc.vue';
$c = file_get_contents($file);

$html = '            <!-- Main Content (HTML) -->
            <div class="article-html-content text-secondary" style="font-size: 1.1rem; line-height: 1.8;" v-html="article.content || defaultContent">
            </div>

            <!-- YouTube Video -->
            <div v-if="article.youtube_url && getYoutubeEmbedUrl(article.youtube_url)" class="mt-5 rounded-4 overflow-hidden shadow-sm">
              <iframe 
                width="100%" 
                height="450" 
                :src="getYoutubeEmbedUrl(article.youtube_url)" 
                title="YouTube video player" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen>
              </iframe>
            </div>

            <!-- Gallery -->
            <div v-if="galleryImages.length > 0" class="mt-5">
              <h4 class="fw-bold text-dark mb-4">Thư viện ảnh phụ</h4>
              <div class="row g-3">
                <div class="col-6 col-md-4" v-for="(img, idx) in galleryImages" :key="idx">
                  <div class="rounded-3 overflow-hidden shadow-sm img-zoom-hover" style="aspect-ratio: 4/3; cursor: pointer;">
                    <img :src="getImageUrl(img)" class="w-100 h-100 object-fit-cover transition-transform" data-bs-toggle="modal" :data-bs-target="\'#galleryModal\' + idx">
                  </div>
                  
                  <!-- Modal cho từng ảnh -->
                  <div class="modal fade" :id="\'galleryModal\' + idx" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                      <div class="modal-content bg-transparent border-0">
                        <div class="modal-header border-0 pb-0">
                          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                          <img :src="getImageUrl(img)" class="img-fluid rounded-3" style="max-height: 80vh;">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>';

$c = preg_replace(
    '/<!-- Main Content \(HTML\) -->\s*<div class="article-html-content text-secondary"[^>]+>\s*<\/div>/',
    $html,
    $c
);

file_put_contents($file, $c);
echo "Patched Client UI\n";
