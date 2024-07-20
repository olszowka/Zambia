import { createContext, useContext, useReducer } from 'react';

import { schedulableSessionType } from '../tab_section/SessionSearchUtilities';

export type UnifiedContextStateType = {
    schedulableSessions: schedulableSessionType[]
}

const initalState : UnifiedContextStateType = {
    schedulableSessions: [] as schedulableSessionType[]
}

export enum ActionTypeEnum {
    AddSchedulableSessions = "ADD_SCHEDULABLE_SESSIONS",
    RemoveSchedulableSession = "REMOVE_SCHEDULABLE_SESSION"
}

export type ActionType =
    | { type: ActionTypeEnum.AddSchedulableSessions; payload: schedulableSessionType[] }
    | { type: ActionTypeEnum.RemoveSchedulableSession; payload: number };

const reducer = (state : UnifiedContextStateType, action: ActionType) => {
    switch (action.type) {
        case ActionTypeEnum.AddSchedulableSessions:
            const newSchedulableSesstions = state.schedulableSessions.concat(action.payload);
            return {...state, schedulableSessions: newSchedulableSesstions}
        case ActionTypeEnum.RemoveSchedulableSession:
            const newSchedulableSesstions2 = state.schedulableSessions.filter(session=> session.sessionid !== action.payload);
            return {...state, schedulableSessions: newSchedulableSesstions2}
    }
}

type UnifiedContextType = {
    state: UnifiedContextStateType,
    dispatch: React.Dispatch<ActionType>
}

// @ts-ignore
const UnifiedContext = createContext() as React.Context<UnifiedContextType>;

export const UnifiedContextProvider: React.FC<{children: React.ReactNode}> = ({ children }) => {
    const [state, dispatch] = useReducer(reducer, initalState);
    return (
        <UnifiedContext.Provider value={{ state, dispatch }}>
            {children}
        </UnifiedContext.Provider>
    );
}

export const useUnifiedContext = () => {
    return useContext(UnifiedContext)
}
