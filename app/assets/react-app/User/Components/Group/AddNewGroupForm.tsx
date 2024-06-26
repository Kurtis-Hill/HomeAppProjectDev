import * as React from 'react';
import { useState } from 'react';

import { addNewGroupRequest } from '../../Request/Group/AddNewGroupRequest';
import InputWLabel from '../../../Common/Components/Inputs/InputWLabel';
import CloseButton from '../../../Common/Components/Buttons/CloseButton';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';
import SubmitButton from '../../../Common/Components/Buttons/SubmitButton';
import GroupResponseInterface from '../../Response/Group/GroupResponseInterface';

export type AddNewGroupUserInput = {
    groupName: string;
}
export function AddNewGroupForm(props: {
    setAddNewGroupModal: ((show: boolean) => void);
    setRefreshNavDataFlag: (newValue: boolean) => void;
}) {
    const setRefreshNavDataFlag = props.setRefreshNavDataFlag;
    const setAddNewGroupModal = props.setAddNewGroupModal;

    const [addNewGroupInputs, setAddNewGroupInputs] = useState<AddNewGroupUserInput>({
        groupName: '',
    })

    const [errors, setErrors] = useState<string[]>([]);

    const [groupRequestLoading, setGroupRequestLoading] = useState<boolean>(false);

    // const [newGroupAddData, setNewGroupAddedData] = useState<GroupResponseInterface|null>(null);
    const [newGroupAddData, setNewGroupAddedData] = useState<GroupResponseInterface|null>(null);

    const handleAddNewGroupInput = (event: Event) => {
        const name = (event.target as HTMLInputElement).name;
        const value = (event.target as HTMLInputElement).value;

        setAddNewGroupInputs({
            ...addNewGroupInputs,
            [name]: value,
        });
    }

    const handleAddNewGroupSubmit = async (e: Event) => {
        e.preventDefault();
        setErrors([]);
        setGroupRequestLoading(true);

        const jsonFormData = {
            groupName: addNewGroupInputs.groupName,
        };

        try {
            const addNewGroupResponse = await addNewGroupRequest(jsonFormData);
            if (addNewGroupResponse !== undefined && addNewGroupResponse.status === 201) {
                const addNewGroupPayload: GroupResponseInterface = addNewGroupResponse.data.payload;
                setNewGroupAddedData(addNewGroupPayload);
                setGroupRequestLoading(false);
                setErrors([]);
                setRefreshNavDataFlag(true);
            } else {
                setGroupRequestLoading(false);
                setErrors(['Something has gone wrong']);
            }
        } catch (error) {
            setGroupRequestLoading(false);
            const addNewGroupErrors: string[] = error;
            setErrors(addNewGroupErrors);
        }
    }

    const addNewGroupFormInputs = (): React => {
        return (
            <>
                <InputWLabel
                    labelName='Group Name'
                    name='groupName'
                    value={addNewGroupInputs.groupName}
                    onChangeFunction={handleAddNewGroupInput}
                    autoFocus={true}
                />

                {
                    groupRequestLoading === false && newGroupAddData === null
                        ?
                            <SubmitButton
                                type="submit"
                                text='Add New Group'
                                name='Add New Group'
                                action='submit'
                                classes='add-new-submit-button'
                            />
                        :
                            null
                }
            </>
        );
    }

    return (
        <>
            {
                errors.length > 0 
                    ?
                        <div className="error-container">
                            <div className="form-modal-error-box">
                                <ol>
                                    {errors.map((error: string, index: number) => (
                                        <li key={index} className="form-modal-error-text">{Object.keys(error).length === 0 ? 'Something has gone wrong' : error}</li>
                                    ))}
                                </ol>
                            </div>
                        </div>
                    : null
            }
            <form onSubmit={(e: Event) => {handleAddNewGroupSubmit(e)}} id="add-new-group-form">
            {
                    groupRequestLoading !== false
                        ? <DotCircleSpinner classes="spinner-inline-center" spinnerSize={3} />
                        :
                        newGroupAddData === null
                            ? addNewGroupFormInputs()
                            : <div className="padding-bottom">Success new group name: {`${newGroupAddData.groupName}`} new groupID: {`${newGroupAddData.groupID}`}</div>
                }
                { 
                    groupRequestLoading === false
                        ?
                            <CloseButton 
                                close={setAddNewGroupModal} 
                                classes={"modal-cancel-button"} 
                            />
                        : 
                            null
                }
            </form>
        </>
    );
}
