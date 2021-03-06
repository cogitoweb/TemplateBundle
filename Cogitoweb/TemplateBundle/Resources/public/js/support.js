// supporto all'interfaccia
function loadFrameMap(lat, long, name){
  var url = "https://maps.google.com/maps?q="+lat+","+long+"&z=12&output=embed";
  openLayer(url, 'Map - '+name);  
}
  
function openLayer(url, title) {
   return openLayerParam(url, title, 900, 600);
 }

function openLayerParam(url, title, width, height) {
  return $('<iframe class="sidebar_actionlayer" frameborder="0" src="' + url + '" />').dialog({
     title: title,
     autoOpen: true,
     width: width,
     height: height,
     modal: true,
     resizable: false,
     autoResize: false }).width(width-14).height(height);
}


function adjustDeleteRows() {
    //conferma ed eliminazione della riga con click su checkbox delete
    $('.sonata-ba-form .delete-check input[type=checkbox]:checked').closest('tr').hide();
    $('.sonata-ba-form .delete-check .fake-check').unbind('click').click(function() {
        var _tr = $(this).closest('tr');
        var _hidden = _tr.find('input[type=hidden][id$="_id"]');
        if(_hidden.length > 0) {
            if(confirm("Eliminare la riga?"))
            {
                if(!_hidden.val())
                {
                    _tr.remove();
                }
                else {
                    // uncheck eventuali traduzioni
                    _tr.find('input[type=checkbox][name$="[translations][enabled_locales][]"]').prop('checked', false);
                
                    _tr.hide();
                    _tr.find('input[type=checkbox][id$="_delete"]').prop('checked', true);
                }
				
				jQuery(document).trigger('sonata-collection-item-deleted');
            }
        }
        return false;
    });
}

function adjustSelect2Submit() {
    
    $('input[type=submit]').off('click').on('click', function() {
        
        $.each($('.select2-container'), function(i, el){
            
            if($(el).next('select').is(':invalid'))
            {
                $(el).addClass('invalid-container');
            }
        });
	
    }); 
}

function resizeActionButtons() {
    if($('.sonata-ba-list td.action-column .btn-group .btn').length > 0) {
        
        var test = $($('.sonata-ba-list td.action-column .btn-group .btn')[0]);
        if(test.closest('td').height() > (test.height()*2))
        {
            $('.sonata-ba-list td.action-column .btn-group').addClass('btn-group-vertical');
        }
        else {
            $('.sonata-ba-list td.action-column .btn-group').removeClass('btn-group-vertical');
        }
    }
    
}

function disableTranslation(locale, el) {
    
    // limito la selezione al contesto dell'elemento
    var context_div = $($(el).closest('.a2lix_translationsLocales_selector').parent());
    
    context_div.find('.a2lix_translationsLocales li[rel=' + locale + '], .a2lix_translationsFields div[rel=' + locale + ']').toggleClass('removed');
    
    if($(el).prop('checked'))
    {
        context_div.find('.a2lix_translationsLocales li[rel=' + locale + '] a').trigger('click');
    }
    
    if(context_div.find('.a2lix_translationsFields div.active.removed').length > 0) {
        context_div.find('.a2lix_translationsLocales li a').first().trigger('click');
    }
}

$(document).ready(function() {
        
        //reopen right tab if error
        (function(){
            var err_div = $('.control-group.error').first();
            if(err_div)
            {
                // compatibile con gli errori standard e gli errori di translations
                // cerca in tutta la gerarchia
                var tabs_to_activate = err_div.parents('.tab-pane');

                for(var a=0; a<tabs_to_activate.length; a++)
                {
                    var current_tab = $(tabs_to_activate[a]);

                    var id_to_activate = current_tab.attr('id');
                    var a_to_activate = current_tab.closest('.tabbable').find('.nav-tabs a[href="#' + id_to_activate + '"]');
                    var li_to_activate = a_to_activate.parent('li');

                    if(a_to_activate.length > 0 && li_to_activate.length > 0)
                    {
                        // attivo il tab se non già attivo (solo sul primo)
                        if(!li_to_activate.hasClass('active')) a_to_activate.click();

                        // mostro icona se nascosta presente 
                        a_to_activate.find('.has-errors.hide').removeClass('hide'); 
                    }
                }   
            }
        })();
        
        //on of delle tabelle sonata-ba-view-title
        $('.collapsed-table .sonata-ba-view-title td').click(function(){
            $(this).closest('table.table').toggleClass('collapsed-table collapsed-table-open');
        });
        
        // nascondo le righe eliminate
        adjustDeleteRows();
        
        // Add class 'had-value-on-load' to inputs/selects with values.
        $(".sonata-filter-form input").add(".sonata-filter-form select").each(function(){ if($(this).val()) { $(this).addClass('had-value-on-load');}});
        // REMOVE ALL EMPTY INPUT FROM FILTER FORM (except inputs, which has class 'had-value-on-load')
        $(".sonata-filter-form").submit(function() {
            $(".sonata-filter-form input").add(".sonata-filter-form select").each(function(){ if(!$(this).val() && !$(this).hasClass('had-value-on-load')) { $(this).remove();}});
        });
        
        // misuro tabella
        resizeActionButtons();
        
        // rimuovo filtri impliciti
        $('.sonata-filter-form .filter_container .implicito').closest('div.form-group').hide();
        
        // raggruppo esteticamente i multipli
        $('.sonata-filter-form .filter_container .clearfix .form-group:eq(1)').closest('div.clearfix').addClass('filter-date-range'); 
        
        // a tutti i  submit attacco evento che 
        adjustSelect2Submit();
        
        // tooltip
        $('.tooltip-cgt').tooltip();
        
		// Rimuovo la schermata di loading dopo 1,5s
		$('form.noloading').submit(function(){
			setTimeout(function() {
				$('body').removeClass('loading');
			}, 1500);
		});
});

jQuery(document).on('sonata.add_element', function(e) {
    // nascondo le righe eliminate
    adjustDeleteRows();
    
    adjustSelect2Submit();
	
	jQuery(document).trigger('sonata-collection-item-added');
});


$(window).resize(function(){
    resizeActionButtons();
});

jQuery(document).on({
    ajaxStart: function() { $("body").addClass("loading"); },
    ajaxStop: function() { $("body").removeClass("loading"); },
    
    submit: function() { $("body").addClass("loading"); }    
});
