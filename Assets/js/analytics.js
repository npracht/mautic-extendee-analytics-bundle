if (!Mautic.eAnalytics) {
    (function (w, d, s, g, js, fjs) {
        g = w.gapi || (w.gapi = {});
        g.analytics = {q: [], ready: function (cb) {this.q.push(cb)}};
        js = d.createElement(s);
        fjs = d.getElementsByTagName(s)[0];
        js.src = 'https://apis.google.com/js/platform.js';
        fjs.parentNode.insertBefore(js, fjs);
        js.onload = function () {g.load('analytics')};
    }(window, document, 'script'));
    Mautic.eAnalytics = gapi.analytics;
}

Mautic.extendeeAnalyticsConfigSave = function (element) {
    var data = element.find('form').formToArray();
    var obj =  element;
    /*Mautic.ajaxActionRequest('plugin:extendeeAnalytics:configSave', data, function (response) {
        console.log('test');
        if(response.content) {
            obj.find('analytics-header').remove();
            obj.prepend(response.content);

        }
    });*/
}


Mautic.eAnalytics.ready(function () {

    mQuery('.analytics-header select').each(function () {
        Mautic.activateChosenSelect(mQuery(this));
    });
    mQuery('.analytics-header select').trigger('chosen:updated');
    mQuery('.analytics-header select').change(function () {
        var parent = mQuery(this).parents('.analytics-case:first');
        Mautic.extendeeAnalyticsConfigSave(parent);
        getData(parent);
    })


    Mautic.eAnalytics.auth.authorize({
        container: 'auth-button',
        clientid: CLIENT_ID,
    });

    if (Mautic.eAnalytics.auth.isAuthorized()) {
        loadData();
    }
    else {
        Mautic.eAnalytics.auth.on('success', function (response) {
            loadData();
        }).on('logout', function (response) {
        }).on('needsAuthorization', function (response) {
            mQuery(".analytics-loading").hide();
            mQuery(".analytics-auth").show();
        }).on('error', function (response) {
            console.log(response);
        })
    }
});

function loadData() {
    mQuery('.analytics-case').each(function () {
        getData(mQuery(this));
    })
}

function getData (parent) {
    parent.find(".analytics-loading").hide();
        var selectedFilters = [];
    if (!parent.data('filters')) {
       return;
    }
    filters = parent.data('filters');
    var dataChart = new gapi.analytics.googleCharts.DataChart({
        query: {
            'ids': ids,
            metrics: metricsGraph,
            dimensions: 'ga:date',
            'start-date': dateFrom,
            'end-date': dateTo,
            'filters': filters
        },
        chart: {
            container: parent.find('.chart-container').attr('id'),
            type: 'LINE',
            options: {
                width: '100%',
                height: '100px'
            }
        }
    })

    dataChart.execute();

    query({
        'ids': ids,
        'dimensions': 'ga:sourceMedium',
        'metrics': metrics,
        'start-date': dateFrom,
        'end-date': dateTo,
        'filters': filters
    })
        .then(function (response) {
            if (response.totalResults > 0) {
                var results = response.totalsForAllResults;
                var symbols = [];
                response.columnHeaders.forEach(function (row, i) {
                    var symbol = '';
                    switch (row['dataType']) {
                        case "PERCENT":
                            symbol = '%';
                            break;
                        case "CURRENCY":
                            symbol = currency;
                            break;
                        case "TIME":
                            console.log(results[row['name']]);
                            symbol = 'm';
                            results[row['name']] = fmtMSS((parseInt(results[row['name']])));
                            //console.log(results[row['name']]);
                            break;

                    }
                    symbols[row['name']] = symbol;
                });
                for (var key in results) {
                    var result = results[key];
                    if (result == parseFloat(result) && result != parseInt(result)) {
                        result = parseFloat(result).toFixed(1);
                    }
                    var classname = key.replace('ga:', '');
                    if (result && parent.find('.'+classname).length) {
                        parent.find('.'+classname).text(result + '' + symbols[key]);
                    }
                }
                parent.find(".eanalytics-stats").show()
            }
            else {
                parent.find(".eanalytics-stats-no-results").show();
            }
        });
}

function fmtMSS (s) {return (s - (s %= 60)) / 60 + (9 < s ? ':' : ':0') + s}

/**
 * Extend the Embed APIs `Mautic.eAnalytics.report.Data` component to
 * return a promise the is fulfilled with the value returned by the API.
 * @param {Object} params The request parameters.
 * @return {Promise} A promise.
 */
function query (params) {
    return new Promise(function (resolve, reject) {
        var data = new Mautic.eAnalytics.report.Data({query: params});
        data.once('success', function (response) { resolve(response); })
            .once('error', function (response) { reject(response); })
            .execute();
    });
}