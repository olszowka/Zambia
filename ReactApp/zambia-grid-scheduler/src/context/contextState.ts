import { createContext, useContext, useReducer } from 'react';

import { schedulableSessionType } from '../tab_section/SessionSearchUtilities';

const initalState = {
    schedulableSessions: [] as schedulableSessionType[]
}

const contextState = createContext(initalState);
