// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Peter Olszowka on 2024-Mar-08
import React from 'react';
import { DndContext, DragOverlay, useSensor, useSensors, KeyboardSensor, PointerSensor } from '@dnd-kit/core';
import './App.css';
import TabSection from './tab_section/TabSection';
import SchedulableSessionsSection from "./schedulable_sessions_section/SchedulableSessionsSection";
import { useUnifiedContext } from "./context/UnifiedContext";
import SessionBeingDragged from "./render_sessions/SessionBeingDragged";

const firstColumnStyle = {
    width: '285px',
    margin: '2px 1px 2px 2px',
    border: '1px solid black'
};

function ZambiaGridScheduler() {
    const sensors = useSensors(
        useSensor(PointerSensor),
        useSensor(KeyboardSensor)
    );
    const { state, dispatch } = useUnifiedContext();

    return (
        <DndContext sensors={sensors}>
            <div className={'flex-row-container flex-column-fixed'} style={firstColumnStyle}>
                <TabSection />
                <SchedulableSessionsSection />
            </div>
            <div id={'scheduleGridContainer'} className={'flex-column-remainder'} />
            <DragOverlay>
                {state.draggingSession ? (
                    <SessionBeingDragged session={state.draggingSession} />
                ): null}
            </DragOverlay>
        </DndContext>
    );
}

export default ZambiaGridScheduler;
