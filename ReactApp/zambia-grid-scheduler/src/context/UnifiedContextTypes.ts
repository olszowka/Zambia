import { SchedulableSessionType } from "../render_sessions/SessionTypes";
import { InfoSectionDataType } from "../tab_section/info/infoSectionUtils";
import { trackType } from "../tab_section/sessions/TrackSelect";
import { tagType } from "../tab_section/sessions/TagPicker";
import { typeType } from "../tab_section/sessions/TypeSelect";
import { divisionType } from "../tab_section/sessions/DivisionSelect";
import { TabKeys } from "../tab_section/TabSection";

export enum TrackTagUsageEnum {
    tagOnly = "TAG_ONLY", /* Track field is not used and will be hidden where possible */
    tagOverTrack = "TAG_OVER_TRACK", /* Both fields are used, but primary sorting and filtering is by Tag */
    trackOverTag = "TRACK_OVER_TAG", /* Both fields are used, but primary sorting and filtering is by Track */
    trackOnly = "TRACK_ONLY" /* Tag field is not used and will be hidden where possible */
}

export type UnifiedContextStateType = {
    schedulableSessions: SchedulableSessionType[],
    infoSectionData?: InfoSectionDataType
    configuration: {
        conStartDateTime: Date,
        rooms: RoomType[],
        sessionsSearchData: SessionsSearchDataType
        trackTagUsage: TrackTagUsageEnum,
    },
    visibleTab: TabKeys
}

export enum ActionTypeEnum {
    AddSchedulableSessions = "ADD_SCHEDULABLE_SESSIONS",
    ClearAllSchedulableSession = "CLEAR_ALL_SCHEDULABLE_SESSIONS",
    RemoveSchedulableSession = "REMOVE_SCHEDULABLE_SESSION",
    UpdateInfoSection = "UPDATE_INFO_SECTION",
    SetVisibleTab = "SET_VISIBLE_TAB"
}

export type ActionType =
    | { type: ActionTypeEnum.AddSchedulableSessions; payload: SchedulableSessionType[] }
    | { type: ActionTypeEnum.ClearAllSchedulableSession; }
    | { type: ActionTypeEnum.RemoveSchedulableSession; payload: number }
    | { type: ActionTypeEnum.UpdateInfoSection; payload: InfoSectionDataType }
    | { type: ActionTypeEnum.SetVisibleTab; payload: TabKeys };

export type UnifiedContextType = {
    state: UnifiedContextStateType,
    dispatch: React.Dispatch<ActionType>
}

export type RoomType = {
    roomid: number;
    roomname: string;
    display_order: number;
};

export type SessionsSearchDataType = {
    tracks: trackType[];
    tags: tagType[];
    types: typeType[];
    divisions: divisionType[];
}
