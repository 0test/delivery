<p>Example:</p>

<code>
	$(document).ready(function(){
		$("select").change( function() {
			//отправка полей формы оформления заказа shopOrderForm в плагин
			jQuery.fillCart($('#shopOrderForm'));
		});
		//обновление корзины после загрузки или перезагрузки страницы, например
		jQuery.fillCart($('#shopOrderForm'));
	});
</code>