import { useCallback, useEffect, useLayoutEffect, useMemo, useRef, useState } from 'react';
import {
  DndContext,
  DragOverlay,
  PointerSensor,
  pointerWithin,
  useSensor,
  useSensors,
  type DragEndEvent,
  type DragStartEvent,
} from '@dnd-kit/core';
import type {
  BootstrapData,
  DragSource,
  DropResolution,
  ScheduleItemData,
  SessionInfoData,
  SessionSearchResult,
  SnapMode,
  TabKey,
} from './types';
import { editSchedule, getSchedule, getSessionInfo } from './api';
import { buildDisplaySegments } from './timeGrid';
import { resolveDrop } from './dropTarget';
import {
  applyPlanToPool,
  describeDrop,
  findUntouchedMoves,
  planDrop,
  revertPlanFromPool,
  type EditPlan,
} from './scheduleEdits';
import { captureSwapCounterpart, prefersReducedMotion, runSwapAnimation, type SwapAnimationStart } from './swapAnimation';
import Grid, { type DropPreview } from './components/Grid';
import LeftPanel from './components/LeftPanel';
import DragPreview from './components/DragPreview';
import type { EditOutcome } from './components/tabs/WarningsTab';

// Pointer state for the drag in progress, kept in a ref (not React state) since it changes on every pointermove.
interface DragTracking {
  source: DragSource;
  startPointerY: number;
  // Viewport top of the dragged element when the drag began. The drag overlay starts there and then moves
  // exactly with the pointer, so its current top is startBoxTop + (pointerY - startPointerY).
  startBoxTop: number;
  pointerX: number;
  pointerY: number;
}

interface PendingEdit {
  plan: EditPlan;
  source: DragSource;
  conflictsHtml: string;
}

// Edge auto-scroll only ever scrolls the grid. @dnd-kit considers the scrollable ancestors of the droppable under the
// pointer -- or, when there isn't one, of the dragged item itself (for a drag out of the pool, the pool list) -- and
// its edge test compares only the pointer's vertical position with each container's top/bottom bands, not whether
// the pointer is horizontally inside that container. So without this, dragging a pool session over the grid at the
// height of the pool's top or bottom edge scrolled the pool. Scrolling the pool mid-drag is never useful anyway:
// the whole list is a single drop target ("return to list").
const AUTO_SCROLL_OPTIONS = {
  threshold: { x: 0.08, y: 0.08 },
  canScroll: (element: Element) => element.classList.contains('grid-scheduler-scroll'),
};

function dropKey(drop: DropResolution | null): string {
  if (!drop) return '';
  switch (drop.kind) {
    case 'place':
      return `place:${drop.roomid}:${drop.startMinutes}`;
    case 'swap':
      return `swap:${drop.target.scheduleid}`;
    default:
      return drop.kind;
  }
}

function sourceDuration(source: DragSource): number {
  return source.kind === 'scheduled' ? source.item.durationMinutes : source.session.durationMinutes;
}

