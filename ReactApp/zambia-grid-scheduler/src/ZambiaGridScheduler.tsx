import React from 'react';
import './App.css';
import TabSection from './tab_section/TabSection';
function ZambiaGridScheduler() {
    const firstColumnStyle = {
        width: '285px',
        margin: '2px 1px 2px 2px',
        border: '1px solid black'
    };

    return (
        <>
            <div className={'flex-row-container flex-column-fixed'} style={firstColumnStyle}>
                <TabSection />
            </div>
            <div id={'scheduleGridContainer'} className={'flex-column-remainder'}></div>
        </>
    );
}

export default ZambiaGridScheduler;
