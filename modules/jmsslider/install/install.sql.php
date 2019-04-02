<?php
/**
* 2007-2017 PrestaShop
*
* Slider Layer module for prestashop
*
*  @author    Joommasters <joommasters@gmail.com>
*  @copyright 2007-2017 Joommasters
*  @license   license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*  @Website: http://www.joommasters.com
*/

$query = "DROP TABLE IF EXISTS `_DB_PREFIX_jms_slides`;
CREATE TABLE IF NOT EXISTS `_DB_PREFIX_jms_slides` (
  `id_slide` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `class_suffix` varchar(100) NOT NULL,
  `bg_type` int(10) NOT NULL DEFAULT '1',
  `bg_image` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `bg_color` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '#FFF',
  `slide_link` varchar(100) NOT NULL,
  `order` int(10) NOT NULL,
  `status` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_slide`)
) ENGINE=_MYSQL_ENGINE_  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

INSERT INTO `_DB_PREFIX_jms_slides` (`id_slide`, `title`, `class_suffix`, `bg_type`, `bg_image`, `bg_color`, `slide_link`, `order`, `status`) VALUES
(17, 'Slide 1 - Homepage 1', '', 1, 'f4dff37f8f3bbcf87af0494b60972a04.jpg', '#ffffff', '#', 0, 1),
(18, 'Slide 2 - Homepage 1', '', 1, 'ff446778f6cce8e64eb1be512ce11a1a.jpg', '#ffffff', '#', 0, 1),
(19, 'Slide 3 - Homepage 1', '', 1, 'e5f732a963b3d2228311823935bdf0ce.jpg', '#ffffff', '#', 0, 1),
(20, 'Slide 1 - Homepage 2', '', 1, 'ff446778f6cce8e64eb1be512ce11a1a.jpg', '#ffffff', '#', 0, 1),
(21, 'Slide 2 - Homepage 2', '', 1, 'f4dff37f8f3bbcf87af0494b60972a04.jpg', '#ffffff', '#', 0, 1),
(22, 'Slide 3 - Homepage 2', '', 1, 'e5f732a963b3d2228311823935bdf0ce.jpg', '#ffffff', '#', 0, 1),
(23, 'Slide 1 - Homepage 3', '', 1, 'e5f732a963b3d2228311823935bdf0ce.jpg', '#ffffff', '#', 0, 1),
(24, 'Slide 2 - Homepage 3', '', 1, 'f4dff37f8f3bbcf87af0494b60972a04.jpg', '#ffffff', '#', 0, 1),
(25, 'Slide 3 - Homepage 3', '', 1, 'ff446778f6cce8e64eb1be512ce11a1a.jpg', '#ffffff', '#', 0, 1);

DROP TABLE IF EXISTS `_DB_PREFIX_jms_slides_lang`;
CREATE TABLE IF NOT EXISTS `_DB_PREFIX_jms_slides_lang` (
  `id_slide` int(10) NOT NULL AUTO_INCREMENT,
  `id_lang` int(10) NOT NULL,
  PRIMARY KEY (`id_slide`,`id_lang`)
) ENGINE=_MYSQL_ENGINE_  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

INSERT INTO `_DB_PREFIX_jms_slides_lang` (`id_slide`, `id_lang`) VALUES
(17, 0),
(18, 0),
(19, 0),
(20, 0),
(21, 0),
(22, 0),
(23, 0),
(24, 0),
(25, 0);

DROP TABLE IF EXISTS `_DB_PREFIX_jms_slides_layers`;
CREATE TABLE IF NOT EXISTS `_DB_PREFIX_jms_slides_layers` (
  `id_layer` int(10) NOT NULL AUTO_INCREMENT,
  `id_slide` int(10) NOT NULL,
  `data_title` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `data_class_suffix` varchar(50) NOT NULL,
  `data_fixed` int(10) NOT NULL DEFAULT '0',
  `data_delay` int(10) NOT NULL DEFAULT '1000',
  `data_time` int(10) NOT NULL DEFAULT '1000',
  `data_x` int(10) NOT NULL DEFAULT '0',
  `data_y` int(10) NOT NULL DEFAULT '0',
  `data_in` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'left',
  `data_out` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'right',
  `data_ease_in` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'linear',
  `data_ease_out` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'linear',
  `data_step` int(10) NOT NULL DEFAULT '0',
  `data_special` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'cycle',
  `data_type` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `data_image` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `data_html` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `data_video` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `data_video_controls` int(10) NOT NULL DEFAULT '1',
  `data_video_muted` int(10) NOT NULL DEFAULT '0',
  `data_video_autoplay` int(10) NOT NULL DEFAULT '1',
  `data_video_loop` int(10) NOT NULL DEFAULT '1',
  `data_video_bg` int(10) NOT NULL DEFAULT '0',
  `data_font_size` int(10) NOT NULL DEFAULT '14',
  `data_line_height` int(10) NOT NULL,
  `data_style` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'normal',
  `data_color` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '#FFFFFF',
  `data_width` int(10) NOT NULL,
  `data_height` int(10) NOT NULL,
  `data_order` int(10) NOT NULL,
  `data_status` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_layer`,`id_slide`)
) ENGINE=_MYSQL_ENGINE_  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

