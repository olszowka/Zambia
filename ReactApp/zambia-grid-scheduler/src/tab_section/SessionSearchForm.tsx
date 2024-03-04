import React from 'react';
import Form from 'react-bootstrap/Form';
import Col from 'react-bootstrap/Col';
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import TrackSelect, { trackType } from "./TrackSelect";
import TagPicker, { tagType } from "./TagPicker";
import TypePicker, { typeType } from "./TypePicker";

type sessionsSearchDataType = {
    tracks: trackType[];
    tags: tagType[];
    types: typeType[];
}
function SessionSearchForm() {
    const root = document.getElementById('zambia-grid-scheduler');
    const zgsSessionsSearchDataEnc = root?.dataset.zgsSessionsSearch;
    let tracksArr: trackType[];
    let tagsArr: tagType[];
    let typesArr: typeType[];
    let sessionsSearchData: sessionsSearchDataType;
    if (zgsSessionsSearchDataEnc) {
        sessionsSearchData = JSON.parse(decodeURIComponent(zgsSessionsSearchDataEnc));
        tracksArr = sessionsSearchData.tracks;
        tagsArr = sessionsSearchData.tags;
        typesArr = sessionsSearchData.types;
    } else {
        tracksArr = [];
        tagsArr = [];
        typesArr = [];
    }

    return(
        <Container fluid>
            <Row>
                <Col xs={3}>
                    <label htmlFor="track-sel">Track:</label>
                </Col>
                <Col xs={9}>
                    <TrackSelect tracksArr={tracksArr} />
                </Col>
            </Row>
            <Row>
                <Col xs={3}>
                    <label htmlFor="tag-picker">Tags:</label>
                </Col>
                <Col xs={9}>
                    <TagPicker tagsArr={tagsArr} />
                </Col>
            </Row>
        </Container>
    );
}


export default SessionSearchForm;
