// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Peter Olszowka on 2024-Jul-20
import React from 'react';
import { useUnifiedContext } from "../context/UnifiedContext";
import { retrieveInfoSection } from "../tab_section/info/infoSectionUtils";
import { TrackTagUsageEnum } from "../context/UnifiedContextTypes";
import { SessionType } from "./SessionTypes";

interface SessionInnerProps {
    session: SessionType;
}

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

function SessionInner(props: SessionInnerProps) {
    const session = props.session;
    const {state, dispatch} = useUnifiedContext();

    const displayTrack = state.configuration.trackTagUsage === TrackTagUsageEnum.trackOnly ||
        state.configuration.trackTagUsage === TrackTagUsageEnum.trackOverTag;

    return (
        <>
            <div style={sessionTitleWrapperStyle}>
                <i className={'bi-info-circle-fill'}
                   onClick={() => retrieveInfoSection(session.sessionid, dispatch)}></i>
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
        </>
    );
}

export default SessionInner;
