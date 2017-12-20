<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php if($transType==150601){echo 'Sales Order';}else if($transType==150603){echo 'Quotation';}else{echo 'Sales Back Order';}?>
    </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        td {
            padding: 4px 2px 2px 2px;
        }
    </style>
</head>
<body>
<img src="<?php echo base_url() ?>/statics/css/img/wechat.png"
     style="position:absolute;right:100px;top:0px;z-index:-1;height:80px;"/>
<img src="<?php echo base_url() ?>/statics/css/img/logo.png"
     style="position:absolute;right:215px;top:0px;z-index:-1;height:80px;"/>
<?php for ($t = 1;
$t <= $countpage;
$t++){ ?>
<table width="800" align="center" style="position:absolute;left:0px;top:35px;">
    <tr height="15px">
        <td align="center" style="font-size:18px; font-weight:normal;height:25px;">
            <?php if($transType==150601){echo 'Sales Order';}else if($transType==150603){echo 'Quotation';}else{echo 'Sales Back Order';}?>
        </td>
    </tr>
    <tr height="10px">
        <td width="740" align="right" style="font-size:10px; font-weight:normal;">BillNo：<?php echo $billNo ?></td>
    </tr>
</table>
<table align="center" border="1" cellpadding="2" cellspacing="1"
       style="border-collapse:collapse;border:solid black;border-width:1px 0 0 1px;font-size:10px;">
    <tr height="16" align="center">
        <td colspan="6" style="background-color:LightGrey;">Customer</td>
    </tr>
    <tr height="16" align="left">
        <td width="70" align="center" style="background-color:LightGrey;">Company Name</td>
        <td width="385" colspan="3"><?php echo $contactName ?></td>
        <td width="30" align="center" style="background-color:LightGrey;">Tel</td>
        <td width="70"><?php echo $buyerPhone ?></td>
    </tr>
    <tr height="16" align="left">
        <td width="70" align="center" style="background-color:LightGrey;">Contact Person</td>
        <td width="385"><?php echo $buyerName ?></td>
        <td width="30" align="center" style="background-color:LightGrey;">Mobile</td>
        <td width="70"><?php echo $buyerMobile ?></td>
        <td width="55" align="center" style="background-color:LightGrey;">Zipcode</td>
        <td width="60"><?php echo $buyerZipcode ?></td>
    </tr>
    <tr height="16" align="left">
        <td width="70" align="center" style="background-color:LightGrey;">Address</td>
        <td width="385"><?php echo $buyerAddress ?></td>
        <td width="30" align="center" style="background-color:LightGrey;">Email</td>
        <td colspan="3"><?php echo $buyerEmail ?></td>
    </tr>
    <tr height="16" align="center">
        <td colspan="6" style="background-color:LightGrey;padding:4px 0 2px 0;">Vendor</td>
    </tr>
    <tr height="16" align="left">
        <td width="70" align="center" style="background-color:LightGrey;">Company Name</td>
        <td width="385"><?php echo $system['companyName'] ?></td>
        <td width="30" align="center" style="background-color:LightGrey;">Tel</td>
        <td width="70"><?php echo $system['phone'] ?></td>
        <td width="55" align="center" style="background-color:LightGrey;">Fax</td>
        <td width="60"><?php echo $system['fax'] ?></td>
    </tr>
    <tr height="16" align="left">
        <td width="70" align="center" style="background-color:LightGrey;">Contact Person</td>
        <td width="385"><?php echo $staffName ?></td>
        <td width="30" align="center" style="background-color:LightGrey;">Mobile</td>
        <td width="70"><?php echo $staffMobile ?></td>
        <td width="55" align="center" style="background-color:LightGrey;">Zipcode</td>
        <td width="60"><?php echo $system['postcode'] ?></td>
    </tr>
    <tr height="16" align="left">
        <td width="70" align="center" style="background-color:LightGrey;">Address</td>
        <td width="385"><?php echo $system['companyAddr'] ?></td>
        <td width="30" align="center" style="background-color:LightGrey;">Email</td>
        <td colspan="3"><?php echo $staffEmail ?></td>
    </tr>
    <!--
    <tr height="16" align="center">
        <td colspan="6" style="background-color:LightGrey;padding:4px 0 2px 0;">Ship to</td>
    </tr>
    <tr>
        <td width="70" align="center" style="background-color:LightGrey;">Warehousename</td>
        <td width="400"><?php echo $storageName ?></td>
        <td colspan="2" align="center" style="background-color:LightGrey;">Contact Person</td>
        <td colspan="2"><?php echo $storageManager ?></td>
    </tr>
    <tr>
        <td width="70" align="center" style="background-color:LightGrey;">Address</td>
        <td width="400"><?php echo $storageAddress ?></td>
        <td colspan="2" align="center" style="background-color:LightGrey;">Tel</td>
        <td colspan="2"><?php echo $storagePhone ?></td>
    </tr>-->
</table>

<table align="center" border="1" cellpadding="2" cellspacing="1"
       style="margin-top:10px;border-collapse:collapse;border:solid #000000;border-width:0 0 0 1px;font-size:10px;">
    <tr>
        <td width="70" align="center" style="background-color:LightGrey;">Request date</td>
        <td width="100"><?php if($deliveryDate!=""){echo ($deliveryDate);}?></td>
        <td width="70" align="center" style="background-color:LightGrey;">Payment terms</td>
        <td width="100"><?php echo $paymentName ?></td>
        <td width="70" align="center" style="background-color:LightGrey;">Freight terms</td>
        <td width="100"><?php echo $shippingName ?></td>
        <td width="70" align="center" style="background-color:LightGrey;">Currency</td>
        <td width="80"><?php echo $currencyCode ?></td>
    </tr>
</table>
<table border="1" cellpadding="2" cellspacing="1" align="center"
       style="margin-top:10px;border-collapse:collapse;border:solid #000000;
       border-width:1px 0 0 1px;font-size:10px;">
    <tr style="background-color:LightGrey;">
        <td width="30" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;" align="center">
            Line
        </td>
        <td width="60" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;" align="center">
            Name
        </td>
        <td width="90" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;" align="center">
            Mpn
        </td>
        <td width="70" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;" align="center">
            Manufacture
        </td>
        <td width="190" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;" align="center">
            Description
        </td>
        <td width="20" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;" align="center">
            Unit
        </td>
        <td width="30" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;" align="center">
            Quantity
        </td>
        <td width="50" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;" align="center">
            Unit Price
        </td>
        <?php if ($haveItemDisrate) { ?>
        <td width="40" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px;" align="center">Discount Rate(%)</td>
        <td width="30" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;" align="center">
            <?php } else{?>
        <td width="105" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px; height:15px;" align="center">
            <?php }?>
            Total
        </td>
    </tr>
    <?php
    $i = ($t - 1) * $num + 1;
    foreach ($list as $arr => $row) {
        if ($row['i'] >= (($t - 1) * $num + 1) && $row['i'] <= $t * $num) {
            ?>
            <tr style="border:solid #000000;border-width:0 1px 1px 0;padding:2px;">
                <td width="30" align="center"><?php echo $row['i'] ?></td>
                <td width="60"
                    style="border:solid #000000;border-width:0 1px 1px 0;">
                    <?php echo $row['goods'];?></td>
                <td width="90" align="center"
                    style="border:solid #000000;border-width:0 1px 1px 0;"><?php echo $row['invSpec'] ?></td>
                <td width="70"><?php echo $row['manufacture'] ?></td>
                <td width="190"><?php echo $row['remark'] ?></td>
                <td width="20" align="center"><?php echo $row['unitName'] ?></td>
                <td width="30" align="right"><?php echo str_money(abs($row['qty']), $system['qtyPlaces']) ?></td>
                <td width="50" align="right"><?php echo str_money(abs($row['price']), 2) ?></td>

                <?php if ($haveItemDisrate) { ?>
                <td width="40" align="center"><?php echo $row['discountRate'] ?></td>
                <td width="30" align="right">
                    <?php } else{?>
                <td width="100" align="right">
                    <?php }?>
                    <?php echo str_money(abs($row['amount']), 2) ?></td>
            </tr>
            <?php
            $s = $row['i'];
        }
        $i++;
    }
    ?>

    <?php if ($t == $countpage) { ?>
        <tr style="height:20px;">
            <td colspan="6" align="right"
                style="border:solid #000000;border-width:0 1px 1px 0;padding:2px;height:15px;background-color:LightGrey;">Total
            </td>
            <td width="30" align="right"><?php echo str_money(abs($totalQty), $system['qtyPlaces']) ?></td>
            <td width="50" ></td>
            <?php if ($haveItemDisrate) { ?>
                <td width="40" ></td>
            <?php } ?>
            <td width="30" align="right"><?php echo str_money(abs($totalAmount), 2) ?></td>
        </tr>
        <?php if ($disRate>0) { ?>
            <tr style="height:20px">
                <td colspan="6" align="right"
                    style="border:solid #000000;border-width:0 1px 1px 0;padding:2px;height:15px;background-color:LightGrey;">Total Discount Rate
                </td>
                <td width="30" align="right"><?php echo $disRate?>%
                </td>
                <?php if ($haveItemDisrate) { ?>
                    <td width="50"></td>
                <?php } ?>
                <td width="40" align="right" style="background-color:LightGrey;">Discounted
                </td>
                <td width="30" align="right"><?php echo str_money(abs($amount), 2) ?></td>
            </tr>
        <?php } ?>

    <?php } ?>
    <?php echo $t == $countpage ? '' : '<br><br><br>';
    } ?>
    <!--   <tr>
        <td style="border:solid #000000;border-width:0 1px 1px 0;padding:2px;height:15px;color:red;">Remark:<br/>注：</td>
        <?php if ($haveItemDisrate) { ?>
        <td colspan="9" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px;height:15px;color:red;">
        <?php }else{ ?>
        <td colspan="8" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px;height:15px;color:red;">
            <?php }?>
            1. Date code must be within 1 years;生产日期必须在1年以内；<br/>
            2. Package must be original packing from the factory;包装必须是出厂原包装；<br/>
            3. All material must be in standard package.所有物料必须是标准包装。
        </td>
    </tr>-->
    <tr>
        <?php if ($haveItemDisrate) { ?>
        <td colspan="7" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px;height:15px;color:red;">
            <?php }else{ ?>
        <td colspan="6" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px;height:15px;">
            <?php }?>
            <!--
            <div style="width:500px;word-break:break-all;word-wrap:break-word;">
                We order according to conditions and specification above, contracts based on this <br/>
                purchase order shall, unless otherwise agreed by the parties in writing, be subject<br/>
                to 95isee's general purchasing condition printed overleaf, please acknow-ledge this order on enclosed
                form. <br/>
            </div>
            <div style="width:500px;word-break:break-all;word-wrap:break-word;">
                我们的订单是以上述条件和说明为前提，除非合同双方以书面形式订下条款，合同将受九五尊易订货条款限制，请
                在附属格式中确认此条款。
            </div>-->
        </td>
        <td colspan="3" style="border:solid #000000;border-width:0 1px 1px 0;padding:2px;height:15px;" align="center">
            95isee Technology （Hongkong） Co., Ltd. <br/>
            九五尊易科技（香港）有限公司
        </td>
    </tr>

