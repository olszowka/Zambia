// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Peter Olszowka on 2024-Mar-08
import React from 'react';
import Nav from 'react-bootstrap/Nav';
import Tab from 'react-bootstrap/Tab';
import Rooms from './Rooms';
import SessionSearchForm from './SessionSearchForm';
function TabSection() {
    return (
        <div id="grid-scheduler-tabs-container" className={'flex-row-fixed flex-row-container'}>
            <Tab.Container id="grid-scheduler-tabs" defaultActiveKey="Rooms">
                <Nav variant="tabs">
                    <Nav.Item>
                        <Nav.Link eventKey="Rooms">Rooms</Nav.Link>
                    </Nav.Item>
                    <Nav.Item>
                        <Nav.Link eventKey="Sessions">Sessions</Nav.Link>
                    </Nav.Item>
                    <Nav.Item>
                        <Nav.Link eventKey="Warnings">Warnings</Nav.Link>
                    </Nav.Item>
                    <Nav.Item>
                        <Nav.Link eventKey="Info">Info</Nav.Link>
                    </Nav.Item>
                </Nav>
                <Tab.Content className={'flex-row-remainder-wrapper'}>
                    <Tab.Pane eventKey="Rooms" className="overflow-y-container rooms-panel">
                        <Rooms />
                    </Tab.Pane>
                    <Tab.Pane eventKey="Sessions" className="overflow-y-container sessions-search-panel">
                        <SessionSearchForm />
                    </Tab.Pane>
                    <Tab.Pane eventKey="Warnings" className="overflow-y-container warnings-panel">Warnings tab content</Tab.Pane>
                    <Tab.Pane eventKey="Info" className="overflow-y-container info-panel">Info tab content</Tab.Pane>
                </Tab.Content>
            </Tab.Container>
        </div>
    );
}

export default TabSection;
