<script src="/js/bootstrap3-typeahead.min.js"></script>
<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<link rel="stylesheet" href="/css/style/addnewproject.css">
<style type="text/css">
    .p-contract-code{height: 32px;line-height: 32px;}
</style>
<?php
$menus = [['text' => '流水认领', 'link' => '/receiveConfirm/'], ['text' => '认领']];
$buttons = [];
$buttons[] = ['text' => '保存', 'attr' => ['data-bind' => 'click:save, html:saveBtnText', 'id' => 'saveButton','class_abbr'=>'action-default-base']];
$buttons[] = ['text' => '提交', 'attr' => ['data-bind' => 'click:submit, html:submitBtnText', 'id' => 'submitButton']];
$this->loadHeaderWithNewUI([], $buttons, '/receiveConfirm/');
?>
<section class="content">
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>银行流水</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <ul class="item-com">
            <li>
                <label>认领人：</label>
                <p><?php echo $receiveConfirm['creator'] ?></p>
            </li>
            <li>
                <label>可认领金额：</label>
                <p class="form-control-static"><?php echo $this->map['currency'][$bankFlow->currency]['ico'] ?><?php echo number_format(($bankFlow->amount - $bankFlow->amount_claim) / 100, 2) ?></p>
            </li>
            <?php
            $this->renderPartial("/common/new_bankFlowDetail", array('bankFlow' => $bankFlow));
            ?>
        </ul>
        <div class="flex-grid">
            <label class="col col-count-1 field flex-grid">
                <p class="form-cell-title w-fixed">备注:</p>
                <span><?php echo $bankFlow->remark ?></span>
            </label>
        </div>

    </div>


    <form role="form" id="mainForm">

        <div class="card-wrapper">

            <div class="z-card">
                <div class="z-card-part">
                    <h3 class="z-body-header">基础信息填写</h3>
                    <div class="flex-grid form-group">
                        <label for="type" class="col col-count-3 field">
                            <p class="form-cell-title">货款（外部）合同编号</p>
                            <input class="form-control" data-bind="typeahead: contract_code, source:sourceFunction" oninput="this.value=this.value.trim()"/>
                            <input class="form-control" type="hidden" data-bind="typeahead: contract_id"/>
                            <input class="form-control" type="hidden" data-bind="value: receive_contract_code"/>
                        </label>
                        <label for="type" class="col col-count-3 field">
                            <p class="form-cell-title">货款合同类型</p>
                            <input class="form-control" data-bind="value:contract_type_str" disabled/>
                        </label>
                        <label for="type" class="col col-count-3 field">
                            <p class="form-cell-title">项目编号</p>
                            <input class="form-control"
                                   data-bind="visible:contract_id()==0,typeahead: project_code, source:sourceProjectFunction"/>
                            <span data-bind="visible:contract_id()>0"><input class="form-control" data-bind="value:project_code" disabled/></span>
