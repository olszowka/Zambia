import { useState } from 'react';

// Purely visual for now -- swaps between the open/closed cabinet images on hover, matching
// webpages/StaffMaintainSchedule.php's fileCabinetIMG / staffMaintainSchedule.js's fileCabinetSwap().
// Actually archiving a session dropped onto this icon is drag-and-drop-phase work (see the "Dropped on
// file-cabinet icon" column of the drag-and-drop matrix in the rewrite plan) -- not wired up yet.
export default function FileCabinetIcon() {
  const [open, setOpen] = useState(false);

  return (
    <img
      id="fileCabinetIMG"
      className="grid-scheduler-file-cabinet"
      height={65}
      width={49}
      src={open ? 'images/FileCabinetOpen.png' : 'images/FileCabinetClosed.png'}
      onMouseOver={() => setOpen(true)}
      onMouseOut={() => setOpen(false)}
      alt="drop here to archive"
    />
  );
}
