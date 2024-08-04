// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Peter Olszowka on 2024-Jul-28
import React from 'react';
import dayjs from 'dayjs';
import { useUnifiedContext } from "../../context/UnifiedContext";
import { Participant } from "./infoSectionUtils";

const infoSectionStyle = {
    padding: '4px'
}

const participantItemStyle = {
    paddingLeft: '20px',
    textIndent: '-20px'
}

const renderPerson = (person: Participant) => (
    `${person.pubsName} (${person.badgeid}) ${person.name}`
);

function InfoSection() {
    const { state } = useUnifiedContext();
    const info = state.infoSectionData;
    if (!info) {
        return null;
    }
    let scheduledEntry;
    if (info.scheduledStart) {
        const startTimeDayJS = dayjs(new Date(info.scheduledStart));
        const startTimeDisplay  = startTimeDayJS.format('ddd h:mm A');
        const endTimeDayJS = startTimeDayJS.add(info.duration, 'm');
        const endTimeDisplay = endTimeDayJS.format('h:mm A', );
        const timeDisplay = `${startTimeDisplay} - ${endTimeDisplay}`;
        scheduledEntry = (
            <>
                <div className={'infolabel'}>Scheduled:</div>
                <div className={'infofield'}>{timeDisplay}</div>
                <div className={'infofield'}>{`in ${info.scheduledRoom}`}</div>
            </>
        );
    } else {
        scheduledEntry = (<div className={'infolabel'}>Not scheduled</div>);
    }
    let moderatorEntry;
    if (info.moderator) {
        moderatorEntry = (
            <>
                <div className={'infolabel'}>Moderator:</div>
                <div className={'infofield'}>{renderPerson(info.moderator)}</div>
            </>
        );
    } else {
        moderatorEntry = (
            <div className={'infolabel'}>No moderator assigned</div>
        );
    }
    let participantsEntry;
    if (info.participants && info.participants.length > 0) {
        participantsEntry = (
            <>
                <div className={'infolabel'}>Participants:</div>
                {info.participants.map((participant) => (
                    <div className={'infofield'} style={participantItemStyle}>{renderPerson(participant)}</div>
                ))}
            </>
        );
    } else {
        participantsEntry = (
            <div className={'infolabel'}>No participants assigned</div>
        );
    }
    return (
        <div style={infoSectionStyle}>
            <div className={'infolabel'}>Title:</div>
            <div className={'infofield'}>{info.title}</div>
            <div className={'infolabel'}>Description:</div>
            <div className={'infofield'}>{info.description}</div>
            <div className={'infolabel'}>Session Id:</div>
            <div className={'infofield'}>{info.sessionid}</div>
            <div className={'infolabel'}>Track:</div>
            <div className={'infofield'}>{info.trackName}</div>
            <div className={'infolabel'}>Tags:</div>
            <div className={'infofield'}>{(info.tagNameList || []).join(', ')}</div>
            <div className={'infolabel'}>Type:</div>
            <div className={'infofield'}>{info.typeName}</div>
            <div className={'infolabel'}>Division:</div>
            <div className={'infofield'}>{info.divisionName}</div>
            {scheduledEntry}
            <div className={'infolabel'}>Notes for programming:</div>
            <div className={'infofield'}>{info.notesForProgramming}</div>
            {moderatorEntry}
            {participantsEntry}
        </div>
    );
}

export default InfoSection;
