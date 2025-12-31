"use strict";

$('#payment_btn').on('click', () => {
    payWithPaystack();
});
payWithPaystack();

function payWithPaystack() {
    var amont = $('#amount').val() * 100;
    let handler = PaystackPop.setup({
        key: $('#public_key').val(), // Replace with your public key
        email: $('#email').val(),
        amount: amont,
        currency: $('#currency').val(),
        ref: 'ps_{{ Str::random(15) }}',
        onClose: function () {
            payWithPaystack();
        },
        callback: function (response) {
            $('#ref_id').val(response.reference);
            $('.status').submit();
        }
    });
    handler.openIframe();
}
