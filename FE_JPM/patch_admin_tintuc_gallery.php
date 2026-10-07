<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/NoiDung/TinTuc.vue';
$c = file_get_contents($file);

// Replace the payload generation to include gallery files
$search = "payload.append('thumbnail', this.thumbnailFile);
        }";

$replace = "payload.append('thumbnail', this.thumbnailFile);
        }
        
        if (this.formData.gallery && this.formData.gallery.length > 0) {
            this.formData.gallery.forEach(g => {
                payload.append('existing_gallery[]', g);
            });
        }
        if (this.galleryFiles && this.galleryFiles.length > 0) {
            this.galleryFiles.forEach(f => {
                payload.append('gallery[]', f);
            });
        }";

$c = str_replace($search, $replace, $c);

file_put_contents($file, $c);
echo "Patched admin gallery upload\n";
