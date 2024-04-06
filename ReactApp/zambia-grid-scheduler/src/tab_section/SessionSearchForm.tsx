// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Peter Olszowka on 2024-Mar-08
import React from 'react';
import Form from 'react-bootstrap/Form';
import Col from 'react-bootstrap/Col';
import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import TrackSelect, { trackType } from "./TrackSelect";
import TagPicker, { tagType } from "./TagPicker";
import TypeSelect, { typeType } from "./TypeSelect";
import DivisionSelect, { divisionType } from "./DivisionSelect";
import { Button } from "react-bootstrap";
import { resetSearchForm, submitSearchForm } from "./SessionSearchUtilities";

type sessionsSearchDataType = {
    tracks: trackType[];
    tags: tagType[];
    types: typeType[];
    divisions: divisionType[];
}
function SessionSearchForm() {
    const root = document.getElementById('zambia-grid-scheduler');
    const zgsSessionsSearchDataEnc = root?.dataset.zgsSessionsSearch;
    let tracksArr: trackType[];
    let tagsArr: tagType[];
    let typesArr: typeType[];
    let divisionsArr: divisionType[];
    let sessionsSearchData: sessionsSearchDataType;
    if (zgsSessionsSearchDataEnc) {
        sessionsSearchData = JSON.parse(decodeURIComponent(zgsSessionsSearchDataEnc));
        tracksArr = sessionsSearchData.tracks;
        tagsArr = sessionsSearchData.tags;
        typesArr = sessionsSearchData.types;
        divisionsArr = sessionsSearchData.divisions;
    } else {
        tracksArr = [];
        tagsArr = [];
        typesArr = [];
        divisionsArr = [];
    }

    return(
        <Container fluid>
            <Row>
                <Col xs={3}>
                    <label htmlFor='track-sel'>Track:</label>
                </Col>
                <Col xs={9}>
                    <TrackSelect tracksArr={tracksArr} />
                </Col>
            </Row>
            <Row>
                <Col xs={3} className='justify-content-center'>
                    <label htmlFor='tag-picker'>Tags:</label>
                </Col>
                <Col xs={9}>
                    <TagPicker tagsArr={tagsArr} />
                </Col>
            </Row>
            <Row>
                <Col xs={{span:10, offset:2}} className='smaller-font'>
                    <Form.Check
                        inline
                        label='Match Any'
                        name='tag-match'
                        type='radio'
                        id='tag-match-any'
                    />
                    <Form.Check
                        inline
                        label='Match All'
                        name='tag-match'
                        type='radio'
                        id='tag-match-all'
                    />
                </Col>
            </Row>
            <Row>
                <Col xs={3}>
                    <label htmlFor='type-sel'>Type:</label>
                </Col>
                <Col xs={9}>
                    <TypeSelect typesArr={typesArr} />
                </Col>
            </Row>
            <Row>
                <Col xs={3}>
                    <label htmlFor='division-sel'>Divison:</label>
                </Col>
                <Col xs={9}>
                    <DivisionSelect divisionsArr={divisionsArr} />
                </Col>
            </Row>
            <Row>
                <Col xs={4} className='no-padding-right'>
                    <label htmlFor='session-id-inp'>Session ID:</label>
                </Col>
                <Col xs={8}>
                    <Form.Control id='session-id-inp' size='sm' type='text' />
                </Col>
            </Row>
            <Row>
                <Col xs={3}>
                    <label htmlFor='title-inp'>Title:</label>
                </Col>
                <Col xs={9}>
                    <Form.Control id='title-inp' size='sm' type='text' />
                </Col>
            </Row>
            <Row>
                <Col xs={{span:9, offset:3}} className='smaller-font'>
                    <span>Leave blank for ANY.</span>
                </Col>
            </Row>
            <Row>
                <Col xs={7}>
                    <label htmlFor='persons-assigned-chk'>Persons assigned:</label>
                </Col>
                <Col xs={5}>
                    <Form.Check
                        type='checkbox'
                        id='persons-assigned-chk'
                    />
                </Col>
            </Row>
            <Row>
                <Col xs={{span:11, offset:1}}>
                    <Button variant="primary" onClick={submitSearchForm}>Retrieve</Button>
                    <Button variant="secondary" onClick={resetSearchForm}>Reset Search</Button>
                </Col>
            </Row>
        </Container>
    );
}


export default SessionSearchForm;
