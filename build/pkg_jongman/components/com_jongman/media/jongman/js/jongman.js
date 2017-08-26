var Jongman =
{
    /**
     * Function to watch an item
     *
     * @param    integer   i      The item number
     * @param    string    v      The name of the view
     * @param    string    fi     The form id (optional)
     * @param    string    nomsg  If set to true, will suppress success messages
     */
    watchItem: function(i, v, fi, nomsg)
    {
        var cid  = 'cb' + i;
        var c    = jQuery('#watch-' + v + '-' + i);
        var btn  = jQuery('#watch-btn-' + v + '-' + i);

        if (btn.length) {
            if (btn.hasClass('disabled') == true) {
                return;
            }
        }

        btn.addClass('disabled');

        if (c.val() == '1') {
            var act = v + '.unwatch';
            var rq  = JMlist.listItemTask(cid, act, fi, true);
        }
        else {
            var act = v + '.watch';
            var rq  = JMlist.listItemTask(cid, act, fi, true);
        }

        rq.done(function(resp)
        {
            if (Jongman.isJsonString(resp)) {
                resp = jQuery.parseJSON(resp);

                if (btn.length && resp.success == "true") {
                    if (c.val() == '0') {
                        c.val('1');
                        btn.addClass('btn-success');
                        btn.addClass('active');
                    }
                    else {
                        c.val('0');
                        btn.removeClass('btn-success');
                        btn.removeClass('active');
                    }
                }
            }
            else {
                btn.addClass('btn-danger');
            }

            btn.removeClass('disabled');
        });
    },

    isJsonString: function(str)
    {
        if (typeof str == 'undefined') return false;

        var l = str.length;
        var e = l - 1;

        if (l == 0) return false;
        if (str[0] != '{' && str[0] != '[') return false;
        if (str[e] != '}' && str[e] != ']') return false;

        return true;
    },


    /**
    * Method to display the ajax response messages
    *
    * @param    object    resp    The ajax response object
    * @param    string    err     The error message
    */
    displayMsg: function(resp, err)
    {
        var mc = jQuery('#system-message-container');

        if (typeof mc == 'undefined') return false;

        if (resp.length != 0 && typeof resp.success != 'undefined') {
            if (typeof resp.messages != 'undefined') {
                var c = (resp.success == "true") ? 'success' : 'error';
                var l = resp.messages.length;
                var x = 0;

                if (l > 0) {
                    for (x = 0; x < l; x++)
                    {
                        mc.append('<div class="alert alert-' + c + '"><a class="close" data-dismiss="alert" href="#">×</a>' + resp.messages[x] + '</div>');
                    }
                }
            }
        }
        else {
            var m = (typeof err != 'undefined' && err.length > 0) ? err : 'Request failed!';

            mc.append('<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">×</a>' + m + '</div>');
        }
    },


    displayException: function(msg)
    {
        var mc = jQuery('#system-message-container');

        (mc.length == 0) ? alert(msg) : mc.append(msg);
    }
}