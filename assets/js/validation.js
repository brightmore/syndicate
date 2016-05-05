var validation = {};

validation.email = function (value) {
    return /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i.test(value);
};

validation.password = function (value) {
    return /[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~\-]{6,36}$/.test(value);
};

validation.contact = function (value) {
    return /^(0)[2-9]{2}\d{7}/.test(value);
}

validation.name = function (value) {
    return /^[a-zA-Z]{1}'?[a-zA-Z]{3,6}[a-zA-Z ]{0,72}/.test(value);
};

validation.dateISO = function (value) {
    return /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/.test(value);
};

validation.number = function (value) {
    return /^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test(value);
};

validation.digits = function (value) {
    return /^\d+$/.test(value);
};

