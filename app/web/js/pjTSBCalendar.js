/*!
 * TimeSlots Booking Calendar v3.0
 * http://www.phpjabbers.com/time-slots-booking-calendar/
 * 
 * Copyright 2014, StivaSoft Ltd.
 * 
 * Date: Wed Nov 27 17:17:20 2013 +0200
 */
(function (window, undefined) {
	"use strict";
	
	pjQ.$.ajaxSetup({
		xhrFields: {
			withCredentials: true
		}
	});
	
	var document = window.document,
		validate = (pjQ.$.fn.validate !== undefined),
		dialog = (pjQ.$.fn.dialog !== undefined),
		$dialogTerms,
		routes = [
		    {pattern: /^#!\/Calendar$/, eventName: "loadCalendar"},
		    {pattern: /^#!\/Calendar\/date:([\d\-\.\/]+)?$/, eventName: "loadCalendar"},
			{pattern: /^#!\/((?:19|20)\d\d)\/(0[1-9]|1[012])\/(0[1-9]|[12][0-9]|3[01])$/, eventName: "loadCalendar"},
		    {pattern: /^#!\/Cart$/, eventName: "loadCart"},
			{pattern: /^#!\/Checkout$/, eventName: "loadCheckout"},
			{pattern: /^#!\/Timeslots$/, eventName: "loadTimeslots"},
			{pattern: /^#!\/Timeslots\/date:([\d\-\.\/]+)?$/, eventName: "loadTimeslots"},
			{pattern: /^#!\/Timeslots\/((?:19|20)\d\d)\/(0[1-9]|1[012])\/(0[1-9]|[12][0-9]|3[01])$/, eventName: "loadTimeslots"},
			{pattern: /^#!\/Weekly$/, eventName: "loadWeekly"},
			{pattern: /^#!\/Weekly\/date:([\d\-\.\/]+)?$/, eventName: "loadWeekly"},
			{pattern: /^#!\/Weekly\/((?:19|20)\d\d)\/(0[1-9]|1[012])\/(0[1-9]|[12][0-9]|3[01])$/, eventName: "loadWeekly"},
			{pattern: /^#!\/Preview$/, eventName: "loadPreview"},
			{pattern: /^#!\/Booking\/([A-Z]{2}\d{10})$/, eventName: "loadBooking"}
		];
	
	function log() {
		if (window.console && window.console.log) {
			for (var x in arguments) {
				if (arguments.hasOwnProperty(x)) {
					window.console.log(arguments[x]);
				}
			}
		}
	}
	
	function assert() {
		if (window && window.console && window.console.assert) {
			window.console.assert.apply(window.console, arguments);
		}
	}
	
	function hashBang(value) {
		if (value !== undefined && value.match(/^#!\//) !== null) {
			if (window.location.hash == value) {
				return false;
			}
			window.location.hash = value;
			return true;
		}
		
		return false;
	}
	
	function onHashChange() {
		var i, iCnt, m;
		for (i = 0, iCnt = routes.length; i < iCnt; i++) {
			m = unescape(window.location.hash).match(routes[i].pattern);
			if (m !== null) {
				pjQ.$(window).trigger(routes[i].eventName, m.slice(1));
				break;
			}
		}
		if (m === null) {
			pjQ.$(window).trigger("loadRouter");
		}
	}
	
	pjQ.$(window).on("hashchange", function (e) {
    	onHashChange.call(null);
    });
	
	if (!Date.prototype.toISOString) {
		(function(){
			function pad(number) {
				var r = String(number);
				if (r.length === 1) {
					r = '0' + r;
				}
				return r;
			}

			Date.prototype.toISOString = function() {
				return this.getUTCFullYear()
				+ '-' + pad(this.getUTCMonth() + 1)
				+ '-' + pad(this.getUTCDate())
				+ 'T' + pad(this.getUTCHours())
				+ ':' + pad(this.getUTCMinutes())
				+ ':' + pad(this.getUTCSeconds())
				+ '.' + String((this.getUTCMilliseconds()/1000).toFixed(3)).slice(2, 5)
				+ 'Z';
			};
		}());
	}
	
	function TSBCalendar(options) {
		if (!(this instanceof TSBCalendar)) {
			return new TSBCalendar(options);
		}
		
		this.reset.call(this);
		this.init.call(this, options);
		
		return this;
	}
	
	// --- Public methods/members
	TSBCalendar.prototype = {
		reset: function () {
			this.$container = null;
			this.container = null;
			this.view = null;
			this.date = new Date().toISOString().slice(0, 10);
			this.booking_uuid = null;
			this.layout = 1;
			this.duplicated = null;
			this.cid = null;
			this.custom = null;
			this.options = {};
			
			return this;
		},
		init: function(opts) {
			var self = this;
			this.options = opts;

			self.layout = this.options.layout;
			self.cid = this.options.cid;
			
			this.container = document.getElementById("pjWrapperTSBCalendar_" + self.cid);
			if (this.container) {
				this.$container = pjQ.$(this.container);
			}
			
			this.$container.on("click.ts", ".pjTsCalendarLinkMonth", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = pjQ.$(this),
					month = String($this.data("month"));
				hashBang(["#!", $this.data("year"), (month.length === 1 ? '0' + month : month), '01'].join("/"));
				
				return false;
				
			}).on("click.ts", ".pjTsCalendarDate", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var iso = pjQ.$(this).data("iso").split("-");
				pjQ.$('.tooltipster-base').hide();
				if(pjQ.$(this).attr('data-custom'))
				{
					self.custom = 1;
				}else{
					self.custom = 0;
				}
				hashBang(["#!/Timeslots", iso[0], iso[1], iso[2]].join("/"));
				return false;
				
			}).on("click.ts", ".pjTsViewTab", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('.pjTsViewTab').removeClass('active');
				pjQ.$(this).addClass('active');
				self.layout = parseInt(pjQ.$(this).attr('data-layout'), 10);
				switch (self.layout) {
				case 2:
					hashBang("#!/Weekly");
					break;
				case 1:
				default:
					hashBang("#!/Calendar");
					break;
				}
				return false;
			}).on("click.ts", ".tsSelectorToggleLegend", function (e) {	
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.$container.find(".tsSelectorLegend").slideToggle();
				
				return false;
				
			}).on("click.ts", ".pjTsSelectorCalendar", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				
				var iso = self.date.split("-");
				switch (self.layout) {
				case 2:
					hashBang(["#!/Weekly", iso[0], iso[1], iso[2]].join("/"));
					break;
				case 1:
				default:
					hashBang(["#!", iso[0], iso[1], iso[2]].join("/"));
					break;
				}
				return false;
				
			}).on("click.ts", ".pjTsSelectorCart", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				
				hashBang("#!/Cart");
				
				return false;
				
			}).on("click.ts", ".pjTsSelectorCheckout", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang("#!/Checkout");
				
				return false;
				
			}).on("click.ts", ".pjTsSelectorPreview", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang("#!/Preview");
				
				return false;

			}).on("click.ts", ".pjTsSelectorAddToCart", function (e) {
				if (e && e.stopPropagation) {
					e.stopPropagation();
				}
				self.add.call(self, pjQ.$(this).data()).done(function (data) {
					switch (self.layout) {
					case 2:
						if(self.custom == 1)
						{
							pjQ.$(window).trigger("loadTimeslots", self.date);
						}else{
							pjQ.$(window).trigger("loadWeekly", self.date);
						}
						break;
					case 1:
					default:
						pjQ.$(window).trigger("loadTimeslots", self.date);
						break;
					}
				});
				
				return false;
				
			}).on("click.ts", ".pjTsSelectorRemoveFromCart", function (e) {
				if (e && e.stopPropagation) {
					e.stopPropagation();
				}
				var $this = pjQ.$(this);
				self.remove.call(self, $this.data()).done(function (data) {
					switch (self.layout) {
					case 2:
						if ($this.hasClass("tsSelectorRemoveTimeslot")) {
							if(self.custom == 1)
							{
								pjQ.$(window).trigger("loadTimeslots", self.date);
							}else{
								pjQ.$(window).trigger("loadWeekly", self.date);
							}
						} else {
							pjQ.$(window).trigger("loadCart");
						}
						break;
					case 1:
					default:
						if ($this.hasClass("tsSelectorRemoveTimeslot")) {
							pjQ.$(window).trigger("loadTimeslots", self.date);
						} else {
							pjQ.$(window).trigger("loadCart");
						}
						break;
					}
				});
				
				return false;
				
			}).on("change.ts", "select[name='payment_method']", function (e) {
				self.$container.find(".pjTsSelectorCCard").hide();
				self.$container.find(".pjTsSelectorBank").hide();
				switch (pjQ.$(this).find("option:selected").val()) {
				case 'creditcard':
					self.$container.find(".pjTsSelectorCCard").show();
					break;
				case 'bank':
					self.$container.find(".pjTsSelectorBank").show();
					break;
				}
			}).on("click.ts", ".pjTsSelectorWeeklyNav", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang(["#!/Weekly", pjQ.$(this).data("date")].join("/"));
				return false;
			}).on("change.ts", ".pjTsSwitchCalendar", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.cid = pjQ.$(this).val();
				if (self.layout === 2) {
					pjQ.$(window).trigger("loadWeekly");
				} else {
					pjQ.$(window).trigger("loadCalendar");
				}
				return false;
			}).on('click.ts', '#pjTsCaptchaImage', function(e){
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $captchaImg = pjQ.$(this);
				if($captchaImg.length > 0){
					var rand = Math.floor((Math.random()*999999)+1); 
					$captchaImg.attr("src", self.options.folder + 'index.php?controller=pjFrontEnd&action=pjActionCaptcha&rand=' + rand + "&session_id=" + self.options.session_id);
					self.$container.find('input[name="captcha"]').val("");
				}
				return false;
			});
			
			//Custom events
			pjQ.$(window).on("loadRouter", this.container, function (e) {
				if (self.layout === 2) {
					pjQ.$(window).trigger("loadWeekly");
				} else {
					pjQ.$(window).trigger("loadCalendar");
				}
			}).on("loadTimeslots", this.container, function (e) {
				switch (arguments.length) {
				case 4:
					self.date = [arguments[1], arguments[2], arguments[3]].join("-");
					break;
				case 2:
					self.date = arguments[1];
					break;
				}
				self.loadTimeslots.call(self);
			}).on("loadCalendar", this.container, function (e) {
				self.layout = 1;
				switch (arguments.length) {
				case 4:
					self.date = [arguments[1], arguments[2], arguments[3]].join("-");
					break;
				case 2:
					self.date = arguments[1];
					break;
				}
				self.loadCalendar.call(self);
			}).on("loadWeekly", this.container, function (e) {
				self.layout = 2;
				switch (arguments.length) {
				case 4:
					self.date = [arguments[1], arguments[2], arguments[3]].join("-");
					break;
				case 2:
					self.date = arguments[1];
					break;
				}
				self.loadWeekly.call(self);
			}).on("loadCart", this.container, function (e) {
				self.loadCart.call(self);
			}).on("loadCheckout", this.container, function (e) {
				self.loadCheckout.call(self);
			}).on("loadPreview", this.container, function (e) {
				self.loadPreview.call(self);
			}).on("loadBooking", this.container, function (e, booking_uuid) {
				self.booking_uuid = booking_uuid;
				self.loadBooking.call(self);
			});
			
			if (window.location.hash.length === 0) {
				pjQ.$(window).trigger("loadRouter");
			} else {
				onHashChange.call(null);
			}
			
			$dialogTerms = pjQ.$("#tsTerms_" + self.cid);
			if ($dialogTerms.length && dialog) {
				$dialogTerms.dialog({
					autoOpen: false,
					draggable: false,
					resizable: false,
					modal: true,
					width: 600,
					open: function () {
						pjQ.$.get([self.options.folder, "index.php?controller=pjFrontEnd&action=pjActionGetTerms", "&session_id=", self.options.session_id, "&cid=", self.cid].join("")).done(function (data) {
							$dialogTerms.html(data);
							$dialogTerms.dialog("option", "position", "center");
						});
					},
					buttons: {
						'OK': function () {
							$dialogTerms.dialog("close");
						}
					}
				});
			}
			
			return this;
		},
		disableButtons: function () {
			this.$container.find(".btn").attr("disabled", "disabled");

			return this;
		},
		enableButtons: function () {
			this.$container.find(".btn").removeAttr("disabled");

			return this;
		},
		errorHandler: function (msg) {
			this.$container.find(".tsSelectorNotice:first").html(msg).show();
			
			return this;
		},
		add: function (obj) {
			var self = this;
			pjQ.$.extend(obj, {});
			
			var jqxhr = pjQ.$.post([this.options.folder + "index.php?controller=pjFrontCart&action=pjActionAdd", "&session_id=", self.options.session_id, "&cid=", self.cid, "&locale=", this.options.locale].join(''), obj);
			
			return jqxhr;
		},
		remove: function (obj) {
			var self = this;
			pjQ.$.extend(obj, {});
			
			var jqxhr = pjQ.$.post([this.options.folder + "index.php?controller=pjFrontCart&action=pjActionRemove", "&session_id=", self.options.session_id, "&cid=", self.cid, "&locale=", this.options.locale].join(''), obj);
			
			return jqxhr;
		},
		loadBooking: function () {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionBooking"].join(""), {
				"session_id": self.options.session_id,
				"cid": self.cid,
				"locale": this.options.locale,
				"booking_uuid": this.booking_uuid,
				"multi" : self.options.multi
			}).done(function (data) {
				self.$container.html(data);
				self.view = 'pjActionBooking';
				
				var $paypal = self.$container.find("form[name='tsPaypal']"),
					$authorize = self.$container.find("form[name='tsAuthorize']");
				
				if ($paypal.length > 0) {
					window.setTimeout(function () {
						$paypal.trigger('submit');
					}, 3000);
				} else if ($authorize.length > 0) {
					window.setTimeout(function () {
						$authorize.trigger('submit');
					}, 3000);
				}
			});
			
			return this;
		},
		loadCalendar: function () {
			var self = this;
			this.disableButtons.call(this);
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionCalendar"].join(""), {
				"session_id": self.options.session_id,
				"cid": self.cid,
				"locale": this.options.locale,
				"layout": self.layout,
				"date": this.date,
				"multi" : self.options.multi
			}).done(function (data) {
				self.$container.html(data);
				self.view = 'pjActionCalendar';
				pjQ.$('.pjTsTooltipster').tooltipster({
					interactive: true,
	        		contentAsHTML: true
	        	});
			}).fail(function () {
				self.enableButtons.call(self);
			});
			
			return this;
		},
		loadCart: function () {
			var self = this;
			this.disableButtons.call(this);
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionCart"].join(""), {
				"session_id": self.options.session_id,
				"cid": self.cid,
				"locale": this.options.locale,
				"layout": self.layout,
				"multi" : self.options.multi
			}).done(function (data) {
				self.$container.html(data);
				self.view = 'pjActionCart';
			}).fail(function () {
				self.enableButtons.call(self);
			});
			
			return this;
		},
		loadCheckout: function () {
			var self = this;
			this.disableButtons.call(this);
			pjQ.$.get(this.options.folder + "index.php?controller=pjFrontPublic&action=pjActionCheckout", {
				"session_id": self.options.session_id,
				"cid": self.cid,
				"locale": this.options.locale,
				"layout": self.layout,
				"multi" : self.options.multi
			}).done(function (data) {
				self.$container.html(data);
				self.view = 'pjActionCheckout';
				pjQ.$('.modal-dialog').css("z-index", "9999"); 
				if (validate) {					
					self.$container.find(".pjTsSelectorCheckoutForm").validate({
						rules: {
							"captcha" : {
								remote: self.options.folder + "index.php?controller=pjFrontEnd&action=pjActionCheckCaptcha&session_id=" + self.options.session_id,
								required: true,
								minlength: 6,
								maxlength: 6
							}
						},
						onkeyup: false,
						errorElement: 'li',
						errorPlacement: function (error, element) {
							if(element.attr('name') == 'captcha' || element.attr('name') == 'terms')
							{
								element.parent().parent().parent().parent().addClass('has-error');
								error.appendTo(element.parent().parent().next().find('ul'));
							}else{
								element.parent().parent().addClass('has-error');
								error.appendTo(element.next().find('ul'));
							}
						},
						success: function(li, element) {
							if(li.parent().parent().parent().hasClass('has-error'))
							{
								li.parent().parent().parent().removeClass('has-error').addClass('has-success');
							}
							if(li.parent().parent().parent().parent().hasClass('has-error'))
							{
								li.parent().parent().parent().parent().removeClass('has-error').addClass('has-success');
							}
			            },
			            unhighlight: function(ele, errorClass, validClass) {
			            	var element = pjQ.$(ele);
			            	if(element.attr('name') == 'captcha')
							{
								element.parent().parent().removeClass('has-error').addClass('has-success');
							}else{
								element.parent().parent().removeClass('has-error').addClass('has-success');
							}
			            },
						submitHandler: function (form) {
							self.disableButtons.call(self);
							var $form = pjQ.$(form);
							pjQ.$.post([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionCheckout", "&session_id=", self.options.session_id, "&cid=", self.cid].join(""), $form.serialize()).done(function (data) {
								if (data.status == "OK") {
									hashBang("#!/Preview");
								}else{
									self.enableButtons.call(self);
								} 
							}).fail(function () {
								self.enableButtons.call(self);
							});
							return false;
						}
					});
				}
			
			}).fail(function () {
				self.enableButtons.call(self);
			});
			
			return this;
		},
		loadPreview: function () {
			var self = this;
			pjQ.$.get(this.options.folder + "index.php?controller=pjFrontPublic&action=pjActionPreview", {
				"session_id": self.options.session_id,
				"cid": self.cid,
				"locale": this.options.locale,
				"layout": self.layout,
				"duplicated": self.duplicated,
				"multi" : self.options.multi
			}).done(function (data) {
				self.$container.html(data);
				self.view = 'pjActionPreview';
				
				if (validate) {
					self.$container.find(".pjTsSelectorPreviewForm").validate({
						rules: {},
						onkeyup: false,
						onclick: false,
						onfocusout: false,
						errorClass: "tsError",
						validClass: "tsValid",
						wrapper: "em",
						errorPlacement: function (error, element) {
							error.insertAfter(element.parent());
						},
						submitHandler: function (form) {
							self.disableButtons.call(self);
							self.duplicated = 0;
							var $form = pjQ.$(form);
							pjQ.$.post([self.options.folder, "index.php?controller=pjFrontEnd&action=pjActionOrder", "&session_id=", self.options.session_id, "&cid=", self.cid, "&locale=", self.options.locale].join(""), $form.serialize()).done(function (data) {
								if (data.status == "OK") {
									hashBang("#!/Booking/" + data.booking_uuid);
								} else if (data.status == "ERR") {
									if(data.code == '101')
									{
										self.duplicated = 1;
										self.loadPreview.call(self);
									}else{
										self.enableButtons.call(self);
									}
								}
							}).fail(function () {
								self.enableButtons.call(self);
							});
							return false;
						}
					});
				}
			});
			
			return this;
		},
		loadTimeslots: function () {
			var self = this;
			this.disableButtons.call(this);
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionTimeslots"].join(""), {
				"session_id": self.options.session_id,
				"cid": self.cid,
				"locale": this.options.locale,
				"layout": self.layout,
				"date": this.date,
				"multi" : self.options.multi
			}).done(function (data) {
				self.$container.html(data);
				self.view = 'pjActionTimeslots';
			}).fail(function () {
				self.enableButtons.call(self);
			});
			
			return this;
		},
		loadWeekly: function () {
			var self = this;
			this.disableButtons.call(this);
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionWeekly"].join(""), {
				"session_id": self.options.session_id,
				"cid": self.cid,
				"locale": this.options.locale,
				"layout": self.layout,
				"date": this.date,
				"multi" : self.options.multi
			}).done(function (data) {
				self.$container.html(data);
				self.view = 'pjActionWeek';
				var $tb_body = self.$container.find('.pj-calendar-body').eq(0);
				$tb_body.find('.pj-calendar-rowspan').each(function(){
					pjQ.$(this).height($tb_body.height());
				});
			}).fail(function () {
				self.enableButtons.call(self);
			});
			
			return this;
		}
	};
	
	// expose
	window.TSBCalendar = TSBCalendar;
})(window);