var AdminOrder,bind=function(t,e){return function(){return t.apply(e,arguments)}},hasProp={}.hasOwnProperty;AdminOrder=function(){function t(t){this.params=t,this.updatePostcode=bind(this.updatePostcode,this),this.updateState=bind(this.updateState,this),this.updateCountry=bind(this.updateCountry,this),this.removeItemClick=bind(this.removeItemClick,this),this.updateItem=bind(this.updateItem,this),this.newItemClick=bind(this.newItemClick,this),this.newItemSelect=bind(this.newItemSelect,this),this.selectShipping=bind(this.selectShipping,this),this.newItemSelect(),this._prepareStateField("#order_billing_address_state"),this._prepareStateField("#order_shipping_address_state"),jQuery("#add-item").on("click",this.newItemClick),jQuery(".jigoshop-order table").on("click","a.remove",this.removeItemClick),jQuery(".jigoshop-order table").on("change",".price input, .quantity input",this.updateItem),jQuery(".jigoshop-data").on("change","#order_billing_address_country",this.updateCountry).on("change","#order_shipping_address_country",this.updateCountry).on("change","#order_billing_address_state",this.updateState).on("change","#order_shipping_address_state",this.updateState).on("change","#order_billing_address_postcode",this.updatePostcode).on("change","#order_shipping_address_postcode",this.updatePostcode),jQuery(".jigoshop-totals").on("click","input[type=radio]",this.selectShipping)}return t.prototype.params={ajax:"",tax_shipping:!1,ship_to_billing:!1},t.prototype.selectShipping=function(t){var e,a,r;return a=jQuery(t.target).closest("div.jigoshop"),e=jQuery(t.target),r=jQuery(".shipping-method-rate",e.closest("li")),jQuery.ajax(this.params.ajax,{type:"post",dataType:"json",data:{action:"jigoshop.admin.order.change_shipping_method",order:a.data("order"),method:e.val(),rate:r.val()}}).done(function(t){return function(e){return null!=e.success&&e.success?(t._updateTotals(e.html.total,e.html.subtotal),t._updateTaxes(e.tax,e.html.tax)):alert(e.error)}}(this))},t.prototype.newItemSelect=function(){return jQuery("#new-item").select2({minimumInputLength:3,ajax:{url:this.params.ajax,type:"post",dataType:"json",data:function(t){return{query:t,action:"jigoshop.admin.product.find"}},results:function(t){return null!=t.success?{results:t.results}:{results:[]}}}})},t.prototype.newItemClick=function(t){var e,a,r,s;return t.preventDefault(),s=jQuery("#new-item").val(),""===s?!1:(a=jQuery(t.target).closest("table"),e=jQuery("tr[data-product="+s+"]",a),e.length>0?(r=jQuery(".quantity input",e),void r.val(parseInt(r.val())+1).trigger("change")):jQuery.ajax({url:this.params.ajax,type:"post",dataType:"json",data:{action:"jigoshop.admin.order.add_product",product:s,order:a.data("order")}}).done(function(t){return function(e){return null!=e.success&&e.success?(jQuery(e.html.row).appendTo(a),jQuery("#product-subtotal",a).html(e.html.product_subtotal),t._updateTotals(e.html.total,e.html.subtotal),t._updateTaxes(e.tax,e.html.tax)):void 0}}(this)))},t.prototype.updateItem=function(t){var e,a;return t.preventDefault(),a=jQuery(t.target).closest("tr"),e=a.closest("table"),jQuery.ajax({url:this.params.ajax,type:"post",dataType:"json",data:{action:"jigoshop.admin.order.update_product",product:a.data("id"),order:e.data("order"),price:jQuery(".price input",a).val(),quantity:jQuery(".quantity input",a).val()}}).done(function(t){return function(r){return null!=r.success&&r.success?(r.item_cost>0?jQuery(".total p",a).html(r.html.item_cost):a.remove(),jQuery("#product-subtotal",e).html(r.html.product_subtotal),t._updateTotals(r.html.total,r.html.subtotal),t._updateTaxes(r.tax,r.html.tax)):void 0}}(this))},t.prototype.removeItemClick=function(t){var e,a;return t.preventDefault(),a=jQuery(t.target).closest("tr"),e=a.closest("table"),jQuery.ajax({url:this.params.ajax,type:"post",dataType:"json",data:{action:"jigoshop.admin.order.remove_product",product:a.data("id"),order:e.data("order")}}).done(function(t){return function(r){return null!=r.success&&r.success?(a.remove(),jQuery("#product-subtotal",e).html(r.html.product_subtotal),t._updateTaxes(r.tax,r.html.tax),t._updateTotals(r.html.total,r.html.subtotal)):void 0}}(this))},t.prototype.updateCountry=function(t){var e,a,r,s;return a=jQuery(t.target),e=a.closest(".jigoshop"),r=a.attr("id"),s=r.replace(/order_/,"").replace(/_country/,""),jQuery.ajax(this.params.ajax,{type:"post",dataType:"json",data:{action:"jigoshop.admin.order.change_country",value:a.val(),order:e.data("order"),type:s}}).done(function(t){return function(e){var a,r,o,i,n,u;if(null!=e.success&&e.success){if(t._updateTotals(e.html.total,e.html.subtotal),t._updateTaxes(e.tax,e.html.tax),t._updateShipping(e.shipping,e.html.shipping),o="#order_"+s+"_state",a=jQuery(o),e.has_states){r=[],n=e.states;for(u in n)hasProp.call(n,u)&&(i=n[u],r.push({id:u,text:i}));return a.select2({data:r})}return a.attr("type","text").select2("destroy").val("")}return addMessage("danger",e.error,6e3)}}(this))},t.prototype.updateState=function(t){var e,a,r,s;return a=jQuery(t.target),e=a.closest(".jigoshop"),r=a.attr("id"),s=r.replace(/order_/,"").replace(/_state/,""),jQuery.ajax(this.params.ajax,{type:"post",dataType:"json",data:{action:"jigoshop.admin.order.change_state",value:a.val(),order:e.data("order"),type:s}}).done(function(t){return function(e){return null!=e.success&&e.success?(t._updateTotals(e.html.total,e.html.subtotal),t._updateTaxes(e.tax,e.html.tax),t._updateShipping(e.shipping,e.html.shipping)):addMessage("danger",e.error,6e3)}}(this))},t.prototype.updatePostcode=function(t){var e,a,r,s;return a=jQuery(t.target),e=a.closest(".jigoshop"),r=a.attr("id"),s=r.replace(/order_/,"").replace(/_postcode/,""),jQuery.ajax(this.params.ajax,{type:"post",dataType:"json",data:{action:"jigoshop.admin.order.change_postcode",value:a.val(),order:e.data("order"),type:s}}).done(function(t){return function(e){return null!=e.success&&e.success?(t._updateTotals(e.html.total,e.html.subtotal),t._updateTaxes(e.tax,e.html.tax),t._updateShipping(e.shipping,e.html.shipping)):addMessage("danger",e.error,6e3)}}(this))},t.prototype._updateTaxes=function(t,e){var a,r,s,o,i;s=[];for(i in e)hasProp.call(e,i)&&(o=e[i],r=".order_tax_"+i+"_field",a=jQuery(r),jQuery("label",a).html(o.label),jQuery("p",a).html(o.value).show(),t[i]>0?s.push(a.show()):s.push(a.hide()));return s},t.prototype._updateTotals=function(t,e){return jQuery("#subtotal").html(e),jQuery("#total").html(t)},t.prototype._updateShipping=function(t,e){var a,r,s,o;for(s in t)hasProp.call(t,s)&&(o=t[s],r=jQuery(".shipping-"+s),r.addClass("existing"),r.length>0?o>-1?(a=jQuery(e[s].html).addClass("existing"),r.replaceWith(a)):r.slideUp(function(){return jQuery(this).remove()}):null!=e[s]&&(a=jQuery(e[s].html),a.hide().addClass("existing").appendTo(jQuery("#shipping-methods")).slideDown()));return jQuery("#shipping-methods > li:not(.existing)").slideUp(function(){return jQuery(this).remove()}),jQuery("#shipping-methods > li").removeClass("existing")},t.prototype._prepareStateField=function(t){var e,a,r;return e=jQuery(t),e.is("select")?(a=jQuery(document.createElement("input")).attr("type","text").attr("id",e.attr("id")).attr("name",e.attr("name")).attr("class",e.attr("class")).val(e.val()),r=[],jQuery("option",e).each(function(){return r.push({id:jQuery(this).val(),text:jQuery(this).html()})}),e.replaceWith(a),a.select2({data:r})):void 0},t}(),jQuery(function(){return new AdminOrder(jigoshop_admin_order)});