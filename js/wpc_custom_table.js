(function( $ ){
	$(document).ready(function(){
		$("#wpc_tables").change(function(){
			var wpc_shortcode_id = $("#wpc_shortcode_id").val();
			$.getJSON(wpc_localize_data.wpc_ajax_path,{"action":"get_keys","table_name":$(this).val(),"post_id":wpc_shortcode_id}).done(function(data){
				var primary_columns=[];
				var all_columns=[];
				var columns_data="";
				for(k=0; k<data.length; k++){
					var checked_string="";
					if(data[k][1]==1){					
						checked_string = "checked='checked'";
					}
					columns_data+="<tr><td><input type='checkbox' name='columns_name["+k+"][]' value='"+data[k][0]+"' "+checked_string+">"+data[k][0]+"</td><td><input type='text' value='"+data[k][2]+"' name='column_title["+k+"][]'></td></tr>";
				}

				$("#columns_data").html("<table>"+columns_data+"</table>");
				
			});

			$.getJSON(wpc_localize_data.wpc_ajax_path,{"action":"get_primary_keys","table_name":$(this).val()}).done(function(data){
				if(data.length>0){
					
				}
			});
		});

		setTimeout(function(){
			jQuery("#wpc_tables").trigger("change");
		},300);

		//function get
	});

	

})( jQuery );