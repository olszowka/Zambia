import { useRef } from 'react';

const SUPPRESS_CLASS = 'suppress-hover';

// When a click causes a different button to render in the exact same screen position (e.g.
// Save -> Edit, or Cancel -> Delete, after the row leaves edit mode), the mouse cursor hasn't
// moved, so the browser correctly treats the new button as hovered -- CSS :hover reflects
// cursor position, not click history, so tabbing to move focus elsewhere has no effect on it.
// Suppress hover styling on this container until the mouse actually moves again.
export function useSuppressHoverAfterClick() {
  const ref = useRef<HTMLDivElement | null>(null);

  function onClickCapture() {
    const el = ref.current;
    if (!el) {
      return;
    }
    el.classList.add(SUPPRESS_CLASS);
    const clear = () => {
      el.classList.remove(SUPPRESS_CLASS);
      window.removeEventListener('mousemove', clear);
    };
    window.addEventListener('mousemove', clear, { once: true });
  }

  return { ref, onClickCapture };
}
