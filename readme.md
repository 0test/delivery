Событие OnSHKcalcTotalPrice
Конфиг:
```
{
  "deliveryID": [
    {
      "label": "идентификатор документа \"доставка\"",
      "type": "string",
      "value": "1",
      "default": "1",
      "desc": ""
    }
  ]
}
```

Открыть 
```classes /class.shopkeeper.php```
найти
```
list($totalItems,$totalPrice) = $this->getTotal($purchases,$addit_params);
```
Добавить
```
$purchases = unserialize($_SESSION['purchases']);
```
Создать товар с именем Доставка, его ID записать в конфиг плагина

<p>На странице:</p>

```javascript
	$(document).ready(function(){
		$("select").change( function() {
			//отправка полей формы shopOrderForm в плагин 
			jQuery.fillCart($('#shopOrderForm'));
		});
		//обновление корзины после загрузки или перезагрузки страницы
		jQuery.fillCart($('#shopOrderForm'));
	});
```
