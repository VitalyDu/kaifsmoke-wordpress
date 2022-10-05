var LeadbackSDK = function (config) {
    var self = this;

    self.config = config;

    self.LEADBACK_TRUST_HOST = 'https://leadback.ru';
    self.LEADBACK_SDK_EVENT  = 'ldbk-sdk-event';
    self.CODES_WINDOW_WIDTH  = 800;
    self.CODES_WINDOW_HEIGHT = 600;
    self.IS_DEBUG            = false;

    self.codesSelectWindow        = null;
    self.codeSelectCallback       = null;
    self.closeCodesWindowOnSelect = false;

    self.isInitialized = false;

    /**
     * @param trusted_host
     */
    self.setDebug = function (trusted_host) {
        self.IS_DEBUG = true;

        if (trusted_host) {
            self.log('Debug trust host set', trusted_host);
            self.LEADBACK_TRUST_HOST = trusted_host;
        }

        return self;
    };
    /**
     * @param log
     * @returns {boolean}
     */
    self.log = function (log) {

        if (!self.IS_DEBUG) {
            return false;
        }

        var args = Array.prototype.slice.call(arguments);
        args.unshift('LeadbackSDK # ' + new Date().getHours() + ':' + new Date().getMinutes() + ':' + new Date().getSeconds() + " ");
        console.log.apply(console, args);

        return self;
    };

    /**
     * @returns {*}
     */
    self.init = function () {
        if (self.isInitialized) {
            return false;
        }

        self.log('LeadBackSDK initialized.');

        self.bindAutoEvents();
        self.bindWindowEvents();

        self.isInitialized = true;

        return self;
    };

    /**
     * @returns {LeadbackSDK}
     */
    self.bindAutoEvents = function () {
        self.log('Binding auto events...');
        var select_buttons = document.querySelectorAll('[data-ldbk-btn-type="select-code"]');

        if (select_buttons && select_buttons.length) {
            for (var i = 0; i < select_buttons.length; i++) {
                select_buttons[i].addEventListener('click', self.showInstallCodesWindow);
            }
        }

        return self;
    };

    /**
     * @returns {LeadbackSDK}
     */
    self.bindWindowEvents = function () {
        self.log('Binding window events...');

        var eventMethod  = window.addEventListener ? 'addEventListener' : 'attachEvent';
        var eventer      = window[eventMethod];
        var messageEvent = eventMethod === 'attachEvent' ? 'onmessage' : 'message';

        eventer(messageEvent, self.onPostMessageHandler);

        return self;
    };

    /**
     * @returns {LeadbackSDK}
     */
    self.showInstallCodesWindow = function () {
        var left               = Math.round(screen.width / 2 - self.CODES_WINDOW_WIDTH / 2);
        var top                = Math.round(screen.height / 2 - self.CODES_WINDOW_HEIGHT / 2);
        self.codesSelectWindow = window.open(self.LEADBACK_TRUST_HOST + '/code-install/index.php' + self.getUrlParamsQueryString(), 'ldbk-codes-window', 'left=' + left + ',top=' + top + ',' + 'width=' + self.CODES_WINDOW_WIDTH + ',height=' + self.CODES_WINDOW_HEIGHT + ',personalbar=0,toolbar=0,scrollbars=1,resizable=1');

        return self;
    };

    /**
     * @returns {string}
     */
    self.getUrlParamsQueryString = function () {
        var query_string = '';

        if (self.config.url_params && typeof self.config.url_params === 'object' && Object.keys(self.config.url_params).length > 0) {
            query_string = Object.keys(self.config.url_params).map(function (key) {
                return key + '=' + self.config.url_params[key];
            }).join('&');
        }

        return query_string !== '' ? '?' + query_string : '';
    };

    /**
     * @param event
     * @returns {boolean}
     */
    self.onPostMessageHandler = function (event) {
        if (!event || !event.data || event.data.indexOf(self.LEADBACK_SDK_EVENT + ':') !== 0 || event.origin !== self.LEADBACK_TRUST_HOST) {
            return false;
        }

        self.log(event.data, event.origin, self.LEADBACK_TRUST_HOST);

        var data = event.data.replace(self.LEADBACK_SDK_EVENT + ':', '');

        try {
            data = JSON.parse(data);
        } catch (parse_exc) {
            data = null;
        }

        if (!data) {
            return false;
        }

        self.log('Postmessage SDK event', data);

        switch (data.event) {
            case 'event_code_selected':
                if (self.codeSelectCallback !== null && typeof self.codeSelectCallback === 'function' && data.code) {
                    self.codeSelectCallback(data.code);

                    if (self.closeCodesWindowOnSelect) {
                        self.codesSelectWindow.close();
                    }

                    return true;
                }

                return false;
        }
    };

    /**
     * @param callback
     * @param close_window
     */
    self.setCodeSelectCallback = function (callback, close_window) {
        if (typeof callback !== 'function') {
            throw new Error('Leadback SDK. Incorrect callback. Callback must be a function.');
        }

        self.codeSelectCallback       = callback;
        self.closeCodesWindowOnSelect = close_window === undefined || close_window !== false;

        return self;
    };

    if (config && config.debug) {
        self.setDebug(config.trusted_host)
    }

    if (!config || config.auto_init || config.auto_init === undefined) {
        self.init();
    }
};