import * as React from 'react';
import { Label } from '../Elements/Label';
import { AcceptButton } from '../Buttons/AcceptButton';
import { DeclineButton } from '../Buttons/DeclineButton';
import { FormSelect } from './FormSelect';

export function FormInlineSelectWLabel(
    props: {
        labelName: string;
        changeEvent: (e: Event) => void;
        selectName: string;
        selectOptions: {
            value: string|number;
            name: string;
        }[];
        acceptClickEvent: (e: Event) => void;
        declineClickEvent: (e: Event) => void;
        selectDefaultValue?: number;
        declineDataName? : string;
    }
) {

    const { 
        labelName,
        changeEvent,
        selectName,
        selectDefaultValue,
        selectOptions,
        acceptClickEvent: acceptClickEven,
        declineClickEvent,
        declineDataName: acceptDeclineDataName,
    } = props;

    return (
        <>
            <Label 
                classes='form-inline font-size-1-5 hover padding-r-1 display-block-important'
                text={labelName}
                
            />
            <div className="form-group">
                <FormSelect 
                    selectName={selectName}
                    changeEvent={changeEvent}
                    selectDefaultValue={selectDefaultValue}
                    selectOptions={selectOptions}
                />
            </div>
            <span style={{ paddingLeft: "2%" }}></span>
            <AcceptButton clickEvent={(e: Event) => acceptClickEven(e)} dataName={acceptDeclineDataName} />
            <DeclineButton clickEvent={(e: Event) => declineClickEvent(e)} dataName={acceptDeclineDataName} />
        </>
    )
}