var addMessage,delay;delay=function(e,t){return setTimeout(t,e)},addMessage=function(e,t,r){var a;return a=jQuery(document.createElement("div")).attr("class","alert alert-"+e).html(t).hide(),a.appendTo(jQuery("#messages")),a.slideDown(),delay(r,function(){return a.slideUp(function(){return a.remove()})})};