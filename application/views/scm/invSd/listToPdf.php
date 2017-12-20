<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $transType==150701 ? '销售出库单' :'销售退货单'?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<style type="text/css">
table{
border:1px;
border-collapse:collapse;
font-size:8px;
}
table tr{
border: 0px solid #f5f6f8; 
}
table tr th,table tr td{
border:solid #000000;
border-width:0px 0px 0px 0px;
padding:2px;
}

</style>
</head>
<body>
<div style="margin-left:450px;">
  			<h3>销售出库记录</h3>
</div>
<table class="list" align="center">
			<thead>
				<tr>
				<th width="30" align="center">单据日期</th>
				    <th width="50" align="center">单据编号</th>
					<th width="60" align="center">采购方</th>
<!-- 					<th width="80" align="center">采购金额</th> -->
<!-- 					<th width="80" align="center">折扣率(%)</th> -->
<!-- 					<th width="80" align="center">折扣额</th> -->
<!-- 					<th width="80" align="center">折后金额</th> -->
				    <th width="20" align="center">制单人</th>
					<th width="20" >审核人</th>
					<th width="100" align="center">单据备注</th>
					<th width="60" align="center">商品</th>
					<th width="20" align="center">单位</th>
					<th width="30" align="center">数量</th>	
		<!--			<th width="30" align="center">采购单价</th>	
		 			<th width="50" align="center">折扣率(%)</th>
					<th width="30" align="center">折扣额</th>	 
					<th width="30" align="center">金额</th>					
					<th width="20" align="center">币种</th>	-->
					<th width="60" align="center">仓库</th>	
					<th width="100" align="center">备注</th>	
<!--					<th width="100" align="center">源单号</th>-->
				</tr>
			</thead>
			<tbody>
			    <?php 
				  $i = 1;
				  $n = 1;
				  $qty = $amount = 0;
				  foreach($list1 as $arr=>$row) {
				      foreach($list2 as $arr1=>$row1) {
						  if ($row1['iid']==$row['id']) {
						      $n++;   
						  }
					  }
				?>
				<tr target="id">
				<td rowspan="<?php echo $n?>" ><?php echo $row['billDate']?></td>
					<td rowspan="<?php echo $n?>" ><?php echo $row['billNo']?></td>
					<td rowspan="<?php echo $n?>" ><?php echo /*$row['contactNo'].' '.*/$row['contactName'];?></td>
		<!-- 			<td rowspan="<?php echo $n?>" ><?php echo $row['totalAmount']?></td>
					<td rowspan="<?php echo $n?>" ><?php echo $row['disRate']?></td>
					<td rowspan="<?php echo $n?>" ><?php echo $row['disAmount']?></td>
					<td rowspan="<?php echo $n?>" ><?php echo $row['amount']?></td>
					 -->
					<td rowspan="<?php echo $n?>" ><?php echo $row['userName']?></td>
					<td rowspan="<?php echo $n?>" ><?php echo $row['checkName']?></td>
				<td rowspan="<?php echo $n?>" ><?php echo $row['description']?></td>
				<?php 
				$i = 1;
				foreach($list2 as $arr1=>$row1) {
				    if ($row1['iid']==$row['id']) {
					   $qty += abs($row1['qty']);
					   $amount += abs($row1['amount']);
					   if ($i==1) {
				?>				    
					<td style="border-left:0px;"><?php echo $row1['invName']?></td>
					<td ><?php echo $row1['mainUnit']?></td>
					<td ><?php echo abs($row1['qty'])?></td>
	<!--				<td ><?php echo $row1['price']?></td>
	 				<td ><?php echo $row1['taxRate']?></td>
					<td ><?php echo $row1['discountRate']?></td>
					<td ><?php echo abs($row1['amount'])?></td>
					<td ><?php echo $row1['currencyCode']?></td>-->
					<td ><?php echo $row1['locationName']?></td>
					<td ><?php echo $row1['description']?></td>
				</tr>
				<?php } else {?>
				<tr target="id">					
					<td  style="border-left:0px;"><?php echo $row1['invName']?></td>
					<td ><?php echo $row1['mainUnit']?></td>
					<td ><?php echo abs($row1['qty'])?></td>
		<!--			<td ><?php echo $row1['price']?></td>
		 			<td ><?php echo $row1['taxRate']?></td>
					<td ><?php echo $row1['discountRate']?></td> 
					<td ><?php echo abs($row1['amount'])?></td>
					<td ><?php echo $row1['currencyCode']?></td>-->
					<td ><?php echo $row1['locationName']?></td>
					<td ><?php echo $row1['description']?></td>
				</tr>
				<?php }$i++;}}?>
				<tr target="id">		
					<td style="border-left:0px;">合计</td>
					<td ></td>
					<td ><?php echo $qty?></td>
					<td ></td>
			<!-- 		<td ></td>
					<td ></td> 
					<td ><?php echo $amount?></td>
					<td ></td>
					<td ></td>-->
					<td ></td>
				</tr>
				<?php $qty = $amount = 0;$n = 1;}?>
 </tbody>
</table>		 
</body>
</html>		