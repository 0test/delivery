<?php
//plugin deliveryCalc
//[X] OnSHKcalcTotalPrice
//Конфигурация плагина: &deliveryID=идентификатор документа "доставка";string;0 

$e = &$modx->Event;
$output = "";

if($e->name=='OnSHKcalcTotalPrice'){

//блок поиска индекса доставки в корзине
//и вычисление стоимости корзины без учёта доставки
$deliveryPrice=0;  //стоимость доставки
$indexDeliv=null;  //индекс доставки в корзине
$totalPrice1=0;  //стоимости корзины без учёта доставки
foreach($purchases as $i => $goods){
    if ($goods[0]==$deliveryID){
        $indexDeliv=$i;
    }else    $totalPrice1 +=$goods[2]*$goods[1];
}

if(IN_MANAGER_MODE!="true"){
   
//если изменение кол-ва товара или удаление товара то наследуем параметры доставки и оплаты из сессии
if( ($_POST['action']=='recount') || ($_POST['action']=='delete') ){
    $payment = $_SESSION['payment'];
    $delivery = $_SESSION['delivery'];
}else{
    //сохраняем в сессию текущие параметры доставки и оплаты, либо обнуляем при отсутствии полей 
    //формы оформления заказа в запросе 
    $payment = isset($_POST['payment']) ? ($_SESSION['payment'] = $_POST['payment']) : ($_SESSION['payment'] = null);
    $delivery = isset($_POST['delivery']) ? ($_SESSION['delivery'] = $_POST['delivery']) : ($_SESSION['delivery'] = null);
}

//выбор или рассчёт стоимости доставки
switch($delivery){
case "RP": //Почта России
    if($payment=="COD"){ //оплата - наложенный платёж
        $deliveryPrice =  100; /*здесь может быть рассчёт стоимости доставки для (Почта России-наложенный платёж)*/
    }elseif($payment=="WEBMONEY"){ //оплата - Webmoney 
        $deliveryPrice =  150;/*здесь может быть рассчёт стоимости доставки (Почта России - Webmoney)*/
    }
    break;
case "COURIER": //доставка курьером
    if($payment=="COD"){ //оплата - на руки курьеру
        $deliveryPrice=50;
    }elseif($payment=="WEBMONEY"){//оплата - Webmoney
        $deliveryPrice=50;
    }
    break;
case "EMS": 
    $deliveryPrice=300;
    break;
case "TRANS-COMP": 
    $deliveryPrice=300;
    break;
default:         
    $deliveryPrice=0;
}


if ($deliveryPrice) {/*если стоимость доставки больше 0, значит в корзину нужно включить доставку, либо исключить при 0*/
    //не обязательная стр. здесь и далее $addit_params = unserialize($_SESSION['addit_params']);
  
    if (isset($indexDeliv)){/*если в корзине уже имеется доставка, то меняем её цену*/
        unset($purchases[$indexDeliv]);
        $purchases[$indexDeliv] = array(0=>$deliveryID, 'catalog'=>0, 1=>1, 2=>$deliveryPrice);
        //$addit_params[$indexDeliv] = array();
    }else{
        $purchases[] = array(0=>$deliveryID, 'catalog'=>0, 1=>1, 2=>$deliveryPrice);
        //$addit_params[] = array();
    }
  
    $_SESSION['purchases'] = serialize($purchases);
    //$_SESSION['addit_params'] = serialize($addit_params);
}else{
    if (isset($purchases[$indexDeliv])){
        unset($purchases[$indexDeliv]);
        $_SESSION['purchases'] = serialize($purchases);
    
        //$addit_params = unserialize($_SESSION['addit_params']);
        //unset($addit_params[$indexDeliv]);
        //$_SESSION['addit_params'] = serialize($addit_params);
    }
}

//в $output - стоимость доставки
$output = $deliveryPrice;
}elseif(isset($purchases[$indexDeliv])){
    $output = $purchases[$indexDeliv][2];
}

//скидка
$discount1=5; //величина скидки в процентах    
if($totalPrice1>=500){ //если стоимость товаров в корзине больше 500, то скидка 5%
    $output += round($totalPrice1 * (1-$discount1/100), 2);

    /*плэйсхолдер с информацией о предоставленной скидке, можно использовать в чанке корзины*/
    $modx->setPlaceholder('discountInfo', 'Ваша скидка 5%'); 
    
}else $output += $totalPrice1;
    
$e->output($output);
}