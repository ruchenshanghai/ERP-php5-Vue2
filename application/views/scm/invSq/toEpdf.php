<?php if(!defined('BASEPATH')) exit('No direct script access allowed');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo '销售报价单'?></title>
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
		<table  width="800"  align="center" style="position:absolute;left:0px;top:35px;">		     
			<tr height="15px">
				<td align="center" style="font-size:18px; font-weight:normal;height:25px;"><?php echo '销售报价单'?></td>
			</tr>
			<tr height="10px">
				<td width="740"  align="right" style="font-size:10px; font-weight:normal;">单据编号：<?php echo $billNo?></td>
			</tr> 
		</table>	
		
		<table width="800" align="center" border="1" cellpadding="2" cellspacing="1" 
		style="border-collapse:collapse;border:solid black;border-width:1px 0 0 1px;font-size:10px;">
		
		<tr height="16" align="center">
		<td width="700" colspan="8" style="background-color:LightGrey;padding:4px 0 2px 0;">甲方（需方）</td>
		</tr>
			<tr height="16" align="left">
			<td width="20" align="center" style="background-color:LightGrey;">名称</td>
			<td ><?php echo $contactName?></td>
			<td  width="20" align="center" style="background-color:LightGrey;">电话</td>
			<td width="50"><?php echo $buyerPhone?></td>
			<td align="center" style="background-color:LightGrey;">传真</td>
			<td width="50"><?php echo $buyerFax?></td>
		</tr>
		<tr height="16" align="left">
			<td align="center" style="background-color:LightGrey;">联系人</td>
			<td width="30"><?php echo $buyerName?></td>
			<td align="center" style="background-color:LightGrey;">手机</td>
			<td width="30"><?php echo $buyerMobile?></td>
			<td align="center" style="background-color:LightGrey;">邮件地址</td>
			<td width="100"><?php echo $buyerEmail?></td>
		</tr>		
		<tr height="16" align="left">
			<td  width="20" align="center" style="background-color:LightGrey;">地址</td>
			<td colspan="3"><?php echo $buyerAddress?></td>
			<td align="center" style="background-color:LightGrey;">邮编</td>
			<td width="50"><?php echo $buyerZipcode?></td>
		</tr>
		
		
		<tr height="16" align="center">
		<td width="738" colspan="8" style="background-color:LightGrey;">乙方（供方）</td>
		</tr>
		<tr height="16" align="left">
			<td width="20" align="center" style="background-color:LightGrey;">名称</td>
			<td ><?php echo $system['companyName']?></td>
			<td  width="20" align="center" style="background-color:LightGrey;">电话</td>
			<td width="50"><?php echo $system['phone']?></td>
			<td align="center" style="background-color:LightGrey;">传真</td>
			<td width="50"><?php echo $system['fax']?></td>
		</tr>
		<tr height="16" align="left">
			<td align="center" style="background-color:LightGrey;">销售人员</td>
			<td width="30"><?php echo $staffName?></td>
			<td align="center" style="background-color:LightGrey;">手机</td>
			<td width="30"><?php echo $staffMobile?></td>
			<td align="center" style="background-color:LightGrey;">邮件地址</td>
			<td width="30"><?php echo $staffEmail?></td>
		</tr>		
		<tr height="16" align="left">
			<td  width="20" align="center" style="background-color:LightGrey;">地址</td>
			<td colspan="3"><?php echo $system['companyAddr']?></td>
			<td align="center" style="background-color:LightGrey;">邮编</td>
			<td width="50"><?php echo $system['postcode']?></td>
		</tr>
		</table>
			
		<table width="900" border="1" cellpadding="2" cellspacing="1" align="center" 
		style="margin-top:10px;border-collapse:collapse;border:solid #000000;border-width:1px 0 0 1px;font-size:10px;">   
			<tr style="height:20px">
				    <td width="30" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;"  align="center">序号</td>
					<td width="220" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;" align="center">商品</td> 
					<td width="40" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;" align="center">商品型号</td>
					<td width="176" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;" align="center">详细说明</td>
					<td width="20" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;" align="center">单位</td>
					<td width="40" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;" align="center">数量</td>
					<td width="40" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;" align="center">销售单价</td>
					<td width="20" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;" align="center">币种</td>
					<td width="60" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;" align="center">销售金额</td>						
				</tr>
		       <?php 
			   $i = ($t-1)*$num + 1;
			   foreach($list as $arr=>$row) {
			       if ($row['i']>=(($t-1)*$num + 1) && $row['i'] <=$t*$num) {
			   ?>
				<tr style="border:solid #000000;border-width:0 1px 1px 0;padding:2px;height:15px;">
				   <td align="center"><?php echo $row['i']?></td>
					<td style="border:solid #000000;border-width:0 1px 1px 0;height:15px;"><?php echo $row['goods'];?></td>
					<td align="center" style="border:solid #000000;border-width:0 1px 1px 0;height:15px;"><?php echo $row['invSpec']?></td>
					<td><?php echo $row['remark']?></td>
					<td align="center"><?php echo $row['unitName']?></td>
					<td align="right"><?php echo str_money(abs($row['qty']),$system['qtyPlaces'])?></td>
					<td align="right"><?php echo str_money(abs($row['price']-$row['deduction']),2)?></td>
					<td align="center"><?php echo $row['currencyCode']?></td>
					<td align="right"><?php echo str_money(abs($row['amount']),2)?></td>
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
				   <td align="center" style="border:solid #000000;border-width:0 1px 1px 0;height:15px;"><?php echo $m?></td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ></td>
					<td ></td>
				</tr>
				<?php }}?>
				
				 <?php if ($t==$countpage) {?>
				 <tr style="height:20px">
				   <td colspan="5" align="right" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px;height:15px;">合计：</td>
					<td align="right"><?php echo str_money(abs($totalQty),$system['qtyPlaces'])?></td>
					<td></td>
					<td></td>
					<td align="right"><?php echo str_money(abs($totalAmount),2)?></td>
				</tr>				 
				<tr target="id">
				    <td colspan="9" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px;height:15px;">合计 金额大写： <?php echo str_num2rmb(abs($totalAmount))?> </td> 
				</tr>
				<?php }?>
		</table>
		
<?php echo $t==$countpage?'':'<br><br><br>';}?>		
<div style="margin:20px 0 0 20px;font-size: 12px;">付款方式：预付款<span style="margin-left: 20px">交货周期：<?php echo $deliveryDate?>&nbsp;天</span></div> 
	<div style="margin:10px 0 0 20px;font-size: 10px;line-height:22px;">
	<div style="font-size: 14px;font-weight:bold;">银行账户信息</div>
人民币（RMB）账户信息：<br/>
账户名称： 苏州九五尊易网络有限公司<br/>
开 户 行：招商银行股份有限公司苏州分行营业部<br/>
银行帐号：5129 0603 0710 301 <br/>
===================================================================================================================<br/>
美金（USD）账户信息：<br/>
账户名称：九五尊易科技（香港）有限公司 / 95isee Technology (HK) Co., Limited<br/>
帐 号：801-519182-838<br/>
开 户 行：香港上海汇丰银行 / The Hongkong and Shanghai Banking Corporation Limited<br/>
Swift Code：HSBCHKHHHKH<br/>
</div>		

</body>
</html>		