<?php
$menus=$this->getIndexMenuWithNewUI();

$menus[] = ['text' => $this->pageTitle];
$buttons = [];
if ($this->checkButtonStatus["pass"] == 1) {
    $buttons[] = ['text' => '通过', 'attr' => ['data-bind' => 'click:doPass,html:passText', 'id' => 'passButton']];
}
if ($this->checkButtonStatus["back"] == 1) {
    $buttons[] = ['text' => '驳回', 'attr' => ['data-bind' => 'click:doBack,html:backText', 'id' => 'checkBackButton','class_abbr'=>'action-default-base']];
}
if ($this->checkButtonStatus["reject"] == 1) {
    $buttons[] = ['text' => '拒绝', 'attr' => ['data-bind' => 'click:doReject,html:rejectText', 'id' => 'rejectButton','class_abbr'=>'action-default-base']];
}
$this->loadHeaderWithNewUI($menus, $buttons, true);
?>
<section class="content sub-container">
    <?php
    if (!empty($this->detailPartialFile))
        $this->renderPartial($this->detailPartialFile, array($this->detailPartialModelName => $model));
    include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_checkItems.php";
    ?>
    <form role="form" id="mainForm">
        <div class="content-wrap">
            <div class="content-wrap-title">
                <div>
                    <p>审核信息</p>
                </div>
            </div>
            <div class="o-row">
                <?php if(Utility::isNotEmpty($items)){ ?>
                <div class="o-col-sm-4">
                    <check-items params='items: items'></check-items>
                </div>
                <div class="o-col-sm-8" style="display: flex; padding-left: 15px;">
                    <label class="w-full" style="flex: 1; display: flex; flex-direction: column">
                        <p class="form-cell-title must-fill">审核意见</p>
                        <textarea class="form-control" data-bind="value:remark" style="flex: 1"></textarea>
                    </label>
                </div>
                <?php }else{ ?>
                <div class="o-col-sm-12" style="display: flex; padding-left: 15px;">
                    <label class="w-full" style="flex: 1; display: flex; flex-direction: column">
                        <p class="form-cell-title must-fill">审核意见</p>
                        <textarea rows="4" class="form-control" data-bind="value:remark" style="flex: 1"></textarea>
                    </label>
                </div>
                <?php } ?>
            </div>
        </div>
    </form>

    <?php
    $checkLogs = FlowService::getCheckLog($data['obj_id'], $this->businessId);
    $this->renderPartial("/common/new_checkLogList", array('checkLogs' => $checkLogs));
    ?>
</section>
<script type="text/javascript">
    var view;
    $(function () {
        view = new ViewModel(<?php echo json_encode($data) ?>);
        view.formatItems(<?php echo json_encode($items) ?>);
        ko.applyBindings(view);
    });

    function ViewModel(option) {
        var defaults = {
            check_id: 0,
            detail_id: 0,
            remark: ''
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.check_id = o.check_id;
        self.detail_id = o.detail_id;
        self.status = ko.observable(o.status);
        self.items = ko.observableArray();
        self.remark = ko.observable(o.remark).extend({required: true, maxLength: 512});
        self.errors = ko.validation.group(self);

        self.passText = ko.observable('通过');
        self.backText = ko.observable('驳回');
        self.rejectText = ko.observable('拒绝');

        self.actionState = 0;
        self.isValid = function () {
            return self.errors().length === 0;
        };

        self.formatItems = function (data) {
            if (data == null || data == undefined)
                return;
            for (var i in data) {
                self.items.push(data[i]);
            }
        }

        self.confirmText = "";

        self.doPass = function () {
            self.confirmText = "通过";
            self.status(1);
            self.save();
        }
        self.doBack = function () {
            self.confirmText = "驳回";
            self.status(-1);
            self.save();
        }
        self.doReject = function () {
            self.confirmText = "拒绝";
            self.status(0);
            self.save();
        }

        self.updateButtonText = function () {
            if (self.actionState == 1) {
                switch (self.status()) {
                    case 1:
                        self.passText("通过 " + inc.loadingIco);
                        break;
                    case 0:
                        self.backText("驳回 " + inc.loadingIco);
                        break;
                    case -1:
                        self.rejectText("拒绝 " + inc.loadingIco);
                        break;
                }
            }
            else {
                switch (self.status()) {
                    case 1:
                        self.passText("通过");
                        break;
                    case 0:
                        self.backText("驳回");
                        break;
                    case -1:
                        self.rejectText("拒绝");
                        break;
                }
            }

        }

        self.save = function () {
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }

            if (self.actionState == 1)
                return;

			inc.vueConfirm({
				content: "您确定要" + self.confirmText + "该信息的审核，该操作不可逆？", onConfirm: function () {
				    console.log(self.items(),'fff');
                    var items='';
                    if(JSON.stringify(self.items()) === '[]'||JSON.stringify(self.items()) === '{}'){
                        items='';
                    }else{
                        items=self.items.getValues();
                    }
					var formData = {
						data: {
							items: items,
							check_id: self.check_id,
							detail_id: self.detail_id,
							checkStatus: self.status(),
							remark: self.remark()
						}
					};
					self.actionState = 1;
					self.updateButtonText();
					$.ajax({
						type: "POST",
						url: "/<?php echo $this->getId() ?>/save",
						data: formData,
						dataType: "json",
						success: function (json) {
							self.actionState = 0;
                            self.updateButtonText();
							if (json.state == 0) {
								/*inc.vueMessage({
									message: '操作成功',
								});
                                //location.href = "/<?php //echo $this->getId() ?>//";*/
                                inc.vueMessage({
                                    message: '操作成功',duration:500, onClose: function () {
                                        location.href = "/<?php echo $this->getId() ?>/";
                                    }
                                });
							} else if (json.state == -2) {
								inc.vueConfirm({
									content: json.data, onConfirm: function () {
										location.href = "/<?php echo $this->getId() ?>/index";
									}
								});
							} else {
								inc.vueAlert(json.data);
							}

						},
						error: function (data) {
							self.actionState = 0;
                            self.updateButtonText();
							inc.vueAlert({content: "操作失败：" + data.responseText});
						}
					});
				}
            });
        }

        self.back = function () {
            location.href = "/<?php echo $this->getId() ?>/?search[checkStatus]=1";
        }
    }
</script>
