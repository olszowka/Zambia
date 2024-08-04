// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Peter Olszowka on 2024-Mar-08
import React from 'react';
import Nav from 'react-bootstrap/Nav';
import Tab from 'react-bootstrap/Tab';
import Rooms from './rooms/Rooms';
import SessionSearchForm from './sessions/SessionSearchForm';
import InfoSection from "./info/InfoSection";
import { useUnifiedContext } from "../context/UnifiedContext";
import {ActionTypeEnum} from "../context/UnifiedContextTypes";

export enum TabKeys {
    Rooms = "Rooms",
    Sessions = "Sessions",
    Warnings = "Warnings",
    Info = "Info"
}

function TabSection() {
    const { state, dispatch } = useUnifiedContext();
    const activeKey = state.visibleTab;
    const setKey = (key: string | null) => {
        if (key) {
            dispatch({
                type: ActionTypeEnum.SetVisibleTab,
                payload: key as TabKeys
            });
        }
    }
    return (
        <div id="grid-scheduler-tabs-container" className={'flex-row-fixed flex-row-container'}>
            <Tab.Container id="grid-scheduler-tabs" activeKey={activeKey} onSelect={setKey}>
                <Nav variant="tabs">
                    <Nav.Item>
                        <Nav.Link eventKey={TabKeys.Rooms}>Rooms</Nav.Link>
                    </Nav.Item>
                    <Nav.Item>
                        <Nav.Link eventKey={TabKeys.Sessions}>Sessions</Nav.Link>
                    </Nav.Item>
                    <Nav.Item>
                        <Nav.Link eventKey={TabKeys.Warnings}>Warnings</Nav.Link>
                    </Nav.Item>
                    <Nav.Item>
                        <Nav.Link eventKey={TabKeys.Info}>Info</Nav.Link>
                    </Nav.Item>
                </Nav>
                <Tab.Content className={'flex-row-remainder-wrapper'}>
                    <Tab.Pane eventKey={TabKeys.Rooms} className="overflow-y-container rooms-panel">
                        <Rooms />
                    </Tab.Pane>
                    <Tab.Pane eventKey={TabKeys.Sessions} className="overflow-y-container sessions-search-panel">
                        <SessionSearchForm />
                    </Tab.Pane>
                    <Tab.Pane eventKey={TabKeys.Warnings} className="overflow-y-container warnings-panel">Warnings tab content</Tab.Pane>
                    <Tab.Pane eventKey={TabKeys.Info} className="overflow-y-container info-panel">
                        <InfoSection />
                    </Tab.Pane>
                </Tab.Content>
            </Tab.Container>
        </div>
    );
}

export default TabSection;
