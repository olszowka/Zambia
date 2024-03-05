import React from 'react';
import Form from 'react-bootstrap/Form';

export type trackType = {
    trackid: number;
    trackname: string;
    display_order: number;
};

interface TrackEntryProps {
    track: trackType;
}

interface TrackSelectProps {
    tracksArr: trackType[];
}

function TrackSelect(props: TrackSelectProps) {
    return(
        <Form.Select id='track-sel' size='sm'>
            <option value={0}>ANY</option>
            {props.tracksArr.
                sort((a, b) => (a.display_order - b.display_order)).
                map((track) => (<TrackEntry track={track} key={track.trackid} />))
            }
        </Form.Select>
    );
}

function TrackEntry(props: TrackEntryProps) {
    return (<option value={props.track.trackid}>{props.track.trackname}</option>)
}

export default TrackSelect;
