<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
  		<table width="1440px" class="list" border="1">
  			<tr><td class='H' align="center" colspan="18"><h3>商品销售利润表<h3></td></tr>
  			<tr><td colspan="18">日期：<?php echo $beginDate?>至<?php echo $endDate?></td></tr>
  		</table>
  		<table width="1440px" class="list" border="1">
  			<thead>
  				<tr>
  				<th>销售日期</th>
  				<th>销售单据号</th>
  				<th>业务类别</th>
				<th>销售人员</th>
  				<th>客户</th>
  				<th>商品编号</th>
  				<th>商品名称</th>
  				<th>规格型号</th>
  				<th>单位</th>
  				<th>仓库</th>
  				<th>数量</th>
  				<th>单价</th>
  				<th>销售收入</th>
  				<th>单位成本</th>
  				<th>销售成本</th>
  				<th>销售毛利<br>(销售收入-销售成本)</th>
  				<th>毛利率</th>
				<th>备注</th>
  				</tr>
  			</thead>
  			<tbody>
				 <?php 
				 foreach($list as $arr=>$row){
				 ?>
  			       <tr class="link" data-id="<?php echo $row['iid']?>" data-type="<?php echo $row['billType']?>">
  			       <td><?php echo $row['billDate']?></td>
  			       <td><?php echo $row['billNo']?></td>
  			       <td><?php echo $row['transTypeName']?></td>
				   <td><?php echo $row['staffName']?></td>
  			       <td><?php echo $row['contactName']?></td>
  			       <td><?php echo $row['invNumber']?></td>
  			       <td><?php echo $row['invName']?></td>
  			       <td><?php echo $row['invSpec']?></td>
  			       <td><?php echo $row['mainUnit']?></td>
  			       <td><?php echo $row['locationName']?></td>
				   
				   <td class="R"><?php echo $row['qty']?></td>
  			       <td class="R"><?php echo $row['price']?></td>
  			       <td class="R"><?php echo $row['amount']?></td>
  			       
				   <td class="R"><?php echo $row['cost']?></td>
  			       <td class="R"><?php echo $row['unitCost']?></td>
  			       <td class="R"><?php echo $row['saleProfit']?></td>
  			       <td class="R"><?php echo $row['salepPofitRate']?></td>
  			         			       
  			       
				   <td class="R"><?php echo $row['description']?></td>
  			       </tr>
				 <?php 				  
				 }
				 ?>
  				<tr>
  				<td colspan="10" class="R B">合计：</td>
				<td class="R B"><?php echo $sumqty?></td>
  				<td class="R B"></td>
  				<td class="R B"><?php echo $amount?></td>
 				<td class="R B"></td>
 				<td class="R B"><?php echo $cost?></td>
 				<td class="R B"><?php echo $saleProfit?></td>
 				<td class="R B"><?php echo $salepPofitRate?></td>
  				</tr>
  			</tbody>
  		</table>
 