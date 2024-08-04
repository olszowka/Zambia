// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Peter Olszowka on 2024-Jul-20
import {ActionType, ActionTypeEnum} from "../../context/UnifiedContextTypes";
import {TabKeys} from "../TabSection";

export type Participant = {
    badgeid: string,
    name: string,
    pubsName: string
}

export type InfoSectionDataType = {
    title: string,
    description: string,
    sessionid: number,
    trackName: string,
    tagNameList: string[],
    typeName: string,
    divisionName: string,
    duration: number, /* minutes */
    notesForProgramming: string,
    scheduledStart: Date | null,
    scheduledRoom: string | null,
    moderator: Participant,
    participants: Participant[],
}

export const retrieveInfoSection = (sessionid: number, dispatch: React.Dispatch<ActionType>) => {
    const submissionData = {
        ajax_request_action: 'retrieveSessionInfo',
        sessionid: sessionid
    }
    fetch("StaffMaintainScheduleAjax.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(submissionData),
    })
        .then((response) => response.json() as Promise<InfoSectionDataType>)
        .then(infoSectionData => {
            dispatch({
                type: ActionTypeEnum.UpdateInfoSection,
                payload: infoSectionData
            });
            dispatch({
                type: ActionTypeEnum.SetVisibleTab,
                payload: TabKeys.Info
            });
        })

}
