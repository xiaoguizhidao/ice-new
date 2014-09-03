var CAData = Class.create();
CAData.prototype = {
	initialize: function(uploadShipUrl, uploadUrl, importUrl) {
        this.uploadShipUrl = uploadShipUrl;
        this.uploadUrl = uploadUrl;
        this.importUrl = importUrl;
	},

    GetData: function () {
        $('get_data_from_ca').addClassName('disabled loading');
        $('get_data_from_ca').disabled = true;
        new Ajax.Request(this.uploadUrl, {
            'method': 'post',
            parameters: '',
            'onSuccess': this.onSuccess.bind(this),
            'onFailure': this.onFailure.bind(this)
        });
    },

    GetShip: function () {
        $('get_ship_from_ca').addClassName('disabled loading');
        $('get_ship_from_ca').disabled = true;
        new Ajax.Request(this.uploadShipUrl, {
            'method': 'post',
            parameters: '',
            'onSuccess': this.Succ,
            'onFailure': this.Fail
        });
    },

    Succ: function(transport) {
        var response = transport.responseText.evalJSON();
        if(response.message){
            alert(response.message);
        }else{
            alert('Upload complete. Success!');
        }
        $('get_ship_from_ca').removeClassName('disabled loading');
        $('get_ship_from_ca').disabled = false;

        $('ship_upload_button').removeClassName('disabled loading');
        $('ship_upload_button').disabled = false;

        window.location.reload();
    },

    Fail: function(transport) {
        var response = transport.responseText.evalJSON();
        if(response.message){
            alert(response.message);
        }else{
            alert('Some problem during shipment import.');
        }
        $('get_ship_from_ca').removeClassName('disabled loading');
        $('get_ship_from_ca').disabled = false;

        $('ship_upload_button').removeClassName('disabled loading');
        $('ship_upload_button').disabled = false;

        window.location.reload();
    },

    ImportProd: function () {
        alert('1');
        $('import_products_btn').addClassName('disabled loading');
        $('import_products_btn').disabled = true;
        new Ajax.Request(this.importUrl, {
            'method': 'post',
            parameters: Form.serialize('import_product'),
            'onSuccess': this.onSuccess.bind(this),
            'onFailure': this.onFailure.bind(this)
        });
    },

    onSuccess: function(transport) {
        var response = transport.responseText.evalJSON();
        if(response.message){
            alert(response.message);
        }else{
            alert('Upload complete. Success!');
        }
        $('get_data_from_ca').removeClassName('disabled loading');
        $('get_data_from_ca').disabled = false;

        $('import_products_btn').removeClassName('disabled loading');
        $('import_products_btn').disabled = false;

    },

    onFailure: function(transport) {
        var response = transport.responseText.evalJSON();
        if(response.message){
            alert(response.message);
        }else{
            alert('Some problem during product import.');
        }
        $('get_data_from_ca').removeClassName('disabled loading');
        $('get_data_from_ca').disabled = false;

        $('import_products_btn').removeClassName('disabled loading');
        $('import_products_btn').disabled = false;
    }
};

if (typeof channelAdvisor == 'undefined') {
    channelAdvisor = {
        productForm: null,
        productGrid: null,
        runningMessage: 'Process is running',

        poller: {
            timeout: 10000,
            interval: null,

            start: function(url) {
                this.interval = setInterval(this.request.bind(this, url), this.timeout)
            },

            stop: function() {
                clearInterval(this.interval);
            },

            request: function(url) {
                Ajax.Responders.unregister(varienLoaderHandler.handler);
                new Ajax.Request(url, {
                    method: 'get',
                    onComplete: (function (response) {
                        this.onSuccess(response.responseJSON.is_running);
                    }).bind(this)
                })
            },

            onSuccess: function(isFinished) {

            }
        },

        startAction: function (form) {
            Ajax.Responders.unregister(varienLoaderHandler.handler);
            this.lock();
            new Ajax.Request(form.action, {
                'method': 'post',
                'parameters': form.serialize(true),
                'onSuccess': this.Suc,
                'onSuccess': this.Fal
            });
        },

        Suc: function(form, response) {
            if (response.responseJSON && typeof response.responseJSON.redirect != 'undefined') {
                setLocation(response.responseJSON.redirect);
            } else {
                window.location.reload();
            }
        },

        Fal: function() {
            window.location.reload();
        },

        lock: function() {
            if (this.itemForm) {
                this.lockButton($(this.itemForm).down('button'));
            }
            if (this.productForm) {
                this.lockButton($(this.productForm).down('button'));
            }
            this.addMessage();
        },

        addMessage: function() {
            var messageBox = $('messages');
            var messageList = $(messageBox).down('ul.messages');
            if (!messageList) {
                messageList = new Element('ul', {class: 'messages'});
                messageBox.update(messageList);
            }
            var message = '<li class="notice-msg">' + this.runningMessage + '</li>';
            messageList.update(message);
        },

        lockButton: function (button) {
            $(button).addClassName('disabled loading');
            $(button).disabled = true;
        }
    }
}

Event.observe(document, 'dom:loaded', function() {
    if(typeof items_massactionJsObject != 'undefined'){
        channelAdvisor.itemForm = items_massactionJsObject.form;
        items_massactionJsObject.prepareForm = items_massactionJsObject.prepareForm.wrap(function (proceed) {
            channelAdvisor.itemForm = proceed();
            channelAdvisor.itemForm.submit = function(){ channelAdvisor.startAction(this); };
            return channelAdvisor.itemForm;
        });

        channelAdvisor.productForm = channeladvisor_selection_search_grid__massactionJsObject.form;
        channeladvisor_selection_search_grid__massactionJsObject.prepareForm = channeladvisor_selection_search_grid__massactionJsObject.prepareForm.wrap(function (proceed) {
            channelAdvisor.productForm = proceed();
            channelAdvisor.productForm.submit = function() { channelAdvisor.startAction(this) };
            return channelAdvisor.productForm;
        });

        channelAdvisor.itemForm.submit = function(){ channelAdvisor.startAction(this); };
        channelAdvisor.productForm.submit = function() { channelAdvisor.startAction(this) };
        if (channelAdvisor.isProcessRunning) {
            channelAdvisor.lock();
            channelAdvisor.poller.onSuccess = function(isRunning){
                if (!isRunning) {
                    this.stop()
                    channelAdvisor.onSuccess();
                }
            }
            channelAdvisor.poller.start(channelAdvisor.statusUrl);
        }
    }
});
