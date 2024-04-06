// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Peter Olszowka on 2024-Mar-08
import React from 'react';
import ReactDOM from 'react-dom/client';
import './index.css';
import ZambiaGridScheduler from './ZambiaGridScheduler';
// import reportWebVitals from './reportWebVitals';

document.addEventListener( "DOMContentLoaded", () => {
    const root = ReactDOM.createRoot(
        document.getElementById('zambia-grid-scheduler') as HTMLElement
    );
    root.render(
        <React.StrictMode>
            <ZambiaGridScheduler />
        </React.StrictMode>
    );
});

// If you want to start measuring performance in your app, pass a function
// to log results (for example: reportWebVitals(console.log))
// or send to an analytics endpoint. Learn more: https://bit.ly/CRA-vitals
// reportWebVitals();
