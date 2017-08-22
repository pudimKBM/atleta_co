var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var tabs = ($.fn.tabs !== undefined),
			tipsy = ($.fn.tipsy !== undefined),
			$tabs = $("#tabs"),
			dialog = ($.fn.dialog !== undefined),
			$dialogCopyOptions = $("#dialogCopyOptions");;
		
		if ($tabs.length > 0 && tabs) {
			$tabs.tabs();
		}
		
		$(".field-int").spinner({
			min: 0
		});
		if (tipsy) {
			$(".listing-tip").tipsy({
				offset: 1,
				opacity: 1,
				html: true,
				gravity: "nw",
				className: "tipsy-listing"
			});
		}
		function reDrawCode() {
			var code = $("#hidden_code").text(),
				layout = $("select[name='layout']").find("option:selected").val(),
				locale = $("select[name='install_locale']").find("option:selected").val(),
				hide = $("input[name='install_hide']").is(":checked") ? "&hide=1" : "",
				switch_layout = $("input[name='switch']").is(":checked") ? "&switch=1" : "&switch=0",
				multi = $("input[name='multi']").is(":checked") ? "&multi=1" : "&multi=0",
				cid = $("select[name='cid']").find("option:selected").val();
			locale = typeof locale !== "undefined" && locale !== null && parseInt(locale.length, 10) > 0 ? "&locale=" + locale : "";
			layout = parseInt(layout.length, 10) > 0 ? "&layout=" + layout : "";
			cid = parseInt(cid, 10) > 0 ? "&cid=" + cid : "";
			$("#install_code").text(code.replace(/(&cid=\d+)/g, function(match, p1) {
	            return [cid, layout, locale, hide, switch_layout, multi].join("");
	        }));
		}
		
		if($('#pjInstallSettings').length > 0)
		{
			reDrawCode.call(null);
		}
		
		$("#content").on("focus", ".textarea_install", function (e) {
			var $this = $(this);
			$this.select();
			$this.mouseup(function() {
				$this.unbind("mouseup");
				return false;
			});
		}).on("keyup", "#uri_page", function (e) {
			console.log(this.value);
			var tmpl = $("#hidden_htaccess").text(),
				index = this.value.indexOf("?");
			$("#install_htaccess").text(tmpl.replace('::URI_PAGE::', index >= 0 ? this.value.substring(0, index) : this.value));
		}).on("change", "select[name='layout']", function (e) {
			
			var $this = $(this),
				$selected = $this.find("option:selected"),
				val = $selected.val();

			if (val == 2 && $selected.data("ok") == 0) {
				$("#weekly_warn").show();
				$(".install_stuff, #btnPreview").hide();
			} else {
				$("#weekly_warn").hide();
				$(".install_stuff, #btnPreview").show();
			}
			
			reDrawCode.call(null);
			
		}).on("change", "select[name='install_locale']", function(e) {
            
            reDrawCode.call(null);
            
		}).on("change", "input[name='install_hide']", function (e) {
			
			reDrawCode.call(null);
			
		}).on("change", "input[name='switch']", function (e) {
			
			reDrawCode.call(null);
			
		}).on("change", "input[name='multi']", function (e) {
			
			reDrawCode.call(null);
			
		}).on("change", "select[name='cid']", function (e) {
			
			reDrawCode.call(null);
			
		}).on("change", "select[name='value-enum-o_send_email']", function (e) {
			switch ($("option:selected", this).val()) {
			case 'mail|smtp::mail':
				$(".boxSmtp").hide();
				break;
			case 'mail|smtp::smtp':
				$(".boxSmtp").show();
				break;
			}
		}).on("change", "input[name='value-bool-o_allow_paypal']", function (e) {
			if ($(this).is(":checked")) {
				$(".boxPaypal").show();
			} else {
				$(".boxPaypal").hide();
			}
		}).on("change", "input[name='value-bool-o_allow_authorize']", function (e) {
			if ($(this).is(":checked")) {
				$(".boxAuthorize").show();
			} else {
				$(".boxAuthorize").hide();
			}
		}).on("change", "input[name='value-bool-o_allow_bank']", function (e) {
			if ($(this).is(":checked")) {
				$(".boxBank").show();
			} else {
				$(".boxBank").hide();
			}
		}).on("click", ".pj-use-theme", function (e) {
			var theme = $(this).attr('data-theme');
			$('.pj-loader').css('display', 'block');
			$.ajax({
				type: "GET",
				async: false,
				url: 'index.php?controller=pjAdminOptions&action=pjActionUpdateTheme&theme=' + theme,
				success: function (data) {
					$('.theme-holder').html(data);
					$('.pj-loader').css('display', 'none');
				}
			});
		}).on("click", "#btnCopyOptions", function () {
			if ($dialogCopyOptions.length > 0 && dialog) {
				$dialogCopyOptions.dialog("open");
			}
		}).on("change", "#preview_calendar_id", function (e) {
			var cid = $(this).val();
			$('.pj_preview_install').each(function(){
				var href = $(this).attr('data-href');
				href = href.replace(/{CID}/g, cid);
				$(this).attr('href', href);
			});
		});
		
		if ($dialogCopyOptions.length > 0 && dialog) {
			var buttons = {};
			buttons[myLabel.btnCopy] = function () {
				var $this = $(this),
					tab_id = $("input[name='copy_tab_id']").val();
				$.post("index.php?controller=pjAdminOptions&action=pjActionCopy", {
					"calendar_id": $("option:selected", $("select[name='copy_calendar_id']")).val(),
					"tab_id": tab_id
				}).done(function (data) {
					$this.dialog("close");
					window.location.href = "index.php?controller=pjAdminOptions&tab=" + tab_id;
				});
			};
			buttons[myLabel.btnCancel] = function () {
				$(this).dialog("close");
			};
			$dialogCopyOptions.dialog({
				resizable: false,
				draggable: false,
				autoOpen: false,
				modal: true,
				buttons: buttons
			});
		}
	});
})(jQuery_1_8_2);