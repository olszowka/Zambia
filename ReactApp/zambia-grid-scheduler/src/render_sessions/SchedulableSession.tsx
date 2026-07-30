// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Peter Olszowka on 2024-Jul-20
import React from 'react';
import { useDraggable } from "@dnd-kit/core";
import { CSS } from "@dnd-kit/utilities";
import { SchedulableSessionType } from "./SessionTypes";
import SessionInner from "./SessionInner";
import { schedulableSessionStyle } from "./sessionStyles";

interface SchedulableSessionProps {
    session: SchedulableSessionType;
}

function SchedulableSession(props: SchedulableSessionProps) {
    const session = props.session;
    const { attributes,
        listeners,
        setNodeRef,
        transform
    } = useDraggable({
        id: `session${session.sessionid}`,
    });
    const style = {
        transform: CSS.Transform.toString(transform)
    };

    return (
        <div key={session.sessionid} style={{...schedulableSessionStyle, ...style}} ref={setNodeRef} {...listeners} {...attributes}>
            <SessionInner session={session} />
        </div>
    );
}

export default SchedulableSession;
