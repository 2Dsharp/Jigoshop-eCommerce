jQuery(function($) {
  delay(8000, function() {
    return $('.alert-danger').slideUp(function() {
      return $(this).remove();
    });
  });
  return delay(4000, function() {
    return $('.alert-success').slideUp(function() {
      return $(this).remove();
    });
  });
});
