jQuery(document).ready(function($) {

    ///////// Xử lý thuộc tính sản phẩm ////////

    //Những thuộc tính cho phép
    var AttributeArray = {"trongluong" : "Trọng lượng", "chieudai" : "Chiều dài", "chieurong" : "Chiều rộng", "chieucao" : "Chiều cao", "duongkinh" : "Đường kính", "mausac" : "Màu sắc", "doday" : "Độ dày", "sotrang" : "Số trang"};
    //Tạo danh sách option
    var AttributeOptionList = $.map(AttributeArray, function(lcn){
            return '<option value="' + lcn + '">' + lcn + '</option>'
        }).join('');

    AttributeOptionList = "<select class='message_Attribute_name_field'>" + AttributeOptionList + "</select>";

    var $AttributeCodeField = $('input[name="message_Attribute_field"]');
    $AttributeCodeField.hide();
    var $AttributeTd = $AttributeCodeField.closest("td");
    $AttributeTd.append("<table id='AttributeTable'><tr id='lastRow'><td><Button class='message_Attribute_button button' data-action='add'>Thêm thuộc tính</button></td><td colspan='2'><i>Nhập số kèm theo đơn vị</i></td></tr></table>");
    var $AttributeTable = $('#AttributeTable');
    var $lastRow = $AttributeTable.find('#lastRow');

	if($AttributeCodeField.val().length >0 )
	{
		var Attributes = JSON.parse($AttributeCodeField.val());
		//Chuyển chỗi dữ liệu thành các hàng
	    $.each(Attributes, function(name, val) {
	    	 $lastRow.before("<tr class='message_Attribute_field_row'><td>" + AttributeOptionList + "</td><td><input class='message_Attribute_value_field' type='text' value='" + val + "' /></td><td><Button class='message_Attribute_button button' data-action='delete'>Xóa</button><td></tr>");
	    	 $('.message_Attribute_name_field:last').val(name);
	    });
	}


    $('#AttributeTable').on("click", ".message_Attribute_button",function() {
    	var action = $(this).data('action');
    	switch(action)
    	{
    		case "add" :
    			$lastRow.before("<tr class='message_Attribute_field_row'><td>" + AttributeOptionList + "</td><td><input class='message_Attribute_value_field' type='text' value='' /></td><td><Button class='message_Attribute_button button' data-action='delete'>Xóa</button><td></tr>");
    			break;
    		case "delete":
    			$(this).closest('tr').remove();
    			break;
    	}
    	return false;
    });

	//hàm xử lý phần field của thuộc tính sản phẩm
	function xuLyThuocTinh()
	{
		var haveFalse = false;
		var data = {};
		//Duyệt qua lần lượt các dòng
		$AttributeTable.find(".message_Attribute_field_row").each(function(index, el) {
			//Tìm hai input tên và giá trị trong dòng
			var $name = $(this).find(".message_Attribute_name_field");
			var $value = $(this).find(".message_Attribute_value_field");
			var sname = $name.val();
			var svalue = $value.val()

			if( (sname.length > 0 ) && ( svalue.length > 0 ) )
			{
				data[sname] = svalue;
				$name.prop('disabled', true);
				$value.prop('disabled', true);
			}
			else
			{
				haveFalse = true;
				$name.closest('.message_Attribute_field_row').css({background: 'red'});
			}
		});
		console.log(data);
		if(!haveFalse)
		{
			//Loại bỏ dấu phẩy (,) cuối cùng.
			//Nạp dữ liệu vào field.
			$AttributeCodeField.val(JSON.stringify(data));
			return true;
		}
		else
		{
			return false;
		}
	}

	///////////// xử lý variant //////////



    /////// Xử lý khi form submit
	$( "#post" ).submit(function( event ) {
		if ( xuLyThuocTinh() )
			return true;
		else
			//if return false, submit will be failse
			return true;
	});
});