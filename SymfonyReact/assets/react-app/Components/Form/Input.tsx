import * as React from 'react';

export default function Input(props) {
    const name = props.name ?? ''
    const value = props.value ?? ''
    const type = props.type ?? 'text'
    const placeHolder = props.placeHolder ?? ''
    const autoComplete = props.autoComplete ?? ''
    const onChangeFunction = props.onChangeFunction ?? function ()

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
