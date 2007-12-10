<?php
set_include_path(dirname(__FILE__) . '/../../');
lmb_require('limb/imagekit/src/lmbImageKit.class.php');
lmbImageKit::create('gd')->load(dirname(__FILE__).'/images/input.jpg', 'jpeg')
            ->resize(array('width' => 100, 'height' => 100))
              ->rotate(array('angle' => 45, 'bgcolor' => 'FF0000'))
                ->waterMark(array('x' => 50, 'y' => 50, 'opacity' => 0, 'water_mark' => dirname(__FILE__).'/images/water_mark.gif'))
                  ->save(dirname(__FILE__).'/images/output.jpg');
echo "Image converted";                  