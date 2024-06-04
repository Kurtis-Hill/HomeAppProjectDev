import * as React from 'react';
import InputWLabel from '../../../Common/Components/Inputs/InputWLabel';
import { AcceptButton } from '../../../Common/Components/Buttons/AcceptButton';
import { DeclineButton } from '../../../Common/Components/Buttons/DeclineButton';
import { checkAdmin } from '../../../Authentication/Session/UserSession';

export default function UpdateUserPassword(props: {
    toggleUserInput: (event: Event) => void, 
    updateUser: (event: Event) => void,
    onInputChange: (event: Event) => void,
}) {
    const { toggleUserInput, updateUser, onInputChange } = props;

    return (
        <React.Fragment>        
            <div className="form-group row">
                <div className="col-sm-6 mb-3 mb-sm-0">
                    <InputWLabel
                        type="password"
                        name="newPassword"
                        labelName="New Password" 
                        onChangeFunction={onInputChange}
                        />
                </div>
                {
                    checkAdmin() !== false 
                        ?                        
                            <div className="col-sm-6">
                                <InputWLabel
                                    type="password"
                                    name="oldPassword"
                                    labelName="Old Password"
                                    onChangeFunction={onInputChange}
                                />
                            </div>
                        :
                            null
                }
                <div style={{margin: 'auto'}}>

                    <AcceptButton
                        clickEvent={(e: Event) => updateUser(e)}
                        dataName='password'
                    />
                    <DeclineButton 
                        clickEvent={(e: Event) => toggleUserInput(e)}
                        dataName='password'
                    />
                </div>
            </div>
        </React.Fragment>
    );
}