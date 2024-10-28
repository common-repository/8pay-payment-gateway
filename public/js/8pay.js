jQuery( function( $ ) {
  $(document).on('click', '.btn-chain', function() {
    const chain = $(this).attr('data-chain');
    const [firstChainTokenBtn] = $('.btn-token[data-chain="' + chain + '"]');
    const token = $(firstChainTokenBtn).attr('data-token');
    const planId = $(firstChainTokenBtn).attr('data-plan-id');
    $('input[name="8pay_payment_gateway_chain"]').val(chain);
    $('input[name="8pay_payment_gateway_token"]').val(token);
    $('input[name="8pay_payment_gateway_plan_id"]').val(planId);
    $('.btn-chain').removeClass('selected');
    $(this).addClass('selected');
    $('.btn-token').hide();
    $('.btn-token[data-chain="' + chain + '"]').show();
    $('.btn-token').removeClass('selected');
    $(firstChainTokenBtn).addClass('selected');
  });

  $(document).on('click', '.btn-token', function() {
    const token = $(this).attr('data-token');
    const planId = $(this).attr('data-plan-id');
    $('input[name="8pay_payment_gateway_token"]').val(token);
    if (planId) {
      $('input[name="8pay_payment_gateway_plan_id"]').val(planId);
    }
    $('.btn-token').removeClass('selected');
    $(this).addClass('selected');
  });
});
