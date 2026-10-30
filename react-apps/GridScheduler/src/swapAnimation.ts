// Animates the counterpart of a Swap Mode drop -- the session displaced by the dropped one -- from where it was to
// where it ended up (another grid slot, or the top of the "sessions to be scheduled" list), like the legacy
// page's swap animation (staffMaintainSchedule.js's dropOnScheduledSlot()). Never scrolls anything: if the
// destination is scrolled out of view, the session flies off the edge of the screen in that direction instead.
//
// FLIP-style: before the swap renders, snapshot the counterpart's box (clone + rect) -- captureSwapCounterpart().
// After the swap has rendered, with the real box already at its destination but hidden, fly the clone there and
// then reveal the real box -- runSwapAnimation().

const DURATION_MS = 600;

export interface SwapAnimationStart {
  sessionid: number;
  destination: 'grid' | 'pool';
  fromRect: DOMRect;
  clone: HTMLElement;
}

// data-sessionid is set on both SessionBox (grid) and UnscheduledSessionBox (pool).
function findGridBox(sessionid: number): HTMLElement | null {
  return document.querySelector<HTMLElement>(`.grid-scheduler-room-column [data-sessionid="${sessionid}"]`);
}

function findPoolBox(sessionid: number): HTMLElement | null {
  return document.querySelector<HTMLElement>(`[data-drop-kind="pool"] [data-sessionid="${sessionid}"]`);
}

export function prefersReducedMotion(): boolean {
  return window.matchMedia?.('(prefers-reduced-motion: reduce)').matches ?? false;
}

// Call before the swap is rendered. Returns null if the counterpart isn't on screen to animate from.
export function captureSwapCounterpart(sessionid: number, destination: 'grid' | 'pool'): SwapAnimationStart | null {
  const box = findGridBox(sessionid);
  if (!box) return null;
  const clone = box.cloneNode(true) as HTMLElement;
  // The clone is decoration only: keep it out of drop hit-testing and ids/selectors meant for the real box.
  clone.removeAttribute('data-drop-kind');
  clone.removeAttribute('data-sessionid');
  clone.removeAttribute('data-scheduleid');
  clone.classList.remove('grid-scheduler-session-box-moved', 'grid-scheduler-drag-source');
  return { sessionid, destination, fromRect: box.getBoundingClientRect(), clone };
}

interface Box {
  left: number;
  top: number;
  width: number;
  height: number;
}

function intersect(a: Box, b: Box): Box {
  const left = Math.max(a.left, b.left);
  const top = Math.max(a.top, b.top);
  const right = Math.min(a.left + a.width, b.left + b.width);
  const bottom = Math.min(a.top + a.height, b.top + b.height);
  return { left, top, width: Math.max(0, right - left), height: Math.max(0, bottom - top) };
}

// The part of the screen where the destination box can actually be seen: its scroll container's client area
// (excluding scrollbars), minus the grid's sticky room headers and time axis, clipped to the viewport.
function visibleAreaFor(destination: 'grid' | 'pool'): Box | null {
  const container = document.querySelector<HTMLElement>(
    destination === 'grid' ? '.grid-scheduler-scroll' : '[data-drop-kind="pool"]'
  );
  if (!container) return null;
  const rect = container.getBoundingClientRect();
  let area: Box = {
    left: rect.left + container.clientLeft,
    top: rect.top + container.clientTop,
    width: container.clientWidth,
    height: container.clientHeight,
  };
  if (destination === 'grid') {
    const corner = document.querySelector('.grid-scheduler-corner')?.getBoundingClientRect();
    if (corner) {
      area = intersect(area, { left: corner.right, top: corner.bottom, width: Infinity, height: Infinity });
    }
  }
  return intersect(area, { left: 0, top: 0, width: window.innerWidth, height: window.innerHeight });
}

function containsPoint(area: Box, x: number, y: number): boolean {
  return x >= area.left && x <= area.left + area.width && y >= area.top && y <= area.top + area.height;
}

// Keeps going from `from` toward `toward` until a box of from's size is entirely outside the viewport.
function offScreenEndpoint(from: DOMRect, toward: DOMRect): Box {
  const cx = from.left + from.width / 2;
  const cy = from.top + from.height / 2;
  const dx = toward.left + toward.width / 2 - cx;
  const dy = toward.top + toward.height / 2 - cy;
  const exits: number[] = [];
  if (dx > 0) exits.push((window.innerWidth + from.width / 2 - cx) / dx);
  if (dx < 0) exits.push((-from.width / 2 - cx) / dx);
  if (dy > 0) exits.push((window.innerHeight + from.height / 2 - cy) / dy);
  if (dy < 0) exits.push((-from.height / 2 - cy) / dy);
  const t = exits.length > 0 ? Math.min(...exits) : 0;
  return { left: from.left + dx * t, top: from.top + dy * t, width: from.width, height: from.height };
}

// Call after the swap has rendered (e.g. from useLayoutEffect), while the real destination box is hidden.
// onDone runs when the animation finishes (or immediately if there's nothing to animate) and should reveal it.
export function runSwapAnimation(start: SwapAnimationStart, onDone: () => void) {
  const destBox = start.destination === 'grid' ? findGridBox(start.sessionid) : findPoolBox(start.sessionid);
  if (!destBox) {
    onDone();
    return;
  }
  const destRect = destBox.getBoundingClientRect();
  const visibleArea = visibleAreaFor(start.destination);
  const destVisible =
    visibleArea !== null &&
    containsPoint(visibleArea, destRect.left + destRect.width / 2, destRect.top + destRect.height / 2);
  // Onscreen: land exactly on the destination box (resizing to it, e.g. grid -> full-width pool entry).
  // Offscreen: keep the original size and just leave the screen heading toward it.
  const end: Box = destVisible
    ? { left: destRect.left, top: destRect.top, width: destRect.width, height: destRect.height }
    : offScreenEndpoint(start.fromRect, destRect);

  const { clone, fromRect } = start;
  clone.classList.add('grid-scheduler-swap-animation');
  Object.assign(clone.style, {
    left: `${fromRect.left}px`,
    top: `${fromRect.top}px`,
    width: `${fromRect.width}px`,
    height: `${fromRect.height}px`,
  });
  document.body.appendChild(clone);
  const animation = clone.animate(
    [
      { left: `${fromRect.left}px`, top: `${fromRect.top}px`, width: `${fromRect.width}px`, height: `${fromRect.height}px` },
      { left: `${end.left}px`, top: `${end.top}px`, width: `${end.width}px`, height: `${end.height}px` },
    ],
    { duration: DURATION_MS, easing: 'ease-in-out', fill: 'forwards' }
  );
  let done = false;
  const finish = () => {
    if (done) return;
    done = true;
    clone.remove();
    onDone();
  };
  // The `finished` promise rather than the finish event: events are only dispatched on a rendered frame, while
  // the promise also settles without one. The timeout is a backstop so the real box is never left hidden.
  animation.finished.then(finish, finish);
  window.setTimeout(finish, DURATION_MS + 500);
}
