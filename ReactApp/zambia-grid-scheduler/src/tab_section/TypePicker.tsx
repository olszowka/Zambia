import React from 'react';
import Form from 'react-bootstrap/Form';

export type typeType = {
    typeid: number;
    typename: string;
    display_order: number;
};

interface TypeEntryProps {
    type: typeType;
}

interface TypePickerProps {
    typesArr: typeType[];
}

function TypePicker(props: TypePickerProps) {
    return(<div></div>);
}

export default TypePicker;
