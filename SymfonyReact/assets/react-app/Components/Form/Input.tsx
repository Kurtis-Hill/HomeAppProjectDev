import * as React from 'react';

export default function Input(props: { name: string; value?: object; type?: string; placeHolder?: string; autoComplete?: string; onChangeFunction?: any }) {
    const name: string = props.name ?? ''
    const value: object = props.value
    const type: string = props.type ?? 'text'
    const placeHolder: string = props.placeHolder ?? ''
    const autoComplete: string = props.autoComplete ?? ''
    const onChangeFunction: (event: { target: { name: string; value: string; }; }) => void = props.onChangeFunction ?? function (){}

    return (
        <React.Fragment>
            <div className="form-group">
                <input
                    type={type}
                    name={name}
                    placeholder={placeHolder}
                    autoComplete={autoComplete}
                    className={"form-control form-control-user login-form-field"}
                    onChange={onChangeFunction}
                    {...value}
                />
            </div>
        </React.Fragment>
    );
}
