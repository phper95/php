function booAlert(msg, title, btn) {
	if (typeof title == 'undefined') title = '温馨提示';
	if (typeof btn == 'undefined') btn = '确定';
	var w = $('#main').width();
	var id = 'booDialog_' + (new Date()).valueOf();
	var html = '<div id="' + id + '" style="left:0;top:0;position:fixed;width:100%;height:100%;z-index:999;background-color:rgba(0,0,0,0.5);">'
				+ '<table style="height:90%;margin:0 auto;position:relative;width:' + (w * 0.88) + 'px">'
					+ '<tr><td>'
						+ '<div style="border-radius:2px;background-color:#fff;box-shadow:1px 1px 8px #333;">'
							+ '<h3 style="border-bottom:2px solid #00b9ff;color:00b9ff;padding:0.8em 0.8em;">' + title + '</h3>'
							+ '<div style="color:#333;padding:0.6em 1em;">' + msg + '</div>'
							+ '<div onclick="$(\'#' + id + '\').remove();" style="border-top:1px solid #e9e9e9;line-height:3.2em;text-align:center;color:#333;font-size:0.8em;">'
								+ btn 
							+ '</div>' 
						+ '</div>' 
					+ '</td></tr>' 
				+ '</table>' 
			+ '</div>';
	$('body').append(html);
}

function GetQueryString(name)
{
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg);
     if(r!=null)return  unescape(r[2]); return null;
}