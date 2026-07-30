// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Peter Olszowka on 2024-Jul-20
import React from 'react';
import { SessionType } from "./SessionTypes";
import { schedulableSessionStyle } from "./sessionStyles";
import SessionInner from "./SessionInner";

interface SessionBeingDraggedProps {
    session: SessionType;
}

function SessionBeingDragged(props: SessionBeingDraggedProps) {
    const session = props.session;

    return (
        <div key={session.sessionid} style={{...schedulableSessionStyle}} >
            <SessionInner session={session} />
        </div>
    );
}

export default SessionBeingDragged;
