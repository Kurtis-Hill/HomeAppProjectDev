import * as React from 'react';
import { useState } from 'react';
import AddNewGroupUserInputInterface from './AddNewGroupUserInputInterface';
import { addNewGroupRequest } from '../../Request/Group/AddNewGroupRequest';
import AddNewGroupResponseInterface from '../../Request/Group/AddNewGroupResponseInterface';

import InputWLabel from '../../../Common/Components/Inputs/InputWLabel';
import CloseButton from '../../../Common/Components/Buttons/CloseButton';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';
import SubmitButton from '../../../Common/Components/Buttons/SubmitButton';


export function AddNewGroup(props: {
    setAddNewGroupModal: ((show: boolean) => void);
    setRefreshNavDataFlag: (newValue: boolean) => void;
}) {
    const setRefreshNavDataFlag = props.setRefreshNavDataFlag;
    const setAddNewGroupModal = props.setAddNewGroupModal;

    const [addNewGroupInputs, setAddNewGroupInputs] = useState<AddNewGroupUserInputInterface>({
        groupName: '',
    })

    const [errors, setErrors] = useState<string[]>([]);

    const [groupRequestLoading, setGroupRequestLoading] = useState<boolean>(false);

    const [newGroupAddData, setNewGroupAddedData] = useState<GroupResponseInterface|null>(null);

    const handleAddNewGroupInput = (event: { target: { name: string; value: string; }; }) => {
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
                const addNewGroupPayload: AddNewGroupResponseInterface = addNewGroupResponse.data.payload;
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

    const addNewGroupForm = (): React => {
        return (
            <form onSubmit={handleAddNewGroupSubmit}>
                <InputWLabel
                    labelName='Room Name'
                    name='roomName'
                    value={addNewGroupInputs.groupName}
                    onChangeFunction={handleAddNewGroupInput}
                />

                {
                    groupRequestLoading === false && newGroupAddData === null
                        ?
                            <SubmitButton
                                type="submit"
                                text='Add New Group'
                                name='Add New Group'
                                action='POST'
                                classes='add-new-submit-button'
                            />
                        :
                            null
                }
                {
                    groupRequestLoading === false
                        ?
                            <CloseButton
                                close={setAddNewGroupModal}
                                classes={'modal-cancel-button'}
                            />
                        :
                            <DotCircleSpinner />


                }
            </form>
        );
    }
}
