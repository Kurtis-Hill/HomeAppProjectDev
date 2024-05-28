import * as React from 'react';

export default function Input(props: { 
    name: string; 
    value?: object; 
    type?: string; 
    placeHolder?: string; 
    autoComplete?: string; 
    onChangeFunction?: any; 
    extraClasses?: string; 
    required?: boolean; 
    autoFocus?: boolean; 
    inputOnClickFn?: (event: Event) => void; 
    dataName?: string,
}) {
    const name: string = props.name ?? ''
    const value: object = props.value
    const type: string = props.type ?? 'text'
    const placeHolder: string = props.placeHolder ?? ''
    const autoComplete: string = props.autoComplete ?? ''
    const extraClasses: string = props.extraClasses ?? ''
    const required: boolean = props.required ?? true
    const autoFocus: boolean = props.autoFocus ?? false
    const inputOnClickFn: (event: Event) => void = props.inputOnClickFn ?? function (){}
    const dataName: string = props.dataName ?? ''

    const onChangeFunction: (event: { target: { name: string; value: string; }; }) => void = props.onChangeFunction ?? function (){}

    return (
        <React.Fragment>
            <div className="form-group">
                <input
                    required={required ?? ''}
                    type={type}
                    name={name}
                    placeholder={placeHolder}
                    autoComplete={autoComplete}
                    className={`form-control form-control-user ${extraClasses}`}
                    onChange={onChangeFunction}
                    autoFocus={autoFocus}
                    value={value}
                    onClick={(e: Event) => inputOnClickFn(e)}
                    data-name={dataName}
                />
            </div>
        </React.Fragment>
    );
}
