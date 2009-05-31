;(function($){
/**
 * jqGrid extension for form editing Grid Data
 * Tony Tomov tony@trirand.com
 * http://trirand.com/blog/ 
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
**/ 
var rp_ge = null;
$.fn.extend({
	searchGrid : function ( p ) {
		p = $.extend({
			top : 0,
			left: 0,
			width: 360,
			height: 80,
			modal: false,
			drag: true,
			closeicon: 'ico-close.gif',
			dirty: false,
			sField:'searchField',
			sValue:'searchString',
			sOper: 'searchOper',
			processData: "",
			checkInput :false,
			beforeShowSearch: null,
			afterShowSearch : null,
			onInitializeSearch: null,
			closeAfterSearch : false,
			closeOnEscape : false,
			// translation
			// if you want to change or remove the order change it in sopt
			// ['bw','eq','ne','lt','le','gt','ge','ew','cn'] 
			sopt: null 
		}, $.jgrid.search, p || {});
		return this.each(function(){
			var $t = this;
			if( !$t.grid ) { return; }
			if(!p.imgpath) { p.imgpath= $t.p.imgpath; }
			var gID = $("table:first",$t.grid.bDiv).attr("id");
			var IDs = { themodal:'srchmod'+gID,modalhead:'srchhead'+gID,modalcontent:'srchcnt'+gID };
			if ( $("#"+IDs.themodal).html() != null ) {
				if( $.isFunction(p.beforeShowSearch) ) { p.beforeShowSearch($("#srchcnt"+gID)); }
				viewModal("#"+IDs.themodal,{modal: p.modal});
				if( $.isFunction(p.afterShowSearch) ) { p.afterShowSearch($("#srchcnt"+gID)); }
			} else {
				var cM = $t.p.colModel;
				var cNames = "<select id='snames' class='search'>";
				var nm, hc, sf;
				for(var i=0; i< cM.length;i++) {
					nm = cM[i].name;
					sf = (cM[i].search===false) ? false: true;
					if(cM[i].editrules && cM[i].editrules.searchhidden === true) {
						hc = true;
					} else {
						if(cM[i].hidden === true ) {
							hc = false;
						} else {
							hc = true;
						}
					}					
					if( nm !== 'cb' && nm !== 'subgrid' && sf && hc===true ) { // add here condition for searchable
						var sname = (cM[i].index) ? cM[i].index : nm;
						cNames += "<option value='"+sname+"'>"+$t.p.colNames[i]+"</option>";
					}
				}
				cNames += "</select>";
				var getopt = p.sopt || ['bw','eq','ne','lt','le','gt','ge','ew','cn'];
				var sOpt = "<select id='sopt' class='search'>";
				for(var i = 0; i<getopt.length;i++) {
					sOpt += getopt[i]=='eq' ? "<option value='eq'>"+p.odata[0]+"</option>" : "";
					sOpt += getopt[i]=='ne' ? "<option value='ne'>"+p.odata[1]+"</option>" : "";
					sOpt += getopt[i]=='lt' ? "<option value='lt'>"+p.odata[2]+"</option>" : "";
					sOpt += getopt[i]=='le' ? "<option value='le'>"+p.odata[3]+"</option>" : "";
					sOpt += getopt[i]=='gt' ? "<option value='gt'>"+p.odata[4]+"</option>" : "";
					sOpt += getopt[i]=='ge' ? "<option value='ge'>"+p.odata[5]+"</option>" : "";
					sOpt += getopt[i]=='bw' ? "<option value='bw'>"+p.odata[6]+"</option>" : "";
					sOpt += getopt[i]=='ew' ? "<option value='ew'>"+p.odata[7]+"</option>" : "";
					sOpt += getopt[i]=='cn' ? "<option value='cn'>"+p.odata[8]+"</option>" : "";
				};
				sOpt += "</select>";
				// field and buttons
				var sField  = "<input id='sval' class='search' type='text' size='20' maxlength='100'/>";
				var bSearch = "<input id='sbut' class='buttonsearch' type='button' value='"+p.Find+"'/>";
				var bReset  = "<input id='sreset' class='buttonsearch' type='button' value='"+p.Reset+"'/>";
				var cnt = $("<table width='100%'><tbody><tr style='display:none' id='srcherr'><td colspan='5'></td></tr><tr><td>"+cNames+"</td><td>"+sOpt+"</td><td>"+sField+"</td><td>"+bSearch+"</td><td>"+bReset+"</td></tr></tbody></table>");
				createModal(IDs,cnt,p,$t.grid.hDiv,$t.grid.hDiv);
				if ( $.isFunction(p.onInitializeSearch) ) { p.onInitializeSearch( $("#srchcnt"+gID) ); };
				if ( $.isFunction(p.beforeShowSearch) ) { p.beforeShowSearch($("#srchcnt"+gID)); };
				viewModal("#"+IDs.themodal,{modal:p.modal});
				if($.isFunction(p.afterShowSearch)) { p.afterShowSearch($("#srchcnt"+gID)); }
				if(p.drag) { DnRModal("#"+IDs.themodal,"#"+IDs.modalhead+" td.modaltext"); }
				$("#sbut","#"+IDs.themodal).click(function(){
					if( $("#sval","#"+IDs.themodal).val() !="" ) {
						var es=[true,"",""];
						$("#srcherr >td","#srchcnt"+gID).html("").hide();
						$t.p.searchdata[p.sField] = $("option[selected]","#snames").val();
						$t.p.searchdata[p.sOper] = $("option[selected]","#sopt").val();
						$t.p.searchdata[p.sValue] = $("#sval","#"+IDs.modalcontent).val();
						if(p.checkInput) {
							for(var i=0; i< cM.length;i++) {
								var sname = (cM[i].index) ? cM[i].index : nm;
								if (sname == $t.p.searchdata[p.sField]) {
									break;
								}
							}
							es = checkValues($t.p.searchdata[p.sValue],i,$t);
						}
						if (es[0]===true) {
							$t.p.search = true; // initialize the search
							// construct array of data which is passed in populate() see jqGrid
							if(p.dirty) { $(".no-dirty-cell",$t.p.pager).addClass("dirty-cell"); }
							$t.p.page= 1;
							$($t).trigger("reloadGrid");
							if(p.closeAfterSearch === true) {
								hideModal("#"+IDs.themodal);
							}
						} else {
							$("#srcherr >td","#srchcnt"+gID).html(es[1]).show();
						}
					}
				});
				$("#sreset","#"+IDs.themodal).click(function(){
					if ($t.p.search) {
						$("#srcherr >td","#srchcnt"+gID).html("").hide();
						$t.p.search = false;
						$t.p.searchdata = {};
						$t.p.page= 1;
						$("#sval","#"+IDs.themodal).val("");
						if(p.dirty) { $(".no-dirty-cell",$t.p.pager).removeClass("dirty-cell"); }
						$($t).trigger("reloadGrid");
					}
				});
			}
		});
	},
	editGridRow : function(rowid, p){
		p = $.extend({
			top : 0,
			left: 0,
			width: 0,
			height: 0,
			modal: false,
			drag: true, 
			closeicon: 'ico-close.gif',
			imgpath: '',
			url: null,
			mtype : "POST",
			closeAfterAdd : false,
			clearAfterAdd : true,
			closeAfterEdit : false,
			reloadAfterSubmit : true,
			onInitializeForm: null,
			beforeInitData: null,
			beforeShowForm: null,
			afterShowForm: null,
			beforeSubmit: null,
			afterSubmit: null,
			onclickSubmit: null,
			afterComplete: null,
			onclickPgButtons : null,
			afterclickPgButtons: null,
			editData : {},
			recreateForm : false,
			closeOnEscape : false,
			addedrow : "first"
		}, $.jgrid.edit, p || {});
		rp_ge = p;
		return this.each(function(){
			var $t = this;
			if (!$t.grid || !rowid) { return; }
			if(!p.imgpath) { p.imgpath= $t.p.imgpath; }
			// I hate to rewrite code, but ...
			var gID = $("table:first",$t.grid.bDiv).attr("id"),
			IDs = {themodal:'editmod'+gID,modalhead:'edithd'+gID,modalcontent:'editcnt'+gID},
			onBeforeShow = $.isFunction(rp_ge.beforeShowForm) ? rp_ge.beforeShowForm : false,
			onAfterShow = $.isFunction(rp_ge.afterShowForm) ? rp_ge.afterShowForm : false,
			onBeforeInit = $.isFunction(rp_ge.beforeInitData) ? rp_ge.beforeInitData : false,
			onInitializeForm = $.isFunction(rp_ge.onInitializeForm) ? rp_ge.onInitializeForm : false,
			maxCols = 1, copydata,
			frmgr = "FrmGrid_"+gID , frmtb = "TblGrid_"+gID;
			if (rowid=="new") {
				rowid = "_empty";
				p.caption=p.addCaption;
			} else {
				p.caption=p.editCaption;
			};
			if(p.recreateForm===true && $("#"+IDs.themodal).html() != null) {
				$("#"+IDs.themodal).remove();
			}
			if ( $("#"+IDs.themodal).html() != null ) {
				$(".modaltext","#"+IDs.modalhead).html(p.caption);
				$("#FormError","#"+frmtb).hide();
				if(onBeforeInit) { onBeforeInit($("#"+frmgr)); }
				fillData(rowid,$t);
				if(rowid=="_empty") { $("#pData, #nData","#"+frmtb).hide(); } else { $("#pData, #nData","#"+frmtb).show(); }
				if(onBeforeShow) { onBeforeShow($("#"+frmgr)); }
				if(p.processing === true)  { // in case of error from server
					p.processing=false;
					$("#sData", "#"+frmtb).attr("disabled",false);
					$("div.loading","#"+IDs.themodal).hide();
				}
				viewModal("#"+IDs.themodal,{modal:p.modal});
				if(onAfterShow) { onAfterShow($("#"+frmgr)); }
			} else {
				$($t.p.colModel).each( function(i) {
					maxCols = Math.max(maxCols, this.formoptions ? this.formoptions.colpos || 0 : 0 );
				});				
				var flr, frm = $("<form name='FormPost' id='"+frmgr+"' class='FormGrid'></form>"),
				tbl =$("<table id='"+frmtb+"' class='EditTable' cellspacing='0' cellpading='0' border='0'><tbody></tbody></table>");
				$(frm).append(tbl);
				flr = $("<tr id='FormError' style='display:none'><td colspan='"+(maxCols*2)+"'></td></tr>");
				flr[0].rp = 0;
				$(tbl).append(flr);
				// set the id.
				// use carefull only to change here colproperties.
				if(onBeforeInit) { onBeforeInit($("#"+frmgr)); }
				var valref = createData(rowid, $t, tbl, maxCols),
				// buttons at footer
				imp = $t.p.imgpath,
				bP  ="<img id='pData' src='"+imp+$t.p.previmg+"'/>",
				bN  ="<img id='nData' src='"+imp+$t.p.nextimg+"'/>",
				bS  ="<input id='sData' type='button' class='EditButton' value='"+p.bSubmit+"'/>",
				bC  ="<input id='cData' type='button'  class='EditButton' value='"+p.bCancel+"'/>";
				flr = $("<tr id='Act_Buttons'><td class='navButton'>"+bP+"&nbsp;"+bN+"</td><td colspan='"+(maxCols*2-1)+"'class='EditButton'>"+bS+"&nbsp;"+bC+"</td></tr>");
				flr[0].rp = valref.length + 100;
				$(tbl).append(flr);
				if(maxCols >  1) {
					var sd=[];
					$.each($(tbl)[0].rows,function(i,r){
						sd[i] = r;
					});
					sd.sort(function(a,b){
						if(a.rp > b.rp) {return 1;}
						if(a.rp < b.rp) {return -1;}
						return 0;
					});
					$.each(sd, function(index, row) {
						$('tbody',tbl).append(row);
					});
				}
				// beforeinitdata after creation of the form
				createModal(IDs,frm,p,$t.grid.hDiv,$t.grid.hDiv);
				// here initform - only once
				if(onInitializeForm) { onInitializeForm($("#"+frmgr)); }
				if( p.drag ) { DnRModal("#"+IDs.themodal,"#"+IDs.modalhead+" td.modaltext"); }
				if(rowid=="_empty") { $("#pData,#nData","#"+frmtb).hide(); } else { $("#pData,#nData","#"+frmtb).show(); }
				if(onBeforeShow) { onBeforeShow($("#"+frmgr)); }
				viewModal("#"+IDs.themodal,{modal:p.modal});
				if(onAfterShow) { onAfterShow($("#"+frmgr)); }
				$("#sData", "#"+frmtb).click(function(e){
					var postdata = {}, ret=[true,"",""], extpost={};
					$("#FormError","#"+frmtb).hide();
					// all depend on ret array
					//ret[0] - succes
					//ret[1] - msg if not succes
					//ret[2] - the id  that will be set if reload after submit false
					var j =0;
					$(".FormElement", "#"+frmtb).each(function(i){
						var suc =  true;
						switch ($(this).get(0).type) {
							case "checkbox":
								if($(this).attr("checked")) {
									postdata[this.name]= $(this).val();
								}else {
									var ofv = $(this).attr("offval");
									postdata[this.name]= ofv;
									extpost[this.name] = ofv;
								}
							break;
							case "select-one":
								postdata[this.name]= $("option:selected",this).val();
								extpost[this.name]= $("option:selected",this).text();
							break;
							case "select-multiple":
								postdata[this.name]= $(this).val();
								var selectedText = [];
								$("option:selected",this).each(
									function(i,selected){
										selectedText[i] = $(selected).text();
									}
								);
								extpost[this.name]= selectedText.join(",");
							break;								
							case "password":
							case "text":
							case "textarea":
								postdata[this.name] = $(this).val();
								ret = checkValues(postdata[this.name],valref[i],$t);
								if(ret[0] === false) {
									suc=false;
								} else {
									postdata[this.name] = !$t.p.autoencode ? postdata[this.name] : htmlEncode(postdata[this.name]);
								}
							break;
						}
						j++;
						if(!suc) { return false; }
					});
					if(j==0) { ret[0] = false; ret[1] = $.jgrid.errors.norecords; }
					if( $.isFunction( rp_ge.onclickSubmit)) { rp_ge.editData = rp_ge.onclickSubmit(p) || {}; }
					if(ret[0]) {
						if( $.isFunction(rp_ge.beforeSubmit))  { ret = rp_ge.beforeSubmit(postdata,$("#"+frmgr)); }
					}
					var gurl = rp_ge.url ? rp_ge.url : $t.p.editurl;
					if(ret[0]) {
						if(!gurl) { ret[0]=false; ret[1] += " "+$.jgrid.errors.nourl; }
					}
					if(ret[0] === false) {
						$("#FormError>td","#"+frmtb).html(ret[1]);
						$("#FormError","#"+frmtb).show();
					} else {
						if(!p.processing) {
							p.processing = true;
							$("div.loading","#"+IDs.themodal).show();
							$(this).attr("disabled",true);
							// we add to pos data array the action - the name is oper
							postdata.oper = postdata.id == "_empty" ? "add" : "edit";
							postdata = $.extend(postdata,rp_ge.editData);
							$.ajax({
								url:gurl,
								type: rp_ge.mtype,
								data:postdata,
								complete:function(data,Status){
									if(Status != "success") {
										ret[0] = false;
										ret[1] = Status+" Status: "+data.statusText +" Error code: "+data.status;
									} else {
										// data is posted successful
										// execute aftersubmit with the returned data from server
										if( $.isFunction(rp_ge.afterSubmit) ) {
											ret = rp_ge.afterSubmit(data,postdata);
										}
									}
									if(ret[0] === false) {
										$("#FormError>td","#"+frmtb).html(ret[1]);
										$("#FormError","#"+frmtb).show();
									} else {
										postdata = $.extend(postdata,extpost);
										// the action is add
										if(postdata.id=="_empty" ) {
											//id processing
											// user not set the id ret[2]
											if(!ret[2]) { ret[2] = parseInt($($t).getGridParam('records'))+1; }
											postdata.id = ret[2];
											if(rp_ge.closeAfterAdd) {
												if(rp_ge.reloadAfterSubmit) { $($t).trigger("reloadGrid"); }
												else {
													$($t).addRowData(ret[2],postdata,p.addedrow);
													$($t).setSelection(ret[2]);
												}
												hideModal("#"+IDs.themodal);
											} else if (rp_ge.clearAfterAdd) {
												if(rp_ge.reloadAfterSubmit) { $($t).trigger("reloadGrid"); }
												else { $($t).addRowData(ret[2],postdata,p.addedrow); }
												$(".FormElement", "#"+frmtb).each(function(i){
													switch ($(this).get(0).type) {
													case "checkbox":
														$(this).attr("checked",0);
														break;
													case "select-one":
													case "select-multiple":
														$("option",this).attr("selected","");
														break;
														case "password":
														case "text":
														case "textarea":
															if(this.name =='id') { $(this).val("_empty"); }
															else { $(this).val(""); }
														break;
													}
												});
												fillData("_empty",$t);
											} else {
												if(rp_ge.reloadAfterSubmit) { $($t).trigger("reloadGrid");}
												else { $($t).addRowData(ret[2],postdata,p.addedrow); }
											}
										} else {
											// the action is update
											if(rp_ge.reloadAfterSubmit) {
												$($t).trigger("reloadGrid");
												if( !rp_ge.closeAfterEdit ) { $($t).setSelection(postdata.id); }
											} else {
												if($t.p.treeGrid === true) {
													$($t).setTreeRow(postdata.id,postdata);
												} else {
													$($t).setRowData(postdata.id,postdata);
												}
											}
											if(rp_ge.closeAfterEdit) { hideModal("#"+IDs.themodal); }
										}
										if($.isFunction(rp_ge.afterComplete)) {
											copydata = data;
											setTimeout(function(){rp_ge.afterComplete(copydata,postdata,$("#"+frmgr));copydata=null},500);
										}
									}
									p.processing=false;
									$("#sData", "#"+frmtb).attr("disabled",false);
									$("div.loading","#"+IDs.themodal).hide();
								},
								error:function(xhr,st,err){
									$("#FormError>td","#"+frmtb).html(st+ " : "+err);
									$("#FormError","#"+frmtb).show();
									p.processing=false;
									$("#sData", "#"+frmtb).attr("disabled",false);
									$("div.loading","#"+IDs.themodal).hide();
								}
							});
						}
					}
					e.stopPropagation();
					return false;
				});
				$("#cData", "#"+frmtb).click(function(e){
					hideModal("#"+IDs.themodal);
					e.stopPropagation();
					return false;
				});
				$("#nData", "#"+frmtb).click(function(e){
					$("#FormError","#"+frmtb).hide();
					var npos = getCurrPos();
					npos[0] = parseInt(npos[0]);
					if(npos[0] != -1 && npos[1][npos[0]+1]) {
						if($.isFunction(p.onclickPgButtons)) {
							p.onclickPgButtons('next',$("#"+frmgr),npos[1][npos[0]]);
						}
						fillData(npos[1][npos[0]+1],$t);
						$($t).setSelection(npos[1][npos[0]+1]);
						if($.isFunction(p.afterclickPgButtons)) {
							p.afterclickPgButtons('next',$("#"+frmgr),npos[1][npos[0]+1]);
						}
						updateNav(npos[0]+1,npos[1].length-1);
					};
					return false;
				});
				$("#pData", "#"+frmtb).click(function(e){
					$("#FormError","#"+frmtb).hide();
					var ppos = getCurrPos();
					if(ppos[0] != -1 && ppos[1][ppos[0]-1]) {
						if($.isFunction(p.onclickPgButtons)) {
							p.onclickPgButtons('prev',$("#"+frmgr),ppos[1][ppos[0]]);
						}
						fillData(ppos[1][ppos[0]-1],$t);
						$($t).setSelection(ppos[1][ppos[0]-1]);
						if($.isFunction(p.afterclickPgButtons)) {
							p.afterclickPgButtons('prev',$("#"+frmgr),ppos[1][ppos[0]-1]);
						}
						updateNav(ppos[0]-1,ppos[1].length-1);
					};
					return false;
				});
			};
			var posInit =getCurrPos();
			updateNav(posInit[0],posInit[1].length-1);
			function updateNav(cr,totr,rid){                
				var imp = $t.p.imgpath;
				if (cr==0) { $("#pData","#"+frmtb).attr("src",imp+"off-"+$t.p.previmg); } else { $("#pData","#"+frmtb).attr("src",imp+$t.p.previmg); }
				if (cr==totr) { $("#nData","#"+frmtb).attr("src",imp+"off-"+$t.p.nextimg); } else { $("#nData","#"+frmtb).attr("src",imp+$t.p.nextimg); }
			};
			function getCurrPos() {
				var rowsInGrid = $($t).getDataIDs();
				var selrow = $("#id_g","#"+frmtb).val();
				var pos = $.inArray(selrow,rowsInGrid);
				return [pos,rowsInGrid];
			};
			function createData(rowid,obj,tb,maxcols){
				var nm, hc,trdata, tdl, tde, cnt=0,tmp, dc,elc, retpos=[], ind=false,
				tdtmpl = "<td class='CaptionTD'>&nbsp;</td><td class='DataTD'>&nbsp;</td>", tmpl=""; //*2
				for (var i =1;i<=maxcols;i++) {
					tmpl += tdtmpl;
				}
				ind = $(obj).getInd(obj.rows,rowid);
				$(obj.p.colModel).each( function(i) {
					nm = this.name;
					// hidden fields are included in the form
					if(this.editrules && this.editrules.edithidden == true) {
						hc = false;
					} else {
						hc = this.hidden === true ? true : false;
					}
					dc = hc ? "style='display:none'" : "";
					if ( nm !== 'cb' && nm !== 'subgrid' && this.editable===true) {
						if(ind === false) {
							tmp = "";
						} else {
							if(nm == obj.p.ExpandColumn && obj.p.treeGrid === true) {
								tmp = $("td:eq("+i+")",obj.rows[ind]).text();
							} else {
								try {
									tmp =  $.unformat($("td:eq("+i+")",obj.rows[ind]),{colModel:this},i);
								} catch (_) {
									tmp = $("td:eq("+i+")",obj.rows[ind]).html();
								}
							}
						}
						var opt = $.extend({}, this.editoptions || {} ,{id:nm,name:nm}),
						frmopt = $.extend({}, {elmprefix:'',elmsuffix:''}, this.formoptions || {}),
						rp = parseInt(frmopt.rowpos) || cnt+1,
						cp = parseInt((parseInt(frmopt.colpos) || 1)*2);
						if(rowid == "_empty" && opt.defaultValue ) {
							tmp = $.isFunction(opt.defaultValue) ? opt.defaultValue() : opt.defaultValue; 
						}
						if(!this.edittype) this.edittype = "text";
						elc = createEl(this.edittype,opt,tmp);
						$(elc).addClass("FormElement");
						// try to find the element
						trdata = $(tbl).find("tr[rowpos="+rp+"]");
						if ( trdata.length==0 ) {
							trdata = $("<tr "+dc+" rowpos='"+rp+"'></tr>").addClass("FormData").attr("id","tr_"+nm);
							$(trdata).append(tmpl);
							$(tb).append(trdata);
							trdata[0].rp = rp;
						}
						$("td:eq("+(cp-2)+")",trdata[0]).html( typeof frmopt.label === 'undefined' ? obj.p.colNames[i]: frmopt.label);
						$("td:eq("+(cp-1)+")",trdata[0]).append(frmopt.elmprefix).append(elc).append(frmopt.elmsuffix);
						//$(tb).append(trdata);
						retpos[cnt] = i;
						cnt++;
					};
				});
				if( cnt > 0) {
					var idrow = $("<tr class='FormData' style='display:none'><td class='CaptionTD'></td><td colspan='"+ (maxcols*2-1)+"' class='DataTD'><input class='FormElement' id='id_g' type='text' name='id' value='"+rowid+"'/></td></tr>");
					idrow[0].rp = cnt+99;
					$(tb).append(idrow);
				}
				return retpos;
			};
			function fillData(rowid,obj){
				var nm, hc,cnt=0,tmp, opt;
				$('#'+rowid+' td',obj.grid.bDiv).each( function(i) {
					nm = obj.p.colModel[i].name;
					// hidden fields are included in the form
					if(obj.p.colModel[i].editrules && obj.p.colModel[i].editrules.edithidden === true) {
						hc = false;
					} else {
						hc = obj.p.colModel[i].hidden === true ? true : false;
					}
					if ( nm !== 'cb' && nm !== 'subgrid' && obj.p.colModel[i].editable===true) {
						if(nm == obj.p.ExpandColumn && obj.p.treeGrid === true) {
							tmp = $(this).text();
						} else {
							try {
								tmp =  $.unformat(this,{colModel:obj.p.colModel[i]},i);
							} catch (_) {
								tmp = $(this).html();
							}
						}
						nm= nm.replace('.',"\\.");
						opt = $.extend({},obj.p.colModel[i].editoptions || {});
						if(rowid == "_empty" && opt.defaultValue ) {
							tmp = $.isFunction(opt.defaultValue) ? opt.defaultValue() : opt.defaultValue; 
						}
						switch (obj.p.colModel[i].edittype) {
							case "password":
							case "text":
								$("#"+nm,"#"+frmtb).val(tmp);
								break;
							case "textarea":
								if(tmp == "&nbsp;" || tmp == "&#160;" || (tmp.length==1 && tmp.charCodeAt(0)==160) ) {tmp='';}
								$("#"+nm,"#"+frmtb).val(tmp);
								break;
							case "select":
								$("#"+nm+" option","#"+frmtb).each(function(j){
									if (!opt.multiple && tmp == $(this).text() ){
										this.selected= true;
									} else if (opt.multiple){
										if(  $.inArray($(this).text(), tmp.split(",") ) > -1  ){
											this.selected = true;
										}else{
											this.selected = false;
										}
									} else {
										this.selected = false;
									}
								});
								break;
							case "checkbox":
								if(tmp==$("#"+nm,"#"+frmtb).val()) {
									$("#"+nm,"#"+frmtb).attr("checked",true);
									$("#"+nm,"#"+frmtb).attr("defaultChecked",true); //ie
								} else {
									$("#"+nm,"#"+frmtb).attr("checked",false);
									$("#"+nm,"#"+frmtb).attr("defaultChecked",""); //ie
								}
								break; 
						}
						if (hc) { $("#"+nm,"#"+frmtb).parents("tr:first").hide(); }
						cnt++;
					}
				});
				if(cnt>0) { $("#id_g","#"+frmtb).val(rowid); }
				else { $("#id_g","#"+frmtb).val(""); }
				return cnt;
			};
		});
	},
	viewGridRow : function(rowid, p){
		p = $.extend({
			top : 0,
			left: 0,
			width: 0,
			height: 0,
			modal: false,
			drag: true, 
			closeicon: 'ico-close.gif',
			imgpath: '',
			closeOnEscape : false,
			labelswidth: '30%'
		}, $.jgrid.view, p || {});
		return this.each(function(){
			var $t = this;
			if (!$t.grid || !rowid) { return; }
			if(!p.imgpath) { p.imgpath= $t.p.imgpath; }
			// I hate to rewrite code, but ...
			var gID = $("table:first",$t.grid.bDiv).attr("id"),
			IDs = {themodal:'viewmod'+gID,modalhead:'viewhd'+gID,modalcontent:'viewcnt'+gID},
			maxCols = 1,
			frmgr = "ViewGrid_"+gID , frmtb = "ViewTbl_"+gID;
			if ( $("#"+IDs.themodal).html() != null ) {
				$(".modaltext","#"+IDs.modalhead).html(p.caption);
				$("#FormError","#"+frmtb).hide();
				fillData(rowid,$t);
				viewModal("#"+IDs.themodal,{modal:p.modal});
				focusaref();
			} else {
				$($t.p.colModel).each( function(i) {
					maxCols = Math.max(maxCols, this.formoptions ? this.formoptions.colpos || 0 : 0 );
				});				
				var flr, frm = $("<form name='FormPost' id='"+frmgr+"' class='FormGrid'></form>"),
				tbl =$("<table id='"+frmtb+"' class='EditTable' cellspacing='0' cellpading='2' border='0' style='table-layout:fixed'><tbody></tbody></table>");
				$(frm).append(tbl);
				// set the id.
				var valref = createData(rowid, $t, tbl, maxCols),
				// buttons at footer
				imp = $t.p.imgpath,
				bP  ="<img id='pData' src='"+imp+$t.p.previmg+"'/>",
				bN  ="<img id='nData' src='"+imp+$t.p.nextimg+"'/>",
				bC  ="<input id='cData' type='button'  class='EditButton' value='"+p.bClose+"'/>";
				flr = $("<tr id='Act_Buttons'><td class='navButton'>"+bP+"&nbsp;"+bN+"</td><td colspan='"+(maxCols*2-1)+"'class='EditButton'>"+bC+"</td></tr>");
				flr[0].rp = valref.length + 100;
				$(tbl).append(flr);
				if(maxCols >  1) {
					var sd=[];
					$.each($(tbl)[0].rows,function(i,r){
						sd[i] = r;
					});
					sd.sort(function(a,b){
						if(a.rp > b.rp) {return 1;}
						if(a.rp < b.rp) {return -1;}
						return 0;
					});
					$.each(sd, function(index, row) {
						$('tbody',tbl).append(row);
					});
				}
				createModal(IDs,frm,p,$t.grid.hDiv,$t.grid.hDiv);
				if( p.drag ) { DnRModal("#"+IDs.themodal,"#"+IDs.modalhead+" td.modaltext"); }
				viewModal("#"+IDs.themodal,{modal:p.modal});
				focusaref();
				$("#cData", "#"+frmtb).click(function(e){
					hideModal("#"+IDs.themodal);
					e.stopPropagation();
					return false;
				});
				$("#nData", "#"+frmtb).click(function(e){
					$("#FormError","#"+frmtb).hide();
					var npos = getCurrPos();
					npos[0] = parseInt(npos[0]);
					if(npos[0] != -1 && npos[1][npos[0]+1]) {
						if($.isFunction(p.onclickPgButtons)) {
							p.onclickPgButtons('next',$("#"+frmgr),npos[1][npos[0]]);
						}
						fillData(npos[1][npos[0]+1],$t);
						$($t).setSelection(npos[1][npos[0]+1]);
						if($.isFunction(p.afterclickPgButtons)) {
							p.afterclickPgButtons('next',$("#"+frmgr),npos[1][npos[0]+1]);
						}
						updateNav(npos[0]+1,npos[1].length-1);
					};
					focusaref();
					return false;
				});
				$("#pData", "#"+frmtb).click(function(e){
					$("#FormError","#"+frmtb).hide();
					var ppos = getCurrPos();
					if(ppos[0] != -1 && ppos[1][ppos[0]-1]) {
						if($.isFunction(p.onclickPgButtons)) {
							p.onclickPgButtons('prev',$("#"+frmgr),ppos[1][ppos[0]]);
						}
						fillData(ppos[1][ppos[0]-1],$t);
						$($t).setSelection(ppos[1][ppos[0]-1]);
						if($.isFunction(p.afterclickPgButtons)) {
							p.afterclickPgButtons('prev',$("#"+frmgr),ppos[1][ppos[0]-1]);
						}
						updateNav(ppos[0]-1,ppos[1].length-1);
					};
					focusaref();
					return false;
				});
			};
			function focusaref(){ //Sfari 3 issues
				if(p.closeOnEscape===true) {
					setTimeout(function(){$(".jqmClose","#"+IDs.modalhead).focus()},0);
				}
			}
			var posInit =getCurrPos();
			updateNav(posInit[0],posInit[1].length-1);
			function updateNav(cr,totr,rid){                
				var imp = $t.p.imgpath;
				if (cr==0) { $("#pData","#"+frmtb).attr("src",imp+"off-"+$t.p.previmg); } else { $("#pData","#"+frmtb).attr("src",imp+$t.p.previmg); }
				if (cr==totr) { $("#nData","#"+frmtb).attr("src",imp+"off-"+$t.p.nextimg); } else { $("#nData","#"+frmtb).attr("src",imp+$t.p.nextimg); }
			};
			function getCurrPos() {
				var rowsInGrid = $($t).getDataIDs();
				var selrow = $("#id_g","#"+frmtb).val();
				var pos = $.inArray(selrow,rowsInGrid);
				return [pos,rowsInGrid];
			};
			function createData(rowid,obj,tb,maxcols){
				var nm, hc,trdata, tdl, tde, cnt=0,tmp, dc,elc, retpos=[], ind=false,
				tdtmpl = "<td class='CaptionTD' width='"+p.labelswidth+"'>&nbsp;</td><td class='DataTD'>&nbsp;</td>", tmpl="",
				tdtmpl2 = "<td class='CaptionTD'>&nbsp;</td><td class='DataTD'>&nbsp;</td>",
				fmtnum = ['integer','number','currency'],max1 =0, max2=0 ,maxw,setme;
				for (var i =1;i<=maxcols;i++) {
					tmpl += i == 1 ? tdtmpl : tdtmpl2;
				}
				// find max number align rigth with property formatter
				$(obj.p.colModel).each( function(i) {
					if(this.editrules && this.editrules.edithidden === true) {
						hc = false;
					} else {
						hc = this.hidden === true ? true : false;
					}
					if(!hc && this.align==='right') {
						if(this.formatter && $.inArray(this.formatter,fmtnum) !== -1 ) {
							max1 = Math.max(max1,parseInt(this.width,10));
						} else {
							max2 = Math.max(max2,parseInt(this.width,10));
						}
					}
				});
				maxw  = max1 !==0 ? max1 : max2 !==0 ? max2 : 0;
				ind = $(obj).getInd(obj.rows,rowid);
				$(obj.p.colModel).each( function(i) {
					nm = this.name;
					setme = false;
					// hidden fields are included in the form
					if(this.editrules && this.editrules.edithidden === true) {
						hc = false;
					} else {
						hc = this.hidden === true ? true : false;
					}
					dc = hc ? "style='display:none'" : "";
					if ( nm !== 'cb' && nm !== 'subgrid' && this.editable===true) {
						if(ind === false) {
							tmp = "";
						} else {
							if(nm == obj.p.ExpandColumn && obj.p.treeGrid === true) {
								tmp = $("td:eq("+i+")",obj.rows[ind]).text();
							} else {
								tmp = $("td:eq("+i+")",obj.rows[ind]).html();
							}
						}
						setme = this.align === 'right' && maxw !==0 ? true : false;
						var opt = $.extend({}, this.editoptions || {} ,{id:nm,name:nm}),
						frmopt = $.extend({}, {elmprefix:'',elmsuffix:''}, this.formoptions || {}),
						rp = parseInt(frmopt.rowpos) || cnt+1,
						cp = parseInt((parseInt(frmopt.colpos) || 1)*2);
						if(!this.edittype) this.edittype = "text";
						trdata = $(tbl).find("tr[rowpos="+rp+"]");
						if ( trdata.length==0 ) {
							trdata = $("<tr "+dc+" rowpos='"+rp+"'></tr>").addClass("FormData").attr("id","tr_"+nm);
							$(trdata).append(tmpl);
							$(tb).append(trdata);
							trdata[0].rp = rp;
						}
						$("td:eq("+(cp-2)+")",trdata[0]).html('<b>'+ (typeof frmopt.label === 'undefined' ? obj.p.colNames[i]: frmopt.label)+'</b>');
						$("td:eq("+(cp-1)+")",trdata[0]).append("<span style='position:absolute;float:left;'>"+tmp+"</span>").attr("id",nm);
						if(setme){
							$("td:eq("+(cp-1)+") span",trdata[0]).css({'text-align':'right',width:maxw+"px"});
						}
						retpos[cnt] = i;
						cnt++;
					};
				});
				if( cnt > 0) {
					var idrow = $("<tr class='FormData' style='display:none'><td class='CaptionTD'></td><td colspan='"+ (maxcols*2-1)+"' class='DataTD'><input class='FormElement' id='id_g' type='text' name='id' value='"+rowid+"'/></td></tr>");
					idrow[0].rp = cnt+99;
					$(tb).append(idrow);
				}
				return retpos;
			};
			function fillData(rowid,obj){
				var nm, hc,cnt=0,tmp, opt;
				$('#'+rowid+' td',obj.grid.bDiv).each( function(i) {
					nm = obj.p.colModel[i].name;
					// hidden fields are included in the form
					if(obj.p.colModel[i].editrules && obj.p.colModel[i].editrules.edithidden === true) {
						hc = false;
					} else {
						hc = obj.p.colModel[i].hidden === true ? true : false;
					}
					if ( nm !== 'cb' && nm !== 'subgrid' && obj.p.colModel[i].editable===true) {
						if(nm == obj.p.ExpandColumn && obj.p.treeGrid === true) {
							tmp = $(this).text();
						} else {
							tmp = $(this).html();
						}
						nm= nm.replace('.',"\\.");
						opt = $.extend({},obj.p.colModel[i].editoptions || {});
						$("#"+nm+" span","#"+frmtb).html(tmp);
						if (hc) { $("#"+nm,"#"+frmtb).parents("tr:first").hide(); }
						cnt++;
					}
				});
				if(cnt>0) { $("#id_g","#"+frmtb).val(rowid); }
				else { $("#id_g","#"+frmtb).val(""); }
				return cnt;
			};
		});
	},
	delGridRow : function(rowids,p) {
		p = $.extend({
			top : 0,
			left: 0,
			width: 240,
			height: 90,
			modal: false,
			drag: true, 
			closeicon: 'ico-close.gif',
			imgpath: '',
			url : '',
			mtype : "POST",
			reloadAfterSubmit: true,
			beforeShowForm: null,
			afterShowForm: null,
			beforeSubmit: null,
			onclickSubmit: null,
			afterSubmit: null,
			onclickSubmit: null,
			closeOnEscape : false,
			delData: {}
		}, $.jgrid.del, p ||{});
		return this.each(function(){
			var $t = this;
			if (!$t.grid ) { return; }
			if(!rowids) { return; }
			if(!p.imgpath) { p.imgpath= $t.p.imgpath; }
			var onBeforeShow = typeof p.beforeShowForm === 'function' ? true: false,
			onAfterShow = typeof p.afterShowForm === 'function' ? true: false,
			gID = $("table:first",$t.grid.bDiv).attr("id"),
			IDs = {themodal:'delmod'+gID,modalhead:'delhd'+gID,modalcontent:'delcnt'+gID},
			dtbl = "DelTbl_"+gID,
			copydata;
			if (isArray(rowids)) { rowids = rowids.join(); }
			if ( $("#"+IDs.themodal).html() != null ) {
				$("#DelData>td","#"+dtbl).text(rowids);
				$("#DelError","#"+dtbl).hide();
				if(p.processing===true) { // error case
					p.processing=false;
					$("#dData", "#"+dtbl).attr("disabled",false);
					$("div.loading","#"+IDs.themodal).hide();
				}
				if(onBeforeShow) { p.beforeShowForm($("#"+dtbl)); }
				viewModal("#"+IDs.themodal,{modal:p.modal});
				if(onAfterShow) { p.afterShowForm($("#"+dtbl)); }
			} else {
				var tbl =$("<table id='"+dtbl+"' class='DelTable'><tbody></tbody></table>");
				// error data 
				$(tbl).append("<tr id='DelError' style='display:none'><td >"+"&nbsp;"+"</td></tr>");
				$(tbl).append("<tr id='DelData' style='display:none'><td >"+rowids+"</td></tr>");
				$(tbl).append("<tr><td >"+p.msg+"</td></tr>");
				// buttons at footer
				var bS  ="<input id='dData' type='button' value='"+p.bSubmit+"'/>";
				var bC  ="<input id='eData' type='button' value='"+p.bCancel+"'/>";
				$(tbl).append("<tr><td class='DelButton'>"+bS+"&nbsp;"+bC+"</td></tr>");
				createModal(IDs,tbl,p,$t.grid.hDiv,$t.grid.hDiv);
				if( p.drag) { DnRModal("#"+IDs.themodal,"#"+IDs.modalhead+" td.modaltext"); }
				$("#dData","#"+dtbl).click(function(e){
					var ret=[true,""];
					var postdata = $("#DelData>td","#"+dtbl).text(); //the pair is name=val1,val2,...
					if( typeof p.onclickSubmit === 'function' ) { p.delData = p.onclickSubmit(p) || {}; }
					if( typeof p.beforeSubmit === 'function' ) { ret = p.beforeSubmit(postdata); }
					var gurl = p.url ? p.url : $t.p.editurl;
					if(!gurl) { ret[0]=false;ret[1] += " "+$.jgrid.errors.nourl;}
					if(ret[0] === false) {
						$("#DelError>td","#"+dtbl).html(ret[1]);
						$("#DelError","#"+dtbl).show();
					} else {
						if(!p.processing) {
							p.processing = true;
							$("div.loading","#"+IDs.themodal).show();
							$(this).attr("disabled",true);
							var postd = $.extend({oper:"del", id:postdata},p.delData);
							$.ajax({
								url:gurl,
								type: p.mtype,
								data:postd,
								complete:function(data,Status){
									if(Status != "success") {
										ret[0] = false;
										ret[1] = Status+" Status: "+data.statusText +" Error code: "+data.status;
									} else {
										// data is posted successful
										// execute aftersubmit with the returned data from server
										if( typeof p.afterSubmit === 'function' ) {
											ret = p.afterSubmit(data,postdata);
										}
									}
									if(ret[0] === false) {
										$("#DelError>td","#"+dtbl).html(ret[1]);
										$("#DelError","#"+dtbl).show();
									} else {
										if(p.reloadAfterSubmit) {
											if($t.p.treeGrid) {
												$($t).setGridParam({treeANode:0,datatype:$t.p.treedatatype});
											}
											$($t).trigger("reloadGrid");
										} else {
											var toarr = [];
											toarr = postdata.split(",");
											if($t.p.treeGrid===true){
												try {$($t).delTreeNode(toarr[0])} catch(e){}
											} else {
												for(var i=0;i<toarr.length;i++) {
													$($t).delRowData(toarr[i]);
												}
											}
											$t.p.selrow = null;
											$t.p.selarrrow = [];
										}
										if($.isFunction(p.afterComplete)) {
											copydata = data;
											setTimeout(function(){p.afterComplete(copydata,postdata);copydata=null;},500);
										}
									}
									p.processing=false;
									$("#dData", "#"+dtbl).attr("disabled",false);
									$("div.loading","#"+IDs.themodal).hide();
									if(ret[0]) { hideModal("#"+IDs.themodal); }
								},
								error:function(xhr,st,err){
									$("#DelError>td","#"+dtbl).html(st+ " : "+err);
									$("#DelError","#"+dtbl).show();
									p.processing=false;
									$("#dData", "#"+dtbl).attr("disabled",false);
									$("div.loading","#"+IDs.themodal).hide();
								}
							});
						}
					}
					return false;
				});
				$("#eData", "#"+dtbl).click(function(e){
					hideModal("#"+IDs.themodal);
					return false;
				});
				if(onBeforeShow) { p.beforeShowForm($("#"+dtbl)); }
				viewModal("#"+IDs.themodal,{modal:p.modal});
				if(onAfterShow) { p.afterShowForm($("#"+dtbl)); }
			}
		});
	},
	navGrid : function (elem, o, pEdit,pAdd,pDel,pSearch, pView) {
		o = $.extend({
			edit: true,
			editicon: "row_edit.gif",

			add: true,
			addicon:"row_add.gif",

			del: true,
			delicon:"row_delete.gif",

			search: true,
			searchicon:"find.gif",

			refresh: true,
			refreshicon:"refresh.gif",
			refreshstate: 'firstpage',

			position : "left",
			closeicon: "ico-close.gif",
			closeOnEscape : true,
			view: true,
			viewicon : "row_view.gif"
		}, $.jgrid.nav, o ||{});
		return this.each(function() {       
			var alertIDs = {themodal:'alertmod',modalhead:'alerthd',modalcontent:'alertcnt'};
			var $t = this;
			if(!$t.grid) { return; }
			if ($("#"+alertIDs.themodal).html() == null) {
				var vwidth;
				var vheight;
				if (typeof window.innerWidth != 'undefined') {
					vwidth = window.innerWidth,
					vheight = window.innerHeight
				} else if (typeof document.documentElement != 'undefined' && typeof document.documentElement.clientWidth != 'undefined' && document.documentElement.clientWidth != 0) {
					vwidth = document.documentElement.clientWidth,
					vheight = document.documentElement.clientHeight
				} else {
					vwidth=1024;
					vheight=768;
				}
				createModal(alertIDs,"<div>"+o.alerttext+"</div><span tabindex='0'><span tabindex='-1' id='jqg_alrt'><span></span>",{imgpath:$t.p.imgpath,closeicon:o.closeicon,caption:o.alertcap,top:vheight/2-25,left:vwidth/2-100,width:200,height:50,closeOnEscape:o.closeOnEscape},$t.grid.hDiv,$t.grid.hDiv,true);
				DnRModal("#"+alertIDs.themodal,"#"+alertIDs.modalhead);
			}
			var navTbl = $("<table cellspacing='0' cellpadding='0' border='0' class='navtable'><tbody></tbody></table>").height(20);
			var trd = document.createElement("tr");
			$(trd).addClass("nav-row");
			var imp = $t.p.imgpath;
			var tbd;
			if (o.add) {
				tbd = document.createElement("td");
				$(tbd).append("&nbsp;").css({border:"none",padding:"0px"});
				trd.appendChild(tbd);
				tbd = document.createElement("td");
				tbd.title = o.addtitle || "";
				$(tbd).append("<table cellspacing='0' cellpadding='0' border='0' class='tbutton'><tr><td><img src='"+imp+o.addicon+"'/></td><td>"+o.addtext+"&nbsp;</td></tr></table>")
				.css("cursor","pointer")
				.addClass("nav-button")
				.click(function(){
					if (typeof o.addfunc == 'function') {
						o.addfunc();
					} else {
						$($t).editGridRow("new",pAdd || {});
					}
					return false;
				})
				.hover(
					function () {
						$(this).addClass("nav-hover");
					},
					function () {
						$(this).removeClass("nav-hover");
					}
				);
				trd.appendChild(tbd);
				tbd = null;
			}
			if (o.edit) {
				tbd = document.createElement("td");
				$(tbd).append("&nbsp;").css({border:"none",padding:"0px"});
				trd.appendChild(tbd);
				tbd = document.createElement("td");
				tbd.title = o.edittitle || "";
				$(tbd).append("<table cellspacing='0' cellpadding='0' border='0' class='tbutton'><tr><td><img src='"+imp+o.editicon+"'/></td><td valign='center'>"+o.edittext+"&nbsp;</td></tr></table>")
				.css("cursor","pointer")
				.addClass("nav-button")
				.click(function(){
					var sr = $($t).getGridParam('selrow');
					if (sr) {
						if(typeof o.editfunc == 'function') {
							o.editfunc(sr);
						} else {
							$($t).editGridRow(sr,pEdit || {});
						}
					} else {
						viewModal("#"+alertIDs.themodal,{toTop:false});
						$("#jqg_alrt").focus();
					}
					return false;
				})
				.hover( function () {
					$(this).addClass("nav-hover");
					},
					function () {
						$(this).removeClass("nav-hover");
					}
				);
				trd.appendChild(tbd);
				tbd = null;
			}
			if (o.view) {
				tbd = document.createElement("td");
				$(tbd).append("&nbsp;").css({border:"none",padding:"0px"});
				trd.appendChild(tbd);
				tbd = document.createElement("td");
				tbd.title = o.viewtitle || "";
				$(tbd).append("<table cellspacing='0' cellpadding='0' border='0' class='tbutton'><tr><td><img src='"+imp+o.viewicon+"'/></td><td valign='center'>"+o.viewtext+"&nbsp;</td></tr></table>")
				.css("cursor","pointer")
				.addClass("nav-button")
				.click(function(){
					var sr = $($t).getGridParam('selrow');
					if (sr) {
						$($t).viewGridRow(sr,pView || {});
					} else {
						viewModal("#"+alertIDs.themodal,{toTop:false});
						$("#jqg_alrt").focus();
					}
					return false;
				})
				.hover( function () {
					$(this).addClass("nav-hover");
					},
					function () {
						$(this).removeClass("nav-hover");
					}
				);
				trd.appendChild(tbd);
				tbd = null;
			}
			if (o.del) {
				tbd = document.createElement("td");
				$(tbd).append("&nbsp;").css({border:"none",padding:"0px"});
				trd.appendChild(tbd);
				tbd = document.createElement("td");
				tbd.title = o.deltitle || "";
				$(tbd).append("<table cellspacing='0' cellpadding='0' border='0' class='tbutton'><tr><td><img src='"+imp+o.delicon+"'/></td><td>"+o.deltext+"&nbsp;</td></tr></table>")
				.css("cursor","pointer")
				.addClass("nav-button")
				.click(function(){
					var dr;
					if($t.p.multiselect) {
						dr = $($t).getGridParam('selarrrow');
						if(dr.length==0) { dr = null; }
					} else {
						dr = $($t).getGridParam('selrow');
					}
					if (dr) { $($t).delGridRow(dr,pDel || {}); }
					else  {	viewModal("#"+alertIDs.themodal,{toTop:false}); $("#jqg_alrt").focus(); }
					return false;
				})
				.hover(
					function () {
						$(this).addClass("nav-hover");
					},
					function () {
						$(this).removeClass("nav-hover");
					}
				);
				trd.appendChild(tbd);
				tbd = null;
			}
			if (o.search) {
				tbd = document.createElement("td");
				$(tbd).append("&nbsp;").css({border:"none",padding:"0px"});
				trd.appendChild(tbd);
				tbd = document.createElement("td");
				if( $(elem)[0] == $t.p.pager[0] ) { pSearch = $.extend(pSearch,{dirty:true}); }
				tbd.title = o.searchtitle || "";
				$(tbd).append("<table cellspacing='0' cellpadding='0' border='0' class='tbutton'><tr><td class='no-dirty-cell'><img src='"+imp+o.searchicon+"'/></td><td>"+o.searchtext+"&nbsp;</td></tr></table>")
				.css({cursor:"pointer"})
				.addClass("nav-button")
				.click(function(){
					$($t).searchGrid(pSearch || {});
					return false;
				})
				.hover(
					function () {
						$(this).addClass("nav-hover");
					},
					function () {
						$(this).removeClass("nav-hover");
					}
				);
				trd.appendChild(tbd);
				tbd = null;
			}
			if (o.refresh) {
				tbd = document.createElement("td");
				$(tbd).append("&nbsp;").css({border:"none",padding:"0px"});
				trd.appendChild(tbd);
				tbd = document.createElement("td");
				tbd.title = o.refreshtitle || "";
				var dirtycell =  ($(elem)[0] == $t.p.pager[0] ) ? true : false;
				$(tbd).append("<table cellspacing='0' cellpadding='0' border='0' class='tbutton'><tr><td><img src='"+imp+o.refreshicon+"'/></td><td>"+o.refreshtext+"&nbsp;</td></tr></table>")
				.css("cursor","pointer")
				.addClass("nav-button")
				.click(function(){
					$t.p.search = false;
					switch (o.refreshstate) {
						case 'firstpage':
							$t.p.page=1;
							$($t).trigger("reloadGrid");
							break;
						case 'current':
							var sr = $t.p.multiselect===true ? selarrrow : $t.p.selrow;
							$($t).setGridParam({gridComplete: function() {
								if($t.p.multiselect===true) {
									if(sr.length>0) {
										for(var i=0;i<sr.length;i++){
											$($t).setSelection(sr[i]);
										}
									}
								} else {
									if(sr) {
										$($t).setSelection(sr);
									}
								}
							}});
							$($t).trigger("reloadGrid");
							break;
					}
					if (dirtycell) { $(".no-dirty-cell",$t.p.pager).removeClass("dirty-cell"); }
					if(o.search) {
						var gID = $("table:first",$t.grid.bDiv).attr("id");
						$("#sval",'#srchcnt'+gID).val("");
					}
					return false;
				})
				.hover(
					function () {
						$(this).addClass("nav-hover");
					},
					function () {
						$(this).removeClass("nav-hover");
					}
				);
				trd.appendChild(tbd);
				tbd = null;
			}
			if(o.position=="left") {
				$(navTbl).append(trd).addClass("nav-table-left");
			} else {
				$(navTbl).append(trd).addClass("nav-table-right");
			}
			$(elem).prepend(navTbl);
		});
	},
	navButtonAdd : function (elem, p) {
		p = $.extend({
			caption : "newButton",
			title: '',
			buttonimg : '',
			onClickButton: null,
			position : "last"
		}, p ||{});
		return this.each(function() {
			if( !this.grid)  { return; }
			if( elem.indexOf("#") != 0) { elem = "#"+elem; }
			var findnav = $(".navtable",elem)[0];
			if (findnav) {
				var tdb, tbd1;
				var tbd1 = document.createElement("td");
				$(tbd1).append("&nbsp;").css({border:"none",padding:"0px"});
				var trd = $("tr:eq(0)",findnav)[0];
				if( p.position !='first' ) {
					trd.appendChild(tbd1);
				}
				tbd = document.createElement("td");
				tbd.title = p.title;
				var im = (p.buttonimg) ? "<img src='"+p.buttonimg+"'/>" : "&nbsp;";
				$(tbd).append("<table cellspacing='0' cellpadding='0' border='0' class='tbutton'><tr><td>"+im+"</td><td>"+p.caption+"&nbsp;</td></tr></table>")
				.css("cursor","pointer")
				.addClass("nav-button")
				.click(function(e){
					if (typeof p.onClickButton == 'function') { p.onClickButton(); }
					e.stopPropagation();
					return false;
				})
				.hover(
					function () {
						$(this).addClass("nav-hover");
					},
					function () {
						$(this).removeClass("nav-hover");
					}
				);
				if(p.position != 'first') {
					trd.appendChild(tbd);
				} else {
					$(trd).prepend(tbd);
					$(trd).prepend(tbd1);
				}
				tbd=null;tbd1=null;
			}
		});
	},
	GridToForm : function( rowid, formid ) {
		return this.each(function(){
			var $t = this;
			if (!$t.grid) { return; } 
			var rowdata = $($t).getRowData(rowid);
			if (rowdata) {
				for(var i in rowdata) {
					if ( $("[name="+i+"]",formid).is("input:radio") )  {
						$("[name="+i+"]",formid).each( function() {
							if( $(this).val() == rowdata[i] ) {
								$(this).attr("checked","checked");
							} else {
								$(this).attr("checked","");
							}
						});
					} else {
					// this is very slow on big table and form.
						$("[name="+i+"]",formid).val(rowdata[i]);
					}
				}
			}
		});
	},
	FormToGrid : function(rowid, formid){
		return this.each(function() {
			var $t = this;
			if(!$t.grid) { return; }
			var fields = $(formid).serializeArray();
			var griddata = {};
			$.each(fields, function(i, field){
				griddata[field.name] = field.value;
			});
			$($t).setRowData(rowid,griddata);
		});
	}
});
})(jQuery);
