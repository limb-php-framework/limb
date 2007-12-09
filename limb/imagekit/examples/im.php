<?php
lmb_require('limb/imagekit/src/lmbImageKit.class.php');
lmbImageKit::create('im')->load('/usr/local/apache/virtual/medkrug/var/input.jpg', 'jpeg')
            ->resize(array('width' => 100, 'height' => 100))
              ->rotate(array('angle' => 45, 'bgcolor' => 'FF0000'))
                ->waterMark(array('x' => 50, 'y' => 50, 'opacity' => 0, 'water_mark' => '/usr/local/apache/virtual/medkrug/var/water_mark.gif'))
                  ->save('/usr/local/apache/virtual/medkrug/var/output.jpg');
echo "Image converted";                  