class AdminProductVariable
  ADD_VARIATION: '1'
  CREATE_VARIATIONS_FROM_ALL_ATTRIBUTES: '2'
  REMOVE_ALL_VARIATIONS: '3'
  SET_PRODUCT_TYPE: '4-(.*)'
  SET_REGULAR_PRICES: '5'
  INCREASE_REGULAR_PRICES: '6'
  DECREASE_REGULAR_PRICES: '7'
  SET_SALE_PRICES: '8'
  INCREASE_SALE_PRICES: '9'
  DECREASE_SALE_PRICES: '10'
  SET_SCHEDULED_SALE_DATES: '11'
  TOGGLE_MANAGE_STOCK: '12'
  SET_STOCK: '13'
  INCREASE_STOCK: '14'
  DECREASE_STOCK: '15'
  SET_LENGTH: '16'
  SET_WIDTH: '17'
  SET_HEIGHT: '18'
  SET_WEIGHT: '19'
  SET_DOWNLOAD_LIMIT: '20'

  params:
    i18n:
      confirm_remove: ''
      variation_removed: ''
      saved: ''
      create_all_variations_confirmation: ''
      remove_all_variations: ''
      set_field: ''
      modify_field: ''
      sale_start_date: ''
      sale_end_date: ''
  disableUpdate: false

  constructor: (@params) ->
    jQuery('#product-type').on 'change', @removeParameters
    jQuery('#do-bulk-action').on 'click', @bulkAction
    jQuery('#product-variations')
      .on 'click', '.remove-variation', @removeVariation
      .on 'click', '.show-variation', (event) ->
        $item = jQuery(event.target)
        jQuery('.list-group-item-text', $item.closest('li')).slideToggle ->
          jQuery('span', $item).toggleClass('glyphicon-collapse-down').toggleClass('glyphicon-collapse-up')
      .on 'change', 'select.variation-attribute', @updateVariation
      .on 'change', '.list-group-item-text input.form-control' , @updateVariation
      .on 'change', '.list-group-item-text input[type="checkbox"]', @updateVariation
      .on 'change', '.list-group-item-text select.form-control', @updateVariation
      .on 'click', '.set_variation_image', @setImage
      .on 'click', '.remove_variation_image', @removeImage
      .on 'change', 'input[type="checkbox"].default_variation', (event) ->
        jQuery('input[type="checkbox"].default_variation').not(jQuery(event.target)).prop 'checked', false
      .on 'change', 'input[type="checkbox"].stock-manage', (event) ->
        $parent = jQuery(event.target).closest 'li'
        if jQuery(event.target).is(':checked')
          jQuery('div.manual-stock-status', $parent).slideUp()
          jQuery('.stock-status', $parent).slideDown()
        else
          jQuery('div.manual-stock-status', $parent).slideDown()
          jQuery('.stock-status', $parent).slideUp()
      .on 'jigoshop.variation.add', () =>
        @connectImage 0, jQuery('.set_variation_image').last()
      .on 'click', '.schedule', (event) ->
        event.preventDefault()
        jQuery(event.target).closest('fieldset').find('.not-active').removeClass('not-active')
        jQuery(event.target).addClass('not-active')
      .on 'click', '.cancel-schedule', (event) ->
        event.preventDefault()
        $parent = jQuery(event.target).closest('fieldset')
        $parent.find('.not-active').removeClass('not-active')
        jQuery(event.target).addClass('not-active')
        $parent.find('.datepicker').addClass('not-active')
        $parent.find('input.daterange-from').val ''
        $parent.find('input.daterange-to')
          .val ''
          .trigger 'change'
    jQuery('.set_variation_image').each @connectImage

  removeParameters: (event) ->
    $item = jQuery(event.target)
    if $item.val() == 'variable'
      jQuery('.product_regular_price_field').slideUp()
  addVariation: (event) ->
    event.preventDefault()
    $parent = jQuery('#product-variations')
    jQuery.ajax
      url: jigoshop.getAjaxUrl()
      type: 'post'
      dataType: 'json'
      data:
        action: 'jigoshop.admin.product.add_variation'
        product_id: $parent.closest('.jigoshop').data('id')
    .done (data) =>
      if data.success? and data.success
        @disableUpdate = true
        jQuery(data.html).hide().appendTo($parent).slideDown =>
          @disableUpdate = false
        .trigger('jigoshop.variation.add')
      else
        jigoshop.addMessage('danger', data.error, 6000)
  updateVariation: (event) =>
    if @disableUpdate
      return
    $container = jQuery('#product-variations')
    $parent = jQuery(event.target).closest('li.list-group-item')

    getOptionValue = (current) ->
      if current.type == 'checkbox' or current.type == 'radio'
        return current.checked
      if current.type == 'select-multiple'
        return jQuery(current).val()
      return current.value

    attributes = {}
    attributesData = jQuery('select.variation-attribute', $parent).toArray()
    for option in attributesData
      results = /(?:^|\s)product\[variation]\[\d+]\[attribute]\[(.*?)](?:\s|$)/g.exec(option.name)
      attributes[results[1]] = getOptionValue(option)

    product = {}
    productData = jQuery('.list-group-item-text input.form-control,
                          .list-group-item-text input[type="checkbox"],
                          .list-group-item-text select.form-control', $parent).toArray()

    for option in productData
      results = /(?:^|\s)product\[variation]\[\d+]\[product]\[(.*?)](\[(.*?)])?(?:\s|$)/g.exec(option.name)
      if results[3]?
        product[results[1]] = {}
        product[results[1]][results[3]] = getOptionValue(option)
      else
        product[results[1]] = getOptionValue(option)

    jQuery.ajax
      url: jigoshop.getAjaxUrl()
      type: 'post'
      dataType: 'json'
      data:
        action: 'jigoshop.admin.product.save_variation'
        product_id: $container.closest('.jigoshop').data('id')
        variation_id: $parent.data('id')
        attributes: attributes
        product: product
    .done (data) =>
      if data.success? and data.success
        $parent.trigger('jigoshop.variation.update')
        jigoshop.addMessage('success', @params.i18n.saved, 2000)
      else
        jigoshop.addMessage('danger', data.error, 6000)
  removeVariation: (event) =>
    event.preventDefault()
    if confirm(@params.i18n.confirm_remove)
      $parent = jQuery(event.target).closest('li')
      jQuery.ajax
        url: jigoshop.getAjaxUrl()
        type: 'post'
        dataType: 'json'
        data:
          action: 'jigoshop.admin.product.remove_variation'
          product_id: $parent.closest('.jigoshop').data('id')
          variation_id: $parent.data('id')
      .done (data) =>
        if data.success? and data.success
          $parent.trigger('jigoshop.variation.remove')
          $parent.slideUp -> $parent.remove()
          jigoshop.addMessage('success', @params.i18n.variation_removed, 2000)
        else
          jigoshop.addMessage('danger', data.error, 6000)
  connectImage: (index, element) ->
    $element = jQuery(element)
    $remove = $element.next('.remove_variation_image')
    $thumbnail = jQuery('img', $element.parent())
    $element.jigoshop_media(
      field: false
      bind: false
      thumbnail: $thumbnail
      callback: (attachment) ->
        $remove.show()
        jQuery.ajax
          url: jigoshop.getAjaxUrl()
          type: 'post'
          dataType: 'json'
          data:
            action: 'jigoshop.admin.product.set_variation_image'
            product_id: $element.closest('.jigoshop').data('id')
            variation_id: $element.closest('.variation').data('id')
            image_id: attachment.id
        .done (data) ->
          if !data.success? or !data.success
            jigoshop.addMessage('danger', data.error, 6000)
      library:
        type: 'image'
    )
  setImage: (event) ->
    event.preventDefault()
    jQuery(event.target).trigger('jigoshop_media')
  removeImage: (event) ->
    event.preventDefault()
    $element = jQuery(event.target)
    $thumbnail = jQuery('img', $element.parent())
    jQuery.ajax
      url: jigoshop.getAjaxUrl()
      type: 'post'
      dataType: 'json'
      data:
        action: 'jigoshop.admin.product.set_variation_image'
        product_id: $element.closest('.jigoshop').data('id')
        variation_id: $element.closest('.variation').data('id')
        image_id: -1
    .done (data) ->
      if data.success? and data.success
        $thumbnail
          .attr('src', data.url)
          .attr('width', 150)
          .attr('height', 150)
        $element.hide()
      else
        jigoshop.addMessage('danger', data.error, 6000)

  bulkAction: (event) =>
    switch jQuery('#variation-bulk-actions').val()
      when @ADD_VARIATION then return @addVariation(event)
      when @CREATE_VARIATIONS_FROM_ALL_ATTRIBUTES then return @createVariationsFromAllAttributes()
      when @REMOVE_ALL_VARIATIONS then return @removeAllVariations()
      when @SET_REGULAR_PRICES then return @setFields('regular-price', @params.i18n.set_field)
      when @INCREASE_REGULAR_PRICES then return @modifyFields('regular-price', @params.i18n.modify_field, 1)
      when @DECREASE_REGULAR_PRICES then return @modifyFields('regular-price', @params.i18n.modify_field, -1)
      when @SET_SALE_PRICES then return @setFields('sale-price', @params.i18n.set_field)
      when @INCREASE_SALE_PRICES then return @modifyFields('sale-price', @params.i18n.modify_field, 1)
      when @DECREASE_SALE_PRICES then return @modifyFields('sale-price', @params.i18n.modify_field, -1)
      when @SET_SCHEDULED_SALE_DATES then return @setDates()
      when @TOGGLE_MANAGE_STOCK then return @toggleCheckboxes('stock-manage')
      when @SET_STOCK then return @setFields('stock-stock', @params.i18n.set_field)
      when @INCREASE_STOCK then return @modifyFields('stock-stock', @params.i18n.modify_field, 1)
      when @DECREASE_STOCK then return @modifyFields('stock-stock', @params.i18n.modify_field, -1)
      when @SET_LENGTH then return @setFields('size-length', @params.i18n.set_field)
      when @SET_WIDTH then return @setFields('size-width', @params.i18n.set_field)
      when @SET_HEIGHT then return @setFields('size-height', @params.i18n.set_field)
      when @SET_WEIGHT then return @setFields('size-weight', @params.i18n.set_field)
      when @SET_DOWNLOAD_LIMIT then return @setFields('download-limit', @params.i18n.set_field)

    if type = jQuery('#variation-bulk-actions').val().match(/4-([a-z]+)/)
      jQuery('select.variation-type', '#product-variations')
        .val type
        .trigger 'change'

  removeAllVariations: () ->
    if confirm(@params.i18n.remove_all_variations) == false
      return
    jigoshop.block(jQuery('#product-variations').closest('.jigoshop'))
    jQuery.ajax
      url: jigoshop.getAjaxUrl()
      type: 'post'
      dataType: 'json'
      data:
        action: 'jigoshop.admin.product.remove_all_variations'
        product_id: jQuery('#product-variations').closest('.jigoshop').data('id')
    .done (data) ->
      if data.success? and data.success
        jQuery('#product-variations').slideUp ->
          jigoshop.unblock(jQuery('#product-variations').closest('.jigoshop'))
          jQuery('#product-variations li').remove()
          jQuery('#product-variations').show()

  setFields: (field, text) ->
    value = prompt(text)
    jQuery('input.' + field, '#product-variations')
      .val value
      .trigger 'change'

  modifyFields: (field, text, operator) ->
    value = prompt(text)
    if value.search('%') > 0
      value = Number value.replace(/%|,| /, '')
      if isNaN value
        return alert('Invalid number')
      value = 1 + operator * (value / 100)
      jQuery('input.' + field, '#product-variations').each () ->
        jQuery(this)
          .val Math.round(jQuery(this).val() * value * 100) / 100
          .trigger 'change'
    else
      value = Number value.replace(/,| /, '')
      if isNaN value
        return alert('Invalid number')
      jQuery('input.' + field, '#product-variations').each () ->
        jQuery(this)
          .val Number(jQuery(this).val()) + operator * value
          .trigger 'change'

  toggleCheckboxes: (field) ->
    jQuery('input[type="checkbox"].' + field, '#product-variations').each () ->
      jQuery(this)
        .prop 'checked', !jQuery(this).is(':checked')
        .trigger 'change'

  setDates: () ->
    from = prompt(@params.i18n.sale_start_date)
    to = prompt(@params.i18n.sale_end_date)
    jQuery('input.daterange-from', '#product-variations').val from
    jQuery('input.daterange-to', '#product-variations')
      .val to
      .trigger 'change'

  createVariationsFromAllAttributes: () ->
    if confirm(@params.i18n.create_all_variations_confirmation) == false
      return
    jigoshop.block(jQuery('#product-variations').closest('.jigoshop'))
    $parent = jQuery('#product-variations')
    jQuery.ajax
      url: jigoshop.getAjaxUrl()
      type: 'post'
      dataType: 'json'
      data:
        action: 'jigoshop.admin.product.create_variations_from_all_attributes'
        product_id: $parent.closest('.jigoshop').data('id')
    .done (data) =>
      if data.success? and data.success
        @disableUpdate = true
        jigoshop.unblock(jQuery('#product-variations').closest('.jigoshop'))
        jQuery(data.html).hide().appendTo($parent).slideDown =>
          @disableUpdate = false
        jQuery('.set_variation_image', data.html).each @connectImage


jQuery ->
  new AdminProductVariable(jigoshop_admin_product_variable)
