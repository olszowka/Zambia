// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Peter Olszowka on 2024-Mar-10
import { createContext, useContext, useReducer } from 'react';
import { ActionType, ActionTypeEnum, UnifiedContextStateType, UnifiedContextType } from "./UnifiedContextTypes";
import { getInitialState } from "./initialState";
import {SchedulableSessionType} from "../render_sessions/SessionTypes";

const reducer = (state : UnifiedContextStateType, action: ActionType) => {
    switch (action.type) {
        case ActionTypeEnum.AddSchedulableSessions:
            const newSchedulableSesstions = state.schedulableSessions.concat(action.payload);
            return {...state, schedulableSessions: newSchedulableSesstions}
        case ActionTypeEnum.ClearAllSchedulableSession:
            const newSchedulableSesstions2 = [] as SchedulableSessionType[];
            return {...state, schedulableSessions: newSchedulableSesstions2}
        case ActionTypeEnum.RemoveSchedulableSession:
            const newSchedulableSesstions3 = state.schedulableSessions.filter(session=> session.sessionid !== action.payload);
            return {...state, schedulableSessions: newSchedulableSesstions3}
        case ActionTypeEnum.SetVisibleTab:
            return {...state, visibleTab: action.payload}
        case ActionTypeEnum.UpdateInfoSection:
            return {...state, infoSectionData: action.payload}
    }
}

// @ts-ignore
const UnifiedContext = createContext() as React.Context<UnifiedContextType>;

export const UnifiedContextProvider: React.FC<{children: React.ReactNode}> = ({ children }) => {
    const [state, dispatch] = useReducer(reducer, getInitialState());
    return (
        <UnifiedContext.Provider value={{ state, dispatch }}>
            {children}
        </UnifiedContext.Provider>
    );
}

export const useUnifiedContext = () => {
    return useContext(UnifiedContext)
}
