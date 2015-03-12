/** * @copyright (C)2011 Cenwor Inc. * @author Moyo <dev@uuland.org> * @package js * @name share.linker.js * @date 2011-09-05 16:57:57 */ function share_to(m,img,ext) {
	if (ext == undefined) ext = '';
	if (m == "baidu") {
		window
				.open('http://cang.baidu.com/do/add?it='
						+ encodeURIComponent(document.title.substring(0, 76))
						+ '&iu=' + encodeURIComponent(location.href)
						+ '&fr=ien#nw=1', 'baidu',
						'scrollbars=no,width=600,height=450,status=no,resizable=yes,left='
						+ (screen.width - 600) / 2 + ',top='
						+ (screen.height - 450) / 2);
	} else if (m == "qq") {
		window
				.open(
						'http://shuqian.qq.com/post?from=3&title='
								+ encodeURIComponent(document.title) + '&uri='
								+ encodeURIComponent(document.location.href)
								+ '&jumpback=2&noui=1',
						'favit',
						'width=930,height=470,toolbar=no,menubar=no,location=no,scrollbars=yes,status=yes,resizable=yes,left='
						+ (screen.width - 930) / 2 + ',top='
						+ (screen.height - 470) / 2);
	} else if (m == "tsina") {
		void ((function(s, d, e) {
			try {
			} catch (e) {
			}
			var f = 'http://v.t.sina.com.cn/share/share.php?', u = d.location.href, p = [
					'url=', e(u) + ext, '&title=', e(d.title), '&appkey=2412592813&pic='+img ]
					.join('');
			function a() {
				if (!window
						.open(
								[ f, p ].join(''),
								'mb',
								[
										'toolbar=0,status=0,resizable=1,width=620,height=450,left=',
										(s.width - 620) / 2, ',top=',
										(s.height - 450) / 2 ].join('')))
					u.href = [ f, p ].join('');
			}
			;
			if (/Firefox/.test(navigator.userAgent)) {
				setTimeout(a, 0)
			} else {
				a()
			}
		})(screen, document, encodeURIComponent));
	} else if (m == "douban") {
		void (function() {
			var d = document, e = encodeURIComponent, s1 = window.getSelection, s2 = d.getSelection, s3 = d.selection, s = s1 ? s1()
					: s2 ? s2() : s3 ? s3.createRange().text : '', r = 'http://www.douban.com/recommend/?url='
					+ e(d.location.href + ext)
					+ '&title='
					+ e(d.title)
					+ '&sel='
					+ e(s) + '&v=1', x = function() {
				if (!window
						.open(r, 'douban',
								'toolbar=0,resizable=1,scrollbars=yes,status=1,width=450,height=355,left='
									+ (screen.width - 450) / 2 + ',top='
									+ (screen.height - 330) / 2))
					location.href = r + '&r=1'
			};
			if (/Firefox/.test(navigator.userAgent)) {
				setTimeout(x, 0)
			} else {
				x()
			}
		})();
	} else if (m == "renren") {
		// 官方分享方式1
//		var connect_url = "http://www.connect.renren.com";
//		var url = window.location.href;
//		var addQS = function(url, qs) {
//			var a = [];
//			for ( var k in qs)
//				if (qs[k])
//					a.push(k.toString() + '=' + encodeURIComponent(qs[k]));
//			return url + '?' + a.join('&');
//		}
//		var href = addQS(connect_url + '/sharer.do', {
//			'url' : url,
//			'title' : url == window.location.href ? document.title : null
//		});
//		window.open(href, 'sharer',
//				'toolbar=0,status=0,width=550,height=400,left='
//						+ (screen.width - 550) / 2 + ',top='
//						+ (screen.height - 500) / 2);

		// 官方分享方式2
		 void ((function(s, d, e) {
		 if (/renren\.com/.test(d.location))
		 return;
		 var f = 'http://share.renren.com/share/buttonshare.do?link=', u =
		 d.location + ext, l = d.title, p = [
		 e(u), '&title=', e(l) ].join('');
		 function a() {
		 if (!window
		 .open(
		 [ f, p ].join(''),
		 'xnshare',
		 [
		 'toolbar=0,status=0,resizable=1,width=626,height=436,left=',
		 (s.width - 626) / 2, ',top=',
		 (s.height - 436) / 2 ].join('')))
		 u.href = [ f, p ].join('');
		 }
		 ;
		 if (/Firefox/.test(navigator.userAgent))
		 setTimeout(a, 0);
		 else
		 a();
		 })(screen, document, encodeURIComponent));
	} else if (m == "kaixin001") {
		var kw = window
				.open(
						'',
						'kaixin001',
						'toolbar=no,titlebar=no,status=no,menubar=no,scrollbars=no,location:no,directories:no,width=570,height=350,left='
								+ (screen.width - 570)
								/ 2
								+ ',top='
								+ (screen.height - 420) / 2);
		var tempForm = kw.document.createElement('form');
		function openPostWindow(url, data, name) {
			var tempForm = document.createElement('form');
			tempForm.id = 'tempForm1';
			tempForm.method = 'post';
			tempForm.action = url;
			tempForm.target = 'kaixin001';
			var hideInput = document.createElement('input');
			hideInput.type = 'hidden';
			hideInput.name = 'rcontent';
			hideInput.value = data;
			tempForm.appendChild(hideInput);
			document.body.appendChild(tempForm);
			tempForm.submit();
			document.body.removeChild(tempForm);
		}
		function add2Kaixin001() {
			var u = document.location.href;
			var t = document.title;
			var c = '' + (document.getSelection ? document.getSelection()
					: document.selection.createRange().text);
			var iframec = '';
			var url = 'http://www.kaixin001.com/repaste/bshare.php?rtitle='
					+ encodeURIComponent(t) + '&rurl=' + encodeURIComponent(u) + ext
					+ '&from=maxthon';
			var data = encodeURIComponent(c);
			openPostWindow(url, c, '_blank')
		}
		add2Kaixin001();
	} else if (m == "google") {
		void (function() {
			var a = window, b = document, c = encodeURIComponent, d = a
					.open(
							'http://www.google.com/bookmarks/mark?op=edit&hl=zh-CN&output=popup&bkmk='
									+ c(b.location + ext) + '&title=' + c(b.title),
							'bkmk_popup',
							'left='
									+ ((a.screenX || a.screenLeft) + 10)
									+ ',top='
									+ ((a.screenY || a.screenTop) + 10)
									+ ',height=420px,width=550px,resizable=1,alwaysRaised=1');
			a.setTimeout(function() {
				d.focus()
			}, 300)
		})();
	} else if (m == "taojianghu") {
		window.open("http://share.jianghu.taobao.com/share/addShare.htm?url="
				+ encodeURIComponent(document.location.href), 'taojianghu',
				'toolbar=0,status=0,width=550,height=400,left='
						+ (screen.width - 550) / 2 + ',top='
						+ (screen.height - 500) / 2);
	} else if (m == "gmail") {
		var a = window, b = document, c = encodeURIComponent;
		var w = window.open(
				"https://mail.google.com/mail/?view=cm&fs=1&tf=1&ui=2&shva=1&to&su="
						+ c(b.title) + "&body=" + c(b.location) + ext, 'gmail',
				'width=' + (window.innerWidth * 0.57) + ',height='
						+ (window.innerHeight * 4 / 5) + ',left='
						+ ((a.screenX || a.screenLeft) + 10) + ',top='
						+ ((a.screenY || a.screenTop) + 10));
	} else if (m == "yahoo") {
		window
				.open('http://myweb.cn.yahoo.com/popadd.html?url='
						+ encodeURIComponent(document.location.href + ext)
						+ '&title=' + encodeURIComponent(document.title),
						'Yahoo',
						'scrollbars=yes,width=440,height=440,left=80,top=80,status=yes,resizable=yes');

	} else if (m == "douban9") {
		void (function() {
			var d = document, e = encodeURIComponent, s1 = window.getSelection, s2 = d.getSelection, s3 = d.selection, s = s1 ? s1()
					: s2 ? s2() : s3 ? s3.createRange().text : '', r = 'http://www.douban.com/recommend/?url='
					+ e(d.location.href)
					+ '&title='
					+ e(d.title)
					+ '&sel='
					+ e(s) + '&v=1&n=1', x = function() {
				if (!window
						.open(r, 'douban9',
								'toolbar=0,resizable=1,scrollbars=yes,status=1,width=450,height=330'))
					location.href = r + '&r=1'
			};
			if (/Firefox/.test(navigator.userAgent)) {
				setTimeout(x, 0)
			} else {
				x()
			}
		})();
	} else if (m == "115") {
		var sel = '' + (document.getSelection ? document.getSelection()
				: document.selection.createRange().text);
		var url = document.location.href;
		var title = document.title;
		var c = encodeURIComponent;
		void (window.open('http://fav.115.com/?ac=add&title=' + c(title)
				+ '&url=' + c(url) + '&c=' + c(sel), '115',
				'toolbar=0,status=0,width=780,height=700,left='
						+ (screen.width - 780) / 2 + ',top='
						+ (screen.height - 700) / 2));
	} else if (m == "51") {
		try {
			var w = window.open(
					"http://share.51.com/share/share.php?type=8&title="
							+ encodeURIComponent(document.title) + '&vaddr='
							+ encodeURIComponent(document.location), '51',
					'toolbar=0,status=0,width=780,height=700,left='
							+ (screen.width - 780) / 2 + ',top='
							+ (screen.height - 700) / 2);
			window.opener.focus();
		} catch (e) {
		}
	} else if (m == "tsohu") {// 非官方
		void ((function(s, d, e) {
			var f = 'http://t.sohu.com/third/post.jsp?link=', u = d.location + ext;
			function a() {
				if (!window
						.open(
								[ f, e(u) ].join(''),
								'tsohu',
								[
										'toolbar=0,status=0,resizable=1,width=660,height=470,left=',
										(s.width - 660) / 2, ',top=',
										(s.height - 470) / 2 ].join('')))
					u.href = [ f, e(u) ].join('');
			}
			;
			if (/Firefox/.test(navigator.userAgent))
				setTimeout(a, 0);
			else
				a();
		})(screen, document, encodeURIComponent));
	} else if (m == "leshou") {
		window
				.open('http://leshou.com/post?act=shou&reuser=&url='
						+ encodeURIComponent(location.href) + '&title='
						+ encodeURIComponent(document.title)
						+ '&intro=&tags=&tool=1', 'leshou',
						'scrollbars=yes,width=700,height=500,left=80,top=80,status=no,resizable=yes');
		return false;
	} else if (m == "vivi") {
		var sel = '' + (document.getSelection ? document.getSelection()
				: document.selection.createRange().text);
		var url = document.location.href;
		var title = document.title;
		void (window
				.open('http://vivi.sina.com.cn/collect/icollect.php?title='
						+ escape(title) + '&url=' + escape(url) + '&desc='
						+ escape(sel), '_blank',
						'scrollbars=no,width=480,height=480,left=75,top=50,status=no,resizable=yes'))
	} else if (m == "bai") {
		void ((function(s, d, e) {
			var f = 'http://bai.sohu.com/share/blank/addbutton.do?link=', u = d.location + ext, l = d.title, p = [
					e(u), '&title=', e(l) ].join('');
			function a() {
				if (!window
						.open(
								[ f, p ].join(''),
								'sohushare',
								[
										'toolbar=0,status=0,resizable=1,width=480,height=340,left=',
										(s.width - 480) / 2, ',top=',
										(s.height - 340) / 2 ].join('')))
					u.href = [ f, p ].join('');
			}
			;
			if (/Firefox/.test(navigator.userAgent))
				setTimeout(a, 0);
			else
				a();
		})(screen, document, encodeURIComponent));
	} else if (m == "qzone") {// 未开放分享
		window.open(
				"http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url="
						+ encodeURIComponent(document.location + ext), 'qzone',
				'toolbar=0,status=0,width=900,height=760,left='
						+ (screen.width - 900) / 2 + ',top='
						+ (screen.height - 760) / 2);
	} else if (m == "live") {
		window
				.open(
						"https://skydrive.live.com/sharefavorite.aspx/.SharedFavorites/?marklet=1&mkt=zh-CN&top=1&url="
								+ encodeURIComponent(document.location)
								+ "&title="
								+ encodeURIComponent(document.title), 'live',
						'toolbar=0,status=0,width=850,height=530,left='
								+ (screen.width - 850) / 2 + ',top='
								+ (screen.height - 530) / 2);
	} else if (m == "greader") {
		var b = document.body;
		var GR________bookmarklet_domain = 'http://www.google.com';
		if (b && !document.xmlVersion) {
			void (z = document.createElement('script'));
			void (z.src = 'http://www.google.com/reader/ui/link-bookmarklet.js');
			void (b.appendChild(z));
		} else {
		}
	} else if (m == "itieba") {// 未开放分享
		var sendT = {
			getContent : function() {
				var allPageTagss = document.getElementsByTagName("div");
				for ( var i = 0; i < allPageTagss.length; i++) {
					if (allPageTagss[i].className == 'content') {
						return allPageTagss[i].innerHTML;
					} else if (allPageTagss[i].className == 'content_txt') {

						return allPageTagss[i].getElementsByTagName("P")[0].innerHTML;
					}

				}
			}
		}
		var itieba_share = 'http://tieba.baidu.com/i/sys/share?link='
				+ encodeURIComponent(window.location.href) + '&type='
				+ encodeURIComponent('text') + '&title='
				+ encodeURIComponent(document.title.substring(0, 76))
				+ '&content=' + encodeURIComponent(sendT.getContent());
		if (!window
				.open(itieba_share, 'itieba',
						'toolbar=0,resizable=1,scrollbars=yes,status=1,width=626,height=436')) {
			location.href = itieba_share;
		}
	} else if (m == "hexun") {
		var t = document.title;
		var u = location.href;
		var e = document.selection ? (document.selection.type != 'None' ? document.selection
				.createRange().text
				: '')
				: (document.getSelection ? document.getSelection() : '');
		void (window.open('http://bookmark.hexun.com/post.aspx?title='
				+ escape(t) + '&url=' + escape(u) + '&excerpt=' + escape(e),
				'HexunBookmark',
				'scrollbars=no,width=600,height=580,status=no,resizable=yes,left='
						+ (screen.width - 600) / 2 + ',top='
						+ (screen.height - 580) / 2));
	} else if (m == "t163") {// 待完善--非官方
		(function() {
			var url = 'link=http://www.shareto.com.cn/&source='
					+ encodeURIComponent('网易新闻   ') + '&info='
					+ encodeURIComponent(document.title) + ' '
					+ encodeURIComponent(document.location.href);
			window
					.open(
							'http://t.163.com/article/user/checkLogin.do?'
									+ url + '&' + new Date().getTime(),
							't163',
							'height=330,width=550,top='
									+ (screen.height - 280)
									/ 2
									+ ',left='
									+ (screen.width - 550)
									/ 2
									+ ', toolbar=no, menubar=no, scrollbars=no,resizable=yes,location=no, status=no');
		})()
	} else if (m == "xianguo") {
		void (function() {
			var d = document, e = encodeURIComponent, s1 = window.getSelection, s2 = d.getSelection, s3 = d.selection, s = s1 ? s1()
					: s2 ? s2() : s3 ? s3.createRange().text : '', r = 'http://xianguo.com/service/submitfav/?link='
					+ e(d.location.href)
					+ '&title='
					+ e(d.title)
					+ '&notes='
					+ e(s), x = function() {
				if (!window
						.open(r + '&r=0', 'xgfav',
								'toolbar=0,resizable=1,scrollbars=yes,status=1,width=800,height=600'))
					location.href = r + '&r=1'
			};
			if (/Firefox/.test(navigator.userAgent)) {
				setTimeout(x, 0)
			} else {
				x()
			}
		})()
	} else if (m == 'hotmail') {
		window.open("http://mail.live.com/secure/start?action=compose&subject="
				+ encodeURIComponent(document.title) + "&body="
				+ encodeURIComponent(document.location.href + ext), 'hotmail',
				'toolbar=0,status=0,width=1010,height=700,left='
						+ (screen.width - 1010) / 2 + ',top='
						+ (screen.height - 700) / 2);
	} else if (m == "delicious") {
		(function() {
			f = 'http://delicious.com/save?url='
					+ encodeURIComponent(window.location.href + ext) + '&title='
					+ encodeURIComponent(document.title) + '&v=5&';
			a = function() {
				if (!window
						.open(f + 'noui=1&jump=doclose', 'deliciousuiv5',
								'location=yes,links=no,scrollbars=no,toolbar=no,width=550,height=550'))
					location.href = f + 'jump=yes'
			};
			if (/Firefox/.test(navigator.userAgent)) {
				setTimeout(a, 0)
			} else {
				a()
			}
		})()
	} else if (m == "digg") {
		window.open("http://digg.com/submit?type=0&url="
				+ encodeURIComponent(window.location.href + ext), 'digg',
				'toolbar=0,status=0,width=965,height=600,left='
						+ (screen.width - 965) / 2 + ',top='
						+ (screen.height - 600) / 2);
	} else if (m == "translate") {
		// 网页内翻译
		// var d = document;
		// var b = d.body;
		// var v = b.insertBefore(d.createElement('div'), b.firstChild);
		// v.id = 'google_translate_element';
		// v.style.display = 'none';
		// var z = d.createElement('script');
		// z.src =
		// 'http://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
		// b.appendChild(z);
		// 新窗口翻译
		var t = ((window.getSelection && window.getSelection())
				|| (document.getSelection && document.getSelection()) || (document.selection
				&& document.selection.createRange && document.selection
				.createRange().text));
		var e = (document.charset || document.characterSet);
		if (t != '') {
			window.open('http://translate.google.cn/?text=' + t
					+ '&hl=zh-CN&langpair=auto|zh-CN&tbb=1&ie=' + e,
					'gtranslate');
		} else {
			window.open('http://translate.google.cn/translate?u='
					+ encodeURIComponent(location.href)
					+ '&hl=zh-CN&langpair=auto|zh-CN&tbb=1&ie=' + e,
					'gtranslate');
		}
		;
	} else if (m == "buzz") {
		window
				.open(
						"http://www.google.com/buzz/post?hl=zh-CN&url="
								+ encodeURIComponent(window.location.href),
						'buzz',
						"width=716,height=480,location=yes,scrollbars=yes,menubar=no,toolbar=no,dialog=yes,alwaysRaised=yes");
	} else if (m == "pdf") {
		var authorId = "ECF83C2F-F3A3-45DE-8A44-2A36A9A9A1FB";
		var pageOrientation = "1";
		var topMargin = "0.5";
		var bottomMargin = "0.5";
		var leftMargin = "0.5";
		var rightMargin = "0.5";
		function savePageAsPDF() {
			var sUriRequest = "";

			sUriRequest = "author_id=" + authorId;
			sUriRequest += "&page=" + pageOrientation;
			sUriRequest += "&top=" + topMargin;
			sUriRequest += "&bottom=" + bottomMargin;
			sUriRequest += "&left=" + leftMargin;
			sUriRequest += "&right=" + rightMargin;

			// savepageaspdf.pdfonline.com
			var pURL = "http://savepageaspdf.pdfonline.com/pdfonline/pdfonline.asp?cURL="
					+ escape(document.location.href) + "&" + sUriRequest;
			window
					.open(pURL, "PDFOnline",
							"scrollbars=yes,resizable=yes,width=920,height=800,menubar,toolbar,location");
		}
		savePageAsPDF();
	} else if (m == "hi") {
		window
				.open(
						'http://apps.hi.baidu.com/share/?url=' + encodeURIComponent(location.href)+ '&title='
						+ encodeURIComponent(document.title),
						'baiduhi',
						'scrollbars=no,width=820,height=550,status=no,resizable=yes,left='
								+ (screen.width - 820) / 2 + ',top='
								+ (screen.height - 550) / 2);
	} else if (m == "reddit") {
		var href = 'http://www.reddit.com/submit?url='
				+ encodeURIComponent(location.href) + '&title='
				+ encodeURIComponent(document.title);
		window.open(href, 'reddit',
				'toolbar=0,status=0,width=900,height=740,left='
						+ (screen.width - 900) / 2 + ',top='
						+ (screen.height - 740) / 2);
	} else if (m == "t139") {
		window.open('http://www.139.com/share/share.php?title='
				+ encodeURIComponent(document.title) + '&url='
				+ encodeURIComponent(location.href), 't139',
				'width=490,height=340,left=' + (screen.width - 490) / 2
						+ ',top=' + (screen.height - 340) / 2);
	} else if (m == "myspace") {
		(function() {
			window.open(
					'http://www.myspace.cn/Modules/PostTo/Pages/DefaultMblog.aspx?t='
							+ encodeURIComponent(document.title) + '&u='
							+ encodeURIComponent(location.href)
							+ '&source=bookmark', 'myspace',
					'width=495,height=450,resizable=yes,left='
							+ (screen.width - 495) / 2 + ',top='
							+ (screen.height - 450) / 2);
		})();
	} else if (m == "ymail") {
		window.open("http://compose.mail.yahoo.com/?subject="
				+ encodeURIComponent(document.title) + "&body="
				+ encodeURIComponent(document.location), 'ymail',
				'toolbar=0,status=0,width=760,height=670,left='
						+ (screen.width - 760) / 2 + ',top='
						+ (screen.height - 670) / 2);
	} else if (m == "csdn") {
		var d = document;
		var t = d.selection ? (d.selection.type != 'None' ? d.selection
				.createRange().text : '') : (d.getSelection ? d.getSelection()
				: '');
		void (saveit = window
				.open('http://wz.csdn.net/storeit.aspx?t=' + escape(d.title)
						+ '&u=' + escape(d.location.href) + '&c=' + escape(t),
						'csdn',
						'scrollbars=no,width=600,height=310,status=no,resizable=yes,left='
						+ (screen.width - 600) / 2 + ',top='
						+ (screen.height - 310) / 2));
		saveit.focus();
	} else if (m == "youdao") {
		void (window
				.open(
						'http://shuqian.youdao.com/manage?a=popwindow&title='
								+ encodeURIComponent(document.title) + '&url='
								+ encodeURIComponent(document.location),
						'youdao',
						'height=200, width=590,toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no,left='
						+ (screen.width - 590) / 2 + ',top='
						+ (screen.height - 200) / 2));
	} else if (m == "facebook") {
		void (window
				.open(
						'http://www.facebook.com/sharer.php?u='
						 		 + encodeURIComponent(document.location + ext) + '&t='
								 + encodeURIComponent(document.title),
						'facebook',
						'height=260, width=590,toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no,left='
						+ (screen.width - 590) / 2 + ',top='
						+ (screen.height - 260) / 2));
	} else if (m == "twitter") {
		void (window
				.open(
						'http://twitter.com/home?status='
								+ encodeURIComponent(document.title) + ' '
								+ encodeURIComponent(document.location + ext),
						'twitter',
						'height=260, width=590,toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no,left='
						+ (screen.width - 590) / 2 + ',top='
						+ (screen.height - 260) / 2));
	} else if (m == 'link') {
		if ($('#share_link_inpbox').val() == '')
		{
			// fill data
			$('#share_link_inpbox').val(document.title + location.href + ext);
			if ($.browser.msie)
			{
				copyText($('#share_link_inpbox').val());
			}
		}
		$('#share_link').css('display', 'block');
	} else if (m == 'msn') {
		void (window
				.open(
						'http://profile.live.com/badge?title='
								+ encodeURIComponent(document.title) + '&url='
								+ encodeURIComponent(document.location + ext),
						'MSN',
						'height=420, width=590,toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no,left='
						+ (screen.width - 590) / 2 + ',top='
						+ (screen.height - 420) / 2));
	}

	// 33twwiter,facebook,digu,有道,白社会(mop),csdn,yahoo bazz,myspace.com(参考chinadaily.com.cn)
	// 待开发:和讯微博
	return false;
}
// function googleTranslateElementInit() {
// new google.translate.TranslateElement({}, 'google_translate_element');
// }
function st_addBookmark(title){
    var url = parent.location.href;
    if (window.sidebar) { // Mozilla Firefox Bookmark
        window.sidebar.addPanel(title, url,"");
    } else if(document.all) { // IE Favorite
        window.external.AddFavorite( url, title);
    } else if(window.opera) { // Opera 7+
        return false; // do nothing
    } else { 
         alert('请按 Ctrl + D 为chrome浏览器添加书签!');
    }
}