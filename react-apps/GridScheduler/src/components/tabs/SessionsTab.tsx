import { useState } from 'react';
import type { LookupEntry, SessionSearchResult } from '../../types';
import { searchSessions } from '../../api';

interface SessionsTabProps {
  tracks: LookupEntry[];
  tags: LookupEntry[];
  types: LookupEntry[];
  divisions: LookupEntry[];
  // sessionids already in the "sessions to be scheduled" pool -- excluded from new search results so
  // Retrieve can be pressed repeatedly (with different filters) without duplicating list entries.
  currSessionIds: number[];
  onResults: (results: SessionSearchResult[]) => void;
}

interface SearchForm {
  trackId: number;
  typeId: number;
  divisionId: number;
  tagIds: number[];
  tagmatch: 'any' | 'all';
  sessionId: string;
  title: string;
  personsAssigned: boolean;
}

const initialFormState: SearchForm = {
  trackId: 0,
  typeId: 0,
  divisionId: 0,
  tagIds: [],
  tagmatch: 'any',
  sessionId: '',
  title: '',
  personsAssigned: false,
};

export default function SessionsTab({ tracks, tags, types, divisions, currSessionIds, onResults }: SessionsTabProps) {
  const [form, setForm] = useState<SearchForm>(initialFormState);
  const [loading, setLoading] = useState(false);
  const [noResultsFound, setNoResultsFound] = useState(false);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  async function handleRetrieve() {
    setLoading(true);
    setErrorMessage(null);
    setNoResultsFound(false);
    try {
      const results = await searchSessions({
        currSessionIds,
        trackId: form.trackId,
        tagIds: form.tagIds,
        tagmatch: form.tagmatch,
        typeId: form.typeId,
        divisionId: form.divisionId,
        sessionId: form.sessionId === '' ? null : parseInt(form.sessionId, 10),
        title: form.title,
        personsAssigned: form.personsAssigned,
      });
      if (results.length === 0) {
        setNoResultsFound(true);
      } else {
        onResults(results);
      }
    } catch (e) {
      setErrorMessage(e instanceof Error ? e.message : 'Search failed.');
    } finally {
      setLoading(false);
    }
  }

  function handleReset() {
    setForm(initialFormState);
    setNoResultsFound(false);
    setErrorMessage(null);
  }

  return (
    <div className="grid-scheduler-sessions-tab">
      <label className="grid-scheduler-form-row">
        <span className="grid-scheduler-form-label">Track:</span>
        <select value={form.trackId} onChange={(e) => setForm({ ...form, trackId: Number(e.target.value) })}>
          <option value={0}>ANY</option>
          {tracks.map((t) => (
            <option key={t.id} value={t.id}>
              {t.name}
            </option>
          ))}
        </select>
      </label>
      <div className="grid-scheduler-form-row grid-scheduler-form-row-top">
        <span className="grid-scheduler-form-label" id="tags-container-label">
          Tag:
        </span>
        {/* Same checkbox-list-container/-label-wrapper/-label/-check classes (and the same markup
            shape) as the Tags control on the Edit/Create Session page
            (webpages/xsl/EditCreateSession.xsl) -- those are already styled by
            webpages/css/zambia_bs5_customizations.css, loaded unconditionally -- plus the
            -compact modifier classes (same file) for tighter padding/smaller checkboxes/smaller label
            text, so fewer tag names wrap in this narrower sidebar. The height is overridden separately
            (grid-scheduler-tag-checklist), to twice the old <select multiple>'s rendered height
            (32px -> 64px) rather than the Edit/Create Session page's own 12rem. */}
        <div
          className="checkbox-list-container checkbox-list-container-compact form-control grid-scheduler-tag-checklist"
          role="group"
          aria-labelledby="tags-container-label"
        >
          {tags.map((t) => (
            <div className="checkbox-list-label-wrapper checkbox-list-label-wrapper-compact" key={t.id}>
              <label className="checkbox-list-label checkbox-list-label-compact">
                <input
                  type="checkbox"
                  className="checkbox-list-check checkbox-list-check-compact"
                  checked={form.tagIds.includes(t.id)}
                  onChange={(e) =>
                    setForm({
                      ...form,
                      tagIds: e.target.checked
                        ? [...form.tagIds, t.id]
                        : form.tagIds.filter((id) => id !== t.id),
                    })
                  }
                />
                {t.name}
              </label>
            </div>
          ))}
        </div>
      </div>
      <div className="grid-scheduler-tagmatch">
        <label>
          <input
            type="radio"
            name="tagmatch"
            checked={form.tagmatch === 'any'}
            onChange={() => setForm({ ...form, tagmatch: 'any' })}
          />
          Match Any
        </label>
        <label>
          <input
            type="radio"
            name="tagmatch"
            checked={form.tagmatch === 'all'}
            onChange={() => setForm({ ...form, tagmatch: 'all' })}
          />
          Match All
        </label>
      </div>
      <label className="grid-scheduler-form-row">
        <span className="grid-scheduler-form-label">Type:</span>
        <select value={form.typeId} onChange={(e) => setForm({ ...form, typeId: Number(e.target.value) })}>
          <option value={0}>ANY</option>
          {types.map((t) => (
            <option key={t.id} value={t.id}>
              {t.name}
            </option>
          ))}
        </select>
      </label>
      <label className="grid-scheduler-form-row">
        <span className="grid-scheduler-form-label">Division:</span>
        <select value={form.divisionId} onChange={(e) => setForm({ ...form, divisionId: Number(e.target.value) })}>
          <option value={0}>ANY</option>
          {divisions.map((d) => (
            <option key={d.id} value={d.id}>
              {d.name}
            </option>
          ))}
        </select>
      </label>
      <label className="grid-scheduler-form-row">
        <span className="grid-scheduler-form-label">Session ID:</span>
        <input value={form.sessionId} onChange={(e) => setForm({ ...form, sessionId: e.target.value })} />
      </label>
      <label className="grid-scheduler-form-row">
        <span className="grid-scheduler-form-label">Title:</span>
        <input value={form.title} onChange={(e) => setForm({ ...form, title: e.target.value })} />
      </label>
      <div className="grid-scheduler-form-note">Leave blank for "any".</div>
      <label className="grid-scheduler-form-row">
        <span className="grid-scheduler-form-label grid-scheduler-form-label-wide">Persons Assigned:</span>
        <input
          type="checkbox"
          className="grid-scheduler-checkbox-field"
          checked={form.personsAssigned}
          onChange={(e) => setForm({ ...form, personsAssigned: e.target.checked })}
        />
      </label>
      <div className="grid-scheduler-sessions-tab-buttons">
        <button type="button" className="btn btn-primary btn-sm" onClick={handleRetrieve} disabled={loading}>
          Retrieve
        </button>
        <button type="button" className="btn btn-secondary btn-sm" onClick={handleReset}>
          Reset Search
        </button>
      </div>
      {noResultsFound && <div className="grid-scheduler-no-sessions-found">No new sessions matched.</div>}
      {errorMessage && <div className="grid-scheduler-search-error">{errorMessage}</div>}
    </div>
  );
}
