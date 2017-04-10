<?php
/*
Plugin Name: Custom WooCommerce Plugin by Karsky
Plugin URI:  https://developer.wordpress.org/woocommerce/
Description: Basic WordPress Plugin for WooCommerce
Version:     20170330
Author:      Evgeny
Author URI:  https://profiles.wordpress.org/karsky
Text Domain: wporg
Domain Path: /languages
License:     GPL2
 
Custom WooCommerce Plugin by Karsky is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Custom WooCommerce Plugin by Karsky is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Custom WooCommerce Plugin by Karsky. If not, see {License URI}.
*/
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

/**
* Проверяем активирован ли WooCommerce
* see https://docs.woocommerce.com/document/query-whether-woocommerce-is-activated/
* see https://docs.woocommerce.com/document/create-a-plugin/
**/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	// Объявляем поддержку WooCommerce нашей темы
	add_action( 'after_setup_theme', function() {
	    	add_theme_support( 'woocommerce' );
		}
	);

	/*
	*
	* Меняем верстку (обертку цикла с продуктами) вывода товаров в цикле
	  https://codex.wordpress.org/Pluggable_Functions
	*
	*/
	function woocommerce_product_loop_start(){
		echo '<div class="row">';
	}

	function woocommerce_product_loop_end(){
		echo '</div>';
	}

	/*
	*
	* Удаление экшенов на хуке-событии фронтэнда wp_head
	  https://codex.wordpress.org/Function_Reference/remove_action
	*
	*/
	add_action( 'wp_head', 'krs_remove_woo_action' );
	function krs_remove_woo_action(){
		// Удаляем счетчик товаров
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );

		// Удаляем сортировку в виде выпадающего списка
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

		// Удаляем цену на странице одиночного товара
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );

		// Удаляем Добавить в корзину на странице одиночного товара
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

		// Удаляем цену у товара в цикле
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

		// Удаление хлебных крошек вукомерс
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );

		// *1 удаление тайтл на странице одиночного товара
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
	}
	
	// *1 вызов функции вызывающей тайтл над картинкой на странице одиночного товара
	add_action( 'woocommerce_before_single_product_summary', 'woocommerce_template_single_title', 5 );

	// *2 вызываем функцию ВП вывода контента
	add_action( 'woocommerce_after_single_product_summary', 'the_content', 15 );

	// вызов анонимной функции, выводящей перед контентом вукомерс вызов формы плагина CF-7
	add_action( 'woocommerce_after_single_product_summary', function(){
		if ( shortcode_exists( 'contact-form-7' ) ){
			echo '<div class="clearfix"></div><hr /><h2 class="mb-2 h4">Заказать комплект</h2>' . do_shortcode( '[contact-form-7 id="587" title="Быстрая заявка"]' ) . '<hr class="mt-3 mb-4" />';
		}
		
	}, 5 );

	/*
	*
	* ФИЛЬТРЫ ВУКОМЕРС
	*
	*/
	// убираем заголовок в табах, возвращая пустую строку
	add_filter( 'woocommerce_product_description_heading', '__return_empty_string', 999 );

	// *2 удаляем вкладку табов, возвращая пустой массив
	add_filter( 'woocommerce_product_tabs', '__return_empty_array', 999 );	
}
?>