INSERT INTO `_DB_PREFIX_jms_slides_layers` (`id_layer`, `id_slide`, `data_title`, `data_class_suffix`, `data_fixed`, `data_delay`, `data_time`, `data_x`, `data_y`, `data_in`, `data_out`, `data_ease_in`, `data_ease_out`, `data_step`, `data_special`, `data_type`, `data_image`, `data_html`, `data_video`, `data_video_controls`, `data_video_muted`, `data_video_autoplay`, `data_video_loop`, `data_video_bg`, `data_font_size`, `data_line_height`, `data_style`, `data_color`, `data_width`, `data_height`, `data_order`, `data_status`) VALUES
(1, 17, 'Large Text', 'home1-large-text home1-slide1', 0, 1000, 2000, 539, 464, 'fade', 'fade', 'linear', 'linear', 0, '', 'text', '', '<div class=\"large-text\">The Best Of Cake</div>', '', 0, 0, 0, 0, 0, 96, 35, 'normal', '#ffffff', 200, 50, 0, 1),
(2, 17, 'Medium Text', 'home1-medium-text home1-slide1', 0, 2000, 3000, 750, 555, 'bottom', 'top', 'easeInBounce', 'easeInOutExpo', 0, '', 'text', '', '<div class=\"medium-text\">Sale Off 40% Now</div>', '', 0, 0, 0, 0, 0, 46, 35, 'normal', '#ffffff', 200, 50, 0, 1),
(3, 17, 'Button Shop Now', 'home1-button home1-slide1', 0, 3000, 3500, 880, 652, 'bottom', 'top', 'easeInQuint', 'linear', 0, '', 'text', '', '<a class=\"btn-shop-now btn-effect\">Shop Now!</a>', '', 0, 0, 0, 0, 0, 12, 45, 'normal', '#ffffff', 200, 50, 0, 1),
(4, 18, 'Large Text', 'home1-slide2 home1-large-text', 0, 1000, 2000, 110, 424, 'topLeft', 'bottomLeft', 'easeInQuint', 'easeInQuint', 0, '', 'text', '', '<div class=\"large-text\">This Week`S Offers</div>', '', 0, 0, 0, 0, 0, 96, 35, 'normal', '#ffffff', 200, 50, 0, 1),
(5, 18, 'Medium Text', 'home1-slide2 home1-medium-text', 0, 2000, 3000, 110, 515, 'bottomRight', 'topRight', 'easeInQuart', 'easeOutQuart', 0, '', 'text', '', '<div class=\"medium-text\">Save Up To 20%</div>', '', 0, 0, 0, 0, 0, 46, 35, 'normal', '#ffffff', 200, 50, 0, 1),
(6, 18, 'Button Shop Now', 'home1-slide2 home1-button', 0, 3000, 3500, 110, 612, 'fade', 'fade', 'easeInQuint', 'linear', 0, '', 'text', '', '<a class=\"btn-shop-now btn-effect\">Shop Now!</a>', '', 0, 0, 0, 0, 0, 12, 45, 'normal', '#ffffff', 200, 50, 0, 1),
(7, 19, 'Large Text', 'home1-large-text home1-slide3', 0, 1000, 2000, 397, 464, 'right', 'left', 'easeInCubic', 'easeOutCubic', 0, '', 'text', '', '<div class=\"large-text\">Get Up To 50% Discounts</div>', '', 0, 0, 0, 0, 0, 96, 35, 'normal', '#ffffff', 200, 50, 0, 1),
(8, 19, 'Medium Text', 'home1-medium-text home1-slide3', 0, 2000, 3000, 649, 555, 'left', 'right', 'easeInSine', 'easeInOutSine', 0, '', 'text', '', '<div class=\"medium-text\">BAKE &amp; CAKE OFFERS YOU ON THIS SEASON. GRAB BESTEVER DISCOUNTS!</div>', '', 0, 0, 0, 0, 0, 18, 35, 'normal', '#ffffff', 200, 50, 0, 1),
(9, 19, 'Button Shop Now', 'home1-button home1-slide3', 0, 3000, 3500, 880, 652, 'bottom', 'top', 'easeInQuint', 'linear', 0, '', 'text', '', '<a class=\"btn-shop-now btn-effect\">Shop Now!</a>', '', 0, 0, 0, 0, 0, 12, 45, 'normal', '#ffffff', 200, 50, 0, 1),
(10, 20, 'Large Text', 'home2-large-text home2-slide1', 0, 1000, 2000, 105, 370, 'fade', 'fade', 'linear', 'linear', 0, '', 'text', '', '<div class=\"large-text\">This Week`S Offers</div>', '', 0, 0, 0, 0, 0, 96, 35, 'normal', '#ffffff', 200, 50, 0, 1),
(11, 20, 'Medium Text', 'home2-medium-text home2-slide1', 0, 2000, 3000, 105, 460, 'bottom', 'top', 'easeInBounce', 'easeInOutExpo', 0, '', 'text', '', '<div class=\"medium-text\">Save Up To 20%</div>', '', 0, 0, 0, 0, 0, 46, 35, 'normal', '#ffffff', 200, 50, 0, 1),
(12, 20, 'Button Shop Now', 'home2-button home2-slide1', 0, 3000, 3500, 105, 545, 'bottom', 'top', 'easeInQuint', 'linear', 0, '', 'text', '', '<a class=\"btn-shop-now btn-effect\">Shop Now!</a>', '', 0, 0, 0, 0, 0, 12, 45, 'normal', '#ffffff', 200, 50, 0, 1),
(13, 21, 'Large Text', 'home2-large-text home2-slide2', 0, 1000, 2000, 579, 370, 'topRight', 'bottomLeft', 'easeInBack', 'easeOutBack', 0, '', 'text', '', '<div class=\"large-text\">The Best Of Cake</div>', '', 0, 0, 0, 0, 0, 96, 35, 'normal', '#ffffff', 200, 50, 0, 1),
(14, 21, 'Medium Text', 'home2-medium-text home2-slide2', 0, 2000, 3000, 770, 460, 'bottomLeft', 'topRight', 'easeInCirc', 'easeInOutCirc', 0, '', 'text', '', '<div class=\"medium-text\">Sale Off 40% Now</div>', '', 0, 0, 0, 0, 0, 46, 35, 'normal', '#ffffff', 200, 50, 0, 1),
(15, 21, 'Button Shop Now', 'home2-button home2-slide2', 0, 3000, 3500, 880, 545, 'bottom', 'top', 'easeInOutBounce', 'easeInOutBounce', 0, '', 'text', '', '<a class=\"btn-shop-now btn-effect\">Shop Now!</a>', '', 0, 0, 0, 0, 0, 12, 45, 'normal', '#ffffff', 200, 50, 0, 1),
(16, 22, 'Large Text', 'home2-large-text home2-slide3', 0, 1000, 2000, 397, 370, 'fade', 'fade', 'linear', 'linear', 0, '', 'text', '', '<div class=\"large-text\">Get Up To 50% Discounts</div>', '', 0, 0, 0, 0, 0, 96, 35, 'normal', '#ffffff', 200, 50, 0, 1),
(17, 22, 'Medium Text', 'home2-medium-text home2-slide3', 0, 2000, 3000, 649, 460, 'fade', 'fade', 'easeInBounce', 'easeInOutExpo', 0, '', 'text', '', '<div class=\"medium-text\">BAKE &amp; CAKE OFFERS YOU ON THIS SEASON. GRAB BESTEVER DISCOUNTS!</div>', '', 0, 0, 0, 0, 0, 18, 35, 'normal', '#ffffff', 200, 50, 0, 1),
(18, 22, 'Button Shop Now', 'home2-button home2-slide3', 0, 3000, 3500, 880, 545, 'fade', 'fade', 'easeInQuint', 'linear', 0, '', 'text', '', '<a class=\"btn-shop-now btn-effect\">Shop Now!</a>', '', 0, 0, 0, 0, 0, 12, 45, 'normal', '#ffffff', 200, 50, 0, 1),
(19, 23, 'Large Text', 'home3-large-text home3-slide1', 0, 1000, 2000, 350, 400, 'fade', 'fade', 'linear', 'linear', 0, '', 'text', '', '<div class=\"large-text\">Get Up To 50% Discounts</div>', '', 0, 0, 0, 0, 0, 96, 35, 'normal', '#ffffff', 200, 50, 0, 1),
(20, 23, 'Medium Text', 'home3-medium-text home3-slide1', 0, 2000, 3000, 590, 480, 'bottom', 'top', 'easeInBounce', 'easeInOutExpo', 0, '', 'text', '', '<div class=\"medium-text\">BAKE &amp; CAKE OFFERS YOU ON THIS SEASON. GRAB BESTEVER DISCOUNTS!</div>', '', 0, 0, 0, 0, 0, 18, 35, 'normal', '#ffffff', 200, 50, 0, 1),
(21, 23, 'Button Shop Now', 'home3-button home3-slide1', 0, 3000, 3500, 880, 580, 'bottom', 'top', 'easeInQuint', 'linear', 0, '', 'text', '', '<a class=\"btn-shop-now btn-effect\">Shop Now!</a>', '', 0, 0, 0, 0, 0, 12, 45, 'normal', '#ffffff', 200, 50, 0, 1),
(22, 24, 'Large Text', 'home3-large-text home3-slide2', 0, 1000, 2000, 579, 400, 'fade', 'fade', 'linear', 'linear', 0, '', 'text', '', '<div class=\"large-text\">The Best Of Cake</div>', '', 0, 0, 0, 0, 0, 96, 35, 'normal', '#ffffff', 200, 50, 0, 1),
(23, 24, 'Medium Text', 'home3-medium-text home3-slide2', 0, 2000, 3000, 770, 480, 'bottom', 'top', 'easeInBounce', 'easeInOutExpo', 0, '', 'text', '', '<div class=\"medium-text\">Sale Off 40% Now</div>', '', 0, 0, 0, 0, 0, 46, 35, 'normal', '#ffffff', 200, 50, 0, 1),
(24, 24, 'Button Shop Now', 'home3-button home3-slide2', 0, 3000, 3500, 880, 580, 'bottom', 'top', 'easeInQuint', 'linear', 0, '', 'text', '', '<a class=\"btn-shop-now btn-effect\">Shop Now!</a>', '', 0, 0, 0, 0, 0, 12, 45, 'normal', '#ffffff', 200, 50, 0, 1),
(25, 25, 'Large Text', 'home3-large-text home3-slide3', 0, 1000, 2000, 105, 400, 'fade', 'fade', 'linear', 'linear', 0, '', 'text', '', '<div class=\"large-text\">This Week`S Offers</div>', '', 0, 0, 0, 0, 0, 96, 35, 'normal', '#ffffff', 200, 50, 0, 1),
(26, 25, 'Medium Text', 'home3-medium-text home3-slide3', 0, 2000, 3000, 105, 480, 'bottom', 'top', 'easeInBounce', 'easeInOutExpo', 0, '', 'text', '', '<div class=\"medium-text\">Save Up To 20%</div>', '', 0, 0, 0, 0, 0, 46, 35, 'normal', '#ffffff', 200, 50, 0, 1),
(27, 25, 'Button Shop Now', 'home3-button home3-slide3', 0, 3000, 3500, 105, 580, 'bottom', 'top', 'easeInQuint', 'linear', 0, '', 'text', '', '<a class=\"btn-shop-now btn-effect\">Shop Now!</a>', '', 0, 0, 0, 0, 0, 12, 45, 'normal', '#ffffff', 200, 50, 0, 1);

DROP TABLE IF EXISTS `_DB_PREFIX_jms_slides_shop`;
CREATE TABLE IF NOT EXISTS `_DB_PREFIX_jms_slides_shop` (
  `id_slide` int(10) NOT NULL AUTO_INCREMENT,
  `id_shop` int(10) NOT NULL,
  PRIMARY KEY (`id_slide`,`id_shop`)
) ENGINE=_MYSQL_ENGINE_  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

INSERT INTO `_DB_PREFIX_jms_slides_shop` (`id_slide`, `id_shop`) VALUES
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1);
";
