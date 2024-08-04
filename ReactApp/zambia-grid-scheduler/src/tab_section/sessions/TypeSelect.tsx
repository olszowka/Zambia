// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Peter Olszowka on 2024-Mar-08
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

interface TypeSelectProps {
    typesArr: typeType[];
}

function TypeSelect(props: TypeSelectProps) {
    return(
        <Form.Select id='type-sel' size='sm'>
            <option value={0}>ANY</option>
            {props.typesArr
                .sort((a, b) => (a.display_order - b.display_order))
                .map((type) => (<TypeEntry type={type} key={type.typeid} />))
            }
        </Form.Select>
    );
}

function TypeEntry(props: TypeEntryProps) {
    return (<option value={props.type.typeid}>{props.type.typename}</option>)
}

export default TypeSelect;
