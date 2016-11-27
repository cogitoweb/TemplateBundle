/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(function () {
	/*
	 * Cogitoweb: AJAX loader
	 * 
	 * To start the loader add class "loader" to body
	 */
	$(document).on({
		ajaxStart : function() { $('body').addClass   ('loader'); },
		ajaxStop  : function() { $('body').removeClass('loader'); },
		submit    : function() { $('body').addClass   ('loader'); }
	});

	// Hide loader after 3s to prevent user navigation lock if no data is received
	$('form.no-loader').submit(function() {
		setTimeout(function() {
			$('body').removeClass('loader');
		}, 3000);
	});
	/* AJAX loader */

	/*
	 * Cogitoweb: sidebar status
	 * 
	 * Remember last used sidebar status
	 */
	// Restore status
	if ('true' == $.cookie('sidebar-collapsed')) {
		$('.sidebar-toggle').click();
	}

	// Save status
	$('.sidebar-toggle').on('click', function(event) {
		if ('undefined' == typeof($.cookie('sidebar-collapsed')) || 'false' == $.cookie('sidebar-collapsed')) {
			$.cookie('sidebar-collapsed', 'true',  { path: '/' });
		} else {
			$.cookie('sidebar-collapsed', 'false', { path: '/' });
		}
	});
	/* Sidebar status */
});