// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Peter Olszowka on 2024-Mar-08
import {ActionType, ActionTypeEnum, UnifiedContextStateType} from "../context/UnifiedContext";

export type sessionSearchSubmissionype = {
    track?: number;
    tags?: number[];
    matchAny?: 1;
    matchAll?: 1;
    type?: number;
    division?: number;
    sessionId?: number;
    title?: string;
    personAssigned?: 1;
    ajax_request_action?: string;
}

export type schedulableSessionType = {
    divisionname: string;
    duration: string;
    durationMins? : number;
    sessionid: number;
    taglist: string;
    title: string;
    trackname: string;
    typename: string;
}

export function resetSearchForm() {
    const {
        trackSel,
        tagCheckArr,
        matchAnyRad,
        matchAllRad,
        typeSel,
        divisionSel,
        sessionIdInp,
        titleInp,
        persAssgChk
    } = getFormElements();
    if (trackSel) {
        trackSel.value = '0';
    }
    tagCheckArr.forEach((tagCheck) => { tagCheck.checked = false; });
    if (matchAnyRad) {
        matchAnyRad.checked = false;
    }
    if (matchAllRad) {
        matchAllRad.checked = false;
    }
    if (typeSel) {
        typeSel.value = '0';
    }
    if (divisionSel) {
        divisionSel.value = '0';
    }
    if (sessionIdInp) {
        sessionIdInp.value = '';
    }
    if (titleInp) {
        titleInp.value = '';
    }
    if (persAssgChk) {
        persAssgChk.checked = false;
    }
}

export function submitSearchForm(state: UnifiedContextStateType, dispatch: React.Dispatch<ActionType>) {
    const submissionData = collectSearchFormData();
    submissionData.ajax_request_action = 'retrieveSessions';
    fetch("StaffMaintainScheduleAjax.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(submissionData),
    })
        .then((response) => response.json() as Promise<schedulableSessionType[]>)
        .then((sessions) => {
            let currentSessionIds = state.schedulableSessions.map(session => session.sessionid);
            sessions.forEach((session => {
                if (currentSessionIds.includes(session.sessionid)) {
                    dispatch({
                        type: ActionTypeEnum.RemoveSchedulableSession,
                        payload: session.sessionid
                    });
                }
            }));
            dispatch({
                type: ActionTypeEnum.AddSchedulableSessions,
                payload: sessions
            });
        });
}

function collectSearchFormData() {
    const submissionData: sessionSearchSubmissionype = {};
    const {
        trackSel,
        tagCheckArr,
        matchAnyRad,
        matchAllRad,
        typeSel,
        divisionSel,
        sessionIdInp,
        titleInp,
        persAssgChk
    } = getFormElements();
    if (trackSel) {
        submissionData.track = parseInt(trackSel.value, 10);
    }
    let tags:number[] = [];
    tagCheckArr.forEach((tagChk) => {
        const tagId = parseInt(tagChk.id.slice(10), 10);
        if (!isNaN(tagId) && tagChk.checked) {
            tags.push(tagId);
        }
    });
    if (tags.length > 0) {
        submissionData.tags = tags;
    }
    if (matchAnyRad?.checked) {
        submissionData.matchAny = 1;
    }
    if (matchAllRad?.checked) {
        submissionData.matchAll = 1;
    }
    if (typeSel) {
        submissionData.type = parseInt(typeSel.value, 10);
    }
    if (divisionSel) {
        submissionData.division = parseInt(divisionSel.value, 10);
    }
    if (sessionIdInp) {
        const sessionId = parseInt(sessionIdInp.value, 10);
        if (!isNaN(sessionId)) {
            submissionData.sessionId = sessionId;
        }
    }
    if (titleInp?.value) {
        submissionData.title = titleInp.value;
    }
    if (persAssgChk?.checked) {
        submissionData.personAssigned = 1;
    }
    return submissionData;
}
function getFormElements() {
    const trackSel = document.getElementById('track-sel') as HTMLSelectElement | null;
    const tagChecks = document.querySelectorAll('.zambia-grid-scheduler .checkbox-list-container input[type="checkbox"]');
    const tagCheckArr = Array.from(tagChecks) as HTMLInputElement[];
    const matchAnyRad= document.getElementById('tag-match-any') as HTMLInputElement | null;
    const matchAllRad= document.getElementById('tag-match-all') as HTMLInputElement | null;
    const typeSel= document.getElementById('type-sel') as HTMLSelectElement | null;
    const divisionSel= document.getElementById('division-sel') as HTMLSelectElement | null;
    const sessionIdInp= document.getElementById('session-id-inp') as HTMLInputElement | null;
    const titleInp= document.getElementById('title-inp') as HTMLInputElement | null;
    const persAssgChk = document.getElementById('persons-assigned-chk') as HTMLInputElement | null;
    return {
        trackSel,
        tagCheckArr,
        matchAnyRad,
        matchAllRad,
        typeSel,
        divisionSel,
        sessionIdInp,
        titleInp,
        persAssgChk
    }
}
