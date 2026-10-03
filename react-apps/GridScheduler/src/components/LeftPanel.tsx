import type { RoomData, LookupEntry, SessionSearchResult, SessionInfoData, SnapMode, TabKey } from '../types';
import RoomsTab from './tabs/RoomsTab';
import SessionsTab from './tabs/SessionsTab';
import InfoTab from './tabs/InfoTab';
import WarningsTab from './tabs/WarningsTab';
import UnscheduledSessionBox from './UnscheduledSessionBox';
import FileCabinetIcon from './FileCabinetIcon';
import OptionsMenu from './OptionsMenu';
import type { WarningsTabProps } from './tabs/WarningsTab';

interface LeftPanelProps {
  rooms: RoomData[];
  visibleRoomIds: Set<number>;
  onToggleRoom: (roomid: number) => void;
  tracks: LookupEntry[];
  tags: LookupEntry[];
  types: LookupEntry[];
  divisions: LookupEntry[];
  sessionsToBeScheduled: SessionSearchResult[];
  onSearchResults: (results: SessionSearchResult[]) => void;
  onClearAll: () => void;
  sessionInfo: SessionInfoData | null;
  sessionInfoLoading: boolean;
  onInfoClick: (sessionid: number) => void;
  activeTab: TabKey;
  onTabChange: (tab: TabKey) => void;
  warnings: WarningsTabProps;
  dragDisabled: boolean;
  // Which of this panel's drop targets a dragged session is currently over, for hover feedback.
  dragOverTarget: 'cabinet' | 'pool' | null;
  // A swap counterpart mid-animation into the pool -- see swapAnimation.ts.
  animatingSessionId: number | null;
  swapMode: boolean;
  onSwapModeChange: (swapMode: boolean) => void;
  snapMode: SnapMode;
  onSnapModeChange: (snapMode: SnapMode) => void;
}

const TABS: { key: TabKey; label: string }[] = [
  { key: 'rooms', label: 'Rooms' },
  { key: 'sessions', label: 'Sessions' },
  { key: 'warnings', label: 'Warnings' },
  { key: 'info', label: 'Info' },
];

export default function LeftPanel({
  rooms,
  visibleRoomIds,
  onToggleRoom,
  tracks,
  tags,
  types,
  divisions,
  sessionsToBeScheduled,
  onSearchResults,
  onClearAll,
  sessionInfo,
  sessionInfoLoading,
  onInfoClick,
  activeTab,
  onTabChange,
  warnings,
  dragDisabled,
  dragOverTarget,
  animatingSessionId,
  swapMode,
  onSwapModeChange,
  snapMode,
  onSnapModeChange,
}: LeftPanelProps) {
  return (
    <div className="grid-scheduler-left-panel">
      <div className="grid-scheduler-tabs">
        <div className="grid-scheduler-tab-bar">
          {TABS.map((tab) => (
            <button
              key={tab.key}
              type="button"
              className={'grid-scheduler-tab-button' + (activeTab === tab.key ? ' active' : '')}
              onClick={() => onTabChange(tab.key)}
            >
              {tab.label}
              {/* Flags a conflicting edit still awaiting Save Anyway / Cancel, in case staff switched to
                  another tab (dragging stays disabled until it's answered). */}
              {tab.key === 'warnings' && warnings.pendingConflict && (
                <span className="grid-scheduler-tab-attention" title="A change is waiting for your response" />
              )}
            </button>
          ))}
        </div>
        <div className="grid-scheduler-tab-content">
          {activeTab === 'rooms' && (
            <RoomsTab rooms={rooms} visibleRoomIds={visibleRoomIds} onToggleRoom={onToggleRoom} />
          )}
          {activeTab === 'sessions' && (
            <SessionsTab
              tracks={tracks}
              tags={tags}
              types={types}
              divisions={divisions}
              currSessionIds={sessionsToBeScheduled.map((s) => s.sessionid)}
              onResults={onSearchResults}
            />
          )}
          {activeTab === 'warnings' && <WarningsTab {...warnings} />}
          {activeTab === 'info' && <InfoTab info={sessionInfo} loading={sessionInfoLoading} />}
        </div>
      </div>
      <div className="grid-scheduler-sessions-panel">
        <div className="grid-scheduler-sessions-panel-header">
          <div className="grid-scheduler-sessions-panel-title">Sessions to be scheduled</div>
          <div className="grid-scheduler-sessions-panel-header-controls">
            <OptionsMenu
              swapMode={swapMode}
              onSwapModeChange={onSwapModeChange}
              snapMode={snapMode}
              onSnapModeChange={onSnapModeChange}
            />
            <button
              type="button"
              className="btn btn-secondary btn-sm"
              onClick={onClearAll}
              disabled={sessionsToBeScheduled.length === 0}
            >
              Clear All
            </button>
            <FileCabinetIcon dragOver={dragOverTarget === 'cabinet'} />
          </div>
        </div>
        {/* Drop target for returning a scheduled session to the pool -- see dropTarget.ts. */}
        <div
          className={'grid-scheduler-sessions-panel-list' + (dragOverTarget === 'pool' ? ' grid-scheduler-drop-hover' : '')}
          data-drop-kind="pool"
        >
          {sessionsToBeScheduled.length === 0 && (
            <div className="grid-scheduler-sessions-panel-empty">
              No sessions in the pool. Use the Sessions tab to search.
            </div>
          )}
          {sessionsToBeScheduled.map((session) => (
            <UnscheduledSessionBox
              key={session.sessionid}
              session={session}
              onInfoClick={onInfoClick}
              dragDisabled={dragDisabled}
              animationHidden={animatingSessionId === session.sessionid}
            />
          ))}
        </div>
      </div>
    </div>
  );
}
