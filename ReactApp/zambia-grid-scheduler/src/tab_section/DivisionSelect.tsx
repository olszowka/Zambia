import React from 'react';
import Form from 'react-bootstrap/Form';

export type divisionType = {
    divisionid: number;
    divisionname: string;
    display_order: number;
};

interface DivisionEntryProps {
    division: divisionType;
}

interface DivisionSelectProps {
    divisionsArr: divisionType[];
}

function DivisionSelect(props: DivisionSelectProps) {
    return(
        <Form.Select id='division-sel' size='sm'>
            <option value={0}>ANY</option>
            {props.divisionsArr.
            sort((a, b) => (a.display_order - b.display_order)).
            map((division) => (<DivisionEntry division={division} key={division.divisionid} />))
            }
        </Form.Select>
    );
}

function DivisionEntry(props: DivisionEntryProps) {
    return (<option value={props.division.divisionid}>{props.division.divisionname}</option>)
}

export default DivisionSelect;
