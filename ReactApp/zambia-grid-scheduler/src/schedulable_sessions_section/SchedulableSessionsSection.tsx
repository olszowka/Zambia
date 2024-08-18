// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Peter Olszowka on 2024-Jul-22
import React from 'react';
import { useDroppable } from '@dnd-kit/core';
import { Badge, Button } from "react-bootstrap";
import { useUnifiedContext } from "../context/UnifiedContext";
import SchedulableSession from "../render_sessions/SchedulableSession";
import { ActionType, ActionTypeEnum } from "../context/UnifiedContextTypes";

function onClickClearAll(dispatch: React.Dispatch<ActionType>) {
    dispatch({type: ActionTypeEnum.ClearAllSchedulableSession})
}

function SchedulableSessionsSection() {
    const { state, dispatch } = useUnifiedContext();
    const { setNodeRef } = useDroppable({
        id: 'file-cabinet',
    });
    return (
        <div id="grid-scheduler-schedulable-sessions" className={'flex-row-fixed flex-row-container'}>
            <div className={'flex-row-fixed p-2'}>
                <h4 className={'text-center'}>
                    <Badge bg={'light'} className={'schedulableSessionsTitleBadge'}>Sessions to be scheduled</Badge>
                </h4>
            </div>
            <div className={'flex-row-fixed p-2'}>
                <Button variant="outline-primary" size="sm" className={'align-middle ms-2 me-3'}
                        onClick={() => onClickClearAll(dispatch)}>Clear All</Button>
                <Button variant="outline-primary" size="sm" className={'align-middle me-3'} >Swap Mode</Button>
                <div id={'file-cabinet'} ref={setNodeRef} />
            </div>
            <div className={'flex-row-remainder mt-2'}>
                {state.schedulableSessions.map(session => (<SchedulableSession session={session}/>))}
            </div>
        </div>
    );
}

export default SchedulableSessionsSection;
