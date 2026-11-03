import type { SnapMode } from './types';
import { SNAP_MODES } from './dropTarget';

// Page options remembered between visits, per user, in this browser's localStorage. Losing them (cleared site data,
// a private window, Safari's 7-day expiry for script-written storage) only means the page starts with its defaults.
export interface GridSchedulerPreferences {
  swapMode: boolean;
  snapMode: SnapMode;
  // Kept as chosen even while Grid Simplification is unavailable (the option is hidden then and has no effect), so
  // the choice comes back if the configuration changes back.
  gridSimplification: boolean;
  visibleRoomIds: number[];
}

export const DEFAULT_PREFERENCES: GridSchedulerPreferences = {
  swapMode: false,
  snapMode: 'grid',
  gridSimplification: false,
  visibleRoomIds: [],
};

// Keyed by badgeid, so staff sharing a browser each get their own options, and by the page's path, so two Zambia
// installations on the same host (whose room ids are unrelated) don't share them.
function storageKey(badgeid: string): string {
  return `zambia.gridScheduler.preferences:${window.location.pathname}:${badgeid}`;
}

// Whatever was saved, checked field by field against what's valid now (a room may have been removed from the grid,
// or a snap mode dropped); anything missing or invalid falls back to its default.
export function loadPreferences(badgeid: string, roomIds: number[]): GridSchedulerPreferences {
  let saved: unknown;
  try {
    const text = window.localStorage.getItem(storageKey(badgeid));
    saved = text === null ? null : JSON.parse(text);
  } catch {
    return DEFAULT_PREFERENCES;
  }
  if (typeof saved !== 'object' || saved === null) return DEFAULT_PREFERENCES;
  const record = saved as Record<string, unknown>;
  const snapMode = SNAP_MODES.find((mode) => mode.value === record.snapMode)?.value;
  const visibleRoomIds = Array.isArray(record.visibleRoomIds)
    ? record.visibleRoomIds.filter((id): id is number => typeof id === 'number' && roomIds.includes(id))
    : DEFAULT_PREFERENCES.visibleRoomIds;
  return {
    swapMode: typeof record.swapMode === 'boolean' ? record.swapMode : DEFAULT_PREFERENCES.swapMode,
    snapMode: snapMode ?? DEFAULT_PREFERENCES.snapMode,
    gridSimplification:
      typeof record.gridSimplification === 'boolean' ? record.gridSimplification : DEFAULT_PREFERENCES.gridSimplification,
    visibleRoomIds,
  };
}

export function savePreferences(badgeid: string, preferences: GridSchedulerPreferences): void {
  try {
    window.localStorage.setItem(storageKey(badgeid), JSON.stringify(preferences));
  } catch {
    // Storage unavailable or full: the options just won't be remembered.
  }
}
