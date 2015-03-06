var fullcalendar_settings = {
			header: {
				left: 'prev, next today',
				center: 'title',
				right: 'month,basicWeek,basicDay,'
			},
                        editable: false,
			lazyFetching:true,
                        axisFormat: 'H:mm',
                        titleFormat: {
                                month: 'MMMM yyyy',
                                week: "d[ MMM][ yyyy]{ '&#8212;' d MMM yyyy}",
                                day: 'dddd, d MMM yyyy'
                        },
                        columnFormat: {
                                month: 'ddd',
                                week: 'ddd d/M',
                                day: 'dddd d/M'
                        },
                        timeFormat: {
                                '': 'H(:mm)',
                                agenda: 'H:mm{ - H:mm}'
                        },
                        firstDay: 1,
                        monthNames: ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'],
                                    monthNamesShort: ['Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'],
                                    dayNames: ['Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato'],
                                    dayNamesShort: ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'],
                                    buttonText: {
                                        prev: "<span class='fc-text-arrow btn'>&lsaquo;</span>",
                                        next: "<span class='fc-text-arrow btn'>&rsaquo;</span>",
                                        prevYear: "<span class='fc-text-arrow btn'>&laquo;</span>",
                                        nextYear: "<span class='fc-text-arrow btn'>&raquo;</span>",
                                        today: '<span class="btn">oggi</span>',
                                        month: '<span class="btn">mese</span>',
                                        week: '<span class="btn">settimana</span>',
                                        day: '<span class="btn">giorno</span>'
                        }
		};