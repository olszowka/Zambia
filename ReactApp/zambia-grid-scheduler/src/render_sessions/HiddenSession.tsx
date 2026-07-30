// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Peter Olszowka on 2024-Jul-20
import React from 'react';
import { SessionType } from "./SessionTypes";
import { hiddenSessionStyle } from "./sessionStyles";
import SessionInner from "./SessionInner";

interface HiddenSessionProps {
    session: SessionType;
}

function HiddenSession(props: HiddenSessionProps) {
    const session = props.session;

    return (
        <div key={session.sessionid} style={{...hiddenSessionStyle}} >
            <SessionInner session={session} />
        </div>
    );
}

export default HiddenSession;
