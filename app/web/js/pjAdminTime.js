var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var datepicker = ($.fn.datepicker !== undefined),
			validate = ($.fn.validate !== undefined),
			datagrid = ($.fn.datagrid !== undefined),
			dialog = ($.fn.dialog !== undefined),
			spinner = ($.fn.spinner !== undefined),
			qs = "",
			$dialogDayPrice = $("#dialogDayPrice"),
			$frmTimeCustom = $("#frmTimeCustom"),
			$frmPriceDefault = $('#frmPriceDefault');
		
		
		if ($frmPriceDefault.length > 0 && validate) {
			$frmPriceDefault.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				submitHandler: function(form){
					if($('#after_midnight').length > 0 && $('.tsIsDayOff').is(':checked') == false)
					{
						$('.tsMidnightMessage').css('color', 'red').show();
					}else{
						form.submit();
					}
					return false;
				}
			});
		}
		
		if ($frmTimeCustom.length > 0 && validate) {
			$frmTimeCustom.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				submitHandler: function(form){
					if($('#after_midnight').length > 0 && $('input[name="is_dayoff"]').is(':checked') == false)
					{
						$('.tsMidnightMessage').css('color', 'red').show();
					}else{
						form.submit();
					}
					return false;
				}
			});
		}
		
		if (spinner) {
			$("input.spin").spinner({
				min: 1,
				step: 1,
				max: 65535
			});
			$('.field-int').spinner({
				min: 0
			});
		}
		
		$('.pj-timepicker ').timepicker({
			showPeriod: myLabel.showperiod,
			onClose: function(){
				if($frmTimeCustom.length > 0)
				{
					getCustomLunchBreak.call(null);
				}else{
					getLunchBreak.call(null);
				}
			}
		});
		
		$("#content").on("click", ".working_day", function () {
			var $this = $(this),
				day = $this.attr('data-day');
			if ($this.is(":checked")) {
				$('.tsWorkingDay_' + day).hide();
				$('.tsMidnightMessage').hide();
			} else {
				$('.tsWorkingDay_' + day).show();
				var lunch_break = $("#frmPriceDefault input[type='radio']:checked").val();
				if(lunch_break == 'F')
				{
					$('#tsLunchBreakContainer').hide();
				}
			}
		}).on("change", ".pps", function () {
			
			if ($("#single_price").is(":checked")) {
				$("#boxPPS").html("");
			} else {
				$.ajax({
					type: "POST",
					data: $("#frmTimeCustom").serialize(),
					url: "index.php?controller=pjAdminTime&action=pjActionGetSlots"
				}).success(function (data) {
					$("#boxPPS").html(data);
				});					
			}
		}).on("click", "a.day-price", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this);
			if (!$this.hasClass("disabled") && $dialogDayPrice.length > 0 && dialog) {
				$dialogDayPrice.data('day', $this.data('day')).dialog('open');
			}
			return false;
		}).on("change", "input[name='is_dayoff']", function () {
			var $this = $(this),
				$form = $this.closest("form");
			if ($this.is(":checked")) {
				$form.find(".business").hide();
				$('.tsMidnightMessage').hide();
			} else {
				$form.find(".business").show();
				var lunch_break = $("#frmTimeCustom input[type='radio']:checked").val();
				if(lunch_break == 'F')
				{
					$('#tsLunchBreakContainer').hide();
				}
			}
		}).on("change", "input[name='lunch_break']", function () {
			var $this = $(this);
			if ($this.val() == 'T') {
				$('#tsLunchBreakContainer').show();
			} else {
				$('#tsLunchBreakContainer').hide();
				$('input[name="lunch_length"]').val('');
			}
			if($frmTimeCustom.length > 0)
			{
				generateCustomSlots.call(null);
			}else{
				generateSlots.call(null);
			}
		}).on("change", "select[name='day_of_week']", function () {

			if($(this).val() != '')
			{
				window.location.href = 'index.php?controller=pjAdminTime&action=pjActionIndex&day=' + $(this).val();
			}
		}).on("change", "#slot_length", function () {
			if($frmTimeCustom.length > 0)
			{
				getCustomLunchBreak.call(null);
			}else{
				getLunchBreak.call(null);
			}
		}).on("change", "#number_of_slots", function () {
			if($frmTimeCustom.length > 0)
			{
				getCustomLunchBreak.call(null);
			}else{
				getLunchBreak.call(null);
			}
		}).on("change", "#lunch_from", function () {
			if($frmTimeCustom.length > 0)
			{
				generateCustomSlots.call(null);
			}else{
				generateSlots.call(null);
			}
		}).on("change", "input[name='lunch_length']", function () {
			var length = $(this).val();
			if(length.match(/^\d+$/)) {
				if($frmTimeCustom.length > 0)
				{
					generateCustomSlots.call(null);
				}else{
					generateSlots.call(null);
				}
			}
		});
		/****Default Time*******/
		function getLunchBreak()
		{
			var $form = $('#frmPriceDefault');
			$.post("index.php?controller=pjAdminTime&action=pjActionGetLunchBreak", $form.serialize()).done(function (data) {
				$('#tsSlotBox').html(data);
				if(!($('#after_midnight').length > 0))
				{
					$('.tsMidnightMessage').hide();
				}
				$('.day-price').hide();
				$('.field-int').spinner({
					min: 0
				});
			});
		}
		function generateSlots()
		{
			var $form = $('#frmPriceDefault');
			$.post("index.php?controller=pjAdminTime&action=pjActionGenerateSlots", $form.serialize()).done(function (data) {
				$('#tsSlotsContainer').html(data);
				if(!($('#after_midnight').length > 0))
				{
					$('.tsMidnightMessage').hide();
				}
				$('.day-price').hide();
			});
		}
		/****Custom Time*******/
		function getCustomLunchBreak()
		{
			if($('input[name="date"]').val() != '' && $('input[name="start"]').val())
			{
				$.post("index.php?controller=pjAdminTime&action=pjActionGetCustomLunchBreak", $frmTimeCustom.serialize()).done(function (data) {
					$('#tsCustomSlotBox').html(data);
					if(!($('#after_midnight').length > 0))
					{
						$('.tsMidnightMessage').hide();
					}
					$('.field-int').spinner({
						min: 0
					});
				});
			}
		}
		function generateCustomSlots()
		{
			$.post("index.php?controller=pjAdminTime&action=pjActionGenerateCustomSlots", $frmTimeCustom.serialize()).done(function (data) {
				$('#tsCustomSlotsContainer').html(data);
				if(!($('#after_midnight').length > 0))
				{
					$('.tsMidnightMessage').hide();
				}
			});
		}
		if ($dialogDayPrice.length > 0) {
			$dialogDayPrice.dialog({
				autoOpen: false,
				resizable: false,
				draggable: false,
				height:420,
				width: 460,
				modal: true,
				close: function(){
					$(this).html("");
				},
				open: function () {
					$.get("index.php?controller=pjAdminTime&action=pjActionGetPrices", {
						"day": $dialogDayPrice.data("day")
					}).done(function (data) {
						$dialogDayPrice.html(data);
					});
				},
				buttons: (function() {
					var buttons = {};
					buttons[tsApp.locale.button.save] = function() {
						$.post("index.php?controller=pjAdminTime&action=pjActionSetPrices", $dialogDayPrice.find("form").serialize()).done(function (data) {
							
						});
						$dialogDayPrice.dialog('close');			
					};
					buttons[tsApp.locale.button.erase_all] = function () {
						$.post("index.php?controller=pjAdminTime&action=pjActionSetPrices", {
							"delete": 1,
							"day" : $dialogDayPrice.find("form :input[name='day']").val()
						}).done(function (data) {
							$dialogDayPrice.dialog('close');
						});
					};
					buttons[tsApp.locale.button.cancel] = function() {
						$dialogDayPrice.dialog('close');
					};
					
					return buttons;
				})()
			});
		}

		function formatPrice(str, obj) {
			return obj.price_format;
		}
		
		if ($("#grid").length > 0 && datagrid) {
			
			var m = window.location.href.match(/&type=(employee|calendar)&foreign_id=(\d+)/);
			if (m !== null) {
				qs = m[0];
			}
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminTime&action=pjActionUpdateCustom"+qs+"&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminTime&action=pjActionDeleteDate&id={:id}"}
				          ],
				columns: [{text: myLabel.time_date, type: "date", sortable: true, editable: false, width: 75, renderer: $.datagrid._formatDate, dateFormat: pjGrid.jsDateFormat},
				          {text: myLabel.time_start, type: "text", sortable: true, editable: false, width: 85},
				          {text: myLabel.time_end, type: "text", sortable: true, editable: false, width: 85},
				          {text: myLabel.time_lunch_start, type: "text", sortable: true, editable: false, width: 85},
				          {text: myLabel.time_lunch_end, type: "text", sortable: true, editable: false, width: 85},
				          {text: myLabel.time_price, type: "text", sortable: true, editable: false, renderer: formatPrice},
				          {text: myLabel.time_dayoff, type: "select", sortable: true, editable: false, options: [
			     				       {label: myLabel.time_yesno.T, value: 'T'}, 
			     				       {label: myLabel.time_yesno.F, value: 'F'}
			     				       ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminTime&action=pjActionGetDate" + qs,
				dataType: "json",
				fields: ['date', 'start_time', 'end_time', 'start_lunch', 'end_lunch', 'price', 'is_dayoff'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminTime&action=pjActionDeleteDateBulk", render: true, confirmation: myLabel.delete_confirmation}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminTime&action=pjActionSaveDate&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		$(document).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				"is_dayoff": ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminTime&action=pjActionGetDate" + qs, "date", "ASC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			$this.addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			obj.is_dayoff = "";
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminTime&action=pjActionGetDate" + qs, "date", "ASC", content.page, content.rowCount);
			return false;
		}).on("focusin", ".datepick", function () {
			if (datepicker) {
				var $this = $(this);
				$this.datepicker({
					firstDay: $this.attr("rel"),
					dateFormat: $this.attr("rev"),
					onClose: function(e){
						getCustomLunchBreak.call(null);
					}
				});
			}
		}).on("click", ".pj-form-field-icon-date", function (e) {
			var $dp = $(this).parent().siblings("input[type='text']");
			if ($dp.hasClass("hasDatepicker")) {
				$dp.datepicker("show");
			} else {
				$dp.trigger("focusin").datepicker("show");
			}
		}).on("keypress", ".field-int", function (e) {
			if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) 
			{
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				return false;
		    }
		}).on("keypress", ".spin", function (e) {
			if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) 
			{
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				return false;
		    }
		}).on("keydown", ".tsPriceField", function (e) {
			if (e.shiftKey == true) {
                e.preventDefault();
            }

            if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 37 || e.keyCode == 39 || e.keyCode == 46 || e.keyCode == 190) {

            } else {
                e.preventDefault();
            }
            
            if($(this).val().indexOf('.') !== -1 && e.keyCode == 190)
            {
            	e.preventDefault();
            }
		});
	});
})(jQuery_1_8_2);