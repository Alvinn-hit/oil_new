<section class="content" id="content">

    <?php
    $menus = [['text'=>'出库管理'],['text'=>'发货单审核','link'=>'/check9/?search[checkStatus]=1'], ['text' => $this->pageTitle]];
    $buttons = [];
    $buttons[] = ['text' => '通过', 'attr' => ['data-bind' => 'click:doPass, html:passText']];
    $buttons[] = ['text' => '驳回', 'attr' => ['data-bind' => 'click:doReject, html:backText', 'class_abbr'=>'action-default-base']];
    $this->loadHeaderWithNewUI($menus, $buttons, true);
    ?>

    <div class="card-wrapper">
        <?php
        $deliveryOrderModel = DeliveryOrder::model()->findByPk($data['obj_id']);
        $this->renderPartial("/deliveryOrder/partial/new_deliveryOrderInfoCard", array('deliveryOrder'=>$deliveryOrderModel));
        ?>

        <div class="z-card">
            <h3 class="z-card-header">
                审核信息
            </h3>
            <div class="z-card-body">
                <form role="form" id="mainForm">
                    <div class="flex-grid">
                        <label class="col col-count-1 field">
                            <p class="form-cell-title">审核意见</p>
                            <textarea class="form-control" cols="105" rows="3" data-bind="value:remark" placeholder="审核意见"></textarea>
                        </label>
                    </div>
                </form>
            </div>
        </div>

        <?php
        $checkLogs = FlowService::getCheckLog($deliveryOrderModel->order_id, $this->businessId);
        $this->renderPartial("/common/new_checkLogList", array('checkLogs' => $checkLogs, 'map_name' => 'transection_check_status'));
        ?>
    </div>
</section>
<script type="text/javascript">
	var view;
	$(function () {
		view = new ViewModel(<?php echo json_encode($data) ?>);
		ko.applyBindings(view);
	});

	function ViewModel(option) {
		var defaults = {
			check_id: 0,
			remark: '',
			status: 0,
		};
		var o = $.extend(defaults, option);
		var self = this;
		self.check_id = o.check_id;
		self.status = ko.observable(o.status);
        self.remark = ko.observable(o.remark).extend({required:true,maxLength:512});
		self.errors = ko.validation.group(self);
		self.passText = ko.observable('通过');
		self.backText = ko.observable('驳回');
		self.actionState = 0;
		self.isValid = function () {
			return self.errors().length === 0;
		};
		self.doPass = function () {
			self.status(1);
			self.sendApprovalAjax();
		}
		self.doReject = function () {
			self.status(-1);
			self.sendApprovalAjax();
		}
		self.sendApprovalAjax = function () {
            if(!self.isValid()){
                self.errors.showAllMessages();
                return;
            }

            if(self.actionState==1)
                return;

            var confirmInfo = '通过该发货单审核';
            if (self.status() == -1) {
                confirmInfo = '驳回该发货单审核';
            }

            inc.vueConfirm({content:"您确定要" + confirmInfo + "，该操作不可逆？", type: 'warning',onConfirm:function(){
                var formData = {
                    obj: {
                        remark: self.remark(),
                        check_id: self.check_id,
                        checkStatus: self.status(),
                    }
                };

                if (self.status() == -1) {
                    self.backText('驳回' + inc.loadingIco);
                } else {
                    self.passText('通过' + inc.loadingIco);
                }
                self.actionState = 1;
                $.ajax({
                    type: "POST",
                    url: "/<?php echo $this->getId() ?>/save",
                    data: formData,
                    dataType: "json",
                    success: function (json) {
                        self.actionState = 0;
                        self.passText("通过");
                        self.backText("驳回");
                        if (json.state == 0) {
                            inc.vueMessage({duration: 500,type: 'success', message: '操作成功',onClose:function(){
                                    location.href = "/<?php echo $this->getId() ?>";
                                }});
                        } else {
                            inc.vueAlert({title:  '错误',content: json.data});
                        }
                    },
                    error: function (data) {
                        self.actionState = 0;
                        self.passText("通过");
                        self.backText("驳回");
                        inc.vueAlert({title:  '错误',content: "操作失败！" + data.responseText});
                    }
                });
            }});
		}

		self.back = function () {
			location.href = "/<?php echo $this->getId() ?>/?search[checkStatus]=1";
		}
	}
</script>

