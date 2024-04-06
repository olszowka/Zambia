// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Peter Olszowka on 2024-Mar-08
import React from 'react';
import Form from 'react-bootstrap/Form';

export type tagType = {
    tagid: number;
    tagname: string;
    display_order: number;
};

interface TagEntryProps {
    tag: tagType;
}

interface TagPickerProps {
    tagsArr: tagType[];
}

function TagPicker(props: TagPickerProps) {
    return(
        <div className='checkbox-list-container'>
            {props.tagsArr
                .sort((a, b) => (a.display_order - b.display_order))
                .map((tag) => (<TagEntry tag={tag} key={tag.tagid} />))
            }
        </div>
    );
}

function TagEntry(props: TagEntryProps) {
    return (
        <Form.Check
            type='checkbox'
            id={`tag-check-${props.tag.tagid}`}
            label={props.tag.tagname}
        />
    );
}

export default TagPicker;
