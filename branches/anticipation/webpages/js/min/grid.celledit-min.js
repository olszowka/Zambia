(function(a){a.fn.extend({editCell:function(e,d,c,b){return this.each(function(){var m=this,q,n,j;if(!m.grid||m.p.cellEdit!==true){return}var f=null;if(a.browser.msie&&a.browser.version<=7&&c===true&&b===true){d=o(m.rows[e],d)}d=parseInt(d,10);m.p.selrow=m.rows[e].id;if(!m.p.knv){a(m).GridNav()}if(m.p.savedRow.length>0){if(c===true){if(e==m.p.iRow&&d==m.p.iCol){return}}var k=a("td:eq("+m.p.savedRow[0].ic+")>#"+m.p.savedRow[0].id+"_"+m.p.savedRow[0].name.replace(".","\\."),m.rows[m.p.savedRow[0].id]).val();if(m.p.savedRow[0].v!=k){a(m).saveCell(m.p.savedRow[0].id,m.p.savedRow[0].ic)}else{a(m).restoreCell(m.p.savedRow[0].id,m.p.savedRow[0].ic)}}else{window.setTimeout(function(){a("#"+m.p.knv).attr("tabindex","-1").focus()},0)}q=m.p.colModel[d].name;if(q=="subgrid"){return}if(m.p.colModel[d].editable===true&&c===true){j=a("td:eq("+d+")",m.rows[e]);if(parseInt(m.p.iCol)>=0&&parseInt(m.p.iRow)>=0){a("td:eq("+m.p.iCol+")",m.rows[m.p.iRow]).removeClass("edit-cell");a(m.rows[m.p.iRow]).removeClass("selected-row")}a(j).addClass("edit-cell");a(m.rows[e]).addClass("selected-row");try{n=a.unformat(j,{colModel:m.p.colModel[d]},d)}catch(p){n=a(j).html()}var h=a.extend(m.p.colModel[d].editoptions||{},{id:e+"_"+q,name:q});if(!m.p.colModel[d].edittype){m.p.colModel[d].edittype="text"}m.p.savedRow[0]={id:e,ic:d,name:q,v:n};if(a.isFunction(m.p.formatCell)){var l=m.p.formatCell(m.rows[e].id,q,n,e,d);if(l){n=l}}var g=createEl(m.p.colModel[d].edittype,h,n,j);if(a.isFunction(m.p.beforeEditCell)){m.p.beforeEditCell(m.rows[e].id,q,n,e,d)}a(j).html("").append(g);window.setTimeout(function(){a(g).focus()},0);a("input, select, textarea",j).bind("keydown",function(r){if(r.keyCode===27){a(m).restoreCell(e,d)}if(r.keyCode===13){a(m).saveCell(e,d)}if(r.keyCode==9){if(r.shiftKey){a(m).prevCell(e,d)}else{a(m).nextCell(e,d)}}r.stopPropagation()});if(a.isFunction(m.p.afterEditCell)){m.p.afterEditCell(m.rows[e].id,q,n,e,d)}}else{if(parseInt(m.p.iCol)>=0&&parseInt(m.p.iRow)>=0){a("td:eq("+m.p.iCol+")",m.rows[m.p.iRow]).removeClass("edit-cell");a(m.rows[m.p.iRow]).removeClass("selected-row")}a("td:eq("+d+")",m.rows[e]).addClass("edit-cell");a(m.rows[e]).addClass("selected-row");if(a.isFunction(m.p.onSelectCell)){n=a("td:eq("+d+")",m.rows[e]).html().replace(/\&nbsp\;/ig,"");m.p.onSelectCell(m.rows[e].id,q,n,e,d)}}m.p.iCol=d;m.p.iRow=e;function o(v,s){var w=0;var u=0;for(i=0;i<v.cells.length;i++){var r=v.cells(i);if(r.style.display=="none"){w++}else{u++}if(u>s){return i}}return i}})},saveCell:function(c,b){return this.each(function(){var h=this,q,k;if(!h.grid||h.p.cellEdit!==true){return}if(h.p.savedRow.length==1){k=0}else{k=null}if(k!=null){var d=a("td:eq("+b+")",h.rows[c]),p,n;q=h.p.colModel[b].name;switch(h.p.colModel[b].edittype){case"select":p=a("#"+c+"_"+q.replace(".","\\.")+">option:selected",h.rows[c]).val();n=a("#"+c+"_"+q.replace(".","\\.")+">option:selected",h.rows[c]).text();break;case"checkbox":var f=["Yes","No"];if(h.p.colModel[b].editoptions){f=h.p.colModel[b].editoptions.value.split(":")}p=a("#"+c+"_"+q.replace(".","\\."),h.rows[c]).attr("checked")?f[0]:f[1];n=p;break;case"password":case"text":case"textarea":p=!h.p.autoencode?a("#"+c+"_"+q.replace(".","\\."),h.rows[c]).val():htmlEncode(a("#"+c+"_"+q.replace(".","\\."),h.rows[c]).val());n=p;break}if(n!=h.p.savedRow[k].v){if(a.isFunction(h.p.beforeSaveCell)){var o=h.p.beforeSaveCell(h.rows[c].id,q,p,c,b);if(o){p=o}}var g=checkValues(p,b,h);if(g[0]===true){var j={};if(a.isFunction(h.p.beforeSubmitCell)){j=h.p.beforeSubmitCell(h.rows[c].id,q,p,c,b);if(!j){j={}}}if(h.p.cellsubmit=="remote"){if(h.p.cellurl){var m={};m[q]=p;m.id=h.rows[c].id;m=a.extend(j,m);a.ajax({url:h.p.cellurl,data:m,type:"POST",complete:function(e,s){if(s=="success"){if(a.isFunction(h.p.afterSubmitCell)){var r=h.p.afterSubmitCell(e,m.id,q,p,c,b);if(r[0]===true){a(d).empty();a(h).setCell(h.rows[c].id,b,n);a(d).addClass("dirty-cell");a(h.rows[c]).addClass("edited");if(a.isFunction(h.p.afterSaveCell)){h.p.afterSaveCell(h.rows[c].id,q,p,c,b)}h.p.savedRow=[]}else{info_dialog(a.jgrid.errors.errcap,r[1],a.jgrid.edit.bClose,h.p.imgpath);a(h).restoreCell(c,b)}}else{a(d).empty();a(h).setCell(h.rows[c].id,b,n);a(d).addClass("dirty-cell");a(h.rows[c]).addClass("edited");if(a.isFunction(h.p.afterSaveCell)){h.p.afterSaveCell(h.rows[c].id,q,p,c,b)}h.p.savedRow=[]}}},error:function(e,r){if(a.isFunction(h.p.errorCell)){h.p.errorCell(e,r);a(h).restoreCell(c,b)}else{info_dialog(a.jgrid.errors.errcap,e.status+" : "+e.statusText+"<br/>"+r,a.jgrid.edit.bClose,h.p.imgpath);a(h).restoreCell(c,b)}}})}else{try{info_dialog(a.jgrid.errors.errcap,a.jgrid.errors.nourl,a.jgrid.edit.bClose,h.p.imgpath);a(h).restoreCell(c,b)}catch(l){}}}if(h.p.cellsubmit=="clientArray"){a(d).empty();a(h).setCell(h.rows[c].id,b,n);a(d).addClass("dirty-cell");a(h.rows[c]).addClass("edited");if(a.isFunction(h.p.afterSaveCell)){h.p.afterSaveCell(h.rows[c].id,q,p,c,b)}h.p.savedRow=[]}}else{try{window.setTimeout(function(){info_dialog(a.jgrid.errors.errcap,p+" "+g[1],a.jgrid.edit.bClose,h.p.imgpath)},100);a(h).restoreCell(c,b)}catch(l){}}}else{a(h).restoreCell(c,b)}}if(a.browser.opera){a("#"+h.p.knv).attr("tabindex","-1").focus()}else{window.setTimeout(function(){a("#"+h.p.knv).attr("tabindex","-1").focus()},0)}})},restoreCell:function(c,b){return this.each(function(){var j=this,d,f;if(!j.grid||j.p.cellEdit!==true){return}if(j.p.savedRow.length==1){f=0}else{f=null}if(f!=null){var h=a("td:eq("+b+")",j.rows[c]);if(a.isFunction(a.fn.datepicker)){try{a.datepicker("hide")}catch(g){try{a.datepicker.hideDatepicker()}catch(g){}}}a(h).empty();a(j).setCell(j.rows[c].id,b,j.p.savedRow[f].v);j.p.savedRow=[]}window.setTimeout(function(){a("#"+j.p.knv).attr("tabindex","-1").focus()},0)})},nextCell:function(c,b){return this.each(function(){var g=this,f=false,e;if(!g.grid||g.p.cellEdit!==true){return}for(var d=b+1;d<g.p.colModel.length;d++){if(g.p.colModel[d].editable===true){f=d;break}}if(f!==false){a(g).saveCell(c,b);a(g).editCell(c,f,true)}else{if(g.p.savedRow.length>0){a(g).saveCell(c,b)}}})},prevCell:function(c,b){return this.each(function(){var g=this,f=false,e;if(!g.grid||g.p.cellEdit!==true){return}for(var d=b-1;d>=0;d--){if(g.p.colModel[d].editable===true){f=d;break}}if(f!==false){a(g).saveCell(c,b);a(g).editCell(c,f,true)}else{if(g.p.savedRow.length>0){a(g).saveCell(c,b)}}})},GridNav:function(){return this.each(function(){var e=this;if(!e.grid||e.p.cellEdit!==true){return}e.p.knv=a("table:first",e.grid.bDiv).attr("id")+"_kn";var d=a("<span style='width:0px;height:0px;background-color:black;' tabindex='0'><span tabindex='-1' style='width:0px;height:0px;background-color:grey' id='"+e.p.knv+"'></span></span>");a(d).insertBefore(e.grid.cDiv);a("#"+e.p.knv).focus();a("#"+e.p.knv).keydown(function(g){switch(g.keyCode){case 38:if(e.p.iRow-1>=1){c(e.p.iRow-1,e.p.iCol,"vu");a(e).editCell(e.p.iRow-1,e.p.iCol,false)}break;case 40:if(e.p.iRow+1<=e.rows.length-1){c(e.p.iRow+1,e.p.iCol,"vd");a(e).editCell(e.p.iRow+1,e.p.iCol,false)}break;case 37:if(e.p.iCol-1>=0){var f=b(e.p.iCol-1,"lft");c(e.p.iRow,f,"h");a(e).editCell(e.p.iRow,f,false)}break;case 39:if(e.p.iCol+1<=e.p.colModel.length-1){var f=b(e.p.iCol+1,"rgt");c(e.p.iRow,f,"h");a(e).editCell(e.p.iRow,f,false)}break;case 13:if(parseInt(e.p.iCol,10)>=0&&parseInt(e.p.iRow,10)>=0){a(e).editCell(e.p.iRow,e.p.iCol,true)}break}return false});function c(o,m,n){if(n.substr(0,1)=="v"){var f=a(e.grid.bDiv)[0].clientHeight,p=a(e.grid.bDiv)[0].scrollTop,q=e.rows[o].offsetTop+e.rows[o].clientHeight,k=e.rows[o].offsetTop;if(n=="vd"){if(q>=f){a(e.grid.bDiv)[0].scrollTop=a(e.grid.bDiv)[0].scrollTop+e.rows[o].clientHeight}}if(n=="vu"){if(k<p){a(e.grid.bDiv)[0].scrollTop=a(e.grid.bDiv)[0].scrollTop-e.rows[o].clientHeight}}}if(n=="h"){var j=a(e.grid.bDiv)[0].clientWidth,h=a(e.grid.bDiv)[0].scrollLeft,g=e.rows[o].cells[m].offsetLeft+e.rows[o].cells[m].clientWidth,l=e.rows[o].cells[m].offsetLeft;if(g>=j+parseInt(h)){a(e.grid.bDiv)[0].scrollLeft=a(e.grid.bDiv)[0].scrollLeft+e.rows[o].cells[m].clientWidth}else{if(l<h){a(e.grid.bDiv)[0].scrollLeft=a(e.grid.bDiv)[0].scrollLeft-e.rows[o].cells[m].clientWidth}}}}function b(j,f){var h,g;if(f=="lft"){h=j+1;for(g=j;g>=0;g--){if(e.p.colModel[g].hidden!==true){h=g;break}}}if(f=="rgt"){h=j-1;for(g=j;g<e.p.colModel.length;g++){if(e.p.colModel[g].hidden!==true){h=g;break}}}return h}})},getChangedCells:function(c){var b=[];if(!c){c="all"}this.each(function(){var d=this;if(!d.grid||d.p.cellEdit!==true){return}a(d.rows).slice(1).each(function(e){var f={};if(a(this).hasClass("edited")){a("td",this).each(function(g){nm=d.p.colModel[g].name;if(nm!=="cb"&&nm!=="subgrid"){if(c=="dirty"){if(a(this).hasClass("dirty-cell")){f[nm]=a.htmlDecode(a(this).html())}}else{f[nm]=a.htmlDecode(a(this).html())}}});f.id=this.id;b.push(f)}})});return b}})})(jQuery);