<!--                            <p class="form-control-static" data-bind="visible:contract_id()>0,text: project_code"></p>-->
                            <input class="form-control" type="hidden" data-bind="typeahead: project_id"/>
                        </label>
                    </div>

                    <div class="flex-grid form-group">
                        <label for="type" class="col col-count-3 field">
                            <p class="form-cell-title">项目类型</p>
                            <input class="form-control" data-bind="value:project_type_str" disabled/>
                        </label>
                        <label for="type" class="col col-count-3 field">
                            <p class="form-cell-title">收款合同类型</p>
                            <select data-live-search="true" class="selectpicker show-menu-arrow form-control" title="收款合同类型" id="sub_contract_type"
                                    name="obj[sub_contract_type]"
                                    data-bind="value: sub_contract_type">
                                <option value="0">请选择收款合同类型</option>
                                <?php foreach ($this->map['contract_category'] as $key => $category): ?>
                                    <option value="<?php echo $key ?>"><?php echo $category ?></option>
                                <?php endforeach ?>
                            </select>
                        </label>
                        <label for="type" class="col col-count-3 field">
                            <p class="form-cell-title">收款合同编号</p>
                            <input class="form-control"
                                   data-bind="visible:contract_code_editable(),value:sub_contract_code"/>
                            <p class="form-control-static p-contract-code"
                               data-bind="visible:!contract_code_editable(),text:sub_contract_code"></p>
                        </label>
                    </div>

                    <div class="flex-grid form-group">
                        <label for="type" class="col col-count-3 field">
                            <p class="form-cell-title">用途 <span class="must-fill"></span></p>
                            <select class="selectpicker show-menu-arrow form-control" title="请选择用途" id="subject" name="obj[subject]"
                                data-live-search="true"  data-bind="selectpicker:subject,valueAllowUnset: true">
                                <?php
                                $subjects = SubjectService::getActiveSubjects();
                                foreach ($subjects as $v) {
                                    echo "<option value='" . $v["subject_id"] . "'>" . $v["name"] . "</option>";
                                } ?>
                            </select>
                        </label>
                    </div>

                </div>

                <div class="z-card-part" data-bind="visible:contract_id()>0">
                    <h3 class="z-body-header">收款计划选择</h3>
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th style="width:60px;">选择</th>
                            <th style="width:120px;">期数</th>
                            <th style="width:120px; text-align: left;">预计收款日期</th>
                            <th style="width:120px; text-align: left;">收款类别</th>
                            <th style="width:100px; text-align: left;">币种</th>
                            <th style="width:200px; text-align: left;">金额</th>
                            <th style="width:200px; text-align: left;">已收金额</th>
                            <th style="width:200px; text-align: left;">未收金额</th>
                            <th style="width:200px; text-align: left;">认领金额</th>
                        </tr>
                        </thead>
                        <tbody data-bind="foreach:payment_plans">
                        <tr>
                            <td>
                                <input type="checkbox" style="width: auto" data-bind="checked:$data.check">
                            </td>
                            <td>
                                <p class="form-control-static" data-bind="text:period"></p>
                            </td>
                            <td>
                                <p class="form-control-static" data-bind="text:pay_date"></p>
                            </td>
                            <td>
                                <p class="form-control-static" data-bind="text:type_str"></p>
                            </td>
                            <td>
                                <p class="form-control-static" data-bind="text:currency_str"></p>
                            </td>
                            <td>
                                <p class="form-control-static" data-bind="moneyText:amount"></p>
                            </td>
                            <td>
                                <p class="form-control-static" data-bind="moneyText:amount_paid"></p>
                            </td>
                            <td>
                                <p class="form-control-static" data-bind="moneyText:less_maount"></p>
                            </td>
                            <td>
                                <input class="form-control" data-bind="money:$data.amount_input">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-wrapper">

                <div class="z-card">
                    <div class="z-card-part ">
                        <h3 class="z-body-header">认领信息</h3>
                        <div class="flex-grid form-group">
                            <label class="col col-count-1 field ">
                                <p class="form-cell-title must-fill">认领金额</p>
                                <div class="input-group" data-bind="visible:amount_editable()">
                                    <span class="input-group-addon"><span><?php echo $this->map['currency'][$bankFlow->currency]['ico']?></span></span>
                                    <input type="text" class="form-control" id="amount" name="obj[amount]"
                                           placeholder="认领金额"
                                           data-bind="money:amount">
                                    <span class="input-group-addon" data-bind="moneyChineseText:amount"></span>
                                </div>
                                <div class="input-group" data-bind="visible:!amount_editable()">
                                    <input class="form-control" type="text" readonly="readonly"
                                           data-bind="money:amount_str(),visible:!amount_editable()"/>
                                    <span class="input-group-addon" data-bind="moneyChineseText:amount_str"></span>
                                </div>
                            </label>
                        </div>
                        <div class="flex-grid form-group">
                            <label class="col col-count-1 field">
                                <p class="form-cell-title">备注</p>
                                <textarea type="text" class="form-control" id="remark" name="obj[remark]"
                                          placeholder="备注"
                                          data-bind="value:remark" rows="3"></textarea>
                            </label>
                        </div>
                        <div class="flex-grid form-group">
                            <div class="col col-count-1 field ">
                                <?php
                                $attachType = $this->map["receive_confirm_file_type"][ConstantMap::RECEIVE_CONFIRM_ATTACH_TYPE];
                                ?>
                                <p class="form-cell-title">
                                    <?php echo $attachType["name"] ?>
                                </p>
                                <div class="form-group-custom-upload">

                                    <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_uploadNew.php"; ?>
                                    <!-- ko component: {
                             name: "file-upload",
                             params: {
                                         status:fileUploadStatus,
                                         controller:"<?php echo $this->getId() ?>",
                                         fileConfig:<?php echo json_encode($attachType) ?>,
                                         files:<?php echo json_encode($attachments[ConstantMap::RECEIVE_CONFIRM_ATTACH_TYPE]); ?>,
                                         fileParams: {
                                            id:receive_id
                                         }
                                         }
                         } -->
                                    <!-- /ko -->
                                </div>
                            </div>
                        </div>

                    </div>


                </div>
            </div>

    </form>

    </div>