</table>

<div style="margin:10px 0 0 20px;font-size: 10px;line-height:22px;">
    <div style="font-size: 14px;font-weight:bold;">Here is our bank information</div>
    Beneficiary：<?php echo $accountName?><br/>
    Bank name：<?php echo $bank?><br/>
    Account No.：<?php echo $account?><br/>
    <?php if (!empty($taxNumber)) {?>
        Tax No.：<?php echo $taxNumber?><br/>
    <?php }?>
    Tel：<?php echo $tel?><br/>
    Address：<?php echo $address?><br/>
    <?php if (!empty($swiftCode)) {?>
        Swift code：<?php echo $swiftCode?><br/>
    <?php }?>
</div>

<table width="800" style="margin-top:20px;font-size:10px;">
    <tr height="50">
        <td width="200">Buyer Signature买方签字确认:</td>
        <td width="200"></td>
        <td width="200">Confirmed Date 确认日：</td>
        <td width="200"></td>
    </tr>
</table>

<!--
<div style="margin-top:30px;width:740px;word-wrap:break-word;font-size:10px;">
    Attchment： Separately agreed upon in writing, the following conditions shall apply in preference to all other terms
    and conditions.
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    1. Price and payment
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    The price stated are fixed Seller shall not be entitled to claim invoicing fees or other additional charges not
    agreed upon. Date of payment shall be determined starting from invoice arrival date. However at the earliest from
    the date of delivery. Interest on delayed payment shall only be paid if the interest exceeds RMB 100.
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    2. Order confirmation
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    Seller shall within two (2) weeks return the order acknowledgment enclosed with the Purchase Order.
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    3. Delivery documents and package
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    All delivery documents, e.g. delivery notes, package receipts and invoice, shall be issued in accordance with
    Buyer’s instructions. Packing of the Product. Including the packing material used shall be included in the price,
    and the Product shall be packed in such a way that transport damage will be prevented. The products and the package
    shall be marked in accordance with Buyer’s instructions.
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    4. Terms of delivery
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    The terms of delivery shall be interpreted in accordance with INCOTERMS 2000. Buyer is entitled to reasonably
    re-schedule the deliveries.
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    5. Confidentiality
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    Seller is responsible for ensuring that in formation given by Buyer is not unauthorized brought to the knowledge of
    third parties.
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    6. Obligation to notify
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    Seller shall immediatany notify Buyer of any disruption that have occurred or are anticipated which may jeopardize
    the scheduled delivery or fulfillment of the requirement specified by the Buyer.
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    7. Delayed deliveries
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    Seller understand and the acknowledges that proper delivery at the agreed upon tunes are of the utmost importance to
    Buyer and that delay can cause severe damage to Buyer. If the product are not delivered at the relevant dates, Buyer
    shall be entitle to liquidate damages,which shall be computed as follows:2% of the purchase price of the Products
    that have been delayed or cannot be used as a consequence of the delay,The liquidated damages shall not,
    however,exceed a total of 20% of the said purchase price.Indepently of Buyer's right to liquidated damages,Buyer
    shall be entitled to cancel the purchase on account of the delay, provided that the delay is not insignificant.
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    8. Inspection of Products quality and manage means
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    For a period of one (1) year from the delivery, the Product shall meet the requirements and specification stated in
    the Purchase Order and otherwise agreed upon, and technically and commercially is fit for their intended purpose.
    Buyer shall be entitled to inspect the products and the production thereof, including the quality assurance systems,
    at the premises of Seller’s subcontractors. If any quality issue inspected by SQE,should be charged to the
    supplier.And the supplier should replace the defective goods and issue credit note to us.
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    9. Defective products
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    Defective Products may be rejected, wholly or in part and returned at Seller’s risk and expense. For such cases.
    Seller shall be obliged to promptly replace or repair the Products with Products that are free form defects unless
    Buyer cancels the purchase, wholly or in part. The fact that the products have been used and /or paid for shall not
    imply that Buyer has waived its right to cancel the purchase.
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    10. Product Liability
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    Should the products have a defect which causes damage to persons or to property other than such products, Seller
    shall indemnify and hold Buyer harmless for any such damage.
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    11. Authority requirements
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    Seller shall be responsible for ensuring that Product, as well as the delivery and packing thereof, satisfy all
    requirements by law or statute relating thereto.
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    12. Export
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    Seller shall be responsible for ensuring that the Products can be exported without restrictions, unless it has
    notified in writing the existence of such restriction, and be responsible for the issuance of any certificate of
    origin and export license when necessary. If, submission of a Purchase Order, it becomes obvious that the Products
    are subject to export rest actions, Buyer shall have the right to cancel the purchase.
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    13. Intellectual Property rights
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    Seller guarantees that the use of the Products does not constitute an infringement of any third party’s patents,
    indemnify and keep Buyer harmless in event of such infringement.
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    14. Amendments and additions
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    Amendments and/or additions to a Purchase Order shall be valid only after written acceptance thereof from Buyer’s
    purchasing department.
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    15. Applicable Law
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    Chinese law is applicable on the Purchase of the Products and any disputes between the parties shall be settled by
    Chinese courts.
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    附：对于本订单订购的产品，除非有其他的书面协议，否则遵从以下条款
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    1、价格及付款
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    本订单中的价格是固定的。卖方无权收取未经双方同意的发票上的费用或其他附加费用。当货物运达时，卖方有权开具发票，买方的付款帐期 是依据发票到达日为始算日而并非以货物运达日。当迟付款利息超过100元人民币时，应付次利息费。
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    2、 定单确认
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    卖方应于两（2）个工作日内确认并返还定单。
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    3、 运输单据及包装
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    按照买方要求提供所有运输单据，如运货通知，包装收据和发票等。产品的包装包括使用的包装材料均应含在价格中，并且在运输中应使用此 包装以免除货物受损，在产品及包装上按照买方要求做出标识。
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    4、 运输及取消期限
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    运输期限应遵守INCOTERMS 2000中的规定。买方有权合理的安排运输日程，如果卖方进入破产程序或类似情况，或者卖方无法赔债时，卖方 有权立即取消定单。
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    5、保密
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    卖方负责保证由买方提供的信息在未经授权的情况下不透露给第三方。卖方只能为生产及运输产品之目的使用买方提供的信息。
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    6、通知的义务
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    卖方应立即将已发生或将要发生、可能影响货物按期交货或满足买方要求的问题通知买方。
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    7、运输延误
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    卖方理解并承认按照约定日期交货对方的重要性，并且运输的延误将给买方造成严重损失。如果产品未能按期送达，买方有权按照下列方式计
    算违约金：每延误一个星期，按所延误产品或因为延误不能使用的产品的买入价的百分之二（2%）支付违约金。但是，违约金不得超出该买入
    价的百分之二十（20%）。一旦延误，卖方应立即组织所有必须的力量安排紧急交货，为此所发生的全部费用，如加急运费将由卖方承担。如 果延误为实质性的，买方除有权获得违约金外，也有权因延误而取消采购。
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    8、产品、质量等的检查及处理办法
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    自发货起的一（1）年之中，产品应符合采购定单或双方同意的需求，规格，并且符合预想的技术及商业要求。买方及其客户有权到卖方或卖 方承包商的所在地检查产品及生产，包括质量保证系统。
    由质检部门发现任何的产品质量问题需有供应商承担责任并及时作出退换货物的处理。
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    9、残品
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    残品可拒收、全部或部分地退还卖方，并由卖方承担风险及费用，在此情况下，卖方有义务立即免费更换或修理残品，除非买方全部或部分地 退货。产品已使用或已付款并意味着买方放弃取消采购的权利。
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    10、产品责任
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    如果产品的缺陷导致对人身或财产的伤害，卖方应当予以赔偿，并保证买方不受损害。
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    11、政府要求
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    卖方应负责保证产品、运输和包装满足法律或相关条例的规定。
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    12、出品
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    卖方应负责保证产品能够出口而不受限制，除非已以书面形式说明该等限制的存在。同时也应负责在需要时提交原产地证明或出口许可证。如 果在提交定单后意识到产品受出口限制，买方有权取消采购。
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    13、知识产权
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    卖方应保证使用该产品不对第三方专利。著作权或其他知识产权构成侵犯，并且承担辩护赔偿及保护买方免受损害的责任。
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    14、修改及增加
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    对采购定单的修改和/或增加只有在买方采购部书面认可后方有效。
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    15、适用法律
</div>
<div style="width:740px;word-wrap:break-word;font-size:10px;">
    中国法律适用于产品的购买，双方之间的任何争议应由有关中国法院解决。
</div>
-->
</body>
</html>