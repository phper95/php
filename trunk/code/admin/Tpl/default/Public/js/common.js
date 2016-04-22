//批量删除，通用
function foreverdel(id, obj){
    var keyValue;
    if (id){
        keyValue = id;
    }else {
        keyValue = getSelectCheckboxValues();
    }

    if (!keyValue){
        alert('请选择删除项！');
        return false;
    }
    
    if (window.confirm('确实要永久删除选择项吗？')){
    	location.href =  obj.href+"&id="+keyValue;
    }
    return false;
}
// 设置查询条件初始值
function searchInitValue (arr){
	var obj = null;
	for (a in arr) {
		if ($.trim($('#'+a).val()) == '') {
			$('#'+a).val(arr[a]);
		}
	}
}


/**
 * 建立一個可存取到該file的url
 * PS: 瀏覽器必須支援HTML5 File API
 */
function booGetObjectURL(file) {
	var url = null ; 
	if (window.createObjectURL!=undefined) { // basic
		url = window.createObjectURL(file) ;
	} else if (window.URL!=undefined) { // mozilla(firefox)
		url = window.URL.createObjectURL(file) ;
	} else if (window.webkitURL!=undefined) { // webkit or chrome
		url = window.webkitURL.createObjectURL(file) ;
	}
	return url ;
}
