import * as React from 'react';
import InputWLabel from './InputWLabel';
import { AcceptButton } from '../Buttons/AcceptButton';
import { DeclineButton } from '../Buttons/DeclineButton';

export function FormInlineInputWLabel(props: {
    labelName: string;
    nameParam: string;
    changeEvent: (event: Event) => void;
    value: object|string|number;
    acceptClickEvent: (e: Event) => void;
    declineClickEvent: (e: Event) => void;
    dataName?: string;
}) {

    const { 
        labelName, 
        nameParam, 
        changeEvent, 
        value, 
        acceptClickEvent, 
        declineClickEvent, 
        dataName ,
    } = props;
    return (
        <>
            <InputWLabel
                labelName={labelName}
                name={nameParam}
                type="text"
                onChangeFunction={(e: Event) => changeEvent(e)}
                autoFocus={true}
                value={value}
                labelExtraClasses='form-inline font-size-1-5 padding-r-1 display-block-important'
                extraClasses=''
            />
            <span style={{ paddingLeft: "2%" }}>
                <AcceptButton clickEvent={(e: Event) => acceptClickEvent(e)} dataName={dataName} />
                <DeclineButton clickEvent={(e: Event) => declineClickEvent(e)} dataName={dataName} />
            </span>
        </>
    )
}
