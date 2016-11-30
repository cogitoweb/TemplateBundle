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

if(Admin) {

    Admin.add_filters = function(subject) {
        Admin.log('[core|add_filters] configure filters on', subject);
        jQuery('a.sonata-toggle-filter', subject).on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (jQuery(e.target).attr('sonata-filter') == 'false') {
                return;
            }

            Admin.log('[core|add_filters] handle filter container: ', jQuery(e.target).attr('filter-container'))

            var filters_container = jQuery('#' + jQuery(e.currentTarget).attr('filter-container'));

            if (jQuery('div[sonata-filter="true"]:visible', filters_container).length == 0) {
                jQuery(filters_container).slideDown();
            }

            var targetSelector = jQuery(e.currentTarget).attr('filter-target'),
                target = jQuery('div[id="' + targetSelector + '"]', filters_container),
                filterToggler = jQuery('i', '.sonata-toggle-filter[filter-target="' + targetSelector + '"]')
            ;

            if (jQuery(target).is(":visible")) {
                filterToggler
                    .removeClass('fa-check-square-o')
                    .addClass('fa-square-o')
                ;

                target.hide();

            } else {
                filterToggler
                    .removeClass('fa-square-o')
                    .addClass('fa-check-square-o')
                ;

                target.show();
            }

            /* 2z -> inibisco chiusura sezione filtri */
            /*
            if (jQuery('div[sonata-filter="true"]:visible', filters_container).length > 0) {
                jQuery(filters_container).slideDown();
            } else {
                jQuery(filters_container).slideUp();
            }
            */
        });

        jQuery('.sonata-filter-form', subject).on('submit', function () {
            jQuery(this).find('[sonata-filter="true"]:hidden :input').val('');
        });

        /* Advanced filters */
        if (jQuery('.advanced-filter :input:visible', subject).filter(function () { return jQuery(this).val() }).length === 0) {
            jQuery('.advanced-filter').hide();
        };

        jQuery('[data-toggle="advanced-filter"]', subject).click(function() {
            jQuery('.advanced-filter').toggle();
        });
    };

}