!function(a,b,c,d){function h(b,d){this.w=a(c),this.el=a(b),this.options=a.extend({},g,d),this.init()}var e="ontouchstart"in c,f=function(){var a=c.createElement("div"),d=c.documentElement;if(!("pointerEvents"in a.style))return!1;a.style.pointerEvents="auto",a.style.pointerEvents="x",d.appendChild(a);var e=b.getComputedStyle&&"auto"===b.getComputedStyle(a,"").pointerEvents;return d.removeChild(a),!!e}(),g={listNodeName:"ol",itemNodeName:"li",rootClass:"dd",listClass:"dd-list",itemClass:"dd-item",dragClass:"dd-dragel",handleClass:"dd-handle",collapsedClass:"dd-collapsed",placeClass:"dd-placeholder",noDragClass:"dd-nodrag",emptyClass:"dd-empty",expandBtnHTML:'<button data-action="expand" type="button">Expand</button>',collapseBtnHTML:'<button data-action="collapse" type="button">Collapse</button>',group:0,maxDepth:5,threshold:20};h.prototype={init:function(){var c=this;c.reset(),c.el.data("nestable-group",this.options.group),c.placeEl=a('<div class="'+c.options.placeClass+'"/>'),a.each(this.el.find(c.options.itemNodeName),function(b,d){c.setParent(a(d))}),c.el.on("click","button",function(b){if(!c.dragEl){var d=a(b.currentTarget),e=d.data("action"),f=d.parent(c.options.itemNodeName);"collapse"===e&&c.collapseItem(f),"expand"===e&&c.expandItem(f)}});var d=function(b){var d=a(b.target);if(!d.hasClass(c.options.handleClass)){if(d.closest("."+c.options.noDragClass).length)return;d=d.closest("."+c.options.handleClass)}d.length&&!c.dragEl&&(c.isTouch=/^touch/.test(b.type),c.isTouch&&1!==b.touches.length||(b.preventDefault(),c.dragStart(b.touches?b.touches[0]:b)))},f=function(a){c.dragEl&&(a.preventDefault(),c.dragMove(a.touches?a.touches[0]:a))},g=function(a){c.dragEl&&(a.preventDefault(),c.dragStop(a.touches?a.touches[0]:a))};e&&(c.el[0].addEventListener("touchstart",d,!1),b.addEventListener("touchmove",f,!1),b.addEventListener("touchend",g,!1),b.addEventListener("touchcancel",g,!1)),c.el.on("mousedown",d),c.w.on("mousemove",f),c.w.on("mouseup",g)},serialize:function(){var b,c=0,d=this;return step=function(b,c){var e=[],f=b.children(d.options.itemNodeName);return f.each(function(){var b=a(this),f=a.extend({},b.data()),g=b.children(d.options.listNodeName);g.length&&(f.children=step(g,c+1)),e.push(f)}),e},b=step(d.el.find(d.options.listNodeName).first(),c)},serialise:function(){return this.serialize()},reset:function(){this.mouse={offsetX:0,offsetY:0,startX:0,startY:0,lastX:0,lastY:0,nowX:0,nowY:0,distX:0,distY:0,dirAx:0,dirX:0,dirY:0,lastDirX:0,lastDirY:0,distAxX:0,distAxY:0},this.isTouch=!1,this.moving=!1,this.dragEl=null,this.dragRootEl=null,this.dragDepth=0,this.hasNewRoot=!1,this.pointEl=null},expandItem:function(a){a.removeClass(this.options.collapsedClass),a.children('[data-action="expand"]').hide(),a.children('[data-action="collapse"]').show(),a.children(this.options.listNodeName).show()},collapseItem:function(a){var b=a.children(this.options.listNodeName);b.length&&(a.addClass(this.options.collapsedClass),a.children('[data-action="collapse"]').hide(),a.children('[data-action="expand"]').show(),a.children(this.options.listNodeName).hide())},expandAll:function(){var b=this;b.el.find(b.options.itemNodeName).each(function(){b.expandItem(a(this))})},collapseAll:function(){var b=this;b.el.find(b.options.itemNodeName).each(function(){b.collapseItem(a(this))})},setParent:function(b){b.children(this.options.listNodeName).length&&(b.prepend(a(this.options.expandBtnHTML)),b.prepend(a(this.options.collapseBtnHTML))),b.children('[data-action="expand"]').hide()},unsetParent:function(a){a.removeClass(this.options.collapsedClass),a.children("[data-action]").remove(),a.children(this.options.listNodeName).remove()},dragStart:function(b){var e=this.mouse,f=a(b.target),g=f.closest(this.options.itemNodeName);this.placeEl.css("height",g.height()),e.offsetX=b.offsetX!==d?b.offsetX:b.pageX-f.offset().left,e.offsetY=b.offsetY!==d?b.offsetY:b.pageY-f.offset().top,e.startX=e.lastX=b.pageX,e.startY=e.lastY=b.pageY,this.dragRootEl=this.el,this.dragEl=a(c.createElement(this.options.listNodeName)).addClass(this.options.listClass+" "+this.options.dragClass),this.dragEl.css("width",g.width()),g.after(this.placeEl),g[0].parentNode.removeChild(g[0]),g.appendTo(this.dragEl),a(c.body).append(this.dragEl),this.dragEl.css({left:b.pageX-e.offsetX,top:b.pageY-e.offsetY});var h,i,j=this.dragEl.find(this.options.itemNodeName);for(h=0;h<j.length;h++)i=a(j[h]).parents(this.options.listNodeName).length,i>this.dragDepth&&(this.dragDepth=i)},dragStop:function(a){var b=this.dragEl.children(this.options.itemNodeName).first();b[0].parentNode.removeChild(b[0]),this.placeEl.replaceWith(b),this.dragEl.remove(),this.el.trigger("change"),this.hasNewRoot&&this.dragRootEl.trigger("change"),this.reset()},dragMove:function(d){var e,g,h,i,j,k=this.options,l=this.mouse;this.dragEl.css({left:d.pageX-l.offsetX,top:d.pageY-l.offsetY}),l.lastX=l.nowX,l.lastY=l.nowY,l.nowX=d.pageX,l.nowY=d.pageY,l.distX=l.nowX-l.lastX,l.distY=l.nowY-l.lastY,l.lastDirX=l.dirX,l.lastDirY=l.dirY,l.dirX=0===l.distX?0:l.distX>0?1:-1,l.dirY=0===l.distY?0:l.distY>0?1:-1;var m=Math.abs(l.distX)>Math.abs(l.distY)?1:0;if(!l.moving)return l.dirAx=m,void(l.moving=!0);l.dirAx!==m?(l.distAxX=0,l.distAxY=0):(l.distAxX+=Math.abs(l.distX),0!==l.dirX&&l.dirX!==l.lastDirX&&(l.distAxX=0),l.distAxY+=Math.abs(l.distY),0!==l.dirY&&l.dirY!==l.lastDirY&&(l.distAxY=0)),l.dirAx=m,l.dirAx&&l.distAxX>=k.threshold&&(l.distAxX=0,h=this.placeEl.prev(k.itemNodeName),l.distX>0&&h.length&&!h.hasClass(k.collapsedClass)&&(e=h.find(k.listNodeName).last(),j=this.placeEl.parents(k.listNodeName).length,j+this.dragDepth<=k.maxDepth&&(e.length?(e=h.children(k.listNodeName).last(),e.append(this.placeEl)):(e=a("<"+k.listNodeName+"/>").addClass(k.listClass),e.append(this.placeEl),h.append(e),this.setParent(h)))),l.distX<0&&(i=this.placeEl.next(k.itemNodeName),i.length||(g=this.placeEl.parent(),this.placeEl.closest(k.itemNodeName).after(this.placeEl),g.children().length||this.unsetParent(g.parent()))));var n=!1;if(f||(this.dragEl[0].style.visibility="hidden"),this.pointEl=a(c.elementFromPoint(d.pageX-c.body.scrollLeft,d.pageY-(b.pageYOffset||c.documentElement.scrollTop))),f||(this.dragEl[0].style.visibility="visible"),this.pointEl.hasClass(k.handleClass)&&(this.pointEl=this.pointEl.parent(k.itemNodeName)),this.pointEl.hasClass(k.emptyClass))n=!0;else if(!this.pointEl.length||!this.pointEl.hasClass(k.itemClass))return;var o=this.pointEl.closest("."+k.rootClass),p=this.dragRootEl.data("nestable-id")!==o.data("nestable-id");if(!l.dirAx||p||n){if(p&&k.group!==o.data("nestable-group"))return;if(j=this.dragDepth-1+this.pointEl.parents(k.listNodeName).length,j>k.maxDepth)return;var q=d.pageY<this.pointEl.offset().top+this.pointEl.height()/2;g=this.placeEl.parent(),n?(e=a(c.createElement(k.listNodeName)).addClass(k.listClass),e.append(this.placeEl),this.pointEl.replaceWith(e)):q?this.pointEl.before(this.placeEl):this.pointEl.after(this.placeEl),g.children().length||this.unsetParent(g.parent()),this.dragRootEl.find(k.itemNodeName).length||this.dragRootEl.append('<div class="'+k.emptyClass+'"/>'),p&&(this.dragRootEl=o,this.hasNewRoot=this.el[0]!==this.dragRootEl[0])}}},a.fn.nestable=function(b){var c=this,d=this;return c.each(function(){var c=a(this).data("nestable");c?"string"==typeof b&&"function"==typeof c[b]&&(d=c[b]()):(a(this).data("nestable",new h(this,b)),a(this).data("nestable-id",(new Date).getTime()))}),d||c}}(window.jQuery||window.Zepto,window,document);

