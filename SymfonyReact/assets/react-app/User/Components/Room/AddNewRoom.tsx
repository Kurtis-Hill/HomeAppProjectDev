import * as React from 'react';
import { useState } from 'react';

import { addNewRoomRequest } from '../../Request/Room/AddNewRoomRequest';
import RoomResponseInterface from '../../Response/Room/RoomResponseInterface';
import AddNewRoomUserInputInterface from './AddNewRoomUserInputInterface';

import InputWLabel from '../../../Common/Components/Inputs/InputWLabel';
import CloseButton from '../../../Common/Components/Buttons/CloseButton';
import DotCircleSpinner from '../../../Common/Components/Spinners/DotCircleSpinner';
import SubmitButton from '../../../Common/Components/Buttons/SubmitButton';

export function AddNewRoom(props: {
        setAddNewRoomModal: ((show: boolean) => void);
        setRefreshNavDataFlag: (newValue: boolean) => void;
}) {
    const setRefreshNavDataFlag = props.setRefreshNavDataFlag;
    const setAddNewRoomModal = props.setAddNewRoomModal;

    const [addNewRoomInputs, setAddNewRoomInputs] = useState<AddNewRoomUserInputInterface>({
        roomName: '',
    })

    const [errors, setErrors] = useState<string[]>([]);

    const [roomRequestLoading, setRoomRequestLoading] = useState<boolean>(false);

    const [newRoomAddData, setNewRoomAddedData] = useState<RoomResponseInterface|null>(null);

    const handleAddNewRoomInput = (event: { target: { name: string; value: string; }; }) => {
        const name = (event.target as HTMLInputElement).name;
        const value = (event.target as HTMLInputElement).value;

        setAddNewRoomInputs({
            ...addNewRoomInputs,
            [name]: value,
        });
    }

    const handleAddNewRoomSubmit = async (e: Event) => {
        e.preventDefault();
        setErrors([]);
        setRoomRequestLoading(true);

        const jsonFormData = {
            roomName: addNewRoomInputs.roomName,
        };

        try {
            const addNewRoomResponse = await addNewRoomRequest(jsonFormData);
            if (addNewRoomResponse !== undefined && addNewRoomResponse.status === 201) {
                // console.log('mee twoo', addNewRoomResponse);
                const addNewRoomPayload: RoomResponseInterface = addNewRoomResponse.data.payload;
                // const addNewRoomResponse = addNewRoomResponse;
                setNewRoomAddedData(addNewRoomPayload);
                setRoomRequestLoading(false);
                setErrors([]);
                setRefreshNavDataFlag(true);
            } else {
                setRoomRequestLoading(false);
                setErrors(['Something has gone wrong']);
            }
        } catch (error) {
            setRoomRequestLoading(false);
            const addNewRoomErrors: string[] = error;
            setErrors(addNewRoomErrors);
        }
    }

    const addNewRoomFormInputs = (): React => {
        return (
            <>
                <InputWLabel
                    labelName='Room Name'
                    name='roomName'
                    value={addNewRoomInputs.roomName}
                    onChangeFunction={handleAddNewRoomInput}
                />

                {
                    roomRequestLoading === false && newRoomAddData === null
                        ?
                            <SubmitButton
                                type="submit"
                                text="Add Room"
                                name="Add-Room"
                                action="submit"
                                classes="add-new-submit-button"
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
            <form onSubmit={(e: Event) => {handleAddNewRoomSubmit(e)}} id="add-new-room-form">
                {
                    roomRequestLoading !== false
                        ? <DotCircleSpinner classes="spinner-inline-center" spinnerSize={3} />
                        :
                        newRoomAddData === null
                            ? addNewRoomFormInputs()
                            : <div className="padding-bottom">Success new room name: {`${newRoomAddData.roomName}`} new roomID: {`${newRoomAddData.roomID}`}</div>
                }
                { 
                    roomRequestLoading === false
                        ?
                            <CloseButton 
                                close={setAddNewRoomModal} 
                                classes={"modal-cancel-button"} 
                            />
                        : 
                            null
                }
            </form>
        </>
    );
}