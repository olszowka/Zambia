import type { RoomData } from '../../types';

interface RoomsTabProps {
  rooms: RoomData[];
  visibleRoomIds: Set<number>;
  onToggleRoom: (roomid: number) => void;
}

export default function RoomsTab({ rooms, visibleRoomIds, onToggleRoom }: RoomsTabProps) {
  return (
    <div className="grid-scheduler-rooms-tab">
      {rooms.map((room) => (
        <label key={room.roomid} className="grid-scheduler-room-checkbox">
          <input
            type="checkbox"
            checked={visibleRoomIds.has(room.roomid)}
            onChange={() => onToggleRoom(room.roomid)}
          />
          {room.roomname}
        </label>
      ))}
    </div>
  );
}
