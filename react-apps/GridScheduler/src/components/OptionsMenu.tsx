import { useEffect, useRef, useState } from 'react';
import type { SnapMode } from '../types';
import { SNAP_MODES } from '../dropTarget';

interface OptionsMenuProps {
  swapMode: boolean;
  onSwapModeChange: (swapMode: boolean) => void;
  snapMode: SnapMode;
  onSnapModeChange: (snapMode: SnapMode) => void;
}

// Page-level scheduling options (Swap Mode, Snap), in a dropdown next to the pool's Clear All button. Styled with
// Bootstrap's dropdown classes but opened/closed by React state rather than Bootstrap's JS, so it doesn't depend
// on bootstrap.bundle being loaded or on Bootstrap's auto-close treating clicks on the controls inside as "outside".
// Stays open while its controls are used; closes on a click elsewhere or Escape.
export default function OptionsMenu({ swapMode, onSwapModeChange, snapMode, onSnapModeChange }: OptionsMenuProps) {
  const [open, setOpen] = useState(false);
  const containerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (!open) return;
    const onPointerDown = (event: PointerEvent) => {
      if (!containerRef.current?.contains(event.target as Node)) setOpen(false);
    };
    const onKeyDown = (event: KeyboardEvent) => {
      if (event.key === 'Escape') setOpen(false);
    };
    document.addEventListener('pointerdown', onPointerDown);
    document.addEventListener('keydown', onKeyDown);
    return () => {
      document.removeEventListener('pointerdown', onPointerDown);
      document.removeEventListener('keydown', onKeyDown);
    };
  }, [open]);

  return (
    <div className="dropdown grid-scheduler-options" ref={containerRef}>
      <button
        type="button"
        className={'btn btn-secondary btn-sm dropdown-toggle' + (open ? ' show' : '')}
        aria-expanded={open}
        onClick={() => setOpen((prev) => !prev)}
      >
        Options
      </button>
      {open && (
        <div className="dropdown-menu show grid-scheduler-options-menu">
          <label className="grid-scheduler-options-row">
            <input
              type="checkbox"
              className="form-check-input"
              checked={swapMode}
              onChange={(e) => onSwapModeChange(e.target.checked)}
            />
            Swap Mode
          </label>
          <label className="grid-scheduler-options-row">
            Snap:
            <select
              className="form-select form-select-sm"
              value={String(snapMode)}
              onChange={(e) => onSnapModeChange(e.target.value === 'grid' ? 'grid' : (Number(e.target.value) as SnapMode))}
            >
              {SNAP_MODES.map((mode) => (
                <option key={mode.value} value={String(mode.value)}>
                  {mode.label}
                </option>
              ))}
            </select>
          </label>
        </div>
      )}
    </div>
  );
}
