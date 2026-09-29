import { useState } from 'react';
import type { BootstrapData, SessionInfoData, SessionSearchResult, TabKey } from './types';
import { getSessionInfo } from './api';
import Grid from './components/Grid';
import LeftPanel from './components/LeftPanel';

export default function App({ initialData }: { initialData: BootstrapData }) {
  // Starts empty -- no room columns shown until staff explicitly check them in the Rooms tab.
  const [visibleRoomIds, setVisibleRoomIds] = useState<Set<number>>(() => new Set());
  const [sessionsToBeScheduled, setSessionsToBeScheduled] = useState<SessionSearchResult[]>([]);
  const [sessionInfo, setSessionInfo] = useState<SessionInfoData | null>(null);
  const [sessionInfoLoading, setSessionInfoLoading] = useState(false);
  const [activeTab, setActiveTab] = useState<TabKey>('rooms');

  function handleToggleRoom(roomid: number) {
    setVisibleRoomIds((prev) => {
      const next = new Set(prev);
      if (next.has(roomid)) {
        next.delete(roomid);
      } else {
        next.add(roomid);
      }
      return next;
    });
  }

  function handleSearchResults(results: SessionSearchResult[]) {
    setSessionsToBeScheduled((prev) => [...prev, ...results]);
  }

  function handleClearAll() {
    setSessionsToBeScheduled([]);
  }

  async function handleInfoClick(sessionid: number) {
    setActiveTab('info');
    setSessionInfoLoading(true);
    try {
      const info = await getSessionInfo(sessionid);
      setSessionInfo(info);
    } finally {
      setSessionInfoLoading(false);
    }
  }

  return (
    <div className="grid-scheduler-app">
      <div className="grid-scheduler-toolbar">
        Grid Scheduler (New) — phase 2 of the React rewrite. Rooms/Sessions/Info tabs are live; no
        drag-and-drop yet.
      </div>
      <div className="grid-scheduler-body">
        <LeftPanel
          rooms={initialData.rooms}
          visibleRoomIds={visibleRoomIds}
          onToggleRoom={handleToggleRoom}
          tracks={initialData.tracks}
          tags={initialData.tags}
          types={initialData.types}
          divisions={initialData.divisions}
          sessionsToBeScheduled={sessionsToBeScheduled}
          onSearchResults={handleSearchResults}
          onClearAll={handleClearAll}
          sessionInfo={sessionInfo}
          sessionInfoLoading={sessionInfoLoading}
          onInfoClick={handleInfoClick}
          activeTab={activeTab}
          onTabChange={setActiveTab}
        />
        <Grid data={initialData} visibleRoomIds={visibleRoomIds} onInfoClick={handleInfoClick} />
      </div>
    </div>
  );
}
