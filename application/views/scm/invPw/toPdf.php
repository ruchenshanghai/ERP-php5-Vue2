<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo '采购入库单'?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style></style>
</head>
<body>
<?php for($t=1; $t<=$countpage; $t++){?>
		<table  width="800"  align="center">
		     
			<tr height="15px">
				<td align="center" style="font-family:'宋体'; font-size:18px; font-weight:normal;height:50px;"></td>
			</tr> 
			<tr height="15px">
				<td align="center" style="font-family:'宋体'; font-size:18px; font-weight:normal;height:25px;"><?php echo '采购入库单'?></td>
			</tr>
		</table>		
		
		<table width="1100" align="center">
			<tr height="15" align="left" >
				<td width="550" style="font-family:'宋体'; font-size:14px;height:20px;">供应商：<?php echo /*$contactNo.' '.*/$contactName?> </td>
				<td width="10" ></td>
				<td width="150" >单据日期：<?php echo $billDate?></td>
				<td width="250" >单据编号：<?php echo $billNo?></td>
<!-- 				<td width="60" >币别：RMB</td> --> 
			</tr>
		</table>
		<table width="1200" border="1" cellpadding="2" cellspacing="1" align="center" style="border-collapse:collapse;border:solid #000000;border-width:1px 0 0 1px;">
		         
				<tr style="height:20px">
				    <td width="30" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; font-family:'宋体'; font-size:14px;height:15px;"  align="center">序号</td>
					<td width="420" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; font-family:'宋体'; font-size:14px;height:15px;" align="center">商品</td> 
					<td width="100" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; font-family:'宋体'; font-size:14px;height:15px;" align="center">商品型号</td>
					<td width="30" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; font-family:'宋体'; font-size:14px;height:15px;" align="center">单位</td>
					<td width="60" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; font-family:'宋体'; font-size:14px;height:15px;" align="center">采购数量</td>
					<td width="80" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; font-family:'宋体'; font-size:14px;height:15px;" align="center">仓库</td>
					<td width="80" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; font-family:'宋体'; font-size:14px;height:15px;" align="center">入库数量</td>
					<td width="80" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; font-family:'宋体'; font-size:14px;height:15px;" align="center">备注</td>	
				</tr>
				
		       <?php 
			   $i = ($t-1)*$num + 1;
			   foreach($list as $arr=>$row) {
			       if ($row['i']>=(($t-1)*$num + 1) && $row['i'] <=$t*$num) {
			   ?>
				<tr style="height:20px">
				   <td style="border:solid #000000;border-width:0 1px 1px 0;height:15px;font-family:'宋体'; font-size:12px;" align="center"><?php echo $row['i']?></td>
					<td ><?php echo $row['goods'];?></td>
					<td align="right"><?php echo $row['invSpec']?></td>
					<td align="right"><?php echo $row['unitName']?></td>
					<td align="right"><?php echo str_money(abs($row['qty']),$system['qtyPlaces'])?></td>
					<td ><?php echo $row['locationName']?></td>
					<td align="right"><?php echo $row['stockinQty']?></td>
					<td ><?php echo $row['description']?></td>					
				</tr>
				<?php 
				    $s = $row['i'];
				    }
				    $i++;
				}
				?>				

				 <?php if ($t==$countpage) {?>
				 <tr style="height:20px">
				   <td colspan="4" align="right" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px;height:15px;font-family:'宋体'; font-size:12px;">合计：</td>
					<td align="right" ><?php echo str_money(abs($totalQty),$system['qtyPlaces'])?></td>
					<td align="center"></td>
					<td align="center"></td>
					<td align="center"></td>
				</tr>
				<?php }?>
		</table>	
		
		<table  width="800" align="center">
		  <tr height="25" align="left">
				<td align="left" width="960" style="font-family:'宋体'; font-size:14px;height:25px;">备注： <?php echo $description?></td>
				<td width="0" ></td>
				<td width="0" ></td>
				<td width="0" ></td>
				<td width="0" ></td>
 
		  </tr>
		</table>	 
		
		<table  width="800" align="center">
			<tr height="25" align="left">
				<td align="left" width="250" style="font-family:'宋体'; font-size:14px;height:25px;">制单人：<?php echo $userName?> </td>
				<td width="250" style="font-family:'宋体'; font-size:14px;height:25px;">收货人签字：____________</td>
				<td width="250" style="font-family:'宋体'; font-size:14px;height:25px;">采购签字：____________</td>
				<td width="100" ></td>
				<td width="100" ></td>
 
			</tr>
		</table>	
<?php echo $t==$countpage?'':'<br><br>';}?>		
		
		
		 
</body>
</html>		