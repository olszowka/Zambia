import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import App from './App';
import type { BootstrapData } from './types';
import './App.css';

function mount() {
  const rootEl = document.getElementById('configure-permissions-root');
  const dataEl = document.getElementById('configure-permissions-data');
  if (!rootEl || !dataEl || !dataEl.textContent) {
    return;
  }
  const bootstrapData = JSON.parse(dataEl.textContent) as BootstrapData;
  createRoot(rootEl).render(
    <StrictMode>
      <App initialData={bootstrapData} />
    </StrictMode>
  );
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', mount);
} else {
  mount();
}
