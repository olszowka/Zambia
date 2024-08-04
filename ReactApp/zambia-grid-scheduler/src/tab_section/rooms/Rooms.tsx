// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Peter Olszowka on 2024-Mar-08
import React from 'react';
import Form from 'react-bootstrap/Form';
import { RoomType } from "../../context/UnifiedContextTypes";
import { useUnifiedContext } from "../../context/UnifiedContext";

interface roomEntryProps {
    room: RoomType;
}

interface roomsListProps {
    roomsArr: RoomType[];
}

function Rooms() {
    const { state } = useUnifiedContext();
    return(<RoomsList roomsArr={state.configuration.rooms} />);
}

function RoomsList(props: roomsListProps) {
    return(
        <>
            {props.roomsArr
                .sort((a, b) => (a.display_order - b.display_order))
                .map((room) => (<RoomEntry room={room} key={room.roomid} />))
            }
        </>
    );
}

function RoomEntry(props: roomEntryProps) {
    return (
        <div>
            <Form.Check
                type='checkbox'
                id={`room-check-${props.room.roomid}`}
                label={props.room.roomname}
            />
        </div>
    );
}
export default Rooms;
