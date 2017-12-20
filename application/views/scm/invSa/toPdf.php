<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $transType==150601 ? '销售出库单' :'销售退货单'?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
td{
padding:4px 2px 2px 2px;
}
</style>
</head>
<body>
<img src="<?php echo base_url()?>/statics/css/img/wechat.png" style="position:absolute;right:100px;top:0px;z-index:-1;height:80px;"/>
<img src="<?php echo base_url()?>/statics/css/img/logo.png" style="position:absolute;right:215px;top:0px;z-index:-1;height:80px;"/>
<?php for($t=1; $t<=$countpage; $t++){?>
		<table  width="800"  align="center" style="position:absolute;left:0px;top:0px;">
		     
			<tr height="15px">
				<td align="center" style="font-size:18px; font-weight:normal;height:50px;"></td>
			</tr> 
			<tr height="15px">
				<td align="center" style="font-size:18px; font-weight:normal;height:25px;"><?php echo $transType==150601 ? '销售出库单' :'销售退货单'?></td>
			</tr>
		</table>	
		
		<table width="800" align="center" border="1" cellpadding="2" cellspacing="1" 
		style="border-collapse:collapse;border:solid black;border-width:1px 0 0 1px;font-size:10px;">
		<tr height="16" align="center">
		<td width="738" colspan="8" style="background-color:LightGrey;">送货单位</td>
		</tr>
		<tr height="16" align="left">
			<td width="20" align="center" style="background-color:LightGrey;">名称</td>
			<td colspan="3"><?php echo $system['companyName']?></td>
			<td width="30" align="center" style="background-color:LightGrey;">送货单编号</td>
			<td colspan="3"><?php echo $billNo?></td>
		</tr>
		<tr height="16" align="left">
			<td  width="20" align="center" style="background-color:LightGrey;">电话</td>
			<td width="50"><?php echo $system['phone']?></td>
			<td align="center" style="background-color:LightGrey;">传真</td>
			<td width="50"><?php echo $system['fax']?></td>
			<td align="center" style="background-color:LightGrey;">邮编</td>
			<td width="50"><?php echo $system['postcode']?></td>
			<td align="center" style="background-color:LightGrey;">送货时间</td>
			<td ></td>
		</tr>		
		<tr height="16" align="left">
			<td  width="20" align="center" style="background-color:LightGrey;">地址</td>
			<td colspan="3"><?php echo $system['companyAddr']?></td>
			<td align="center" style="background-color:LightGrey;">销售人员</td>
			<td width="30"><?php echo $staffName?></td>
			<td align="center" style="background-color:LightGrey;">手机</td>
			<td width="30"><?php echo $staffMobile?></td>
		</tr>
		<tr height="16" align="center">
		<td colspan="8" style="background-color:LightGrey;padding:4px 0 2px 0;">收货单位</td>
		</tr>
		<tr>
		<td  width="20" align="center" style="background-color:LightGrey;">名称</td>
		<td  colspan="7"><?php echo $contactName?></td>
		</tr>
		<tr>
		<td  width="20" align="center" style="background-color:LightGrey;">地址</td>
		<td  colspan="7"><?php echo $buyerAddress?></td>
		</tr>
		
		<tr height="16" align="left">
			<td align="center" style="background-color:LightGrey;">联系人</td>
			<td colspan="3"><?php echo $buyerName?></td>
			<td align="center" style="background-color:LightGrey;">电话</td>
			<td colspan="3"><?php echo $buyerMobile?></td>
		</tr>
		
		</table>
			
		<table width="900" border="1" cellpadding="2" cellspacing="1" align="center" 
		style="margin-top:10px;border-collapse:collapse;border:solid #000000;border-width:1px 0 0 1px;font-size:10px;">   
			<tr style="height:20px">
				    <td width="30" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;"  align="center">序号</td>
					<td width="220" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px;height:15px;" align="center">商品</td> 
					<td width="50" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;" align="center">商品型号</td>
					<td width="200" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;" align="center">详细说明</td> 
					<td width="30" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px;height:15px;" align="center">单位</td>
					<td width="40" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px;height:15px;" align="center">数量</td>	
					<td width="100" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px;height:15px;" align="center">备注</td>	
				</tr>
		       <?php 
			   $i = ($t-1)*$num + 1;
			   foreach($list as $arr=>$row) {
			       if ($row['i']>=(($t-1)*$num + 1) && $row['i'] <=$t*$num) {
			   ?>
				<tr style="border:solid #000000;border-width:0 1px 1px 0;padding:2px;height:15px;">
				   <td align="center"><?php echo $row['i']?></td>
					<td><?php echo $row['goods'];?></td>
					<td align="right"><?php echo $row['invSpec']?></td>
					<td  align="right"></td>
					<td align="right"><?php echo $row['unitName']?></td>
					<td align="right"><?php echo str_money(abs($row['qty']),$system['qtyPlaces'])?></td>
					<td ><?php echo $row['description']?></td>
				</tr>
				<?php 
				    $s = $row['i'];
				    }
				    $i++;
				}
				?>			
				
				<?php 
				//补全
				if ($t==$countpage) {
				    for ($m=$s+1;$m<=$t*$num;$m++) {
				?>
				<tr style="border:solid #000000;border-width:0 1px 1px 0;padding:2px;height:15px;">
				   <td width="30" align="center" style="border:solid #000000;border-width:0 1px 1px 0;height:15px;"><?php echo $m?></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<?php }}?>
		</table>

		
		<table  width="800" style="font-size:10px;">
		  <tr height="15" align="left">
				<td width="700" style="height:25px;">备注： 本出库单一式两份，客户收到货后回签一份，客户留存一份。</td>
				<td width="0" ></td>
				<td width="0" ></td>
				<td width="0" ></td>
				<td width="0" ></td> 
		  </tr>
		</table>	 
		
		<table  width="800" style="font-size:10px;">
			<tr>
				<td height="25" align="right" width="100">送货单位：</td>
				<td width="200"><?php echo $system['companyName']?></td>
				<td align="right" width="200">收货单位：</td>
				<td width="200"><?php echo $contactName?></td>
			</tr>
			<tr height="50">
				<td align="right" width="100">发货人签字：</td>
				<td width="200"></td>
				<td align="right" width="200">收货人签字：</td>
				<td width="200"></td>
			</tr>
			<tr>
				<td height="25" align="right" width="100">日期：</td>
				<td width="200"><?php echo $billDate?></td>
				<td align="right" width="200">日期：</td>
				<td width="200"></td>
			</tr>
		</table>	
<?php echo $t==$countpage?'':'<br><br><br>';}?>		 
</body>
</html>		