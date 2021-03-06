<?php
	//indicate that this is the main page [needed in modules pages -- to stop calling lib in the module pages]
	$mainpage=true;
	//include lib.php (this file is a liberary for most common modules and function used by the system)
	if(file_exists("lib.php"))
		require("lib.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="softex, software, house, egypt, cairo, online, store, e-commerce, commerce" />
<meta name="description" content="Add, organize, show, and sell your products online" />
<meta name="author" content="Ibrahim Samir, himalking@yahoo.com" />
<meta name="copyright" content="softex software house" />
<title>Print Shopping cart items</title>
<link href="osstyles.css" rel="stylesheet" type="text/css" />

<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style>
</head>

<body>
<?php
	//get client's company name for header title
	$company=consumeWebservice("getCompany",array(clientid=>$clientid));
	$company=$company->node->attributes()->company;
	
	//get all items of the cart
	//get cart items from the cookie
	//if there is a cookie defiened and there is items in the cart
	if(isset($_COOKIE['cart']) && count($_COOKIE['cart'])){
		//generate list of IDs
		foreach($_COOKIE['cart'] as $itemid => $quantity){
			$id_list.="&id[]=".$itemid;
		}
		//consume a given webservice using given $parametars string
		$items=consumeWebservice2("getCartItems",$id_list);
	
		//caluculate bill info
		//total before discount
		$total=0;
		//total after discount
		$subtotal=0;
		//total discounts
		$saves=0;
		
		//loop through cart items
		foreach($items->node as $node){
			$itemid=(string)$node->attributes()->itemid;
			$itemprice=(int)$node->attributes()->price;
			$itemdiscount=(int)$node->attributes()->discount;
			
			$total+=$itemprice*$_COOKIE['cart'][$itemid];
			if(chk_storesetting("discount")){
				$saves+=$itemdiscount*$_COOKIE['cart'][$itemid];
			}
		}//end foreach
		
		if(chk_storesetting("discount"))
			$subtotal=$total-$saves;
		else
			$subtotal=$total;
?>
<div style="height:10px"></div>
<div align="center" class="title3" ><?=$company?></div>
<div style="height:10px"></div>

<input name="action" type="hidden" id="action" value="update" />
<table width="650" border="0" align="center" cellpadding="0" cellspacing="0">
  <!--DWLayoutTable-->
  <tr>
    <td width="650" height="180" valign="top">
	<div style="width:650px;border:#CFCFCF 1px solid">
	
		<div class="block2_B2" style="width:309px; border-right:#BCBCBC 1px solid">Item</div>
		<div class="block2_B2" style="width:88px; border-left:#FFFFFF 1px solid; border-right:#BCBCBC 1px solid">Price</div>
		<div class="block2_B2" style="width:78px;border-left:#FFFFFF 1px solid; border-right:#BCBCBC 1px solid">You Save </div>
		<div class="block2_B2" style="width:78px;border-left:#FFFFFF 1px solid; border-right:#BCBCBC 1px solid">Quantity</div>
		<div class="block2_B2" style="width:89px;border-left:#FFFFFF 1px solid;">Total</div>
	<?php
		//loop through cart items
		foreach($items->node as $node){
			$itemid=(string)$node->attributes()->itemid;
			$itemprice=(int)$node->attributes()->price;
			$itemdiscount=(int)$node->attributes()->discount;
	?>
      <div class="cartitems_block2">
        <div class="floatdiv" style="width:290px; padding-left:10px; padding-right:10px"><span class="title2"><strong><a href="productdetails.php?iid=<?=$itemid?>" target="_blank"><?=$node->attributes()->itemname?></a></strong></span>
          <div class="text1 size1" style="padding-top:10px; padding-bottom:5px"><strong>Item Code&nbsp;&nbsp;&nbsp;&nbsp; : </strong><?=$node->attributes()->itemcode?><br />
            <strong>Part Number :</strong> <?=$node->attributes()->partnumber?></div> 
	  </div>
	  <div class="price floatdiv" style="width:90px; text-align:center; font-weight:normal">
	   <?php
			if($itemdiscount && $itemprice && chk_storesetting("discount")){
				echo "<span class='priceoff' style='font-weight:normal'>$".$itemprice."</span><br />";
				$itemprice-=$itemdiscount;
			}
			if($itemprice)
				echo "$".$itemprice;
			else
				echo "&nbsp;"
	  ?>
	    </div>
	  <div class="floatdiv" style="width:80px; text-align:center">
	    <?php
		if(chk_storesetting("discount") && $itemprice && $itemdiscount){
	?>
	    <span class="bloxtitle1" style="font-size:14px">
	      $<?=$itemdiscount?> </span><span class="text2"> (<?=round(($itemdiscount/$itemprice)*100, 1)?>%)</span>
	    <?php
		}//end if
		else echo "<img src='images/blank.gif' width='1' height='1' />";
	?>
	    </div>
	  <div class="floatdiv" style="width:80px; text-align:center">
	    <input name="quantities[<?=$itemid?>]" type="text" class="fields" id="quantities[<?=$itemid?>]" style="height:15px;width:30px" value="<?=$_COOKIE['cart'][$itemid]?>" onKeyPress="return false" />
	    </div>
	  <div class="floatdiv" style="width:90px; text-align:center"><span class="price">
	    <?php
		if($itemprice){
			echo "$".$itemprice*$_COOKIE['cart'][$itemid];
		}
	  ?>
	    </span></div>
		<div style="clear:both; height:5px"><img src="images/blank.gif" width="1" height="1" /></div>
	  </div>
      <?php
		}//end foreach
	?>
	  <div class="cartitems_block2">
	    <div align="center" class="floatdiv price " style="width:310px; font-size:14px">
	      SUBTOTALS</div>
	    <div class="floatdiv" style="width:90px; text-align:center"><img src="images/blank.gif" width="1" height="1" /></div>
	    <div class="floatdiv" style="width:80px; text-align:center">
	      <?php if($saves){ ?>
	      <span class="bloxtitle1" style="font-size:14px">$<?=$saves?></span>
	      <?php }//end if ?>
	      </div>
	  
	  <div class="floatdiv" style="width:80px; text-align:center"><img src="images/blank.gif" width="1" height="1" /></div>
	    <div class="floatdiv" style="width:90px; text-align:center"><span class="price">$<?=$subtotal?></span></div>
	  <div style="clear:both; height:10px;" ><img src="images/blank.gif" width="1" height="1" /></div>
	    </div>
	</div>	</td>
  </tr>
</table>
<div align="center">
  <div class="link1" style="padding-top:15px"><a href="javascript: window.print();"><strong>PRINT</strong> <img src="images/arrow3.gif" width="16" height="15" border="0" align="absmiddle" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="javascript:window.close();"><strong>CLOSE</strong> <img src="images/arrow3.gif" width="16" height="15" border="0" align="absmiddle" /></a></div>
</div>
  <?php
	}//end if isset(cookie[cart])
?>
<div align="center" class="footer" style="padding:20px"><br />
<a href="http://www.softexsw.com" target="_blank">Powered by SOFTEXSW </a></div>

</body>
</html>