Array.prototype.getUnique = function() {
    var o = {}, a = [], i, e;
    for (i = 0; e = this[i]; i++) {o[e] = 1};
    for (e in o) {a.push (e)};
    return a;
}

$Ready(function () {

    $('#manage-categories').nestable();

    $('#manage-categories').on('change', function(){

        function treeToList(tree, pid)
        {
            for (var i in tree) {
                var node  = tree[i];
                if (node.id != undefined) {
                    listOrder.push({
                        id: node.id,
                        parent_id: pid
                    });
                }
                if (node.children != undefined) {
                    treeToList(node.children, node.id);
                }
            }
        }

        $Core.processing();
        var tree = $('#manage-categories').nestable('serialize');
        var url = PF.url.make('admincp/digitaldownload/categories/order');
        var listOrder = [];
        treeToList(tree, 0);
        $.ajax({
            url: url,
            type: 'POST',
            data: {order: listOrder},
            success: function() {
                $('.ajax_processing').remove();
            }
        });
    });

    $('#manage-categories .actions .status').on('click', function(e){

        e.preventDefault();
        $Core.processing();

        var willShowActionClass = ($(this).hasClass('activate') ? '.deactivate' : '.activate');
        $(this).hide();

        var status = willShowActionClass == '.deactivate';
        var node = $(this).closest('.dd-item');
        var url = $(this).data('url');
        var ids = [];
        node.find(willShowActionClass).show();
        ids.push(node.data('id'));
        if (!status) {
            node.find('.dd-list .dd-item').each(function(){
                ids.push($(this).data('id'));
                $(this).find('.status').hide();
                $(this).find(willShowActionClass).show();
            });
        } else {
            node.parents('.dd-list .dd-item').each(function(){
                ids.push($(this).data('id'));
                $(this).find('.actions').each(function(index){
                    if (index > 0) return false;
                    $(this).find('.status').hide();
                });
                $(this).find(willShowActionClass).each(function(index){
                    if (index > 0) return false;
                    $(this).show();
                });
                node.find('.dd-list .dd-item').each(function(){
                    ids.push($(this).data('id'));
                    $(this).find('.status').hide();
                    $(this).find(willShowActionClass).show();
                });
            });
            node.find('.dd-list .dd-item').each(function(){
                ids.push($(this).data('id'));
                $(this).find('.status').hide();
                $(this).find(willShowActionClass).show();
            });
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: {ids: ids.getUnique(), status: +status},
            success: function() {
                $('.ajax_processing').remove();
            }
        });
    });

    $('#manage-categories .actions .delete').on('click', function(e) {
        e.preventDefault();
        var sure = confirm(oTranslations['core.are_you_sure']);
        if (sure) {
            $Core.processing();
            var deleteNode = $(this).closest('.dd-item');
            var url = $(this).attr('href');
            //set childs to root
            deleteNode.find('.dd-item').each(function(){
                $(this).find('.dd-item, button[data-action="collapse"],button[data-action="expand"]').remove();
                $(this).find('button[data-action="collapse"],button[data-action="expand"]').hide();
                $(this).clone().appendTo('#manage-categories > ol');
                $(this).remove();
            });
            deleteNode.remove();
            $.ajax({
                url: url,
                type: 'POST',
                success: function() {
                    $('#manage-categories').trigger('change');
                }
            });
        }
    });
});