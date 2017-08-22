<?php
$cid = (int) @$_GET['cid'];
?>
<div class="container-fluid">
	<br/>
	<div id="pjWrapperTSBCalendar_<?php echo $cid;?>"></div>
</div>

<div style="display: none" title="<?php echo pjSanitize::html(__('front_terms', true)); ?>" id="tsTerms_<?php echo $cid; ?>"></div>
<script type="text/javascript">
var pjQ = pjQ || {},
	TSBCalendar_<?php echo $cid; ?>;
(function () {
	"use strict";
	var isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor),

	loadCssHack = function(url, callback){
		var link = document.createElement('link');
		link.type = 'text/css';
		link.rel = 'stylesheet';
		link.href = url;

		document.getElementsByTagName('head')[0].appendChild(link);

		var img = document.createElement('img');
		img.onerror = function(){
			if (callback && typeof callback === "function") {
				callback();
			}
		};
		img.src = url;
	},
	loadRemote = function(url, type, callback) {
		if (type === "css" && isSafari) {
			loadCssHack(url, callback);
			return;
		}
		var _element, _type, _attr, scr, s, element;
		
		switch (type) {
		case 'css':
			_element = "link";
			_type = "text/css";
			_attr = "href";
			break;
		case 'js':
			_element = "script";
			_type = "text/javascript";
			_attr = "src";
			break;
		}
		
		scr = document.getElementsByTagName(_element);
		s = scr[scr.length - 1];
		element = document.createElement(_element);
		element.type = _type;
		if (type == "css") {
			element.rel = "stylesheet";
		}
		if (element.readyState) {
			element.onreadystatechange = function () {
				if (element.readyState == "loaded" || element.readyState == "complete") {
					element.onreadystatechange = null;
					if (callback && typeof callback === "function") {
						callback();
					}
				}
			};
		} else {
			element.onload = function () {
				if (callback && typeof callback === "function") {
					callback();
				}
			};
		}
		element[_attr] = url;
		s.parentNode.insertBefore(element, s.nextSibling);
	},
	loadScript = function (url, callback) {
		loadRemote(url, "js", callback);
	},
	loadCss = function (url, callback) {
		loadRemote(url, "css", callback);
	},
	randomString = function (length, chars) {
		var result = "";
		for (var i = length; i > 0; --i) {
			result += chars[Math.round(Math.random() * (chars.length - 1))];
		}
		return result;
	},
	getSessionId = function () {
		return sessionStorage.getItem("session_id") == null ? "" : sessionStorage.getItem("session_id");
	},
	createSessionId = function () {
		if(getSessionId()=="") {
			sessionStorage.setItem("session_id",randomString(32, "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"));
		}
	},
	options = {
		server: "<?php echo PJ_INSTALL_URL; ?>",
		folder: "<?php echo PJ_INSTALL_URL; ?>",
		cid: <?php echo $cid; ?>,
		locale: <?php echo isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? (int) $_GET['locale'] : $controller->pjActionGetLocale(); ?>,
		layout: <?php echo isset($_GET['layout']) && in_array($_GET['layout'], $controller->getLayoutRange()) ? (int) $_GET['layout'] : (int) $tpl['option_arr']['o_layout']; ?>,
		theme: "<?php echo isset($_GET['theme']) ? $_GET['theme'] : $tpl['option_arr']['o_theme']; ?>",
		fields: <?php echo pjAppController::jsonEncode(__('front_app', true)); ?>,
		year: <?php echo date('Y'); ?>,
		month: <?php echo date('n'); ?>,
		multi: <?php echo isset($_GET['multi']) ? $_GET['multi'] : 0;?>
	};
	<?php
	$dm = new pjDependencyManager(PJ_THIRD_PARTY_PATH);
	$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
	?>
	if (isSafari) {
		createSessionId();
		options.session_id = getSessionId();
	}else{
		options.session_id = "";
	}
	loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_jquery'); ?>pjQuery.min.js", function () {
		loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_validate'); ?>pjQuery.validate.min.js", function () {
			loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_bootstrap'); ?>pjQuery.bootstrap.min.js", function () {
				loadScript("<?php echo PJ_INSTALL_URL . $dm->getPath('pj_tooltipster'); ?>pjQuery.tooltipster.js", function () {
					loadScript("<?php echo PJ_INSTALL_URL . PJ_JS_PATH; ?>pjTSBCalendar.js", function () {
						TSBCalendar_<?php echo $cid; ?> = new TSBCalendar(options);
					});
				});
			});
		});
	});
})();
</script>