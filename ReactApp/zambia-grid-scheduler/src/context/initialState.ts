import { RoomType, SessionsSearchDataType, UnifiedContextStateType } from "./UnifiedContextTypes";
import { SchedulableSessionType } from "../render_sessions/SessionTypes";
import {TabKeys} from "../tab_section/TabSection";

export function getInitialState(): UnifiedContextStateType {
    const root = document.getElementById('zambia-grid-scheduler');
    const zgsRoomData = root?.dataset.zgsRooms;
    const rooms: RoomType[] = zgsRoomData ? JSON.parse(decodeURIComponent(zgsRoomData)) : [];
    const configurationData = root?.dataset.zgsConfiguration;
    const configuration = configurationData ? JSON.parse(decodeURIComponent(configurationData)) : { trackTagUsage : "TRACK_OVER_TAG" }
    const zgsSessionsSearchDataEnc = root?.dataset.zgsSessionsSearch;
    let sessionsSearchData:SessionsSearchDataType;
    if (zgsSessionsSearchDataEnc) {
        sessionsSearchData = JSON.parse(decodeURIComponent(zgsSessionsSearchDataEnc));
    } else {
        sessionsSearchData = {
            tracks: [],
            tags: [],
            types: [],
            divisions: []
        }
    }
    return ({
        schedulableSessions: [] as SchedulableSessionType[],
        configuration: {
            conStartDateTime: new Date(configuration.conStartDateTime),
            rooms,
            trackTagUsage: configuration.trackTagUsage,
            sessionsSearchData
        },
        visibleTab: TabKeys.Rooms
    });
}
