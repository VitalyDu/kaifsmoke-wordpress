class EasyGet {
    static updateParam(key, value) {
        let get_params = window.location.search.substr(1);
        let state = '';
        if (get_params === '') {
            state = key + '=' + value;
        } else {
            get_params = get_params.split('&');
            let param_found = false;
            get_params.forEach((param, i) => {
                param = param.split('=');
                if (param[0] === key) {
                    get_params[i] = key + '=' + value;
                    param_found = true;
                }
            });
            if (!param_found) {
                get_params.push(key + '=' + value);
            }
            state = get_params.join('&');
        }
        history.pushState({}, "", '?' + state);

        return true;
    }

    static getParam(key) {
        let get_params = window.location.search.substr(1);
        let result = null;

        if (get_params !== '') {
            get_params = get_params.split('&');
            get_params.forEach((param, i) => {
                param = param.split('=');
                if (param[0] === key) {
                    result = param[1];
                }
            });
        }

        return result;
    }
}