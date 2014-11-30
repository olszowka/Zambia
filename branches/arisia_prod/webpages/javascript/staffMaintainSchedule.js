var staffMaintainSchedule = new StaffMaintainSchedule;

function StaffMaintainSchedule() {
	var sessionMasterArray = [];
	var dropped = false;
	var dropTarget = "";
	var confirmationMsg = "";
	var schedScrollTop = "";
	var elemToAdd = "";
	var helperElem = "";
	var dragParent = "";

	this.anyChange = function anyChange(element) {
	}
	
	this.changeRoomDisplay = function changeRoomDisplay() {
		var roomsToDisplayArray = [];
		$("[id^='roomidCHK']:checked").each(function() {
			roomsToDisplayArray.push($(this).attr("id").substring(10,999));
			});
		var postdata = {
			ajax_request_action : "retrieveRoomsTable",
			roomsToDisplayArray : roomsToDisplayArray
			};
		$.ajax({
			url: "staffMaintainScheduleSubmit.php",
			dataType: "html",
			data: postdata,
			error: staffMaintainSchedule.retrieveRoomsTableError,
			success: staffMaintainSchedule.retrieveRoomsTableCallback,
			type: "POST"
			});
	}
	
	this.clearAllClick = function clearAllClick() {
		$("#sessionsToBeScheduled").html("&nbsp;");
		sessionMasterArray = [];
		$("#noSessionsFoundMSG").hide();
	}

	this.dragStart = function(event, ui) {
		dragParent = $(this).parent();
		dragParent.droppable("option","disabled",true);
		dropped = false;
		dropTarget = "";
		$(this).css("visibility", "hidden");
		ui.helper.css("opacity","0.75");
	}
	
	this.dropOnCompoundEmptySlot = function dropOnCompoundEmptySlot(pEvent, pUi, pThis) {
		// always redraw table if dropping in compound area
		var newSessionDiv = $(pThis).clone();
		$(pThis).remove();
		if (newSessionDiv.hasClass("scheduledSessionBlock")) {
				// was in schedule -- do reschedule		
				staffMaintainSchedule.editScheduleAjax({
					returnTable: true,
					editsArray: new Array({
							action: "reschedule", 
							sessionid: newSessionDiv.attr("sessionid"),
							roomid: dropTarget.attr("roomid"),
							oldroomid: newSessionDiv.attr("roomid"),
							starttimeunits: dropTarget.attr("starttimeunits"),
							oldstarttime: newSessionDiv.attr("starttime"),
							oldendtime: newSessionDiv.attr("endtime"),
							duration: newSessionDiv.attr("duration"),
							title: newSessionDiv.find(".sessionBlockTitle").text(),
							scheduleid: newSessionDiv.attr("scheduleid")
							})
						});
				}
			else {
				staffMaintainSchedule.removeFromSessionArray(newSessionDiv.attr("sessionid"));
				staffMaintainSchedule.editScheduleAjax({
					returnTable: true,
					editsArray: new Array({
							action: "insert", 
							sessionid: newSessionDiv.attr("sessionid"),
							roomid: dropTarget.attr("roomid"),
							starttimeunits: dropTarget.attr("starttimeunits"),
							duration: newSessionDiv.attr("duration"),
							title: newSessionDiv.find(".sessionBlockTitle").text()
							})
						});
				}
	}

	this.dropOnEmptySlot = function dropOnEmptySlot(pEvent, pUi, pThis) {
		$("#myhelper").hide();
		var newSessionDiv = $(pThis).clone();
		$(pThis).remove();
		if (newSessionDiv.attr("durationUnits") !=3 || parseInt(dropTarget.attr("endtimeunits"),10) - parseInt(dropTarget.attr("starttimeunits"),10) != 3) {
				// will refresh the whole grid, so only need to gather enough info to describe schedule change and send to server
				if (newSessionDiv.hasClass("scheduledSessionBlock")) {
						// was in schedule -- do reschedule		
						staffMaintainSchedule.editScheduleAjax({
							returnTable: true,
							editsArray: new Array({
									action: "reschedule", 
									sessionid: newSessionDiv.attr("sessionid"),
									roomid: dropTarget.attr("roomid"),
									oldroomid: newSessionDiv.attr("roomid"),
									starttimeunits: dropTarget.attr("starttimeunits"),
									oldstarttime: newSessionDiv.attr("starttime"),
									oldendtime: newSessionDiv.attr("endtime"),
									duration: newSessionDiv.attr("duration"),
									title: newSessionDiv.find(".sessionBlockTitle").text(),
									scheduleid: newSessionDiv.attr("scheduleid")
									})
								});
						}
					else {
						// wasn't already in schedule -- do insert
						staffMaintainSchedule.removeFromSessionArray(newSessionDiv.attr("sessionid"));
						staffMaintainSchedule.editScheduleAjax({
							returnTable: true,
							editsArray: new Array({
									action: "insert", 
									sessionid: newSessionDiv.attr("sessionid"),
									roomid: dropTarget.attr("roomid"),
									starttimeunits: dropTarget.attr("starttimeunits"),
									duration: newSessionDiv.attr("duration"),
									title: newSessionDiv.find(".sessionBlockTitle").text()
									})
								});
						}
				}
			else {
				// not refreshing the schedule grid, so need to deal with all issues here
				// first do as many things as possible which are common to both insert and reschedule
				var oldRoomId = newSessionDiv.attr("roomid");
				var oldStartTime = newSessionDiv.attr("starttime");
				var oldEndTime = newSessionDiv.attr("endtime");
				var oldstarttimeunits = newSessionDiv.attr("starttimeunits");
				var oldendtimeunits = newSessionDiv.attr("endtimeunits");
				newSessionDiv.attr("roomid",dropTarget.attr("roomid"));
				newSessionDiv.attr("starttimeunits",dropTarget.attr("starttimeunits"));
				newSessionDiv.attr("starttime",staffMaintainSchedule.timeAttrFromUnits(dropTarget.attr("starttimeunits")));
				newSessionDiv.attr("endtimeunits",(parseInt(dropTarget.attr("starttimeunits"),10) + parseInt(newSessionDiv.attr("durationunits"),10)));
				newSessionDiv.attr("endtime",staffMaintainSchedule.endTimeAttrFromSTUnD(dropTarget.attr("starttimeunits"),newSessionDiv.attr("duration")));
				newSessionDiv.css("visibility", "visible");
				newSessionDiv.find(".getSessionInfo").on("click", staffMaintainSchedule.onClickInfo);
				newSessionDiv.draggable({
					addClasses: false,
					revert: 'invalid',
					containment: "#mainContentContainer",
					helper: 'clone',
					appendTo: "#mainContentContainer",
					start: staffMaintainSchedule.dragStart,
					stop: staffMaintainSchedule.dropSession,
					scroll: true
					});
				dropTarget.html("");
				dropTarget.prepend(newSessionDiv);
				dropTarget.addClass("schedulerGridContainer");
				dropTarget.removeClass("scheduleGridEmptyDIV");
				dropTarget.removeAttr("style");
				dropTarget.height(52);
				dropTarget.droppable({
					drop: function (event, ui) {
						dropped=true;
						dropTarget = $(this);
						},
					over: function() {
						var helperSel = $("#myhelper");
						var targetTDSel = $(this).parent();
						helperSel.css("height",targetTDSel.height() - 4);
						helperSel.css("width",targetTDSel.width() - 4);
						helperSel.css("top",parseInt(targetTDSel.offset().top,10) + 1);
						helperSel.css("left",parseInt(targetTDSel.offset().left,10) + 1);
						helperSel.show();
						},
					out: function() {
						$("#myhelper").hide();
						},
					tolerance: 'intersect'
					});
				//dropTarget.css("border","");
				if (newSessionDiv.hasClass("scheduledSessionBlock")) {
						// was in schedule -- do reschedule	and clean up source slot
						if (dragParent.hasClass("schedulerGridContainer")) {
							dragParent.removeAttr("style");
							dragParent.height(49);
							dragParent.removeClass("schedulerGridContainer").addClass("scheduleGridEmptyDIV");
							dragParent.attr("roomid", oldRoomId).attr("starttimeunits", oldstarttimeunits).attr("endtimeunits", oldendtimeunits);
							dragParent.droppable({
								drop: function (event, ui) {
									dropped=true;
									dropTarget = $(this);
									},
								over: function () {
									$(this).css("border-color","green");
									},
								out: function () {
									$(this).css("border-color","white");
									},
								tolerance: 'intersect'
								});
							dragParent.droppable("option","disabled",false);
							dragParent.removeAttr("aria-disabled");
							dragParent = "";
							}
						staffMaintainSchedule.editScheduleAjax({
							returnTable: false,
							editsArray: new Array({
								action: "reschedule", 
								sessionid: newSessionDiv.attr("sessionid"),
								roomid: newSessionDiv.attr("roomid"),
								oldroomid: oldRoomId,
								starttimeunits: newSessionDiv.attr("starttimeunits"),
								oldstarttime: oldStartTime,
								oldendtime: oldEndTime,
								duration: newSessionDiv.attr("duration"),
								title: newSessionDiv.find(".sessionBlockTitle").text(),
								scheduleid: newSessionDiv.attr("scheduleid")
								})
							});						}
					else {
						// wasn't already in schedule -- do insert and remove from unscheduled list
						staffMaintainSchedule.removeFromSessionArray(newSessionDiv.attr("sessionid"));
						newSessionDiv.addClass("scheduledSessionBlock");
						newSessionDiv.removeClass("sessionBlock");
						staffMaintainSchedule.editScheduleAjax({
							returnTable: false,
							editsArray: new Array({
									action: "insert", 
									sessionid: newSessionDiv.attr("sessionid"),
									roomid: dropTarget.attr("roomid"),
									starttimeunits: dropTarget.attr("starttimeunits"),
									duration: newSessionDiv.attr("duration"),
									title: newSessionDiv.find(".sessionBlockTitle").text()
									})
								});
						}	
				dropTarget.removeAttr("roomid").removeAttr("starttimeunits").removeAttr("endtimeunits");
				}
	}

	this.dropOnFileCab = function dropOnFileCab(pEvent, pUi, pThis) {
		if ($(pThis).hasClass("scheduledSessionBlock")) {
				// item was in schedule
				var returnTable = true;
				// if item was anything other than 3 units long in simple box, redraw the schedule
				var starttimeunits = $(pThis).attr("starttimeunits");
				var endtimeunits = $(pThis).attr("endtimeunits");
				if (dragParent.hasClass("schedulerGridContainer") && (endtimeunits - starttimeunits == 3))
					returnTable = false;
				var editsArray = [];
				editsArray[0] = {
					action: "delete",
					sessionid: $(pThis).attr("sessionid"),
					scheduleid: $(pThis).attr("scheduleid"),
					roomid: $(pThis).attr("roomid"),
					starttimeunits: starttimeunits,
					endtimeunits: endtimeunits,
					starttime: $(pThis).attr("starttime"),
					endtime: $(pThis).attr("endtime"),
					title: $(pThis).find(".sessionBlockTitle").text()
					};
				if (!returnTable) {
					// need to clean up everything myself
					//var thisParent = $(pThis).parent();
					dragParent.removeClass("schedulerGridContainer");
					dragParent.addClass("scheduleGridEmptyDIV");
					dragParent.attr("roomid",$(pThis).attr("roomid"));
					dragParent.attr("starttimeunits",starttimeunits);
					dragParent.attr("endtimeunits",endtimeunits);
					dragParent.css("height","44px");
					dragParent.droppable({
						drop: function (event, ui) {
							dropped=true;
							dropTarget = $(this);
							},
						over: function () {
							$(this).css("border-color","green");
							},
						out: function () {
							$(this).css("border-color","white");
							},
						tolerance: 'intersect'
						});
					dragParent.droppable("option","disabled",false);
					$(pThis).remove();
					}
				staffMaintainSchedule.editScheduleAjax({
					returnTable: returnTable,
					editsArray: editsArray
					});
				}
			else {
				// item was not in schedule/
				staffMaintainSchedule.removeFromSessionArray($(pThis).attr("id").substring(16,999))
				$(pThis).remove();
				}	
	}

	this.dropOnScheduledSlot = function dropOnScheduledSlot(pEvent, pUi, pThis) {
		// need to determine if source was already scheduled
		// need to determine status of Swap Mode button
		$("#myhelper").hide();
		var tarSessSEL = dropTarget.find(".scheduledSessionBlock");
		var tarSessDurUnits = parseInt(tarSessSEL.attr("endTimeUnits"),10) - parseInt(tarSessSEL.attr("startTimeUnits"),10);
		var editsArray = [];
		if ($(pThis).attr("scheduleid")) {
				//dropped item was previously scheduled		
				if ($("#swapModeCheck").attr("mychecked") == "true") {
						//swap mode (prev sched); 1 of 4; not done
						var dropDurationUnits = parseInt($(pThis).attr("endtimeunits"),10) - parseInt($(pThis).attr("starttimeunits"),10);
						var targetDurationUnits = parseInt(dropTarget.attr("endtimeunits"),10) - parseInt(dropTarget.attr("starttimeunits"),10);
						editsArray.push({
							action: "reschedule", 
							sessionid: $(pThis).attr("sessionid"),
							roomid: tarSessSEL.attr("roomid"),
							oldroomid: $(pThis).attr("roomid"),
							starttimeunits: tarSessSEL.attr("starttimeunits"),
							oldstarttime: $(pThis).attr("starttime"),
							oldendtime: $(pThis).attr("endtime"),
							duration: $(pThis).attr("duration"),
							title: $(pThis).find(".sessionBlockTitle").text(),
							scheduleid: $(pThis).attr("scheduleid")
							});
						editsArray.push({
							action: "reschedule", 
							sessionid: tarSessSEL.attr("sessionid"),
							roomid: $(pThis).attr("roomid"),
							oldroomid: tarSessSEL.attr("roomid"),
							starttimeunits: $(pThis).attr("starttimeunits"),
							oldstarttime: tarSessSEL.attr("starttime"),
							oldendtime: tarSessSEL.attr("endtime"),
							duration: tarSessSEL.attr("duration"),
							title: tarSessSEL.find(".sessionBlockTitle").text(),
							scheduleid: tarSessSEL.attr("scheduleid")
							});
						// use clones to swap items in the DOM
						// then still have all old data available
						var orig = tarSessSEL.offset();
						var origTop = parseInt(orig.top, 10);
						var origLeft = parseInt(orig.left, 10);
						var dest = $(pThis).offset();
						var destTop = parseInt(dest.top, 10);
						var destLeft = parseInt(dest.left, 10);
						var dropItemClone = $(pThis).clone();
						var targetItemClone = tarSessSEL.clone();
						var helper = tarSessSEL.clone();
						$(pThis).hide();
						tarSessSEL.hide();
						dropTarget.prepend(dropItemClone);
						dropItemClone.css("visibility","visible");
						targetItemClone.css("visibility","hidden");
						elemToAdd = targetItemClone;
						$(pThis).parent().prepend(targetItemClone);
						helper.removeClass("scheduledSessionBlock");
						helper.addClass("animHelper");
						helper.css("top",origTop);
						helper.css("left",origLeft);
						$("#fullPageContainer").prepend(helper);
						helper.animate({
    							top: destTop,
    							left: destLeft
  								},
							650,
							function() {
								$(this).remove();
								elemToAdd.css("visibility","visible");
								elemToAdd.draggable({
									addClasses: false,
									revert: 'invalid',
									containment: "#mainContentContainer",
									helper: 'clone',
									appendTo: "#mainContentContainer",
									start: staffMaintainSchedule.dragStart,
									stop: staffMaintainSchedule.dropSession,
									scroll: false
									});
								elemToAdd = "";
    							});
						if ($(pThis).parent().hasClass("schedulerGridContainer") && dropDurationUnits == targetDurationUnits) {
								// can do simple swap
								targetItemClone.attr("roomid",$(pThis).attr("roomid"));
								dropItemClone.attr("roomid",tarSessSEL.attr("roomid"));
								targetItemClone.attr("starttimeunits",$(pThis).attr("starttimeunits"));
								dropItemClone.attr("starttimeunits",tarSessSEL.attr("starttimeunits"));
								targetItemClone.attr("starttime",staffMaintainSchedule.timeAttrFromUnits($(pThis).attr("starttimeunits")));
								dropItemClone.attr("starttime",staffMaintainSchedule.timeAttrFromUnits(tarSessSEL.attr("starttimeunits")));
								targetItemClone.attr("endtimeunits",parseInt($(pThis).attr("starttimeunits"),10) + targetDurationUnits);
								dropItemClone.attr("endtimeunits",parseInt(tarSessSEL.attr("starttimeunits"),10) + dropDurationUnits);
								targetItemClone.attr("endtime",staffMaintainSchedule.endTimeAttrFromSTUnD(tarSessSEL.attr("starttimeunits"),$(pThis).attr("duration")));
								dropItemClone.attr("endtime",staffMaintainSchedule.endTimeAttrFromSTUnD($(pThis).attr("starttimeunits"),tarSessSEL.attr("duration")));
								$(pThis).parent().droppable("option","disabled",false);
								$(pThis).remove();
								tarSessSEL.remove();
								staffMaintainSchedule.editScheduleAjax({
									returnTable: false,
									editsArray: editsArray
									});
								}
							else {
								// must refresh table
								$(pThis).remove();
								tarSessSEL.remove();
								staffMaintainSchedule.editScheduleAjax({
									returnTable: true,
									editsArray: editsArray
									});
								}
						}
					else {
						//not swap mode (prev sched); 2 of 4; done
						staffMaintainSchedule.editScheduleAjax({
							returnTable: true,
							editsArray: new Array({
								action: "reschedule", 
								sessionid: $(pThis).attr("sessionid"),
								roomid: tarSessSEL.attr("roomid"),
								oldroomid: $(pThis).attr("roomid"),
								starttimeunits: tarSessSEL.attr("starttimeunits"),
								oldstarttime: $(pThis).attr("starttime"),
								oldendtime: $(pThis).attr("endtime"),
								duration: $(pThis).attr("duration"),
								title: $(pThis).find(".sessionBlockTitle").text(),
								scheduleid: $(pThis).attr("scheduleid")
								})
							});
						}
				}
			else {
				//dropped item was not previously scheduled
				if ($("#swapModeCheck").attr("mychecked") == "true") {
						//swap mode (not prev sched); 3 of 4; done
						editsArray[0] = {
							action: "delete",
							sessionid: tarSessSEL.attr("sessionid"),
							scheduleid: tarSessSEL.attr("scheduleid"),
							roomid: tarSessSEL.attr("roomid"),
							starttimeunits: tarSessSEL.attr("starttimeunits"),
							endtimeunits: tarSessSEL.attr("endtimeunits"),
							starttime: tarSessSEL.attr("starttime"),
							endtime: tarSessSEL.attr("endtime"),
							title: tarSessSEL.find(".sessionBlockTitle").text()
							};
						editsArray[1] = {
							action: "insert", 
							sessionid: $(pThis).attr("sessionid"),
							roomid: tarSessSEL.attr("roomid"),
							starttimeunits: tarSessSEL.attr("starttimeunits"),
							duration: $(pThis).attr("duration"),
							title: $(pThis).find(".sessionBlockTitle").text()
							};
						// need to animate moving previously scheduled Session to Session Pool 
						// and then actually do it
						var helper = tarSessSEL.clone();
						helper.removeClass("scheduledSessionBlock");
						helper.addClass("animHelper");
						helper.css("top",tarSessSEL.offset().top);
						helper.css("left",tarSessSEL.offset().left);
						var dest = $("#sessionsToBeScheduled").offset();
						var destTop = parseInt(dest.top, 10) + 2;
						var destLeft = parseInt(dest.left, 10) + 1;
						$("#fullPageContainer").prepend(helper);

						var unschDup = 	tarSessSEL.clone();
						unschDup.attr("durationunits",parseInt(unschDup.attr("endtimeunits"),10) - parseInt(unschDup.attr("starttimeunits"),10));
						unschDup.attr("scheduleid","");
						unschDup.attr("roomid","");
						unschDup.attr("starttimeunits","");
						unschDup.attr("starttime","");
						unschDup.attr("endtimeunits","");
						unschDup.attr("endtime","");
						unschDup.removeClass("scheduledSessionBlock");
						unschDup.addClass("sessionBlock");
						unschDup.css("visibility","hidden");
						unschDup.find(".getSessionInfo").on("click", staffMaintainSchedule.onClickInfo);
						$("#sessionsToBeScheduled").prepend(unschDup);
						elemToAdd = unschDup; // will unhide and make draggable when animation ends
						sessionMasterArray.push(tarSessSEL.attr("sessionid"));
						$(pThis).addClass("scheduledSessionBlock");
						$(pThis).removeClass("sessionBlock");
						// add the dropped session to the target and make it visible
						tarSessSEL.parent().prepend($(pThis));
						$(pThis).css("visibility","visible");
						// set properties on the dropped item calculated from the session dropped on before it is removed
						$(pThis).attr("roomid",tarSessSEL.attr("roomid"));
						$(pThis).attr("starttimeunits",tarSessSEL.attr("starttimeunits"));
						$(pThis).attr("starttime",staffMaintainSchedule.timeAttrFromUnits(tarSessSEL.attr("starttimeunits")));
						$(pThis).attr("endtimeunits",(parseInt(tarSessSEL.attr("starttimeunits"),10) + parseInt($(pThis).attr("durationunits"),10)));
						$(pThis).attr("endtime",staffMaintainSchedule.endTimeAttrFromSTUnD(tarSessSEL.attr("starttimeunits"),$(pThis).attr("duration")));
						// remove the session dropped on from the DOM
						tarSessSEL.remove();
						helper.animate({
    							top: destTop,
    							left: destLeft
  								},
							750 /* ms */,
							function() {
								$(this).remove();
								elemToAdd.css("visibility","visible");
								elemToAdd.draggable({
									addClasses: false,
									revert: 'invalid',
									containment: "#mainContentContainer",
									helper: 'clone',
									appendTo: "#mainContentContainer",
									start: staffMaintainSchedule.dragStart,
									stop: staffMaintainSchedule.dropSession,
									scroll: false
									});
								elemToAdd = "";
    							});
						staffMaintainSchedule.removeFromSessionArray($(pThis).attr("sessionid"));
						if ($(pThis).attr("durationUnits") == tarSessDurUnits) {
								//don't reload table; do all page manipulation here
								$(pThis).draggable({
									addClasses: false,
									revert: 'invalid',
									containment: "#mainContentContainer",
									helper: 'clone',
									appendTo: "#mainContentContainer",
									start: staffMaintainSchedule.dragStart,
									stop: staffMaintainSchedule.dropSession,
									scroll: false
									});
								staffMaintainSchedule.editScheduleAjax({
									returnTable: false,
									editsArray: editsArray
									});
								}
							else {
								//reload table; just build editArray
								staffMaintainSchedule.editScheduleAjax({
									returnTable: true,
									editsArray: editsArray
									});
								}
						}
					else {
						//not swap mode (not prev sched); 4 of 4; done
						editsArray[0] = {
							action: "insert", 
							sessionid: $(pThis).attr("sessionid"),
							roomid: tarSessSEL.attr("roomid"),
							starttimeunits: tarSessSEL.attr("starttimeunits"),
							duration: $(pThis).attr("duration"),
							title: $(pThis).find(".sessionBlockTitle").text()
							};
						staffMaintainSchedule.removeFromSessionArray($(pThis).attr("sessionid"));
						$(pThis).remove();
						// rebuild schedule grid because dropping on a scheduled session just created a compound section
						staffMaintainSchedule.editScheduleAjax({
							returnTable: true,
							editsArray: editsArray
							});
						}
				}
	}

	this.dropOnUnschedSessions = function dropOnUnschedSessions(pEvent, pUi, pThis) {
		/// item was in schedule
		var returnTable = true;
		// if item was anything other than 3 units long in simple box, redraw the schedule
		var starttimeunits = $(pThis).attr("starttimeunits");
		var endtimeunits = $(pThis).attr("endtimeunits");
		if ($(pThis).parent().hasClass("schedulerGridContainer") && (endtimeunits - starttimeunits == 3))
			returnTable = false;
		var editsArray = [];
		editsArray[0] = {
			action: "delete",
			sessionid: $(pThis).attr("sessionid"),
			scheduleid: $(pThis).attr("scheduleid"),
			roomid: $(pThis).attr("roomid"),
			starttimeunits: starttimeunits,
			endtimeunits: endtimeunits,
			starttime: $(pThis).attr("starttime"),
			endtime: $(pThis).attr("endtime"),
			title: $(pThis).find(".sessionBlockTitle").text()
			};
		if (!returnTable) {
			// need to clean up everything myself
			var thisParent = $(pThis).parent();
			thisParent.removeClass("schedulerGridContainer");
			thisParent.addClass("scheduleGridEmptyDIV");
			thisParent.attr("roomid",$(pThis).attr("roomid"));
			thisParent.attr("starttimeunits",starttimeunits);
			thisParent.attr("endtimeunits",endtimeunits);
			thisParent.css("height","44px");
			thisParent.droppable("option","disabled",false);
			//thisParent.droppable({
			//	drop: function (event, ui) {
			//		dropped=true;
			//		dropTarget = $(this);
			//		},
			//	over: function () {
			//		$(this).css("border-color","green");
			//		},
			//	out: function () {
			//		$(this).css("border-color","white");
			//		},
			//	tolerance: 'intersect'
			//	});
			}
		$("#sessionsToBeScheduled").prepend($(pThis));
		$(pThis).removeClass("scheduledSessionBlock");
		$(pThis).addClass("sessionBlock");
		$(pThis).attr("scheduleid","");
		$(pThis).attr("durationunits",endtimeunits - starttimeunits);
		$(pThis).attr("duration",staffMaintainSchedule.durationFromSTAET($(pThis).attr("starttime"),$(pThis).attr("endtime")));
		$(pThis).attr("starttimeunits","");
		$(pThis).attr("starttime","");
		$(pThis).attr("endtimeunits","");
		$(pThis).attr("endtime","");
		$(pThis).css("visibility","visible");
		$(pThis).draggable({
			addClasses: false,
			revert: 'invalid',
			containment: "#mainContentContainer",
			helper: 'clone',
			appendTo: "#mainContentContainer",
			start: staffMaintainSchedule.dragStart,
			stop: staffMaintainSchedule.dropSession,
			scroll: false
			});
		sessionMasterArray.push($(pThis).attr("sessionid"));
		staffMaintainSchedule.editScheduleAjax({
			returnTable: returnTable,
			editsArray: editsArray
			});
	}

	this.dropSession = function dropSession(event, ui) {
		confirmationMsg = "";
		if (dropped==true) {
				if (dropTarget.attr("id") == "fileCabinetIMG")
						staffMaintainSchedule.dropOnFileCab(event, ui, this);
					else if (dropTarget.attr("id") == "sessionsToBeSchedContainer")
						staffMaintainSchedule.dropOnUnschedSessions(event, ui, this);
					else if (dropTarget.hasClass("scheduleGridCompoundEmptyDIV"))
						staffMaintainSchedule.dropOnCompoundEmptySlot(event, ui, this)
					else if (dropTarget.hasClass("scheduleGridEmptyDIV"))
						staffMaintainSchedule.dropOnEmptySlot(event, ui, this);
					else if (dropTarget.hasClass("schedulerGridContainer"))
						staffMaintainSchedule.dropOnScheduledSlot(event, ui, this);
					else
						$(this).css("visibility", "visible");
				}
			else
				$(this).css("visibility", "visible");
		dropped = false;
		dropTarget = "";
		if (dragParent)
			dragParent.droppable("option", "disabled", false);
		dragParent = "";
	}

	this.durationFromSTAET = function durationFromSTAET(startTime,endTime) {
		// compute duration string in hh:mm from startTime and endTime strings in hhh:mm:ss
		var startTimeArr = startTime.split(":");
		var startTimeHours = parseInt(startTimeArr[0],10);
		var startTimeMins = parseInt(startTimeArr[1],10);
		var endTimeArr = endTime.split(":");
		var endTimeHours = parseInt(endTimeArr[0],10);
		var endTimeMins = parseInt(endTimeArr[1],10);
		var durHours, durMins;
		if (startTimeMins > endTimeMins) {
				durMins = 60 + endTimeMins - startTimeMins;
				durHours = endTimeHours - startTimeHours - 1;
				}
			else {
				durMins = endTimeMins - startTimeMins;
				durHours = endTimeHours - startTimeHours;
				}
		return durHours + ":" + ((durMins < 10) ? "0" : "") + durMins;
	}

	this.editScheduleAjax = function editScheduleAjax(paramObj) {
		var returnTable = paramObj.returnTable;
		var editsArray = paramObj.editsArray;
		var roomname;
		var thisEdit;
		var startTime;
		var endTime;
		var startTimeStr;
		var endTimeStr;
		var oldroomname;
		var oldstartTime;
		var oldendTime;
		confirmationMsg = "";
		for (thisEdit in editsArray) {
			roomname = $("#roomnameSPN_" + editsArray[thisEdit].roomid).text();
			confirmationMsg += 	"<div class=\"warnConfMsg\">";
			confirmationMsg += 		"Session ";
			confirmationMsg += 		"<span class=\"warnConfMsgTitle\">" + editsArray[thisEdit].title + "</span> ";
			confirmationMsg += 		"<span class=\"warnConfMsgSessionid\">(" + editsArray[thisEdit].sessionid + ")</span> ";
			if (editsArray[thisEdit].action == "insert") {
					startTime = staffMaintainSchedule.startTimeFromUnits(editsArray[thisEdit].starttimeunits);
					endTime = staffMaintainSchedule.endTimeFromSTnD(startTime,editsArray[thisEdit].duration);
					confirmationMsg += 		"added to room " + roomname + " from ";
					}
				else if (editsArray[thisEdit].action == "delete") {
					startTime = staffMaintainSchedule.timeFromTimeAttr(editsArray[thisEdit].starttime);
					endTime = staffMaintainSchedule.timeFromTimeAttr(editsArray[thisEdit].endtime);
					confirmationMsg += 		"removed from room " + roomname + " from ";
					}
				else if (editsArray[thisEdit].action == "reschedule") {
					oldroomname = $("#roomnameSPN_" + editsArray[thisEdit].oldroomid).text();
					oldstartTime = staffMaintainSchedule.timeStrFromTime(staffMaintainSchedule.timeFromTimeAttr(editsArray[thisEdit].oldstarttime));
					oldendTime = staffMaintainSchedule.timeStrFromTime(staffMaintainSchedule.timeFromTimeAttr(editsArray[thisEdit].oldendtime));
					startTime = staffMaintainSchedule.startTimeFromUnits(editsArray[thisEdit].starttimeunits);
					endTime = staffMaintainSchedule.endTimeFromSTnD(startTime,editsArray[thisEdit].duration);
					confirmationMsg += 		"changed from room " + oldroomname + " from " + oldstartTime + "-";
					confirmationMsg +=      oldendTime + " to room " + roomname + " from ";
					}
			startTimeStr = staffMaintainSchedule.timeStrFromTime(startTime);		
			endTimeStr = staffMaintainSchedule.timeStrFromTime(endTime);		
			confirmationMsg += 		startTimeStr + "-" + endTimeStr;
			confirmationMsg += "</div>";
			}
		if (returnTable) {
				schedScrollTop = $("#scheduleGridContainer").scrollTop();
				var roomsToDisplayArray = [];
				$("[id^='roomidCHK']:checked").each(function() {
					roomsToDisplayArray.push($(this).attr("id").substring(10,999));
					});
				var postdata = {
					ajax_request_action: "editSchedule",
					returnTable: true,
					editsArray: editsArray,
					roomsToDisplayArray: roomsToDisplayArray
					};
				$.ajax({
					url: "staffMaintainScheduleSubmit.php",
					dataType: "html",
					data: postdata,
					//error: staffMaintainSchedule.retrieveSessionsError,
					success: staffMaintainSchedule.retrieveRoomsTableCallback,
					type: "POST"
					});
				}
			else {
				var postdata = {
					ajax_request_action: "editSchedule",
					returnTable: false,
					editsArray: editsArray
					};
				$.ajax({
					url: "staffMaintainScheduleSubmit.php",
					dataType: "html",
					data: postdata,
					//error: staffMaintainSchedule.retrieveSessionsError,
					success: staffMaintainSchedule.editScheduleCallback,
					type: "POST"
					});
				}
	}

	this.editScheduleCallback = function editScheduleCallback(responseData,returnString,jqXHR) {
		$("#tabs-warnings").html(confirmationMsg + responseData);
		$("#tabs-warnings").find(".conflictEditConfirmation").remove();
		$("#tabs-warnings").find("div.insertedScheduleId").each(function() {
			$("#sessionBlockDIV_" + $(this).attr("sessionId")).attr("scheduleId",$(this).attr("scheduleId"));
			$(this).remove();
			});
		$("#tabs-warnings-link").click();
		confirmationMsg = "";
	}
	
	this.endTimeAttrFromSTUnD = function endTimeAttrFromSTUnD(startTimeUnits,duration) {
		var durArr = duration.split(":");
		var minutes = (parseInt(startTimeUnits,10) % 2) * 30 + parseInt(durArr[1],10);
		var hours = Math.floor(parseInt(startTimeUnits,10) / 2) + Math.floor(minutes / 60) + parseInt(durArr[0],10);
		minutes = minutes % 60;
		return hours + ":" + ((minutes < 10) ? "0" : "") + minutes + ":00";
	}

	this.endTimeFromSTnD = function endTimeFromSTnD(startTime, duration) {
		var durArr = duration.split(":");
		var endTime = new Date(startTime);
		endTime.setHours(endTime.getHours() + parseInt(durArr[0],10));
		endTime.setMinutes(endTime.getMinutes() + parseInt(durArr[1],10));
		return endTime;
	}
	
	this.onClickInfo = function onClickInfo() {
		var postdata = {
			ajax_request_action : "retrieveSessionInfo",
			sessionid: $(this).parent().parent().attr("sessionid")
			};
		$.ajax({
			url: "staffMaintainScheduleSubmit.php",
			dataType: "html",
			data: postdata,
			//error: staffMaintainSchedule.retrieveSessionsError,
			success: staffMaintainSchedule.retrieveSessionInfoCallback,
			type: "POST"
			});
	}
	
	this.onClickSwapMode = function onClickSwapMode() {
		if ($("#swapModeCheck").attr("mychecked") == "true") {
				$("#swapModeCheck").attr("mychecked","false");
				$("#swapModeCheck").removeClass("btn-inverse");
				}
			else {
				$("#swapModeCheck").attr("mychecked","true");
				$("#swapModeCheck").addClass("btn-inverse");
				}
		$("#swapModeCheck").blur();
	}

	this.temp = function temp() {
		var i = 1;
	}
	
	this.fileCabinetSwap = function fileCabinetSwap(open) {
		if (typeof (open) == 'object')
			if (open.type=='dropover')
					open = true;
				else if (open.type=='dropout')
					open = false;
				else
					return;
		if (open)
				$("#fileCabinetIMG").get(0).src="images/FileCabinetOpen.png";
			else
				$("#fileCabinetIMG").get(0).src="images/FileCabinetClosed.png";
	}

	this.initialize = function initialize() {
		$("#arisiaLens").ready(staffMaintainSchedule.resizeMe);
		$("#staffNav").ready(staffMaintainSchedule.resizeMe);
		$("#tabs").tabs();
		//$("#clearAllButton").button();
		$("#clearAllButton").click(staffMaintainSchedule.clearAllClick);
		//$("#retrieveSessionsBUT").button();
		$("#retrieveSessionsBUT").click(staffMaintainSchedule.retrieveSessionsClick);
		//$("#resetSessionsSearchBUT").button();
		$("#resetSessionsSearchBUT").click(staffMaintainSchedule.resetSessionSearchClick);
		//$("#swapModeCheck").button();
		$("#swapModeCheck").click(staffMaintainSchedule.onClickSwapMode);
		$("[id^='roomidCHK']").click(staffMaintainSchedule.roomCheckClick);
		$("#tabs-rooms").find(".checkboxContainer").click(lib.toggleCheckbox);
		$(window).resize(staffMaintainSchedule.resizeMe);
		$(window).resize();
		$("#fileCabinetIMG").droppable({
			greedy: true,
			drop: function (event, ui) {
				dropped = true;
				dropTarget = $(this);
				staffMaintainSchedule.fileCabinetSwap(false);
				},
			over: staffMaintainSchedule.fileCabinetSwap,
			out: staffMaintainSchedule.fileCabinetSwap,
			tolerance: 'intersect'
			});
		$("#sessionsToBeSchedContainer").droppable({
			accept: ".scheduledSessionBlock",	
			drop: function (event, ui) {
				$(this).css("border-color","white");
				dropped=true;
				dropTarget = $(this);
				},
			over: function () {
				$(this).css("border-color","green");
				},
			out: function () {
				$(this).css("border-color","white");
				},
			tolerance: 'intersect'
			});
		$("#tabs-rooms-link").click();
		// ugly hack because nav bar doesn't seem to be its final size when the first resizeMe runs.
		window.setTimeout(staffMaintainSchedule.resizeMe,250);
		window.setTimeout(staffMaintainSchedule.resizeMe,750);
	}

	this.removeFromSessionArray = function removeFromSessionArray(sessionid) {
		var foo = sessionMasterArray.indexOf(sessionid);
		if (foo != -1)
			sessionMasterArray.splice(foo,1);
	}

	this.retrieveRoomsTableCallback = function retrieveRoomsTableCallback(responseData,returnString,jqXHR) {
		insertedSessionDiv = "";
		$("#scheduleGridContainer").html(responseData);
		$("#scheduleGridContainer").find(".getSessionInfoP").on("click",function() {
			var postdata = {
				ajax_request_action : "retrieveSessionInfo",
				sessionid: $(this).parent().parent().attr("sessionid")
				};
			$.ajax({
				url: "staffMaintainScheduleSubmit.php",
				dataType: "html",
				data: postdata,
				//error: staffMaintainSchedule.retrieveSessionsError,
				success: staffMaintainSchedule.retrieveSessionInfoCallback,
				type: "POST"
				});
			});
		$("#scheduleGridContainer").find(".getSessionInfoP").addClass("getSessionInfo");
		$("#scheduleGridContainer").find(".getSessionInfoP").removeClass("getSessionInfoP");
		$("#scheduleGridContainer").find(".scheduleGridEmptyDIV").droppable({
			drop: function (event, ui) {
				dropped=true;
				dropTarget = $(this);
				},
			over: function () {
				$(this).css("border-color","green");
				},
			out: function () {
				$(this).css("border-color","white");
				},
			tolerance: 'intersect'
			});
		$("#scheduleGridContainer").find(".scheduleGridCompoundEmptyDIV").droppable({
			drop: function (event, ui) {
				dropped=true;
				dropTarget = $(this);
				},
			over: function () {
				$(this).css("height",$(this).height()-4);
				$(this).css("width",$(this).width()-4);
				$(this).css("border-color","green");
				$(this).css("border-width","3px");
				},
			out: function () {
				$(this).css("height",$(this).height()+4);
				$(this).css("width",$(this).width()+4);
				$(this).css("border-color","grey");
				$(this).css("border-width","1px");
				},
			tolerance: 'intersect'
			});
		$("#scheduleGridContainer").find(".scheduledSessionBlock").parent(".schedulerGridContainer").droppable({
			drop: function (event, ui) {
				dropped=true;
				dropTarget = $(this);
				},
			over: function() {
				var helperSel = $("#myhelper");
				var targetTDSel = $(this).parent();
				helperSel.css("height",targetTDSel.height() - 4);
				helperSel.css("width",targetTDSel.width() - 4);
				/*helperSel.offset({
					top: parseInt($(this).offset().top,10) - 3,
					left: parseInt($(this).offset().left,10) - 3
					});*/
				helperSel.css("top",parseInt(targetTDSel.offset().top,10) + 1);
				helperSel.css("left",parseInt(targetTDSel.offset().left,10) + 1);
				helperSel.show();
				//helperSel.text($(this).offset().top + ":" + $(this).offset().left);
				},
			out: function() {
				$("#myhelper").hide();
				},
			tolerance: 'intersect'
			});
		/// editing this chunk
		$("#scheduleGridContainer").find(".scheduledSessionBlock").draggable({
			addClasses: false,
			revert: 'invalid',
			containment: "#mainContentContainer",
			helper: 'clone',
			appendTo: "#mainContentContainer",
			start: staffMaintainSchedule.dragStart,
			stop: staffMaintainSchedule.dropSession,
			scroll: true
			});
		/// editing this chunk
		var foo;
		if (confirmationMsg)
			foo = confirmationMsg;
		var scratch = $("#scheduleGridContainer").find("#warningsDivContent");
		if (scratch.length > 0) {
			foo += 	scratch.html();
			scratch.remove();
			}
		if (foo) {
			$("#tabs-warnings").html(foo);
			$("#tabs-warnings").find(".conflictEditConfirmation").remove();
			$("#tabs-warnings-link").click();
			}
		confirmationMsg = "";
		if (schedScrollTop != "")
			$("#scheduleGridContainer").scrollTop(schedScrollTop);
	}

	this.retrieveRoomsTableError = function retrieveRoomsTableError(event,jqXHR, ajaxSettings, thrownError) {
		$("#testForNow1").html(thrownError);
	}
	
	this.resetSessionSearchClick = function resetSessionSearchClick() {
		//debugger;
		$("#trackSEL").val("0");
		$("#typeSEL").val("0");
		$("#divisionSEL").val("0");
		$("#sessionIdINP").val("");
		$("#titleINP").val("");
		$("#noSessionsFoundMSG").hide();
	}

	this.resizeMe = function resizeMe() {
		lib.onePageResize();
		$("#tabsContent").css("top", $("#tabsBar").outerHeight(true) + 1);
	}

	this.retrieveSessionsCallback = function retrieveSessionsCallback(responseData,returnString,jqXHR) {
		if (responseData == "noNewSessionsFound") {
			$("#noSessionsFoundMSG").show();
			return true;
			}
		if ($("#sessionsToBeScheduled").find("[id^='sessionBlockDIV_']").length > 0) {
				$("#sessionsToBeScheduled").html(responseData + $("#sessionsToBeScheduled").html());
				}
			else {
				$("#sessionsToBeScheduled").html(responseData);
				}
		$("#sessionsToBeScheduled").find("[id^='sessionBlockDIV_']").not(".draggable").draggable({
			addClasses: false,
			revert: 'invalid',
			containment: "#mainContentContainer",
			helper: 'clone',
			appendTo: "#mainContentContainer",
			start: staffMaintainSchedule.dragStart,
			stop: staffMaintainSchedule.dropSession,
			scroll: true
			});
		sessionMasterArray = [];
		$("#sessionsToBeScheduled").find("[id^='sessionBlockDIV_']").each(function() {
			sessionMasterArray.push($(this).attr("id").substring(16,999));
			});
		$("#noSessionsFoundMSG").hide();
		// make all the ? (info) icons on the session blocks work
		$(".getSessionInfoP").on("click", staffMaintainSchedule.onClickInfo);
		$(".getSessionInfoP").addClass("getSessionInfo");
		$(".getSessionInfoP").removeClass("getSessionInfoP");
		return true;
	}

	this.retrieveSessionInfoCallback = function retrieveSessionInfoCallback(responseData,returnString,jqXHR) {
		$("#tabs-info").html(responseData);
		$("#tabs-info-link").click();
	}

	this.retrieveSessionsClick = function retrieveSessionsClick() {
		var i;
		//var currSessionIdArray = [];
		//for (i in sessionMasterArray)
		//	currSessionIdArray.push(sessionMasterArray[i].sessionid);
		var postdata = {
			ajax_request_action : "retrieveSessions",
			currSessionIdArray : sessionMasterArray,
			trackId: $("#trackSEL").val(),
			typeId: $("#typeSEL").val(),
			divisionId: $("#divisionSEL").val(),
			sessionId : $("#sessionIdINP").val(),
			title : $("#titleINP").val()	
			};
		if (x = $("#password").val())
			postdata.password = x;
		// url: "http://www.hashemian.com/tools/form-post-tester.php",
		$.ajax({
			url: "staffMaintainScheduleSubmit.php",
			dataType: "html",
			data: postdata,
			error: staffMaintainSchedule.retrieveSessionsError,
			success: staffMaintainSchedule.retrieveSessionsCallback,
			type: "POST"
			});
	}

	this.retrieveSessionsError = function retrieveSessionsError(event,jqXHR, ajaxSettings, thrownError) {
		$("#testForNow1").html(thrownError);
	}
	
	this.roomCheckClick = function roomCheckClick(e) {
		e.stopPropagation();
		staffMaintainSchedule.changeRoomDisplay();
		return;
	}

	this.timeFromTimeAttr = function timeFromTimeAttr(thisTime) {
		var myTime = new Date(conStartDateTime);
		var thisTimeArr = thisTime.split(":");
		myTime.setHours(myTime.getHours() + parseInt(thisTimeArr[0],10));
		myTime.setMinutes(myTime.getMinutes() + parseInt(thisTimeArr[1],10));
		return myTime;
	}

	this.timeAttrFromUnits = function timeAttrFromUnits(timeUnits) {
		return Math.floor(timeUnits/2) + ((timeUnits % 2 == 1) ? ":30:00" : ":00:00");
	}

	this.startTimeFromUnits = function startTimeFromUnits(startTimeUnits) {
		var startTime = new Date(conStartDateTime);
		startTime.setHours(startTime.getHours() + Math.floor(startTimeUnits/2));
		startTime.setMinutes(startTime.getMinutes() + (startTimeUnits % 2) * 30);
		return startTime;
	}
	
	this.timeStrFromTime = function timeStrFromTime(thisTime) {
		var minStr = ((thisTime.getMinutes() < 10) ? "0" : "") + thisTime.getMinutes();
		return ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"][thisTime.getDay()] + " " + ((thisTime.getHours()+11) % 12 + 1) +
			":" + minStr + ((thisTime.getHours() >= 12) ? " PM" : " AM");
	}
}