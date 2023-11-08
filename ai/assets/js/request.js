class RestRequest {
    constructor(route, params, method, success_callback, fail_callback, error_callback) {
        this.success_callback = success_callback;
        this.fail_callback = fail_callback;
        this.error_callback = error_callback;
        this.route = route;
        this.params = params;
        this.method = method;
    }

    twbb_send_rest_request() {

        if (twbb_admin_vars.limitation_expired == "1") {
            this.show_error('plan_limit_exceeded');
            this.fail_callback({'data': 'plan_limit_exceeded'});
            return;
        }

        this.twbb_rest_request(this.route, this.params, this.method, function (that) {
            that.get_ai_data();
        });
    }

    twbb_rest_request(route, params, method, callback) {
        let rest_route = twbb_admin_vars.rest_route + "/" + route;
        let form_data = null;
        if (params) {
            form_data = new FormData();
            for (let param_name in params) {
                form_data.append(param_name, params[param_name]);
            }
        }

        fetch(rest_route, {
            method: method,
            headers: {
                'X-WP-Nonce': twbb_admin_vars.ajaxnonce
            },
            body: form_data,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data['success']) {
                    this.data = data;
                    callback(this);
                } else {
                    this.fail_result(data);
                }
            }).catch((error) => {
            this.error_callback(error);
        });
    }

    fail_result(err) {
        this.show_error(err.data);
        this.fail_callback(err);
    }

    get_ai_data() {
        let self = this;
        setTimeout(function () {
            self.twbb_rest_request('ai_output', null, "GET", function (success) {
                success = success.data;

                if (success['data']['status'] !== 'done') {
                    self.get_ai_data();
                } else {
                    if (!success['data']['output']) {
                        this.show_error("something_wrong");
                        self.fail_callback(success);
                    } else {
                        self.success_callback(success);
                    }
                }
            })
        }, 3000);
    }

    show_error( notif_key ) {
      if( notif_key == 'plan_limit_exceeded' ) {
        if (twbb_admin_vars.plan == 'Free') {
            notif_key = 'free_limit_reached';
        } else {
            notif_key = 'plan_limit_reached';
        }
      }
      if (typeof twbb_admin_vars.error_data[notif_key] === "undefined") {
         notif_key = "something_wrong";
      }

      let message = twbb_admin_vars.error_data[notif_key]['text'];
      jQuery(document).find(".twbb-ai-error-message").text(message).show();
    }
}
