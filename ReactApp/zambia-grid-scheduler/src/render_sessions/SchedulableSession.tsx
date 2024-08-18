// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Peter Olszowka on 2024-Jul-20
import React from 'react';
import { useDraggable } from '@dnd-kit/core';
import { useUnifiedContext } from "../context/UnifiedContext";
import { retrieveInfoSection } from "../tab_section/info/infoSectionUtils";
import { TrackTagUsageEnum } from "../context/UnifiedContextTypes";
import { SchedulableSessionType } from "./SessionTypes";

interface SchedulableSessionProps {
    session: SchedulableSessionType;
}

const schedulableSessionStyle = {
    width: '260px',
    margin: '4px 4px 6px 4px',
    border: '1px solid black',
    padding: '4px',
    fontSize: '0.9rem'
};

const sessionTitleWrapperStyle = {
    textOverflow: 'ellipsis',
    overflow: 'hidden',
    whiteSpace: 'nowrap'
}

const sessionTitleStyle = {
    paddingLeft: '4px',
}

const sessionIdAndTypeWrapperStyle = {
    textOverflow: 'ellipsis',
    overflow: 'hidden',
    whiteSpace: 'nowrap'
}

const sessionIdStyle = {
    display: 'inline-block',
    width: '3rem',
    color: 'blue'
}

const sessionTypeStyle = {
    display: 'inline-block',
    color: 'green'
}

const sessionTrackStyle = {
    textOverflow: 'ellipsis',
    overflow: 'hidden',
    whiteSpace: 'nowrap'
}

const sessionTagStyle = {
    textOverflow: 'ellipsis',
    overflow: 'hidden',
    whiteSpace: 'nowrap'
}

function SchedulableSession(props: SchedulableSessionProps) {
    const session = props.session;
    const { state, dispatch } = useUnifiedContext();
    const { attributes,
        listeners,
        setNodeRef,
        transform} = useDraggable({
        id: `session${session.sessionid}`,
    });
    const transformStyle = transform ? {
        transform: `translate3d(${transform.x}px, ${transform.y}px, 0)`,
    } : {};


    const displayTrack = state.configuration.trackTagUsage === TrackTagUsageEnum.trackOnly ||
        state.configuration.trackTagUsage === TrackTagUsageEnum.trackOverTag
    return (
        <div key={session.sessionid} style={{...schedulableSessionStyle, ...transformStyle}} ref={setNodeRef}>
            <div style={sessionTitleWrapperStyle}>
                <i className={'bi-info-circle-fill'} onClick={() => retrieveInfoSection(session.sessionid, dispatch)}></i>
                <span style={sessionTitleStyle}>
                    {session.title}
                </span>
            </div>
            <div style={sessionIdAndTypeWrapperStyle}>
                <div style={sessionIdStyle}>
                    {session.sessionid}
                </div>
                <div style={sessionTypeStyle}>
                    {`Type: ${session.typeName}`}
                </div>
            </div>
            {displayTrack ? (
                <div style={sessionTrackStyle}>
                    {`Track: ${session.trackName}`}
                </div>
                ) : (
                <div style={sessionTagStyle}>
                    {'Tags: ' + ((session.tagNameArray) ? (session.tagNameArray.join(', ')) : '')}
                </div>
                )
            }
        </div>
    );
}

export default SchedulableSession;