</section><!--end content-->
<script>
    var view;
    $(function () {
        view = new ViewModel(
            <?php echo json_encode(
                array(
                    'flow_id' => $bankFlow->flow_id,
                    'corporation_id' => $bankFlow->corporation_id,
                    'receive_id' => empty($receiveConfirm['receive_id']) ? '' : $receiveConfirm['receive_id'],
                    'contract_id' => empty($receiveConfirm['contract_id']) ? '' : $receiveConfirm['contract_id'],
                    'contract_code' => empty($receiveConfirm['contract_code']) ? '' : $receiveConfirm['contract_code'],
                    'contract_type' => empty($receiveConfirm['contract_type']) ? '' : $receiveConfirm['contract_type'],
                    'project_id' => empty($receiveConfirm['project_id']) ? '' : $receiveConfirm['project_id'],
                    'project_code' => empty($receiveConfirm['project_code']) ? '' : $receiveConfirm['project_code'],
                    'project_type' => empty($receiveConfirm['project_type']) ? '' : $receiveConfirm['project_type'],
                    'sub_contract_type' => empty($receiveConfirm['sub_contract_type']) ? '' : $receiveConfirm['sub_contract_type'],
                    'sub_contract_code' => empty($receiveConfirm['sub_contract_code']) ? '' : $receiveConfirm['sub_contract_code'],
                    'receive_contract_code'=>empty($receiveConfirm['sub_contract_code']) ? '' : $receiveConfirm['sub_contract_code'],
                    'subject' => empty($receiveConfirm['subject']) ? 0 : $receiveConfirm['subject'],
                    'attachments' => $attachs,
                    'payment_plans' => empty($payments) ? array() : $payments,
                    'buy_sell_type' => $this->map['buy_sell_type'],
                    'buy_sub_contract_type' => $this->map['contract_category'],
                    'project_types' => $this->map['project_type'],
                    'contract_category_buy_type' => array_keys($this->map['contract_category_buy_type']),
                    'contract_category_sell_type' => array_keys($this->map['contract_category_sell_type']),
                    'allAccounts' => Account::getActiveAccounts(),
                    'partners' => PartnerService::getPartners(),
                    'currencies' => array_values($this->map['currency']),
                    'proceed_type' => array_values($this->map['proceed_type']),
                    'currency' => array_values($this->map['currency']),
                    'amount' => empty($receiveConfirm['amount']) ? '' : $receiveConfirm['amount'],
                    'remark' => empty($receiveConfirm['remark']) ? '' : $receiveConfirm['remark'],
                )
            )
            ?>);
        ko.applyBindings(view);
    });
    
    function ViewModel(option) {
        var defaults = {
            flow_id: '',
            receive_id: '',
            corporation_id: '',
            contract_id: "",
            contract_code: "",
            contract_type: "",
            project_id: "",
            project_code: "",
            project_type: "",
            buy_sell_type: [],
            project_types: [],
            sub_contract_type: 1,
            sub_contract_code: '',
            receive_contract_code:'',
            amount: "",
            subject: 0,
            remark: "",
            payment_plans: [],
            proceed_type: [],
            currency: [],
            contract_category_buy_type: [],
            contract_category_sell_type: [],
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.flow_id = o.flow_id;
        self.receive_id = o.receive_id;
        self.corporation_id = o.corporation_id;
        self.contract_id = ko.observable(o.contract_id);
        self.project_id = ko.observable(o.project_id);
        self.contract_code = ko.observable(o.contract_code);
        self.receive_contract_code = ko.observable(o.receive_contract_code);
        self.project_code = ko.observable(o.project_code);
        self.contract_type = ko.observable(o.contract_type);
        self.project_type = ko.observable(o.project_type);

        self.contractUrl = ko.observable('/receiveConfirm/ajaxContract?corporation_id=' + self.corporation_id);
        self.projectUrl = ko.observable('/receiveConfirm/ajaxProject?corporation_id=' + self.corporation_id);
        self.urlOptions = ko.observableArray();
        self.urlProjectOptions = ko.observableArray();
        self.sourceFunction = function (query, process) {
            $.ajax({
                url: self.contractUrl(),
                data: {
                    search: query,
                },
                method: 'post',
                dataType: 'json',
                async: false,
                success: function (data) {
                    if (data.state == 0) {
                        var options = [], retData = data.data;
                        for (var i = 0; i <= retData.length - 1; i++) {
                            var thisData = new ContractModel({
                                'id': retData[i].contract_id,
                                'name': retData[i].code_out==''||retData[i].code_out==null?retData[i].contract_code : (retData[i].contract_code + '(' +retData[i].code_out)+')',
                                'receive_contract_code':retData[i].contract_code,
                                'type': retData[i].type,
                                'project_id': retData[i].project_id,
                                'project_code': retData[i].project_code,
                                'project_type': retData[i].project_type,
                                'code_out':retData[i].code_out,
                            });
                            options.push(thisData);
                        }
                        self.urlOptions(options);
                        if ($.isFunction(process)) {
                            process(options);
                        }
                    }
                },
                error: function (res) {

                }
            });
        }

        self.sourceProjectFunction = function (query, process) {
            $.ajax({
                url: self.projectUrl(),
                data: {
                    search: query,
                },
                method: 'post',
                dataType: 'json',
                async: false,
                success: function (data) {
                    if (data.state == 0) {
                        var options = [], retData = data.data;
                        for (var i = 0; i <= retData.length - 1; i++) {
                            var thisData = new ProjecoptiontModel({
                                'id': retData[i].project_id,
                                'name': retData[i].project_code,
                                'type': retData[i].type,
                            });
                            options.push(thisData);
                        }
                        self.urlProjectOptions(options);
                        if ($.isFunction(process)) {
                            process(options);
                        }
                    }
                },
                error: function (res) {

                }
            });
        }

        self.buy_sell_type = o.buy_sell_type;
        self.proceed_type = o.proceed_type;
        self.currency = o.currency;
        self.project_types = o.project_types;
        self.contract_category = ko.observable(o.contract_category);
        self.contract_category_buy_type = o.contract_category_buy_type;
        self.contract_category_sell_type = o.contract_category_sell_type;
        self.sub_contract_type = ko.observable(o.sub_contract_type);
        self.sub_contract_code = ko.observable(o.sub_contract_code);
        self.amount = ko.observable(o.amount);
        self.subject = ko.observable(o.subject).extend({
            custom: {
                params: function (v) {
                    if (v > 0) {
                        return true;
                    }
                    else
                        return false;
                },
                message: "请选择用途"
            }
        });
        self.remark = ko.observable(o.remark);

        var plans = [];
        if (self.contract_type() == 2) {
            for (var ind = 0; ind < o.payment_plans.length; ind++) {
                plans.push(new PaymentModel(o.payment_plans[ind], self.proceed_type, self.currency));
            }
        }
        self.payment_plans = ko.observableArray(plans);

        self.fileUploadStatus = ko.observable();

        self.contract_code.extend({
            custom: {
                params: function (v) {
                    return !(self.contract_id() == 0 && v != '');
                },
                message: "请填写该主体下的合同编号, 或置空"
            }
        });

        self.sub_contract_code.extend({
            custom: {
                params: function (v) {
                    return !(self.sub_contract_type() != 0 && v == '');
                },
                message: "请填写收款合同编码"
            }
        });

        self.contract_code.subscribe(function (newValue) {
            if (newValue != '' && self.urlOptions().length > 0) {
                var options = self.urlOptions();
                for (var i = options.length - 1; i >= 0; i--) {
                    if (options[i].name == newValue) {
                        self.contract_id(options[i].id);
                        self.contract_type(options[i].type);
                        self.project_id(options[i].project_id);
                        self.project_code(options[i].project_code);
                        self.project_type(options[i].project_type);
                        self.receive_contract_code(options[i].receive_contract_code);
                        break;
                    } else {
                        self.contract_id(0);
                        self.contract_type(0);
                    }
                }
                if ($.inArray(parseInt(this.sub_contract_type()), this.contract_category_buy_type) >= 0 && this.contract_type() == 1) {
                    this.sub_contract_code(self.receive_contract_code());
                }
                if ($.inArray(parseInt(this.sub_contract_type()), this.contract_category_sell_type) >= 0 && this.contract_type() == 2) {
                    this.sub_contract_code(self.receive_contract_code());
                }
            } else {
                self.contract_id(0);
                self.contract_type(0);
                self.project_id(0);
                self.project_code('');
                self.project_type(0);
            }
        }, this);

        self.contract_type.subscribe(function (newValue) {
            // if(newValue == 1 && this.contract_id() > 0) {
            //     // 当选择的合同类型为采购合同的时候收款合同不能改
            //     this.sub_contract_code(this.contract_code());
            // }
        }, self);

        self.sub_contract_type.subscribe(function (newValue) {
            if ($.inArray(parseInt(newValue), this.contract_category_buy_type) >= 0 && this.contract_type() == 1) {
                this.sub_contract_code(this.receive_contract_code());
            } else if ($.inArray(parseInt(newValue), this.contract_category_sell_type) >= 0 && this.contract_type() == 2) {
                this.sub_contract_code(this.receive_contract_code());
            }
        }, self);

        self.sub_contract_type.subscribe(function (newValue) {
            if ($.inArray(parseInt(newValue), this.contract_category_buy_type) >= 0 && this.contract_type() == 1) {
                this.sub_contract_code('');
            } else if ($.inArray(parseInt(newValue), this.contract_category_sell_type) >= 0 && this.contract_type() == 2) {
                this.sub_contract_code('');
            }
        }, self, 'beforeChange');

        self.project_code.extend({
            custom: {
                params: function (v) {
                    return !(self.project_id() == 0 && v != '');
                },
                message: "请填写该主体已录入系统的项目编号, 或置空"
            }
        });

        self.project_code.subscribe(function (newValue) {
            if (self.contract_id() == 0 || self.contract_id() == '') {
                if (newValue != '' && self.urlProjectOptions().length > 0) {
                    var options = self.urlProjectOptions();
                    for (var i = options.length - 1; i >= 0; i--) {
                        if (options[i].name == newValue) {
                            self.project_id(options[i].id);
                            self.project_code(options[i].name);
                            self.project_type(options[i].type);
                            break;
                        } else {
                            self.project_id(0);
                            self.project_type(0);
                        }
                    }
                } else {
                    self.project_id(0);
                    self.project_type(0);
                }
            }
        }, this);

        self.contract_id.subscribe(function (newValue) {
            self.payment_plans([]);
            if (newValue != '' && newValue > 0) {
                $.ajax({
                    url: '/receiveConfirm/ajaxContractPayments',
                    data: {
                        search: newValue,
                    },
                    method: 'post',
                    dataType: 'json',
                    success: function (ret) {
                        if (ret.state == 0 && self.contract_type() == 2) {
                            self.payment_plans([]);
                            var newPayments = [];
                            var payments = ret.data;
                            for (var ind in payments) {
                                newPayments.push(new PaymentModel(payments[ind], self.proceed_type, self.currency));
                            }
                            self.payment_plans(newPayments);
                        }
                    },
                    error: function (ret) {

                    }
                });
            }
        }, self);

        self.contract_code_editable = ko.computed(function () {
            return (this.contract_type() == 0) || ($.inArray(parseInt(this.sub_contract_type()), this.contract_category_buy_type) < 0 && this.contract_type() == 1) || ($.inArray(parseInt(this.sub_contract_type()), this.contract_category_sell_type) < 0 && this.contract_type() == 2);
        }, this);

        self.contract_type_str = ko.computed(function () {
            if (this.contract_type() > 0) {
                for (var i in this.buy_sell_type) {
                    if (i == this.contract_type()) {
                        return this.buy_sell_type[i];
                    }
                }
            }
            return '';
        }, this);

        self.project_type_str = ko.computed(function () {
            if (this.project_type() > 0) {
                for (var i in this.project_types) {
                    if (i == this.project_type()) {
                        return this.project_types[i];
                    }
                }
            }
            return '';
        }, this);

        self.amount_str = ko.computed(function () {
            if (this.payment_plans().length > 0) {
                var payments = this.payment_plans();
                var amount = 0;
                for (var ind in payments) {
                    if (payments[ind].check()) {
                        amount += parseInt(payments[ind].amount_input());
                    }
                }
                return amount;
            }
            return '';
        }, this);

        self.amount_editable = ko.computed(function () {
            if (this.payment_plans().length > 0) {
                var payments = this.payment_plans();
                var amount = 0;
                for (var ind in payments) {
                    if (payments[ind].check()) {
                        return false;
                    }
                }
            }
            return true;
        }, this);

        self.status = ko.observable(0);

        self.amount.extend({
            custom: {
                params: function (v) {
                    if (self.payment_plans().length == 0 && (v == '' || v == 0)) {
                        return false;
                    }
                    return true;
                },
                message: "请填写认领金额"
            }
        });

        self.getPostData = function () {
            self.payments = [];

            if (Array.isArray(self.payment_plans()) && self.payment_plans().length > 0) {
                ko.utils.arrayForEach(self.payment_plans(), function (item, i) {
                    self.payments[i] = inc.getPostData(item, ["less_maount", "currency_str", "type_str", "pay_date",]);
                });
            }
            return inc.getPostData(self, ["buy_sell_type", "project_types", "payment_plans", "proceed_type", "currency", "contract_category", "project_types", "isSubmit", "submitBtnText", "saveBtnText", "payment_plans", "urlOptions"]);
        }

        self.errors = ko.validation.group(self);

        self.isValid = function () {
            return self.errors().length === 0;
        }

        self.submit = function () {
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }
			inc.vueConfirm({
				content: "是否确认提交流水认领单，本操作无法撤回？", onConfirm: function () {
					self.status(1);
					self.post();
				}
            });
        }

        self.save = function () {
            self.status(0);
            self.post();
        }

        self.post = function () {
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }
            var formData = self.getPostData();
            self.isSubmit(1);
            $.ajax({
                type: "POST",
                url: "/<?php echo $this->getId() ?>/save",
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        inc.vueMessage({message:"操作成功"});
                        location.href = "/<?php echo $this->getId() ?>/detail?id=" + json.data;
                    } else {
                        // layer.alert(json.data, {icon: 5});
                        self.submitBtnText("提交");
                        self.saveBtnText("保存");
                        self.isSubmit(0);
                        inc.vueAlert(json.data);
                    }
                },
                error: function (data) {
                    // layer.alert("保存失败！" + data.responseText, {icon: 5});
                    self.submitBtnText("提交");
                    self.saveBtnText("保存");
                    self.isSubmit(0);
                    inc.vueAlert("操作失败！" + data.responseText);
                }
            });
        }

        self.back = function () {
//            window.location.href="/<?php //echo $this->getId() ?>///";
            if (document.referrer) {
                location.href = document.referrer;
            } else {
                location.href = '/<?php echo $this->getId(); ?>/';
            }
        }


        self.isSubmit = ko.observable(0);
        self.submitBtnText = ko.observable("提交");
        self.saveBtnText = ko.observable("保存");

    }

    var ContractModel = function (options) {
        var o = {
            'id': 0,
            'name': '',
            'type': '',
            'project_id': 0,
            'project_code': '',
            'project_type': '',
            'code_out':'',
            'receive_contract_code':'',
        }
        o = $.extend(o, options);
        var self = this;
        this.id = o.id;
        this.name = o.name;
        this.type = o.type;
        this.project_id = o.project_id;
        this.project_code = o.project_code;
        this.project_type = o.project_type;
        this.receive_contract_code = o.receive_contract_code;
    }

    var ProjectModel = function (options) {
        var o = {
            'id': 0,
            'name': '',
            'type': '',
        }
        o = $.extend(o, options);
        var self = this;
        this.id = o.id;
        this.name = o.name;
        this.type = o.type;
    }

    var PaymentModel = function (options, proceed_type, currency) {
        var o = {
            'plan_id': 0,
            'project_id': '',
            'contract_id': '',
            'type': 0,
            'expense_type': '',
            'expense_name': '',
            'amount': '',
            'amount_paid': '',
            'currency': '',
            'period': '',
            'pay_date': '',
            'check': false,
            'amount_input': 0,
        }
        o = $.extend(o, options);
        var self = this;
        self.plan_id = o.plan_id;
        self.project_id = o.project_id;
        self.contract_id = o.contract_id;
        self.type = o.type;
        self.expense_type = o.expense_type;
        self.expense_name = o.expense_name;
        self.amount = o.amount;
        self.amount_paid = o.amount_paid;
        self.less_maount = o.amount - o.amount_paid;
        self.currency = o.currency;
        self.period = o.period;
        self.pay_date = o.pay_date;
        self.type_str = '';
        self.currency_str = '';
        for (var ind in proceed_type) {
            if (proceed_type[ind]['id'] == self.expense_type) {
                self.type_str = proceed_type[ind]['name'];
                break;
            }
        }
        if (self.expense_type == 5) {
            self.type_str = self.type_str + ' : ' + self.expense_name;
        }
        for (var ind in currency) {
            if (currency[ind]['id'] == self.currency) {
                self.currency_str = currency[ind]['name'];
                break;
            }
        }

        self.check = ko.observable(o.check);
        self.amount_input = ko.observable(o.amount_input);

        self.amount_input.extend({
            custom: {
                params: function (v) {
                    if (self.check() == 1 && (v == '' || v == 0)) {
                        return false;
                    }
                    return true;
                },
                message: "请填写认领金额"
            }
        });
    }
</script>