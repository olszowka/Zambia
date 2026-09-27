import type { BootstrapData } from './types';
import Grid from './components/Grid';

export default function App({ initialData }: { initialData: BootstrapData }) {
  return (
    <div className="grid-scheduler-app">
      <div className="grid-scheduler-toolbar">
        Grid Scheduler (New) — read-only preview, phase 1 of the React rewrite. No drag-and-drop yet.
      </div>
      <Grid data={initialData} />
    </div>
  );
}
