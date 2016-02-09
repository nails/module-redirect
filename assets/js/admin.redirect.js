/* globals ko */
/* exported redirects*/
ko.bindingHandlers.select2 = {
    init: function(element)
    {
        $(element).select2();
    }
};

var redirects = function(redirects) {

    /**
     * Avoid scope issues in callbacks and anonymous functions by referring to `this` as `base`
     * @type {Object}
     */
    var base = this;

    // --------------------------------------------------------------------------

    /**
     * The list of redirects
     * @type {observableArray}
     */
    base.redirects = ko.observableArray(redirects);

    // --------------------------------------------------------------------------

    /**
     * The typs of redirect which the user can choose from
     * @type {Object}
     */
    base.redirectTypes = [
        {
            'code': 301,
            'label': '301 - Moved Permanently'
        },
        {
            'code': 302,
            'label': '302 - Moved Temporarily'
        }
    ];

    // --------------------------------------------------------------------------

    base.addRedirect = function()
    {
        var redirect = {
            'old_url': '',
            'new_url': '',
            'type': 301
        };
        base.redirects.push(redirect);
    };

    // --------------------------------------------------------------------------

    base.removeRedirect = function()
    {
        base.redirects.remove(this);
    };
};