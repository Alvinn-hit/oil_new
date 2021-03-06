    <?php if(!empty($lading_bills)):?>
      <?php foreach ($lading_bills as $key=>$value):?>
    <div class="box">
        <div class="box-header link with-border">
            <h3 class="box-title">入库通知单结算 <a href="/stockIn/detail?id=<?php echo $value['batch_id'];?>&t=1" target="blank"><?php echo $value['batch_code'];?></a></h3>
        </div>
        <div class="box-body form-horizontal">
         <?php if(!empty($value['settlementGoods'])):?>
           <?php foreach ($value['settlementGoods'] as $k=>$v):?>
            <div class="row">
                <form class="col-sm-12">
                    <fieldset style="border: 1px solid; padding: 0.35em 0.625em 0.75em;margin-bottom: 15px">
                        <legend  class="h4 text-primary" style="border: 0;  width: auto;"><?php echo $v['goods_name'];?></legend>
                        <div class="clearfix">
                            <div class="pull-right">
                                <?php if(!empty($v['hasDetail'])){ ?>
                                <button type="button" class="btn btn-link hideBtn"  onclick="hideDetail(<?php echo $v['goods_id'] ?>)"  id="<?php echo 'hideDetail_'.$v['goods_id'] ?>"> 收起明细</button>
                                <button type="button" class="btn btn-link showBtn" onclick="showDetail(<?php echo $v['goods_id'] ?>)" id="<?php echo 'showDetail_'.$v['goods_id'] ?>"> 展开明细</button>
                                <?php } ?>
                                <button type="button" class="btn btn-link" onclick="displayLockPriceDetail(<?php echo $v['batch_id'] ?>, <?php echo $v['goods_id'] ?>)"> 查看锁价</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <label class="col-sm-4 control-label">入库单数量 </label>
                                <p class="form-control-static col-sm-8">
                                <?php 
                                echo number_format($v['in_quantity']['quantity'],4);?><?php echo Map::$v['goods_unit'][$v['in_quantity']['unit']]['name'];
                              
                               if( !empty($v['in_quantity_sub']['unit']) && $v['in_quantity_sub']['unit']!=$v['in_quantity']['unit']) {
                                    echo '/'. Utility::numberFormatToDecimal($v['in_quantity_sub']['quantity'], 4). $this->map["goods_unit"][$v["in_quantity_sub"]["unit"]]['name'];
                                }
                                ?>
                                </p>
                            </div>
                            <div class="col-sm-4">
                                <label class="col-sm-4 control-label">结算数量</label>
                                <p class="form-control-static col-sm-8"><?php echo number_format($v['quantity']['quantity'],4);?><?php echo Map::$v['goods_unit'][$v['quantity']['unit']]['name'];?></p>
                            </div>
                            <div class="col-sm-4">
                                <label class="col-sm-4 control-label">损耗量</label>
                                <p class="form-control-static col-sm-8"><?php echo number_format($v['quantity_loss']['quantity'],4);?><?php echo Map::$v['goods_unit'][$v['quantity_loss']['unit']]['name'];?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <label class="col-sm-4 control-label">结算单价 </label>
                                <p class="form-control-static col-sm-8"><?php echo $value['settle_currency']['ico'].number_format($v['price']/100,2);?></p>
                            </div>
                            <div class="col-sm-4">
                                <label class="col-sm-4 control-label">结算金额</label>
                                <p class="form-control-static col-sm-8"><?php echo $value['settle_currency']['ico'].number_format($v['amount']/100,2);?></p>
                            </div>
                             <?php if(isset($value['settle_currency']['id'])&&$value['settle_currency']['id']==ConstantMap::CURRENCY_DOLLAR):?>
                            <div class="col-sm-4">
                                <label class="col-sm-4 control-label">结算汇率</label>
                                <p class="form-control-static col-sm-8"><?php echo $v['unit_rate'];?></p>
                            </div>
                            <?php endif;?>
                        </div>
                         <?php if(isset($value['settle_currency']['id'])&&$value['settle_currency']['id']==ConstantMap::CURRENCY_DOLLAR):?>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <label class="col-sm-4 control-label">人民币结算单价</label>
                                <p class="form-control-static col-sm-8"><?php echo '￥'.number_format($v['price_cny']/100,2);?></p>
                            </div>
                            <div class="col-sm-4">
                                <label class="col-sm-4 control-label">人民币结算金额 </label>
                                <p class="form-control-static col-sm-8"><?php echo '￥'.number_format($v['amount_cny']/100,2);?></p>
                            </div>
                           
                        </div>
                        <?php endif;?>
                        <?php if(!empty($v['hasDetail'])){ ?>
                        <fieldset id="<?php echo  'displayDetail_'.$v['goods_id'] ?>">
                            <legend class="h5 text-info">结算明细</legend>
                            <div class="form-group col-sm-12">
                                <div class="h5">贷款金额</div>
                                <table class="table table-striped table-bordered">
                                    <tr>
                                        <td>计价币种</td>
                                        <td>计价币种货款金额</td>
                                        <td>汇率</td>
                                        <td>人民币货款总额</td>
                                        <td>货款单价</td>
                                        <td>计税汇率</td>
                                        <td>计税人民币货款总额</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p class="form-control-static"><?php echo isset($v['settlementGoodsDetail']['currency']['name'])?$v['settlementGoodsDetail']['currency']['name']:'';?></p>
                                        </td>
                                        <td><p class="form-control-static"><?php echo $value['settle_currency']['ico'].number_format($v['settlementGoodsDetail']['amount_currency']/100,2);?></p></td>
                                        <td><p class="form-control-static"><?php echo $v['settlementGoodsDetail']['exchange_rate'];?></p></td>
                                        <td><p class="form-control-static">￥<?php echo number_format($v['settlementGoodsDetail']['amount_goods']/100,2);?></p></td>
                                        <td><p class="form-control-static">￥<?php number_format($v['settlementGoodsDetail']['price_goods']/100,2);?></p></td>
                                        <td><p class="form-control-static"><?php echo $v['settlementGoodsDetail']['exchange_rate_tax'];?></p></td>
                                        <td><p class="form-control-static">￥<?php echo number_format($v['settlementGoodsDetail']['amount_goods_tax']/100,2);?></p></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="form-group col-sm-12">
                                <div class="h5">相关税收</div>
                                <table class="table table-striped table-bordered">
                                    <tr>
                                        <td>税收名目</td>
                                        <td>税率</td>
                                        <td>税收总金额</td>
                                        <td>税收单价</td>
                                        <td>备注</td>
                                    </tr>
                                    <?php if(!empty($v['settlementGoodsDetail']['tax_detail_item'])):?>
                                     <?php foreach ($v['settlementGoodsDetail']['tax_detail_item'] as $tax_key=>$tax_value):?>
                                    <tr>
                                        <td>
                                            <p class="form-control-static"><?php echo isset($tax_value['subject_list']['name'])?$tax_value['subject_list']['name']:''; ?></p>
                                        </td>
                                        <td><p class="form-control-static"><?php echo ($tax_value['rate']*100).'%'; ?></p></td>
                                        <td><p class="form-control-static">￥<?php echo number_format($tax_value['amount']/100,2); ?></p></td>
                                        <td><p class="form-control-static">￥<?php echo number_format($tax_value['price']/100,2); ?></p></td>
                                        <td><p class="form-control-static"><?php echo $tax_value['remark']; ?></p></td>
                                    </tr>
                                    <?php endforeach;?>
                                    <?php endif;?>
                                </table>
                            </div>
                            <div class="form-group col-sm-12">
                                <div class="h5">其他费用</div>
                                <table class="table table-striped table-bordered">
                                    <tr>
                                        <td>科目</td>
                                        <td>费用总额</td>
                                        <td>费用单价</td>
                                        <td>备注</td>
                                    </tr>
                                     <?php if(!empty($v['settlementGoodsDetail']['other_detail_item'])):?>
                                     <?php foreach ($v['settlementGoodsDetail']['other_detail_item'] as $other_key=>$other_value):?>
                                    <tr>
                                        <td>
                                            <p class="form-control-static"><?php echo isset($other_value['subject_list']['name'])?$other_value['subject_list']['name']:''; ?></p>
                                        </td>
                                        <td><p class="form-control-static">￥<?php echo number_format($other_value['amount']/100,2); ?></p></td>
                                        <td><p class="form-control-static">￥<?php echo number_format($other_value['price']/100,2); ?></p></td>
                                        <td><p class="form-control-static"><?php echo $other_value['remark']; ?></p></td>
                                    </tr>
                                     <?php endforeach;?>
                                    <?php endif;?>
                                </table>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">调整金额 </label>
                                    <p class="form-control-static col-sm-8"><?php echo '￥'.number_format($v['settlementGoodsDetail']['amount_adjust']/100,2);?></p>
                                </div>
                                <div class="col-sm-8" style="margin-left: -4px;">
                                    <label class="col-sm-2 control-label">调整原因 </label>
                                    <p class="form-control-static col-sm-10"><?php echo $v['settlementGoodsDetail']['reason_adjust'];?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">总结算数量 </label>
                                    <p class="form-control-static col-sm-8"><?php echo number_format($v['settlementGoodsDetail']['quantity']['quantity'],4);?><?php echo Map::$v['goods_unit'][$v['settlementGoodsDetail']['quantity']['unit']]['name'];?></p>
                                </div>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">人民币结算金额 </label>
                                    <p class="form-control-static col-sm-8"><?php echo '￥'.number_format($v['settlementGoodsDetail']['amount']/100,2);?></p>
                                </div>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">人民币结算单价</label>
                                    <p class="form-control-static col-sm-8"><?php echo '￥'.number_format($v['settlementGoodsDetail']['price']/100,2);?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">确定总结算数量 </label>
                                    <p class="form-control-static col-sm-8"><?php echo number_format($v['settlementGoodsDetail']['quantity_actual']['quantity'],4);?><?php echo Map::$v['goods_unit'][$v['settlementGoodsDetail']['quantity_actual']['unit']]['name'];?></p>
                                </div>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">确定人民币结算金额 </label>
                                    <p class="form-control-static col-sm-8"><?php echo '￥'.number_format($v['settlementGoodsDetail']['amount_actual']/100,2);?></p>
                                </div>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">确定人民币结算单价 </label>
                                    <p class="form-control-static col-sm-8"><?php echo '￥'.number_format($v['settlementGoodsDetail']['price_actual']/100,2);?></p>
                                </div>
                            </div>
                        </fieldset>
                        <?php } ?>
                        <fieldset>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">结算单据 </label>
                                    <?php
                                    if(!empty($v['settleFiles'])){
                                        foreach ($v['settleFiles'] as $sf){
                                            echo '<p class="form-control-static col-sm-offset-4"><a href="/stockBatchSettlement/getFile/?id='.$sf['id'].'&fileName='.$sf['name'].'" target="_blank" class="btn btn-primary btn-xs">'.$sf['name'].'</a></p>';
                                        }
                                    }else{
                                        echo '<p class="form-control-static col-sm-8">无</p>';
                                    }?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">其他附件 </label>
                                    <?php
                                    if(!empty($v['goodsOtherFiles'])){
                                        foreach ($v['goodsOtherFiles'] as $sf){
                                            echo '<p class="form-control-static col-sm-offset-4"><a href="/stockBatchSettlement/getFile/?id='.$sf['id'].'&fileName='.$sf['name'].'" target="_blank" class="btn btn-primary btn-xs">'.$sf['name'].'</a></p>';
                                        }
                                    }else{
                                        echo '<p class="form-control-static col-sm-8">无</p>';
                                    }?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-8" style="margin-left: -4px;">
                                    <label class="col-sm-2 control-label">备注说明 </label>
                                    <p class="form-control-static col-sm-10"><?php echo $v['remark'];?></p>
                                </div>
                            </div>
                        </fieldset>
                    </fieldset>
                </form>
            </div>
            <?php endforeach;?>
           <?php endif;?>
            <div class="modal fade in" id="buy_lock_dialog">
                <div class="modal-dialog" style="width:80%">
                    <div class="modal-content">
                        <div class="modal-header" >
                            <a class="close" data-dismiss="modal">×</a>
                            <h5>锁价/转月记录</h5>
                        </div>
                        <div class="modal-body" id="buy_lock_dialog_body">
                        </div>
                        <div class="modal-footer">
                            <input type="button" value="&nbsp;关闭&nbsp;" class="btn btn-success btn-sm" data-dismiss="modal">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
      <?php endforeach;?>
    <?php endif;?>


    <script>
        $(".hideBtn").show();
        $(".showBtn").hide();
        function showDetail(id){
            $('#displayDetail_'+id).show();
            $("#hideDetail_" + id).show();
            $("#showDetail_"+id).hide();
        }

        function hideDetail(id){
            $('#displayDetail_'+id).hide();
            $("#hideDetail_" + id).hide();
            $("#showDetail_"+id).show();
        }

        function displayLockPriceDetail(batch_id, goods_id) {
            $.ajax({
                data: {
                    batch_id:batch_id,
                    goods_id:goods_id
                },
                url:"/stockBatchSettlement/ajaxGetBuyLockList",
                method:'post',
                success:function(res) {
                    $("#buy_lock_dialog_body").html(res);
                    $("#buy_lock_dialog").modal("show");
                },
                error:function(res) {
                    layer.alert("操作失败！" + res.responseText, {icon: 5});
                }
            });
        }

        $("div.link").unbind('click').click(function () {
            $(this).next().toggle();
        });
    </script>
