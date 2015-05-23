jQuery(document).ready(function($){
		$('.addtocart').submit(function(){
			var Id =$(this).attr("id");
			var data = {action: 'eshop_special_action',post:$('#'+Id).serialize() };
			$.post(""+eshopCartParams.adminajax, data,
				function(response){
				$('#'+Id +" .eshopajax").after(this).fadeIn(parseInt(eshopCartParams.addfadein)).html(response).fadeOut(parseInt(eshopCartParams.addfadeout));
				setTimeout (cleareshopCart,parseInt(eshopCartParams.cartcleardelay));
				setTimeout (doeshopRequest,parseInt(eshopCartParams.cartdelay));
				setTimeout (cleareshopRequest,parseInt(eshopCartParams.cartupdate));
			});
			function doeshopRequest(){
				var tdata = {action: 'eshop_cart'};
				$.post(""+eshopCartParams.adminajax, tdata,
				function(response){
					$(".ajaxcart").after(this).fadeOut(parseInt(eshopCartParams.cartfadeout)).html(response).fadeIn(parseInt(eshopCartParams.cartfadein));
				});
			}
			function cleareshopRequest(){
				$(".eshopajax").empty();
			}
			function cleareshopCart(){
				$(".ajaxcart").after();
			}
			return false;
		});

	});