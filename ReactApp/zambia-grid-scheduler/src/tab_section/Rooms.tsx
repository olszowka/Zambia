import React from 'react';
import Form from 'react-bootstrap/Form';

type roomType = {
    roomid: number;
    roomname: string;
    display_order: number;
};

interface roomEntryProps {
    room: roomType;
}

interface roomsListProps {
    roomsArr: roomType[];
}

function Rooms() {
    const root = document.getElementById('zambia-grid-scheduler');
    const zgsRoomData = root?.dataset.zgsRooms;
    let roomsArr: roomType[];
    if (zgsRoomData) {
        roomsArr = JSON.parse(decodeURIComponent(zgsRoomData));
    } else {
        roomsArr = [];
    }
    return(<RoomsList roomsArr={roomsArr} />);
}

function RoomsList(props: roomsListProps) {
    return(
        <>
            {props.roomsArr.
                sort((a, b) => (a.display_order - b.display_order)).
                map((room) => (<RoomEntry room={room} key={room.roomid} />))
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