export default function App({ initialData }: { initialData: BootstrapData }) {
  // Starts empty -- no room columns shown until staff explicitly check them in the Rooms tab.
  const [visibleRoomIds, setVisibleRoomIds] = useState<Set<number>>(() => new Set());
  const [schedule, setSchedule] = useState<ScheduleItemData[]>(initialData.schedule);
  const [sessionsToBeScheduled, setSessionsToBeScheduled] = useState<SessionSearchResult[]>([]);
  const [sessionInfo, setSessionInfo] = useState<SessionInfoData | null>(null);
  const [sessionInfoLoading, setSessionInfoLoading] = useState(false);
  const [activeTab, setActiveTab] = useState<TabKey>('rooms');
  const [swapMode, setSwapMode] = useState(false);
  const [snapMode, setSnapMode] = useState<SnapMode>('grid');
  const [saving, setSaving] = useState(false);
  const [lastOutcome, setLastOutcome] = useState<EditOutcome | null>(null);
  const [pendingEdit, setPendingEdit] = useState<PendingEdit | null>(null);
  const [activeSource, setActiveSource] = useState<DragSource | null>(null);
  const [currentDrop, setCurrentDrop] = useState<DropResolution | null>(null);
  // Sessions whose room/time changed without the user's own gesture causing it (see findUntouchedMoves()), drawn
  // with a highlight border until the next schedule edit or room show/hide.
  const [movedSessionIds, setMovedSessionIds] = useState<Set<number>>(() => new Set());
  // The last schedule the server confirmed -- the baseline for detecting untouched moves, and what a failed save
  // reverts to. Distinct from `schedule`, which may briefly hold an optimistic, unconfirmed state.
  const confirmedScheduleRef = useRef<ScheduleItemData[]>(initialData.schedule);
  // Swap Mode counterpart animation (swapAnimation.ts): the session whose real box is hidden at its destination
  // while a clone flies there, and the snapshot taken at drop time for the layout effect below to start from.
  const [animatingSessionId, setAnimatingSessionId] = useState<number | null>(null);
  const pendingSwapAnimationRef = useRef<SwapAnimationStart | null>(null);
  // Bumped by every request that will return a schedule (refreshes and saves), so a refresh response is only
  // applied if nothing newer was requested after it -- e.g. rapid room toggles, or a save started meanwhile.
  const scheduleRequestSeqRef = useRef(0);

  const displaySegments = useMemo(() => buildDisplaySegments(initialData.daySegments), [initialData.daySegments]);
  const planContext = useMemo(
    () => ({ schedule, rooms: initialData.rooms, conStartDateTime: initialData.conStartDateTime }),
    [schedule, initialData.rooms, initialData.conStartDateTime]
  );

  const dragRef = useRef<DragTracking | null>(null);
  const dropRef = useRef<DropResolution | null>(null);
  // Latest render's values, for the window-level listeners below (which outlive any single render).
  const latestRef = useRef({ swapMode, snapMode, schedule, displaySegments });
  latestRef.current = { swapMode, snapMode, schedule, displaySegments };

  // Editing is blocked while a save is in flight (so every drag starts from the server's latest schedule) and
  // while a conflicting edit awaits a Save Anyway / Cancel decision.
  const dragDisabled = saving || pendingEdit !== null;

  const sensors = useSensors(
    // A small activation distance so a plain click (e.g. on a box's info icon) never starts a drag.
    useSensor(PointerSensor, { activationConstraint: { distance: 5 } })
  );

  const recomputeDrop = useCallback(() => {
    const drag = dragRef.current;
    if (!drag) return;
    const latest = latestRef.current;
    const drop = resolveDrop({
      source: drag.source,
      pointerX: drag.pointerX,
      pointerY: drag.pointerY,
      boxTop: drag.startBoxTop + (drag.pointerY - drag.startPointerY),
      swapMode: latest.swapMode,
      snapMode: latest.snapMode,
      gridLineResolutionMinutes: initialData.gridLineResolutionMinutes,
      displaySegments: latest.displaySegments,
      schedule: latest.schedule,
    });
    if (dropKey(drop) !== dropKey(dropRef.current)) {
      dropRef.current = drop;
      setCurrentDrop(drop);
    }
  }, [initialData.gridLineResolutionMinutes]);

  // Track the pointer ourselves while dragging, and re-resolve the drop target whenever anything scrolls
  // (including @dnd-kit's own edge auto-scroll, which moves the grid under a stationary pointer).
  useEffect(() => {
    if (!activeSource) return;
    const onPointerMove = (event: PointerEvent) => {
      if (!dragRef.current) return;
      dragRef.current.pointerX = event.clientX;
      dragRef.current.pointerY = event.clientY;
      recomputeDrop();
    };
    window.addEventListener('pointermove', onPointerMove);
    document.addEventListener('scroll', recomputeDrop, true);
    return () => {
      window.removeEventListener('pointermove', onPointerMove);
      document.removeEventListener('scroll', recomputeDrop, true);
    };
  }, [activeSource, recomputeDrop]);

  function handleDragStart(event: DragStartEvent) {
    const source = event.active.data.current as DragSource;
    const activator = event.activatorEvent as PointerEvent;
    const boxEl = (activator.target as Element).closest('.grid-scheduler-session-box');
    dragRef.current = {
      source,
      startPointerY: activator.clientY,
      startBoxTop: boxEl ? boxEl.getBoundingClientRect().top : activator.clientY,
      pointerX: activator.clientX,
      pointerY: activator.clientY,
    };
    dropRef.current = null;
    setCurrentDrop(null);
    setActiveSource(source);
  }

  function endDrag() {
    dragRef.current = null;
    dropRef.current = null;
    setActiveSource(null);
    setCurrentDrop(null);
  }

  function handleDragEnd(event: DragEndEvent) {
    const source = event.active.data.current as DragSource;
    recomputeDrop();
    const drop = dropRef.current;
    endDrag();
    if (!drop) return;
    const plan = planDrop(planContext, source, drop);
    if (plan) {
      void performEdit(plan, source, false);
    }
  }

  // Runs right after the render that moved a swap counterpart to its destination (hidden, via animatingSessionId),
  // before paint, so the counterpart never visibly jumps.
  useLayoutEffect(() => {
    const start = pendingSwapAnimationRef.current;
    if (!start) return;
    pendingSwapAnimationRef.current = null;
    runSwapAnimation(start, () => setAnimatingSessionId((current) => (current === start.sessionid ? null : current)));
  }, [animatingSessionId, schedule, sessionsToBeScheduled]);

  // The single entry point for schedules coming back from the server: highlights whatever moved relative to the
  // last confirmed schedule that the caller's own edits didn't touch, then adopts the new schedule as confirmed.
  function receiveServerSchedule(serverSchedule: ScheduleItemData[], touchedSessionIds: Set<number>) {
    setMovedSessionIds(findUntouchedMoves(confirmedScheduleRef.current, serverSchedule, touchedSessionIds));
    confirmedScheduleRef.current = serverSchedule;
    setSchedule(serverSchedule);
  }

  async function performEdit(plan: EditPlan, source: DragSource, ignoreConflicts: boolean) {
    // Snapshot the swap counterpart where it is now, before the optimistic update below moves it.
    if (plan.counterpart && !prefersReducedMotion()) {
      const start = captureSwapCounterpart(plan.counterpart.sessionid, plan.counterpart.destination);
      if (start) {
        pendingSwapAnimationRef.current = start;
        setAnimatingSessionId(start.sessionid);
      }
    }
    // Optimistic: show the result immediately, then replace it with the server's fresh schedule.
    setSchedule(plan.optimisticSchedule);
    setSessionsToBeScheduled((prev) => applyPlanToPool(prev, plan));
    setPendingEdit(null);
    if (plan.edits.length === 0) return; // client-side only (an unscheduled session dropped on the cabinet)

    // A real schedule edit clears the previous highlights; the response below may set new ones.
    setMovedSessionIds(new Set());
    scheduleRequestSeqRef.current++; // supersedes any refresh still in flight
    setSaving(true);
    try {
      const response = await editSchedule(plan.edits, ignoreConflicts);
      // Every session named in an edit -- the dragged one and any swap counterpart -- counts as touched.
      receiveServerSchedule(response.schedule, new Set(plan.edits.map((edit) => edit.sessionid)));
      if (response.status === 'applied') {
        setLastOutcome({ kind: 'applied', messages: plan.messages, conflictsHtml: response.conflictsHtml });
        if (response.conflictsHtml) setActiveTab('warnings');
      } else {
        setSessionsToBeScheduled((prev) => revertPlanFromPool(prev, plan, source));
        if (response.status === 'conflicts') {
          setPendingEdit({ plan, source, conflictsHtml: response.conflictsHtml });
        } else {
          setLastOutcome({ kind: 'error', messages: [response.message], conflictsHtml: '' });
        }
        setActiveTab('warnings');
      }
    } catch (e) {
      setSchedule(confirmedScheduleRef.current);
      setSessionsToBeScheduled((prev) => revertPlanFromPool(prev, plan, source));
      setLastOutcome({ kind: 'error', messages: [e instanceof Error ? e.message : 'Save failed.'], conflictsHtml: '' });
      setActiveTab('warnings');
    } finally {
      setSaving(false);
    }
  }

  // Re-fetches the schedule, picking up other staff members' changes (highlighted as untouched moves). Skipped
  // while a save is in flight: its response carries an even fresher schedule, and a refresh racing it could
  // land after it with older data.
  async function refreshSchedule() {
    if (saving) return;
    const seq = ++scheduleRequestSeqRef.current;
    try {
      const serverSchedule = await getSchedule();
      if (seq === scheduleRequestSeqRef.current) {
        receiveServerSchedule(serverSchedule, new Set());
      }
    } catch (e) {
      if (seq === scheduleRequestSeqRef.current) {
        setLastOutcome({
          kind: 'error',
          heading: "Couldn't refresh the schedule.",
          messages: [e instanceof Error ? e.message : 'Request failed.'],
          conflictsHtml: '',
        });
        setActiveTab('warnings');
      }
    }
  }

  function handleToggleRoom(roomid: number) {
    // Showing/hiding rooms shifts columns sideways; clear highlights so that never reads as sessions moving.
    // The refresh below may then highlight sessions others have moved since the last refresh.
    setMovedSessionIds(new Set());
    void refreshSchedule();
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

  // While dragging: where a release would land. While a conflicting edit awaits Save Anyway / Cancel: where it
  // would have landed (the grid itself has reverted to the confirmed schedule by then).
  const dropPreview: DropPreview | null =
    activeSource && currentDrop?.kind === 'place'
      ? { roomid: currentDrop.roomid, startMinutes: currentDrop.startMinutes, durationMinutes: sourceDuration(activeSource) }
      : (pendingEdit?.plan.landing ?? null);
  const dropLabel = activeSource && currentDrop ? describeDrop(planContext, activeSource, currentDrop) : null;
  const panelDragOver = currentDrop?.kind === 'cabinet' || currentDrop?.kind === 'pool' ? currentDrop.kind : null;

  return (
    <DndContext
      sensors={sensors}
      // Only used to tell @dnd-kit's auto-scroller which container the pointer is over (see Grid.tsx's
      // droppable); drop targets themselves are resolved by dropTarget.ts.
      collisionDetection={pointerWithin}
      autoScroll={AUTO_SCROLL_OPTIONS}
      onDragStart={handleDragStart}
      onDragEnd={handleDragEnd}
      onDragCancel={endDrag}
    >
      <div
        className={
          'grid-scheduler-app' +
          (activeSource ? ' grid-scheduler-dragging' : '') +
          (pendingEdit ? ' grid-scheduler-awaiting-confirmation' : '')
        }
      >
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
            warnings={{
              lastOutcome,
              pendingConflict: pendingEdit && {
                messages: pendingEdit.plan.messages,
                conflictsHtml: pendingEdit.conflictsHtml,
              },
              saving,
              onOverrideConflict: () => pendingEdit && void performEdit(pendingEdit.plan, pendingEdit.source, true),
              onCancelConflict: () => setPendingEdit(null),
            }}
            dragDisabled={dragDisabled}
            dragOverTarget={panelDragOver}
            animatingSessionId={animatingSessionId}
            swapMode={swapMode}
            onSwapModeChange={setSwapMode}
            snapMode={snapMode}
            onSnapModeChange={setSnapMode}
          />
          <Grid
            data={initialData}
            schedule={schedule}
            visibleRoomIds={visibleRoomIds}
            onInfoClick={handleInfoClick}
            dropPreview={dropPreview}
            dragDisabled={dragDisabled}
            movedSessionIds={movedSessionIds}
            animatingSessionId={animatingSessionId}
          />
        </div>
      </div>
      <DragOverlay dropAnimation={null} style={{ pointerEvents: 'none' }}>
        {activeSource && (
          <DragPreview source={activeSource} trackTagUsage={initialData.trackTagUsage} label={dropLabel} />
        )}
      </DragOverlay>
    </DndContext>
  );
}
