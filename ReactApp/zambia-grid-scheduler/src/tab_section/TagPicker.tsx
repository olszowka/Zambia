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
    return(<div></div>);
}

export default TagPicker;
