import { useState } from 'react';

interface FileCabinetIconProps {
  // True while a dragged session is over the icon -- plain mouseover doesn't fire mid-drag, so the hover swap
  // has to be driven by the drag hit-testing instead (see dropTarget.ts).
  dragOver: boolean;
}

// Swaps between the open/closed cabinet images on hover, matching webpages/StaffMaintainSchedule.php's
// fileCabinetIMG / staffMaintainSchedule.js's fileCabinetSwap(). Dropping a session here removes it from the
// schedule (or just from the pool, if it wasn't scheduled) -- see planDrop() in scheduleEdits.ts.
export default function FileCabinetIcon({ dragOver }: FileCabinetIconProps) {
  const [hover, setHover] = useState(false);
  const open = hover || dragOver;

  return (
    <img
      id="fileCabinetIMG"
      data-drop-kind="cabinet"
      className="grid-scheduler-file-cabinet"
      height={65}
      width={49}
      src={open ? 'images/FileCabinetOpen.png' : 'images/FileCabinetClosed.png'}
      onMouseOver={() => setHover(true)}
      onMouseOut={() => setHover(false)}
      alt="drop here to archive"
      draggable={false}
    />
  );
}
